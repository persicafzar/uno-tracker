<?php

/**
 * نمایش فرمول محاسبه امتیاز
 */
?>

<div class="bg-gradient-to-br from-blue-50 to-indigo-50/70 border border-blue-200/60 rounded-xl p-3.5 mb-4 shadow-sm">
    <div class="flex items-start gap-2.5">
        <span class="text-2xl flex-shrink-0 mt-0.5">🧮</span>
        <div class="flex-1 min-w-0">
            <div class="font-bold text-gray-700 text-sm sm:text-base mb-2">
                فرمول محاسبه امتیاز هر دور:
            </div>

            <div class="bg-white rounded-lg p-2.5 font-mono text-[11px] sm:text-xs overflow-x-auto whitespace-nowrap shadow-inner border border-gray-200/60">
                <span class="text-blue-600 font-black">امتیاز پایه (<?= number_format($baseScore, 2) ?>)</span>
                <span class="text-gray-400 mx-1">×</span>
                <span class="text-purple-600 font-black">ضریب کارت</span>
                <span class="text-gray-400 mx-1">×</span>
                <span class="text-emerald-600 font-black">ضریب نوع برد</span>
                <?php if ($game->isTeamMode()): ?>
                    <span class="text-gray-400 mx-1">×</span>
                    <span class="text-orange-600 font-black">ضریب تیمی (<?= number_format($teamMultiplier, 2) ?>)</span>
                <?php endif; ?>
                <span class="text-gray-400 mx-1">+</span>
                <span class="text-amber-600 font-black">پاداش لقب</span>
            </div>

            <p class="text-[10px] sm:text-xs text-gray-500 mt-2">
                <span class="inline-block bg-blue-100 text-blue-700 px-2 py-0.5 rounded mx-0.5">امتیاز پایه</span>
                <span class="inline-block bg-purple-100 text-purple-700 px-2 py-0.5 rounded mx-0.5">ضریب کارت</span>
                <span class="inline-block bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded mx-0.5">ضریب نوع برد</span>
                <?php if ($game->isTeamMode()): ?>
                    <span class="inline-block bg-orange-100 text-orange-700 px-2 py-0.5 rounded mx-0.5">ضریب تیمی</span>
                <?php endif; ?>
                <span class="inline-block bg-amber-100 text-amber-700 px-2 py-0.5 rounded mx-0.5">پاداش لقب</span>
            </p>
        </div>
    </div>
</div>