<?php
$hasData = !empty($weeklyStats) && count($weeklyStats) > 0;
$totalGames = array_sum(array_column($weeklyStats ?? [], 'games'));
$totalWins = array_sum(array_column($weeklyStats ?? [], 'wins'));
$winRate = $totalGames > 0 ? round(($totalWins / $totalGames) * 100, 1) : 0;
?>

<?php if ($hasData): ?>
    <!-- Summary Cards -->
    <div class="grid !grid-cols-3 gap-3 mb-5">
        <div class="bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-xl p-3.5 text-center shadow-md border border-indigo-300/50">
            <div class="text-2xl font-black text-indigo-700"><?= $totalGames ?></div>
            <div class="text-[10px] text-indigo-600 font-semibold mt-0.5">کل بازی‌ها</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl p-3.5 text-center shadow-md border border-emerald-300/50">
            <div class="text-2xl font-black text-emerald-700"><?= $totalWins ?></div>
            <div class="text-[10px] text-emerald-600 font-semibold mt-0.5">کل بردها</div>
        </div>
        <div class="bg-gradient-to-br from-violet-100 to-violet-200 rounded-xl p-3.5 text-center shadow-md border border-violet-300/50">
            <div class="text-2xl font-black text-violet-700"><?= $winRate ?>%</div>
            <div class="text-[10px] text-violet-600 font-semibold mt-0.5">نرخ برد</div>
        </div>
    </div>

    <!-- Chart -->
    <div style="position: relative; height: 290px;">
        <canvas id="weeklyStatsChart"></canvas>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('weeklyStatsChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($weeklyStats, 'day')) ?>,
                    datasets: [{
                            label: 'بازی',
                            data: <?= json_encode(array_column($weeklyStats, 'games')) ?>,
                            backgroundColor: 'rgba(79, 70, 229, 0.85)',
                            borderColor: '#4f46e5',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(79, 70, 229, 1)',
                            hoverBorderColor: '#4f46e5',
                            hoverBorderWidth: 3
                        },
                        {
                            label: 'برد',
                            data: <?= json_encode(array_column($weeklyStats, 'wins')) ?>,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)',
                            borderColor: '#10b981',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(16, 185, 129, 1)',
                            hoverBorderColor: '#10b981',
                            hoverBorderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            rtl: true,
                            labels: {
                                font: {
                                    family: 'Vazir',
                                    size: 12,
                                    weight: 'bold'
                                },
                                padding: 18,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            rtl: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.85)',
                            titleFont: {
                                family: 'Vazir',
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                family: 'Vazir',
                                size: 12
                            },
                            padding: 12,
                            cornerRadius: 12,
                            boxShadow: '0 10px 40px rgba(0,0,0,0.3)'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    family: 'Vazir',
                                    size: 11,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.06)',
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Vazir',
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
<?php else: ?>
    <div class="text-center py-14">
        <div class="text-6xl mb-4 opacity-50">📊</div>
        <p class="text-gray-500 text-sm font-medium">در ۷ روز اخیر بازی‌ای نداشته‌اید</p>
        <p class="text-gray-400 text-xs mt-1.5">با انجام بازی، نمودار آمار هفتگی نمایش داده می‌شود</p>
    </div>
<?php endif; ?>