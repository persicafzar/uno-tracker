<?php
/**
 * نمودار توزیع بردها (Doughnut)
 * داده‌ها: $winDistribution = [['label' => '...', 'value' => X], ...]
 */
$hasData = !empty($winDistribution) && count($winDistribution) > 0;
$totalWins = array_sum(array_column($winDistribution ?? [], 'value'));
?>

<?php if ($hasData && $totalWins > 0): ?>
<div class="flex flex-col lg:flex-row items-center gap-4">
    <div style="position: relative; height: 250px; width: 250px;" class="flex-shrink-0">
        <canvas id="winDistributionChart"></canvas>
    </div>
    
    <!-- Legend سفارشی -->
    <div class="flex-1 space-y-2 w-full">
        <?php 
        $colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
        foreach ($winDistribution as $index => $item): 
            $color = $colors[$index % count($colors)];
            $percentage = round(($item['value'] / $totalWins) * 100, 1);
        ?>
        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full" style="background-color: <?= $color ?>"></div>
                <span class="text-sm text-gray-700"><?= htmlspecialchars($item['label']) ?></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-gray-800"><?= $item['value'] ?></span>
                <span class="text-xs text-gray-500">(<?= $percentage ?>%)</span>
            </div>
        </div>
        <?php endforeach; ?>
        
        <div class="pt-2 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700">مجموع بردها:</span>
                <span class="text-lg font-bold text-indigo-600"><?= $totalWins ?></span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('winDistributionChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($winDistribution, 'label')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($winDistribution, 'value')) ?>,
                backgroundColor: <?= json_encode(array_slice($colors, 0, count($winDistribution))) ?>,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false // از legend سفارشی استفاده می‌کنیم
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
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const percentage = ((value / <?= $totalWins ?>) * 100).toFixed(1);
                            return ` ${value} برد (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
<?php else: ?>
<div class="text-center py-12">
    <div class="text-5xl mb-3">🥧</div>
    <p class="text-gray-500 text-sm">هنوز بردی ثبت نشده است</p>
    <p class="text-gray-400 text-xs mt-2">با کسب پیروزی در بازی‌ها، نمودار توزیع بردها نمایش داده می‌شود</p>
</div>
<?php endif; ?>