<?php

use function Livewire\Volt\{state, mount};

// State variables (same as your React props)
state([
    'result' => null,
    'sortedPositiveThemes' => [],
    'sortedNegativeThemes' => [],
    'chartData' => [],
]);

mount(function ($result) {
    $this->result = $result;

    // Sorting like React
    $this->sortedPositiveThemes = collect($result['keyPositiveThemes'])
        ->sortByDesc('count')
        ->values()
        ->toArray();

    $this->sortedNegativeThemes = collect($result['keyNegativeThemes'])
        ->sortByDesc('count')
        ->values()
        ->toArray();

    // Chart data for your Livewire/JS chart
    $this->chartData = [
        ['name' => 'Positive', 'value' => $result['positiveCount'], 'fill' => '#4ade80'],
        ['name' => 'Negative', 'value' => $result['negativeCount'], 'fill' => '#f87171'],
        ['name' => 'Neutral',  'value' => $result['neutralCount'],  'fill' => '#facc15'],
    ];
});
?>

<div class="space-y-8 animate-fade-in">

    {{-- Summary Section --}}
    <section class="p-6 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
        <h2 class="text-2xl font-bold mb-4 text-transparent bg-clip-text
            bg-gradient-to-r from-blue-600 to-teal-500
            dark:from-blue-400 dark:to-teal-300">
            Analysis Summary
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <div>
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    {{ $result['summary'] }}
                </p>

                @php
                    $sentiment = $result['overallSentiment'];

                    $sentimentColors = [
                        'POSITIVE' => 'text-green-600 dark:text-green-400',
                        'NEGATIVE' => 'text-red-600 dark:text-red-400',
                        'NEUTRAL'  => 'text-yellow-600 dark:text-yellow-400',
                    ];

                    $sentimentBg = [
                        'POSITIVE' => 'bg-green-100 border-green-300 dark:bg-green-900/50 dark:border-green-700',
                        'NEGATIVE' => 'bg-red-100 border-red-300 dark:bg-red-900/50 dark:border-red-700',
                        'NEUTRAL'  => 'bg-yellow-100 border-yellow-300 dark:bg-yellow-900/50 dark:border-yellow-700',
                    ];
                @endphp

                <div class="p-4 rounded-lg border {{ $sentimentBg[$sentiment] }}">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Overall Sentiment</p>
                    <p class="text-3xl font-bold {{ $sentimentColors[$sentiment] }}">
                        {{ $sentiment }}
                    </p>
                </div>

            </div>

            {{-- Replace with your chart component --}}
            <div class="h-64">
                @livewire('sentiment-chart', ['data' => $chartData])
            </div>
        </div>
    </section>

    {{-- Key Themes Section --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Positive Themes --}}
        <div class="p-6 rounded-2xl border bg-white/60 border-gray-200
            dark:bg-gray-800/50 dark:border-gray-700">

            <h3 class="text-xl font-semibold mb-3 text-green-600 dark:text-green-400">
                What Customers Love
            </h3>

            <ul class="space-y-4">
                @foreach ($sortedPositiveThemes as $item)
                    <li class="flex flex-col text-gray-700 dark:text-gray-300">

                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-500 flex-shrink-0"
                                     fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd"
                                           d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                           clip-rule="evenodd"/>
                                </svg>
                                {{ $item['theme'] }}
                            </div>

                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full
                                bg-green-100 text-green-700
                                dark:bg-green-500/20 dark:text-green-300">
                                {{ $item['count'] }}
                            </span>
                        </div>

                        <div class="mt-2 pl-7 flex items-start space-x-2 ml-2 pt-1 pb-1
                            text-gray-600 border-l-2 border-gray-300
                            dark:text-gray-400 dark:border-gray-700">

                            <svg class="h-5 w-5 flex-shrink-0 text-pink-600 dark:text-pink-400"
                                 fill="currentColor" viewBox="0 0 20 20">
                                 <path fill-rule="evenodd"
                                       d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                       clip-rule="evenodd"/>
                            </svg>

                            <p class="text-sm italic">"{{ $item['summarySnippet'] }}"</p>
                        </div>

                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Negative Themes --}}
        <div class="p-6 rounded-2xl border bg-white/60 border-gray-200
            dark:bg-gray-800/50 dark:border-gray-700">

            <h3 class="text-xl font-semibold mb-3 text-red-600 dark:text-red-400">
                Negative Themes & Actions
            </h3>

            <ul class="space-y-4">
                @foreach ($sortedNegativeThemes as $item)
                    <li class="flex flex-col text-gray-700 dark:text-gray-300">

                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0 text-red-700 dark:text-red-500"
                                     fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd"
                                           d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                           clip-rule="evenodd"/>
                                </svg>
                                {{ $item['theme'] }}
                            </div>

                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full
                                bg-red-100 text-red-700
                                dark:bg-red-500/20 dark:text-red-300">
                                {{ $item['count'] }}
                            </span>
                        </div>

                        {{-- Details --}}
                        <div class="mt-2 pl-7 ml-2 space-y-3 py-2
                            text-gray-600 border-l-2 border-gray-300
                            dark:text-gray-400 dark:border-gray-700">

                            {{-- Summary Snippet --}}
                            <div class="flex items-start space-x-2">
                                <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="currentColor"
                                     viewBox="0 0 20 20">
                                     <path fill-rule="evenodd"
                                           d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                           clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm italic">"{{ $item['summarySnippet'] }}"</p>
                            </div>

                            {{-- Action Item --}}
                            <div class="flex items-start space-x-2">
                                <svg class="h-5 w-5 text-yellow-500 dark:text-yellow-400 flex-shrink-0 mt-0.5"
                                     fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round"
                                           d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <p class="text-sm">
                                    <span class="font-semibold text-yellow-500 dark:text-yellow-300">Suggestion:</span>
                                    {{ $item['actionItem'] }}
                                </p>
                            </div>
                        </div>

                    </li>
                @endforeach
            </ul>
        </div>

    </section>

    {{-- Rating Trend --}}
    <section class="p-6 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
        <h2 class="text-2xl font-bold mb-4 text-transparent bg-clip-text
            bg-gradient-to-r from-blue-600 to-teal-500 dark:from-blue-400 dark:to-teal-300">
            6-Month Rating Trend
        </h2>

        <div class="h-72">
            @livewire('rating-trend-chart', ['data' => $result['ratingTrend']])
        </div>
    </section>

</div>
