<div class="p-6 w-full md:w-3/5 lg:w-3/5 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-gray-200">
        Comparative Standing
    </h3>
    <div class="h-72">
        <div class="w-full h-72">
            <canvas id="comparative-standing-chart"></canvas>
        </div>

        <script type="module">
            const currentHotel = @json($result['hotel']);
            const marketComparisonData = @json($result['market_comparison']);
            const marketLabels = marketComparisonData.map(m => m.hotel)
            const marketValues = marketComparisonData.map(m => m.rating)
            const marketColors = marketLabels.map(l => l === currentHotel ? '#FF4B4B' : '#30363D')

            let comparativeStandingChart;

            const comparativeStandingCtx = document.getElementById('comparative-standing-chart').getContext('2d')

            if (comparativeStandingChart) comparativeStandingChart.destroy()

            comparativeStandingChart = new Chart(comparativeStandingCtx, {
                type: 'bar',
                data: {
                    labels: marketLabels,
                    datasets: [
                        {
                            data: marketValues,
                            backgroundColor: marketColors,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            color: '#F0F6FC',
                            anchor: 'end',
                            align: 'start',
                            formatter: v => v.toFixed(2)
                        }
                    },
                    scales: {
                        x: {
                            max: 5,
                            grid: { color: '#30363D' }
                        },
                        y: {
                            grid: { display: false }
                        }
                    }
                }
            })
        </script>
    </div>
</div>
