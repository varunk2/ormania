@props(['result'])

<div class="p-6 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-gray-200">
        Approval Rating for Experience Categories
    </h3>
    <div class="h-72">
        <div class="w-full h-72">
            <canvas id="approval-rating-chart"></canvas>
        </div>

        <script type="module">
            const topicAnalysis = @json($result['topic_analysis']);
            const topics = Object.keys(topicAnalysis)
            const posData = Object.values(topicAnalysis).map(t => t.Positive || 0)
            const negData = Object.values(topicAnalysis).map(t => t.Negative || 0)

            const sortedPos = topics.map((t, i) => ({ topic: t, val: posData[i] }))
                                    .sort((a, b) => b.val - a.val)
                                    .slice(0, 5)
            const sortedTopics = sortedPos.map(i => i.topic)

            const sortedNeg = topics.map((t, i) => ({ topic: t, val: negData[i] }))
                                    .sort((a, b) => b.val - a.val)
                                    .slice(0, 5)

            const totalReviews = @json($result['total_reviews']) || 1
            const posPercents = sortedPos.map(i => (i.val / totalReviews * 100))
            const negPercents = sortedNeg.map(i => (i.val / totalReviews * 100))

            const globalMax = Math.max(...posPercents, ...negPercents, 0)
            const dynamicMax = Math.ceil(globalMax / 10) * 10

            const approvalRatingCtx = document.getElementById('approval-rating-chart').getContext('2d')

            new Chart(approvalRatingCtx, {
                type: "bar",
                data: {
                    labels: sortedTopics,
                    datasets: [
                        {
                            label: "Positive Reviews",
                            data: posPercents,
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
                            color: '#F0F6FC',
                            formatter: v => v.toFixed(1) + "%"
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { max: dynamicMax, grid: { color: '#30363D' } } }
                }
            })
        </script>
    </div>
</div>
