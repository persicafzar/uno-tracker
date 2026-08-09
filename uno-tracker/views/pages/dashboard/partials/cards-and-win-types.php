<?php

/**
 * 🃏 کارت‌ها و نوع‌های برد
 */

$cards = $cards ?? [];
$winTypes = $winTypes ?? [];

// رنگ‌های راریتی
$rarityColors = [
    'common' => ['bg' => 'from-gray-100 to-gray-200', 'border' => 'border-gray-300', 'text' => 'text-gray-600', 'badge' => 'bg-gray-400'],
    'rare' => ['bg' => 'from-blue-100 to-blue-200', 'border' => 'border-blue-300', 'text' => 'text-blue-700', 'badge' => 'bg-blue-500'],
    'legendary' => ['bg' => 'from-yellow-100 to-orange-200', 'border' => 'border-yellow-400', 'text' => 'text-yellow-700', 'badge' => 'bg-yellow-500'],
];
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

    <!-- ======= کارت‌ها ======= -->
    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                <span class="text-2xl ml-2">🃏</span>
                کارت ها و ضریب انها
            </h2>
            <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                <?= count($cards) ?> کارت
            </span>
        </div>

        <?php if (empty($cards)): ?>
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-2 opacity-50">🃏</div>
                <p class="text-sm font-medium">هیچ کارتی تعریف نشده است</p>
            </div>
        <?php else: ?>
            <div class="grid !grid-cols-3 gap-2.5">
                <?php foreach ($cards as $card): ?>
                    <?php
                    $rarity = $rarityColors[$card['rarity']] ?? $rarityColors['common'];
                    $emoji = $card['emoji'] ?? '🃏';
                    ?>
                    <div class="relative group">
                        <div class="rounded-xl p-3 border-2 <?= $rarity['border'] ?> bg-gradient-to-br <?= $rarity['bg'] ?> shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.03] text-center">
                            <div class="text-3xl sm:text-4xl mb-1 drop-shadow"><?= htmlspecialchars($emoji) ?></div>
                            <div class="font-bold text-gray-800 text-xs sm:text-sm truncate" title="<?= htmlspecialchars($card['name']) ?>">
                                <?= htmlspecialchars($card['name']) ?>
                            </div>
                            <div class="flex items-center justify-center gap-1 mt-1">
                                <span class="text-lg font-black <?= $rarity['text'] ?>">
                                    ×<?= number_format($card['score_multiplier'], 1) ?>
                                </span>

                            </div>
                            <?php if (!empty($card['description'])): ?>
                                <!-- 🆕 تولتیپ با پشتیبانی از متن چندخطی و حداکثر عرض -->
                                <div class="absolute invisible group-hover:visible z-20 bottom-full left-1/2 -translate-x-1/2 -mb-1 px-4 py-2.5 bg-gray-900/95 backdrop-blur-sm text-white text-xs rounded-xl shadow-2xl max-w-36 w-max break-words whitespace-normal text-center leading-relaxed border border-white/10">
                                    <?= htmlspecialchars($card['description']) ?>
                                    <!-- پیکان -->
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900/95"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ======= نوع‌های برد ======= -->
    <div class=" bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                <span class="text-2xl ml-2">⚡</span>
                نوع‌های برد و ضریب انها
            </h2>
            <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                <?= count($winTypes) ?> نوع
            </span>
        </div>

        <?php if (empty($winTypes)): ?>
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-2 opacity-50">⚡</div>
                <p class="text-sm font-medium">هیچ نوع بردی تعریف نشده است</p>
            </div>
        <?php else: ?>
            <div class="grid !grid-cols-3 gap-2.5">
                <?php foreach ($winTypes as $winType): ?>
                    <div class="relative group">
                        <div class="rounded-xl p-3 border-2 border-indigo-200 bg-gradient-to-br from-indigo-50 to-violet-50 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.03] text-center">
                            <div class="text-3xl sm:text-4xl mb-1 drop-shadow"><?= htmlspecialchars($winType['icon'] ?? '⚡') ?></div>
                            <div class="font-bold text-gray-800 text-xs sm:text-sm truncate" title="<?= htmlspecialchars($winType['name']) ?>">
                                <?= htmlspecialchars($winType['name']) ?>
                            </div>
                            <div class="flex items-center justify-center gap-1 mt-1">
                                <span class="text-lg font-black text-indigo-600">
                                    ×<?= number_format($winType['score_multiplier'], 1) ?>
                                </span>
                            </div>
                            <?php if (!empty($winType['description'])): ?>
                                <!-- 🆕 تولتیپ با پشتیبانی از متن چندخطی و حداکثر عرض -->
                                <div class="absolute invisible group-hover:visible z-20 bottom-full left-1/2 -translate-x-1/2 -mb-1 px-4 py-2.5 bg-gray-900/95 backdrop-blur-sm text-white text-xs rounded-xl shadow-2xl max-w-36 w-max break-words whitespace-normal text-center leading-relaxed border border-white/10">
                                    <?= htmlspecialchars($winType['description']) ?>
                                    <!-- پیکان -->
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900/95"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>