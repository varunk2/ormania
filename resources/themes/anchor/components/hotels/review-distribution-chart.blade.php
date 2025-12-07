@props(['result'])

<div class="p-6 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-gray-200">
        Review Distribution
    </h3>
    <div class="h-72">
        <div class="w-full h-72">
            <canvas id="review-distribution-chart"></canvas>
        </div>

        <script type="module">
            let reviewDistributionChart = null;
            const reviewDistributionCtx = document.getElementById('review-distribution-chart').getContext('2d')

            if (reviewDistributionChart) reviewDistributionChart.destroy()

            reviewDistributionChart = new Chart(reviewDistributionCtx, {
                type: 'doughnut', // Doughnut looks more premium
                data: {
                    labels: Object.keys(@json($result['source_breakdown'])),
                    datasets: [{
                        data: Object.values(@json($result['source_breakdown'])),
                        backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        datalabels: {
                            color: 'white',
                            font: { weight: 'bold' },
                            formatter: (val, ctx) => {
                                let sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                let pct = (val * 100 / sum).toFixed(1);
                                return pct > 5 ? pct + "%" : "";
                            }
                        }
                    }
                }
            })
        </script>
    </div>
</div>
