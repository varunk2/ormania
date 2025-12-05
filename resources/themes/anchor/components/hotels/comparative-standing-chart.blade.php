<div class="p-6 w-3/5 rounded-2xl border bg-white/60 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
    <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-gray-200">
        Comparative Standing
    </h3>
    <div class="h-72">
        <div class="w-full h-72">
            <canvas id="comparative-standing-chart"></canvas>
        </div>

        <script type="module">
            const comparativeStandingCtx = document.getElementById('comparative-standing-chart').getContext('2d')

            new Chart(comparativeStandingCtx, {
            type: 'bar',
            data: {
                labels: ["jüSTa Sarang Rameswaram", "Gulf Hotel Colaba", "Hotel Aalayam Rameshwaram", "Daiwik Hotels Rameswaram", "Hotel Star Palace"],
                datasets: [
                    {
                        data: [
                            3.6003683241252302,
                            3.6478930307941653,
                            3.956043956043956,
                            4.266666666666667,
                            4.560082304526749
                        ],
                        backgroundColor: [
                            "#30363D",
                            "#30363D",
                            "#30363D",
                            "#30363D",
                            "#FF4B4B"
                        ],
                        borderRadius: 4
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, datalabels: { color: '#F0F6FC', anchor: 'end', align: 'start', formatter: v => v.toFixed(2) } },
                scales: { x: { max: 5, grid: { color: '#30363D' } }, y: { grid: { display: false } } }
            }
        })
        </script>
    </div>
</div>
