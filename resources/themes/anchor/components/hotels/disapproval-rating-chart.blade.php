<div class="p-6 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-gray-200">
        Disapproval Rating for Experience Categories
    </h3>
    <div class="h-72">
        <div class="w-full h-72">
            <canvas id="disapproval-rating-chart"></canvas>
        </div>

        <script type="module">
            const disapprovalRatingCtx = document.getElementById('disapproval-rating-chart').getContext('2d')

            new Chart(disapprovalRatingCtx, {
                type: "bar",
                data: {
                    labels: ["Cleanliness", "Food", "Facilities", "Service", "Value"],
                    datasets: [
                        {
                            label: "Negative Reviews",
                            data: [1.646090534979424, 1.646090534979424, 1.5637860082304527, 1.2345679012345678, 0.49382716049382713],
                            backgroundColor: "#DA3633",
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: "end",
                            align: "end",
                            color: "#F0F6FC"
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            max: 50,
                            grid: {
                                color: "#30363D"
                            }
                        }
                    }
                }
            })
        </script>
    </div>
</div>
