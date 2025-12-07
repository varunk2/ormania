<?php
use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Models\Hotels;
middleware('auth');
name('hotels');

new class extends Component {
    public $hotels;

    public function mount() {
        if (auth()->user()->hasRole('admin')) {
            $this->hotels = Hotels::all();
        } else {
            $this->hotels = Hotels::join('hotel_user', 'hotels.id', '=', 'hotel_user.hotel_id')
                                    ->where('hotel_user.user_id', auth()->user()->id)
                                    ->get();
        }
    }
}

?>

<x-layouts.app>
    @volt('hotels')
        <div class='py-10 lg:mt-10' x-data="{ openModal: false }">
            <div class="flex items-center justify-between">
                <div class='max-w-7xl px-6 lg:px-8'>
                    <div class='max-w-4xl text-left'>
                        <h2 class='mt-2 text-4xl font-bold tracking-tight text-foreground sm:text-5xl dark:text-gray-200'>
                            <span class='text-primary'>Client</span> Hotels
                        </h2>
                    </div>
                    <p class='mt-6 max-w-2xl text-center text-lg leading-8 text-muted-foreground'>
                        This is an example hotel display. Our app needs this. Maybe it doesn't. Don't know.
                    </p>
                </div>
                <div class="w-1/2 mr-10 pt-6 text-right">
                    <x-button
                        tag="a"
                        href="/hotels/create"
                        class="dark:bg-gray-600 dark:text-gray-100"
                    >
                        <span class="text-xl">Add Hotel</span>
                    </x-button>
                </div>
            </div>

            {{-- Hotel Display goes here --}}
            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($hotels as $hotel)
                    <a
                        href="/hotels/{{ $hotel['slug'] }}"
                        target="_blank"
                        class="cursor-pointer rounded-2xl overflow-hidden shadow-md bg-card
                                    border border-border hover:shadow-lg transition dark:border-gray-500 hover:scale-[1.01]"
                    >
                        <img
                            src="{{ $hotel['image'] }}"
                            alt="{{ $hotel['name'] }}"
                            class="h-48 w-full object-cover"
                        />
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-foreground dark:text-gray-300">
                                {{ $hotel['name'] }}
                            </h3>
                            <p class="text-muted-foreground text-sm mt-1">
                                {{ $hotel['location'] }}
                            </p>
                            <p class="text-primary font-medium mt-3">
                                {{ $hotel['price_per_night'] }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    @endvolt
</x-layouts.app>
