<?php
use function Laravel\Folio\{middleware, name};
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Services\GeminiService;
middleware('auth');
name('hotels.create');

new class extends Component {
    public $info;
    public $analysis;

    #[Validate('required|url')]
    public $url = '';

    // public function mount() {
    //     $result = json_decode(Storage::get('sentimentAnalysisData/harshithram_residency.json'));

    // }

    public function save(GeminiService $geminiService) {
        $validated = $this->validate();

        try {
            $this->info = $this->extractBusinessInfoFromUrl($this->url);

            if (!$this->info) {
                session()->flash("error", "No valid business info found. Please enter a valid Google Maps business URL.");
                return;
            }

            $name = $this->info['name'];
            $location = $this->info['location'];

            $this->analysis = Storage::get('sentimentAnalysisData/harshithram_residency.json');

            // Fetch reviews
            $reviews = $geminiService->getBusinessReviews($name, $location);

            if(empty($reviews)) {
                session()->flash("error", "No customer reviews found for this business. Try another one.");
            }

            // Analyze sentiment from reviews
            $this->analysis = $geminiService->analyzeSentiment($name, $reviews);

            session()->flash('message', 'Review analysis completed successfully!');

            // $this->redirect(route('hotels'));

        } catch (\Throwable $th) {
            \Log::error('Hotel Review Analysis Error', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            session()->flash("error", "An error occurred during analysis. Check logs.");
        }
    }

    private function extractBusinessInfoFromUrl(string $url): ?array
    {
        try {
            $urlParts = parse_url($url);
            if (!isset($urlParts['host']) || !str_contains($urlParts['host'], 'google.com')) {
                return null;
            }

            $path = $urlParts['path'] ?? '';

            // Regex: capture name, latitude, longitude
            if (preg_match('/\/maps\/place\/([^\/]+)\/@(-?\d+\.\d+),(-?\d+\.\d+)/', $path, $matches)) {
                $name = urldecode(str_replace('+', ' ', $matches[1]));
                $latitude = floatval($matches[2]);
                $longitude = floatval($matches[3]);

                if ($name && $latitude && $longitude) {
                    return [
                        'name' => $name,
                        'address' => "Lat: {$latitude}, Lng: {$longitude}",
                        'location' => [
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                        ]
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

}
?>

<x-layouts.app>
    @volt('hotels.create')
        <x-app.container>
            <x-elements.back-button
                class="max-w-full mx-auto mb-3"
                text="Back to Hotels"
                :href="route('hotels')"
            />

            <div class="flex items-center justify-between mb-3">
                <x-app.heading
                    title="New Hotel"
                    description=""
                    :border="false"
                />
            </div>

            <form wire:submit="save" class="space-y-4 mb-5">
                <div>
                    <label
                        for="name"
                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200"
                    >
                        Hotel Google Map URL
                    </label>
                    <input
                        id="url"
                        type="text"
                        autofocus
                        wire:model.live="url"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-xs
                            focus:border-indigo-300 focus:ring-3 focus:ring-opacity-50"
                    >
                    @error('url') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-button type="submit">
                        Analyze
                    </x-button>
                </div>

                <div wire:loading>
                    <p class="text-xl">Analyzing...</p>
                </div>
            </form>

            @if($analysis)
                <pre class="overflow-x-auto font-mono text-xs text-gray-600 border rounded-lg bg-gray-50 dark:bg-zinc-700/50 dark:text-gray-100 dark:border-gray-800 border-gray-200/80 p-5">
                    {{ json_encode($analysis) }}
                </pre>
            @endif


            @if($info)
                <div class="mt-2 p-2 bg-green-100 rounded">
                    <strong>Name:</strong> {{ str(Str::slug($info['name'], '_'))->append('.json')->prepend('sentimentAnalysisData/') }} <br>
                    <strong>Address:</strong> {{ $info['address'] }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-500 text-white px-4 py-2 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
        </x-app.container>
    @endvolt
</x-layouts.app>
