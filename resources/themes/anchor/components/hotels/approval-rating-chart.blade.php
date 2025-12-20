@props(['result'])

<div class="p-6 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-gray-200">
        Approval Rating for Experience Categories
    </h3>
    <div class="h-72">
        <div class="w-full h-72" wire:ignore>
            <canvas id="approval-rating-chart"></canvas>
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

                    const dynamicMax = Math.ceil(Math.max(...posPercents, ...negPercents, 0) / 10) * 10

                    return { labels, posPercents, dynamicMax }
                }

                const approvalRatingCtx = document.getElementById('approval-rating-chart').getContext('2d')

                const initial = processData(@json($result['topic_analysis']))

                const approvalRatingChart = new Chart(approvalRatingCtx, {
                    type: "bar",
                    data: {
                        labels: initial.labels,
                        datasets: [
                            {
                                label: "Positive Reviews",
                                data: initial.posPercents,
                                backgroundColor: "#238636",
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
                                anchor: 'end',
                                align: 'end',
                                formatter: v => v.toFixed(1) + "%"
                            }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: { max: initial.dynamicMax, grid: { color: '#30363D' } } }
                    }
                })

                Livewire.on('resultUpdated', ([result]) => {
                    totalReviews = result.total_reviews
                    const updated = processData(result.topic_analysis)

                    approvalRatingChart.data.labels = updated.labels
                    approvalRatingChart.data.datasets[0].data = updated.posPercents
                    approvalRatingChart.options.scales.y.max = updated.dynamicMax

                    approvalRatingChart.update()
                })
            })
        </script>
    </div>
</div>
