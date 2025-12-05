<?php
use function Laravel\Folio\{name};
use function Livewire\Volt\{state, mount};
use Livewire\Volt\Component;
name('hotels.slug');

new class extends Component {
    public $slug;
    public $result;
    public $sortedPositiveThemes = [];
    public $sortedNegativeThemes = [];
    public $chartData = [];
    public $sentiment;
    public $ratingTrendChart = [];

    public $sentimentColors = [
        'POSITIVE' => 'text-green-600 dark:text-green-400',
        'NEGATIVE' => 'text-red-600 dark:text-red-400',
        'NEUTRAL'  => 'text-yellow-600 dark:text-yellow-400',
    ];

    public $sentimentBg = [
        'POSITIVE' => 'bg-green-100 border-green-300 dark:bg-green-900/50 dark:border-green-700',
        'NEGATIVE' => 'bg-red-100 border-red-300 dark:bg-red-900/50 dark:border-red-700',
        'NEUTRAL'  => 'bg-yellow-100 border-yellow-300 dark:bg-yellow-900/50 dark:border-yellow-700',
    ];

    public function mount($slug) {
        $this->slug = Str::headline($slug);
        $this->result = json_decode(Storage::get('sentimentAnalysisData/'.$slug.'.json'));

        $labels = array_map(
            fn ($item) => $item->month,
            $this->result->ratingTrend
        );

        $data = array_map(
            fn ($item) => $item->averageRating,
            $this->result->ratingTrend
        );

        $this->ratingTrendChart = compact('labels', 'data');

        $this->sentiment = $this->result->overallSentiment;

        // Sort positive themes
        $this->sortedPositiveThemes = collect($this->result->keyPositiveThemes)
            ->sortByDesc('count')
            ->values()
            ->toArray();

        // Sort negative themes
        $this->sortedNegativeThemes = collect($this->result->keyNegativeThemes)
            ->sortByDesc('count')
            ->values()
            ->toArray();

        // Pie chart data
        $this->chartData = [
            'labels' => ['Negative', 'Neutral', 'Positive'],
            'data' => [$this->result->negativeCount, $this->result->neutralCount, $this->result->positiveCount],
            'backgroundColors' => ['#f87171', '#facc15', '#4ade80']
        ];
    }
}
?>

<x-layouts.app>
    @volt('hotels.show')
        <div class='py-2 lg:mt-2'>
            <div class='w-full'>
                <div class="flex items-center justify-between">
                    <x-elements.back-button
                        class="w-1/2 max-w-full mx-auto mb-3"
                        text="Back to Hotels"
                        :href="route('hotels')"
                    />

                    <div class="w-1/2 flex items justify-between gap-4">
                        {{-- Platform Dropdown --}}
                        <x-hotels.platforms-dropdown />
                        {{-- Target Rating --}}
                        <x-hotels.integer-input
                            name="target_ratings"
                            title="Target Ratings"
                            value="4.5"
                        />
                        {{-- Days to achieve --}}
                        <x-hotels.integer-input
                            name="days_to_achieve"
                            title="Days to achieve"
                            value="45"
                        />
                    </div>

                </div>
                <div class="flex items-center justify-between mb-6">
                    <div class='max-w-4xl w-1/2 text-left'>
                        <h2 class='mt-2 text-2xl font-bold tracking-tight text-foreground text-gray-800 sm:text-4xl dark:text-gray-200'>
                            <span class='text-primary'>{{ $slug }}</span>
                        </h2>
                    </div>
                    <div class="w-1/2 pt-6 text-right">
                        <x-button class="dark:bg-gray-600 dark:text-gray-100" type="submit">
                            <i class="ri-download-2-line ri-xl mb-1"></i>
                            <span class="text-xl">Reports</span>
                        </x-button>
                    </div>
                </div>
            </div>

            <div class="space-y-8 animate-fade-in">

                {{-- Summary Section --}}
                <section class="p-6 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <h2 class="text-2xl font-bold mb-4 text-transparent bg-clip-text
                        bg-gradient-to-r from-blue-600 to-teal-500
                        dark:from-blue-400 dark:to-teal-300">
                        Analysis Summary
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                        <div>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                {{ $result->summary }}
                            </p>

                            <div class="p-4 rounded-lg border {{ $sentimentBg[$sentiment] }}">
                                <p class="text-sm text-gray-600">Overall Sentiment</p>
                                <p class="text-3xl font-bold {{ $sentimentColors[$sentiment] }}">
                                    {{ $sentiment }}
                                </p>
                            </div>

                        </div>

                        {{-- Replace with your chart component --}}
                        <div class="h-64">
                            <canvas id="sentiment-chart"></canvas>
                        </div>
                        <script type="module">
                            const ctx = document.getElementById('sentiment-chart').getContext('2d')

                            new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: @json($chartData['labels']),
                                    datasets: [
                                        {
                                            label: 'Avg. Rating',
                                            data: @json($chartData['data']),
                                            backgroundColor: @json($chartData['backgroundColors']),
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        intersect: false,
                                        mode: 'index',
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            })
                        </script>
                    </div>
                </section>

                {{-- Key Themes Section --}}
                <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="
                            p-6 rounded-2xl border
                            bg-white/60 border-gray-200
                            dark:bg-gray-800/50 dark:border-gray-700
                        "
                    >
                        <h3 class="text-xl font-semibold mb-3 text-green-600 dark:text-green-400">
                            What Customers Love
                        </h3>
                        <ul class="space-y-4">
                            @foreach ($sortedPositiveThemes as $item)
                                <li
                                    class="flex flex-col text-gray-700 dark:text-gray-300"
                                >
                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex items-center">
                                            <svg
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                                class="w-5 h-5 mr-2 text-green-600 dark:text-green-500 flex-shrink-0"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    clip-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                ></path>
                                            </svg>
                                            {{ $item->theme }}
                                        </div>
                                        <span
                                            class="
                                                text-xs font-semibold px-2.5 py-0.5 rounded-full
                                                bg-green-100 text-green-700
                                                dark:bg-green-500/20 dark:text-green-300
                                            "
                                        >
                                            {{ $item->count }}
                                        </span>
                                    </div>
                                    <div
                                        class="
                                            mt-2 pl-7 flex items-start space-x-2 ml-2 pt-1 pb-1
                                            text-gray-600 border-l-2 border-gray-300
                                            dark:text-gray-400 dark:border-gray-700
                                        "
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="
                                                h-5 w-5 flex-shrink-0
                                                text-pink-600
                                                dark:text-pink-400
                                            "
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                            />
                                        </svg>
                                        <p class="text-sm italic">"{{ $item->summarySnippet }}"</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div
                        class="
                            p-6 rounded-2xl border
                            bg-white/60 border-gray-200
                            dark:bg-gray-800/50 dark:border-gray-700
                        "
                    >
                    <h3 class="text-xl font-semibold mb-3 text-red-600 dark:text-red-400">
                        Negative Themes & Actions
                    </h3>
                        <ul class="space-y-4">
                            @foreach ($sortedNegativeThemes as $item)
                                <li
                                    class="flex flex-col text-gray-700 dark:text-gray-300"
                                >
                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex items-center">
                                            <svg
                                                class="w-5 h-5 mr-2 flex-shrink-0 text-red-700 dark:text-red-500"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"
                                                ></path>
                                            </svg>
                                            {{ $item->theme }}
                                        </div>
                                        <span
                                            class="
                                                text-xs font-semibold px-2.5 py-0.5 rounded-full
                                                bg-red-100 text-red-700
                                                dark:bg-red-500/20 dark:text-red-300
                                            "
                                        >
                                            {{ $item->count }}
                                        </span>
                                    </div>
                                    <div
                                        class="
                                            mt-2 pl-7 ml-2 space-y-3 py-2
                                            text-gray-600 border-l-2 border-gray-300
                                            dark:text-gray-400 dark:border-gray-700
                                        "
                                    >
                                        {{-- Summary Snippet --}}
                                        <div class="flex items-start space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            <p class="text-sm italic">"{{ $item->summarySnippet }}"</p>
                                        </div>

                                        {{--  Action Item --}}
                                        <div class="flex items-start space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                            <p class="text-sm">
                                                <span class="font-semibold not-italic text-yellow-500 dark:text-yellow-300">Suggestion:</span>
                                                {{ $item->actionItem }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>

                {{-- Rating Trend Section --}}
                <section
                    class="
                        p-6 rounded-2xl border
                        bg-white/60 border-gray-200
                        dark:bg-gray-800/50 dark:border-gray-700
                    "
                >
                    <h2 class="text-2xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-teal-500
                        dark:from-blue-400 dark:to-teal-300">
                        6-Month Rating Trend
                    </h2>
                    <div class="h-72">
                        <div class="w-full h-72">
                            <canvas id="rating-trend-chart"></canvas>
                        </div>

                        <script type="module">
                            const ctx = document.getElementById('rating-trend-chart').getContext('2d')

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: @json($ratingTrendChart['labels']),
                                    datasets: [
                                        {
                                            label: 'Avg. Rating',
                                            data: @json($ratingTrendChart['data']),
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            backgroundColor: 'rgba(54, 162, 235, 0.4)',
                                            pointStyle: 'circle',
                                            pointRadius: 5,
                                            pointHoverRadius: 15
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        intersect: false,
                                        mode: 'index',
                                    },
                                    scales: {
                                        y: {
                                            min: 1,
                                            max: @json(ceil(max($ratingTrendChart['data']))+1),
                                            ticks: {
                                                stepSize: 1,
                                                callback: (value) => value
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            })
                        </script>
                    </div>
                </section>
            </div>

        </div>
    @endvolt
</x-layouts.app>
