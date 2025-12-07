<div
    x-data="{ open: false }"
    class="w-full relative inline-block text-left"
>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
        Platforms
    </label>
    <x-button
        type="button"
        @click="open = !open"
        id="dropdownDefaultButton"
        class="w-full dark:bg-gray-600 dark:text-gray-100"
    >
        @php echo array_keys($platform)[0]; @endphp
        <svg
            class="w-4 h-4 ms-1.5 -me-0.5 transition-transform duration-200 transform origin-center"
            :class="{ 'rotate-180': open }"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            width="24"
            height="24"
            viewBox="0 0 24 24"
        >
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
        </svg>
    </x-button>

    <!-- Dropdown menu -->
    <div
        id="dropdown"
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="rounded-lg shadow-lg w-48 mt-2 z-20
                absolute ease-out duration-300
               bg-zinc-200 dark:bg-zinc-800
               border border-zinc-200 dark:border-zinc-700
               dark:ring-zinc-800 ring-2 ring-zinc-200/50"
    >
        <ul
            x-data="{ items: {
                'All Platforms': 'All',
                'Booking.com': 'booking',
                'TripAdvisor': 'tripadvisor',
                'Google': 'google-maps'
            } }"
            aria-labelledby="dropdownDefaultButton"
            class="p-1 text-sm text-body font-medium dark:text-gray-200"
        >
            <template x-for="(value, title) in items" :key="value">
                <li>
                    <a
                        href="#"
                        @click.prevent="$dispatch('platform-selected', { [title]: value }); open = false"
                        class="border-transparent transition-colors border px-2.5 py-2 flex rounded-lg w-full h-auto text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700/60 justify-start items-center hover:text-zinc-900 dark:hover:text-zinc-100 space-x-2 overflow-hidden group-hover:autoflow-auto items"
                    >
                        <span
                            x-text="title"
                            class="flex-shrink-0 ease-out duration-50"
                        ></span>
                    </a>
                </li>
            </template>
        </ul>
    </div>
</div>
