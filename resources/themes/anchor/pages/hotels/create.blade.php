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

    #[Validate('requried|string')]
    public $name = '';

    #[Validate('requried|string')]
    public $city = '';

    #[Validate('requried|string')]
    public $country = '';

    #[Validate('required|url')]
    public $image = '';

    public function save() {
        $validated = $this->validate();

        $project = auth()->user()->projects()->create($validated);

        session()->flash('message', 'Project created successfully!!');

        $this->redirect(route('projects'));
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
                        Hotel Name
                    </label>
                    <input
                        id="name"
                        type="text"
                        autofocus
                        wire:model.live="name"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-xs
                            focus:border-indigo-300 focus:ring-3 focus:ring-opacity-50"
                    >
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between gap-5">
                    <div class="w-1/2">
                        <label
                            for="city"
                            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            City
                        </label>
                        <input
                            id="city"
                            type="text"
                            autofocus
                            wire:model.live="city"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-xs
                                focus:border-indigo-300 focus:ring-3 focus:ring-opacity-50"
                        >
                        @error('city') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="w-1/2">
                        <label
                            for="country"
                            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            Country
                        </label>
                        <input
                            id="country"
                            type="text"
                            autofocus
                            wire:model.live="country"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-xs
                                focus:border-indigo-300 focus:ring-3 focus:ring-opacity-50"
                        >
                        @error('country') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label
                        for="image"
                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200"
                    >
                        Image URL
                    </label>
                    <input
                        id="image"
                        type="url"
                        autofocus
                        wire:model.live="image"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-xs
                            focus:border-indigo-300 focus:ring-3 focus:ring-opacity-50"
                    >
                    @error('image') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-button type="submit">
                        Save
                    </x-button>
                    <x-button class="ml-2 bg-red-600" type="cancel">
                        Cancel
                    </x-button>
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
