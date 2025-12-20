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

                const DATASETS_META = [
                    { key: 'Positive', label: 'Positive', color: '#238636' },
                    { key: 'Negative', label: 'Negative', color: '#DA3633' },
                    { key: 'Neutral', label: 'Neutral', color: '#8957E5' },
                ]

                const processTopicAnalysis = (topicAnalysis) => {
                    const topics = Object.keys(topicAnalysis)

                    const raw = topics.map(topic => {
                        const t = topicAnalysis[topic]
                        return {
                            Positive: t.Positive || 0,
                            Negative: t.Negative || 0,
                            Neutral: t.Neutral || 0,
                        }
                    })

                    const totals = raw.map(r => r.Positive + r.Negative + r.Neutral)

                    const datasets = DATASETS_META.map(meta => ({
                        label: meta.label,
                        data: raw.map((r, i) =>
                            totals[i] ? (r[meta.key] / totals[i]) * 100 : 0
                        ),
                        backgroundColor: meta.color,
                        borderRadius: 4
                    }))

                    return { topics, datasets }
                }

                const topicSentimentCtx = document.getElementById('topic-sentiment-chart').getContext('2d')

                let { topics, datasets } = processTopicAnalysis(
                    @json($result['topic_analysis'])
                )

                let topicSentimentChart = new Chart(topicSentimentCtx, {
                    type: 'bar',
                    data: {
                        labels: topics,
                        datasets
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
                    const processed = processTopicAnalysis(result[0].topic_analysis)

                    topicSentimentChart.data.labels = processed.topics
                    topicSentimentChart.data.datasets.forEach((ds, i) => {
                        ds.data = processed.datasets[i].data
                    })

                    topicSentimentChart.update()
                })
            })
        </script>
    </div>
</div>
