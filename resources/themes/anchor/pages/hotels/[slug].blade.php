<?php
use function Laravel\Folio\{name};
use function Livewire\Volt\{state, mount};
use Livewire\Volt\Component;
use App\Services\GeminiService;
name('hotels.slug');

new class extends Component {
    public $slug;
    public $result;
    public $targetRating = 4.5;
    public $daysGoal = 45;
    public $reviewsNeeded;
    public $perDayNeeded;

    public function mount($slug, GeminiService $service) {
        $this->slug = Str::headline($slug);
        $this->result = $service->getHotelAnalysis($this->slug);

        $this->calculateGoals();
    }

    public function calculateGoals() {
        if (!$this->result) return;
        $current = $this->result['average_rating'];
        $N = $this->result['total_reviews'];

        if ($current < $this->targetRating) {
            $currentSum = $current * $N;
            $needed = ceil((($this->targetRatingtarget * $N) - $currentSum) / (5 - $this->targetRating));
            $perDay = ceil($needed / $this->daysGoal);
            $this->reviewsNeeded = $needed > 0 ? $needed : 0;
            $this->perDayNeeded = $perDay . " -> 5 ⭐ reviews per day to reach a rating of " . $this->targetRating . " in the next " . $this->daysGoal . " days";
        } else {
            $this->reviewsNeeded = "Goal Met!";
            $this->perDayNeeded = "";
        }
    }
}
?>

<x-layouts.app>
    @volt('hotels.show')
        <div class='py-2 lg:mt-2'>

            {{-- Filters start --}}
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
                            step="0.1"
                        />

                        {{-- Days to achieve --}}
                        <x-hotels.integer-input
                            name="days_to_achieve"
                            title="Days to achieve"
                            value="45"
                            step="1"
                        />
                    </div>
                </div>
                <div class="flex items-center justify-between mb-6">
                    <div class="max-w-4xl w-1/2 text-left">
                        <h2 class="mt-2 text-2xl font-bold tracking-tight text-foreground text-gray-800 sm:text-4xl dark:text-gray-200">
                            <span class="text-primary">{{ $slug }}</span>
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
            {{-- Filters end --}}

            <div class="space-y-8 animate-fade-in">

                {{-- Metrics Grid starts --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Current Rating -->
                    <div class="rounded-xl p-4 border bg-white/60 dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400">
                            Current Rating
                        </p>
                        <div class="flex items-center space-x-2 mt-2">
                            <p class="text-3xl font-semibold !text-gray-800 dark:!text-gray-200">{{ $result['average_rating'] }}</p>
                            <span class="text-yellow-500 text-3xl">⭐</span>
                        </div>
                    </div>

                    <!-- Market Rank -->
                    <div class="rounded-xl p-4 border bg-white/60 dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400">
                            Market Rank
                        </p>
                        <div class="mt-2">
                            <p class="text-3xl font-semibold !text-gray-800 dark:!text-gray-200">#{{ $result['rank'] }}</p>
                            <p class="text-xs !text-green-600 dark:!text-green-400">out of {{ $result['total_hotels'] }}</p>
                        </div>
                    </div>

                    <!-- Total Reviews -->
                    <div class="rounded-xl p-4 border bg-white/60 dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400">
                            Total Reviews
                        </p>
                        <p class="text-3xl font-semibold !text-gray-800 dark:!text-gray-200 mt-2">{{ $result['total_reviews'] }}</p>
                    </div>

                    <!-- Reviews Needed -->
                    <div class="rounded-xl p-4 border bg-white/60 dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                        <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400">
                            Reviews Needed
                        </p>
                        <p class="text-2xl font-semibold !text-gray-800 dark:!text-gray-200 mt-2">
                            <span>{{ $reviewsNeeded }}</span>
                            <span class="text-green-600 dark:text-green-400">{{ $perDayNeeded }}</span>
                        </p>
                    </div>
                </div>

                {{-- Chart Section 1 starts --}}
                <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-hotels.review-distribution-chart :result="$result" />
                    <x-hotels.topic-sentiment-chart :result="$result" />
                </section>
                {{-- Chart Section 1 ends --}}

                {{-- Chart Section 2 starts --}}
                <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-hotels.approval-rating-chart :result="$result" />
                    <x-hotels.disapproval-rating-chart :result="$result" />
                </section>
                {{-- Chart Section 2 ends --}}

                {{-- Market Intelligence section starts --}}
                <section class="bg-white/60 dark:bg-gray-800/50">
                    <h2 class="text-2xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-teal-500
                        dark:from-blue-400 dark:to-teal-300">
                        Market Intelligence
                    </h2>
                    <div class="flex items-center justify-between gap-6">
                        <x-hotels.area-trends />
                        <x-hotels.comparative-standing-chart :result="$result" />
                    </div>
                </section>
                {{-- Market Intelligence section ends --}}
            </div>

        </div>
    @endvolt
</x-layouts.app>
