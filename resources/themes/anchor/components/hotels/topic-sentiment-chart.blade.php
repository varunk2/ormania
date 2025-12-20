@props(['result'])

<div class="p-6 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-gray-200">
        Sentiment by topic
    </h3>
    <div class="h-72">
        <div class="w-full h-72" wire:ignore>
            <canvas id="topic-sentiment-chart"></canvas>
        </div>

        <script type="module">
            document.addEventListener('livewire:init', () => {
                let topicAnalysis = @json($result['topic_analysis']);
                let topics = Object.keys(topicAnalysis)
                let posData = Object.values(topicAnalysis).map(t => t.Positive || 0)
                let negData = Object.values(topicAnalysis).map(t => t.Negative || 0)
                let neuData = Object.values(topicAnalysis).map(t => t.Neutral || 0)

                let totalData = topics.map((t, i) => posData[i] + negData[i] + neuData[i])
                let posPct = posData.map((v, i) => totalData[i] ? (v / totalData[i] * 100) : 0)
                let negPct = negData.map((v, i) => totalData[i] ? (v / totalData[i] * 100) : 0)
                let neuPct = neuData.map((v, i) => totalData[i] ? (v / totalData[i] * 100) : 0)

                const topicSentimentCtx = document.getElementById('topic-sentiment-chart').getContext('2d')

                let topicSentimentChart = new Chart(topicSentimentCtx, {
                    type: 'bar',
                    data: {
                        labels: topics,
                        datasets: [
                            {
                                label: "Positive",
                                data: posPct,
                                backgroundColor: "#238636",
                                borderRadius: 4
                            },
                            {
                                label: "Negative",
                                data: negPct,
                                backgroundColor: "#DA3633",
                                borderRadius: 4
                            },
                            {
                                label: "Neutral",
                                data: neuPct,
                                backgroundColor: "#8957E5",
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true, grid: { display: false } },
                            y: { stacked: true, max: 100, grid: { color: '#30363D' } }
                        },
                        plugins: {
                            legend: {
                                labels: { usePointStyle: true }
                            },
                            datalabels: {
                                color: 'white',
                                font: { size: 10 },
                                formatter: (v) => v > 8 ? v.toFixed(0) + "%" : ""
                            }
                        }
                    }
                })

                Livewire.on('resultUpdated', (result) => {
                    topicAnalysis = result[0]['topic_analysis']
                    topics = Object.keys(topicAnalysis)
                    posData = Object.values(topicAnalysis).map(t => t.Positive || 0)
                    negData = Object.values(topicAnalysis).map(t => t.Negative || 0)
                    neuData = Object.values(topicAnalysis).map(t => t.Neutral || 0)

                    totalData = topics.map((t, i) => posData[i] + negData[i] + neuData[i])
                    posPct = posData.map((v, i) => totalData[i] ? (v / totalData[i] * 100) : 0)
                    negPct = negData.map((v, i) => totalData[i] ? (v / totalData[i] * 100) : 0)
                    neuPct = neuData.map((v, i) => totalData[i] ? (v / totalData[i] * 100) : 0)

                    topicSentimentChart.data.datasets = [
                        {
                            label: "Positive",
                            data: posPct,
                            backgroundColor: "#238636",
                            borderRadius: 4
                        },
                        {
                            label: "Negative",
                            data: negPct,
                            backgroundColor: "#DA3633",
                            borderRadius: 4
                        },
                        {
                            label: "Neutral",
                            data: neuPct,
                            backgroundColor: "#8957E5",
                            borderRadius: 4
                        }
                    ]

                    topicSentimentChart.update()
                })
            })
        </script>
    </div>
</div>
