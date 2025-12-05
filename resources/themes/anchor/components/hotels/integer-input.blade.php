<div class="w-full inline-block text-left">
    <label
        for="{{ $name }}"
        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
    >
        {{ $title }}
    </label>
    <input
        name="{{ $name }}"
        type="number"
        inputmode="numeric"
        pattern="\d+"
        value="{{ $value ?? 0 }}"
        min="0"
        step="1"
        aria-label="Quantity (integer)"
        placeholder="0"
        class="block w-full rounded-md border px-3 py-2 text-sm
                bg-white text-gray-900 placeholder-gray-400
                dark:bg-zinc-800 dark:text-gray-100 dark:placeholder-gray-500
                border-gray-200 dark:border-zinc-700 focus:ring-2 focus:outline-none focus:ring-brand"
    />
</div>
