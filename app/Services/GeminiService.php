<?php

namespace App\Services;

use App\Models\Hotels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GeminiService {

    protected $apiUrl = "https://ormania.in/api";

    /**
     * Fetches customer reviews using internal AI API
     */
    public function getHotelAnalysis(string $hotelName, ?string $platform = Hotels::ALL_PLATFORMS): array
    {
        $url = $this->apiUrl . '/stats/' . $hotelName . '?platform=' . $platform;
        $response = Http::get($url);

        return $response->json();
    }
}

?>
