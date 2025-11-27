<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GeminiService {

    protected $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";

    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');

        if (!$this->apiKey) {
            throw new \Exception("GEMINI_API_KEY environment variable is not set");
        }
    }

    /**
     * Fetches customer reviews using Gemini + Maps grounding
     */
    public function getBusinessReviews(string $businessName, ?array $location = null): array
    {
        $prompt = 'Find and list the text of several recent customer reviews for the business named "' . $businessName . '".';

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "tools" => [
                ["googleMaps" => new \stdClass()]
            ]
        ];

        // Inject location grounding config if provided
        if ($location) {
            $payload["toolConfig"] = [
                "retrievalConfig" => [
                    "latLng" => [
                        "latitude" => $location["latitude"],
                        "longitude" => $location["longitude"]
                    ]
                ]
            ];
        }

        $response = Http::post($this->apiUrl . "?key=" . $this->apiKey, $payload);

        $responseData = $response->json();
        $reviews = [];

        $candidate = $responseData['candidates'][0] ?? null;

        if ($candidate && isset($candidate['groundingMetadata']['groundingChunks'])) {
            foreach ($candidate['groundingMetadata']['groundingChunks'] as $chunk) {
                $snippets = $chunk['maps']['placeAnswerSources']['reviewSnippets'] ?? null;
                if ($snippets) {
                    foreach ($snippets as $snippet) {
                        if (!empty($snippet['text'])) {
                            $reviews[] = $snippet['text'];
                        }
                    }
                }
            }
        }

        // Fallback: extract text if no structured review data
        $text = trim($candidate['content']['parts'][0]['text'] ?? '');

        if (empty($reviews) && !empty($text)) {
            $potential = array_filter(explode("\n", $text), fn($line) => strlen($line) > 20);
            if (count($potential) > 1) {
                $reviews = array_values($potential);
            } else {
                $reviews[] = $text;
            }
        }

        return $reviews;
    }

    /**
     * Sentiment analysis with structured JSON output
     */
    public function analyzeSentiment(string $businessName, array $reviews): array
    {
        if (empty($reviews)) {
            return ['error' => 'No reviews provided'];
        }

        $reviewsText = "";
        foreach ($reviews as $i => $review) {
            $reviewsText .= "Review " . ($i + 1) . ': "' . $review . "\"\n\n";
        }

        $prompt = "Here are customer reviews for a business:\n\n" . $reviewsText . "\n\nPlease perform a detailed sentiment analysis. For each positive and negative theme, count how many times it is referred to in the reviews and include this count. For each positive theme, provide a short summary snippet of what customers loved about it. Crucially, for each negative theme, provide a concise summary snippet of the complaint, and then provide a separate, actionable suggestion for the business manager to address the issue. Finally, based on the overall sentiment of these reviews, create a plausible trend of average monthly star ratings for the last 6 months (ending in the current month). The rating must be a number between 1 and 5.";

        $analysisSchema = [
            "type" => "object",
            "properties" => [
                "overallSentiment" => [ "type" => "string", "description" => 'Overall sentiment: POSITIVE, NEGATIVE, or NEUTRAL.' ],
                "positiveCount" => [ "type" => "integer", "description" => 'Total count of positive reviews.' ],
                "negativeCount" => [ "type" => "integer", "description" => 'Total count of negative reviews.' ],
                "neutralCount" => [ "type" => "integer", "description" => 'Total count of neutral reviews.' ],
                "summary" => [ "type" => "string", "description" => 'A concise 2-3 sentence summary of the key findings from all reviews.' ],
                "keyPositiveThemes" => [
                    "type" => "array",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "theme" => [ "type" => "string", "description" => 'The identified positive theme.' ],
                            "count" => [ "type" => "integer", "description" => 'The number of times this theme was mentioned in the reviews.' ],
                            "summarySnippet" => [ "type" => "string", "description" => 'A short snippet or quote summarizing what customers loved about this theme.' ]
                        ],
                        "required" => ['theme', 'count', 'summarySnippet']
                    ],
                    "description" => 'A list of key positive themes or topics mentioned, sorted in descending order of their count, with a summary snippet for each.'
                ],
                "keyNegativeThemes" => [
                    "type" => "array",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "theme" => [ "type" => "string", "description" => 'The identified negative theme.' ],
                            "count" => [ "type" => "integer", "description" => 'The number of times this theme was mentioned in the reviews.' ],
                            "summarySnippet" => [ "type" => "string", "description" => 'A short snippet or quote summarizing the customer complaint for this theme.' ],
                            "actionItem" => [ "type" => "string", "description" => 'A concise, actionable suggestion for the business manager to address this theme.' ]
                        ],
                        "required" => ['theme', 'count', 'summarySnippet', 'actionItem']
                    ],
                    "description" => 'A list of key negative themes or topics mentioned, sorted in descending order of their count, with a summary snippet and an actionable suggestion for each.'
                ],
                "ratingTrend" => [
                    "type" => "array",
                    "description" => 'A plausible trend of average monthly star ratings for the last 6 months, estimated from the sentiment of the provided reviews. The array should contain exactly 6 entries, sorted from oldest to most recent month. Ratings must be between 1 and 5.',
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "month" => [ "type" => "string", "description" => 'The abbreviated month name, e.g., "Jan", "Feb".' ],
                            "averageRating" => [ "type" => "number", "description" => 'The estimated average star rating for that month (a number from 1 to 5).' ]
                        ],
                        "required" => ['month', 'averageRating']
                    ]
                ],
            ],
            "required" => ['overallSentiment', 'positiveCount', 'negativeCount', 'neutralCount', 'summary', 'keyPositiveThemes', 'keyNegativeThemes', 'ratingTrend'],
        ];

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "systemInstruction" => [
                "parts" => [
                    ["text" => "You are an expert sentiment analysis AI. Your task is to analyze customer reviews and provide a structured JSON output based on the provided schema. Ensure your analysis is accurate and covers all aspects of the schema, including counting theme occurrences, sorting them, generating summary snippets for both positive and negative themes, generating actionable suggestions for negative themes, and estimating a plausible 6-month rating trend."]
                ]
            ],
            "generationConfig" => [
                "responseMimeType" => "application/json",
                "responseSchema" => $analysisSchema
            ]
        ];

        $response = Http::post($this->apiUrl . "?key=" . $this->apiKey, $payload);

        $jsonText = $response->body(); // Already JSON
        $fileContents = json_decode($jsonText)->candidates[0]->content->parts[0]->text;

        $fileName = Str::slug($businessName, '_');
        $file = str($fileName)->prepend('sentimentAnalysisData/')->append('.json');
        Storage::put($file, $fileContents);

        // return json_decode($jsonText, true) ?? [];
        return json_decode($fileContents, true) ?? [];
    }
}

?>
