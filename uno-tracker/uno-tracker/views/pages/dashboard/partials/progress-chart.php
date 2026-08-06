<?php
/**
 * نمودار پیشرفت ۳۰ روز اخیر
 * داده‌ها: $progressData = ['labels' => [...], 'points' => [...], 'wins' => [...]]
 */
$hasData = !empty($progressData['labels']) && count($progressData['labels']) > 0;
?>

<?php if ($hasData): ?>
<div style="position: relative; height: 300px;">
    <canvas id="progressChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('progressChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($progressData['labels']) ?>,
            datasets: [
                {
                    label: 'امتیاز',
                    data: <?= json_encode($progressData['points']) ?>,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'بردها',
                    data: <?= json_encode($progressData['wins']) ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                            size: 12
                        },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    rtl: true,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        family: 'Vazir',
                        size: 13
                    },
                    bodyFont: {
                        family: 'Vazir',
                        size: 12
                    },
                    padding: 10,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            family: 'Vazir',
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Vazir',
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 0
                    }
                }
            }
        }
    });
});
</script>
<?php else: ?>
<div class="text-center py-12">
    <div class="text-5xl mb-3">📈</div>
    <p class="text-gray-500 text-sm">هنوز داده‌ای برای نمایش وجود ندارد</p>
    <p class="text-gray-400 text-xs mt-2">با انجام بازی‌ها، نمودار پیشرفت شما نمایش داده می‌شود</p>
</div>
<?php endif; ?>