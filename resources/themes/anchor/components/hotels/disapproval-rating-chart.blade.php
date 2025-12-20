<div class="p-6 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-gray-200">
        Disapproval Rating for Experience Categories
    </h3>
    <div class="h-72">
        <div class="w-full h-72" wire:ignore>
            <canvas id="disapproval-rating-chart"></canvas>
        </div>

        <script type="module">
            document.addEventListener('livewire:init' , () => {
                const TOP_N = 5

                let totalReviews = @json($result['total_reviews']) || 1

                const getTopTopics = (analysis, key, limit = TOP_N) =>
                    Object.entries(analysis)
                            .map(([topic, data]) => ({
                                topic,
                                val: data[key] || 0
                            }))
                            .sort((a, b) => b.val - a.val)
                            .slice(0, limit)

                const toPercent = (val) => (val / totalReviews) * 100

                const processData = (analysis) => {
                    const topPositive = getTopTopics(analysis, 'Positive')
                    const topNegative = getTopTopics(analysis, 'Negative')

                    const labels = topPositive.map(i => i.topic)

                    const posPercents = topPositive.map(i => toPercent(i.val))
                    const negPercents = topNegative.map(i => toPercent(i.val))

                    return { labels, negPercents }
                }

                const disapprovalRatingCtx = document.getElementById('disapproval-rating-chart').getContext('2d')

                const initial = processData(@json($result['topic_analysis']))

                const disapprovalRatingChart = new Chart(disapprovalRatingCtx, {
                    type: "bar",
                    data: {
                        labels: initial.labels,
                        datasets: [
                            {
                                label: "Negative Reviews",
                                data: initial.negPercents,
                                backgroundColor: "#DA3633",
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            datalabels: {
                                anchor: "end",
                                align: "end",
                                formatter: v => v.toFixed(1) + "%"
                            }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: { max: 50, grid: { color: '#30363D' } }
                        }
                    }
                })

                Livewire.on('resultUpdated', ([result]) => {
                    totalReviews = result.total_reviews
                    const updated = processData(result.topic_analysis)

                    disapprovalRatingChart.data.labels = updated.labels
                    disapprovalRatingChart.data.datasets[0].data = updated.negPercents

                    disapprovalRatingChart.update()
                })
            })


        </script>
    </div>
</div>
