<!-- Modal Background -->
<div
    x-data="hotelModal"
    x-show="openModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
>
    <!-- Modal Box -->
    <div
        @click.away="openModal = false"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 w-full max-w-lg"
    >
        <!-- Modal Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Add New Hotel
            </h2>

            <button @click="openModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-white">
                ✕
            </button>
        </div>

        <!-- Modal Body (Add your form here) -->
        <form @submit.prevent="submitHotel">
            <div class="mb-4">
                <label class="block text-foreground dark:text-gray-300">Google Map URL</label>
                <input
                    type="text"
                    x-model=googleMapUrl
                    class="w-full mt-1 p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600"
                />
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-end gap-3">
                <x-button type="button" @click="openModal = false" class="dark:bg-gray-600">
                    Cancel
                </x-button>

                <x-button type="submit" class="bg-primary text-white">
                    Save
                </x-button>
            </div>
        </form>

    </div>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('hotelModal', () => ({
            openModal: false,
            googleMapUrl: '',

            async submitHotel() {
                const response = await fetch('/api/hotels', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        google_map_url: this.googleMapUrl
                    })
                })

                const data = await response.json()
                console.log(data)

                if (response.ok) {
                    alert("Hotel added!")
                    this.openModal = false
                    this.googleMapUrl = ''
                } else {
                    alert(data.message || "Error occurred")
                }
            }
        }))
    })
</script>
