<?php

/**
 * نمایش یک دور - بازی انفرادی
 */

$cardMultiplier = $cardInfo ? $cardInfo['multiplier'] : 1.0;
$winTypeMultiplier = $winTypeInfo ? $winTypeInfo['multiplier'] : 1.0;
$titleBonus = 0;

if ($winnerParticipant && $winnerParticipant->user_id) {
    $title = \Core\Database::getInstance()->fetchOne(
        "SELECT t.bonus_points FROM users u LEFT JOIN titles t ON u.current_title_id = t.id WHERE u.id = ?",
        [$winnerParticipant->user_id]
    );
    if ($title && isset($title['bonus_points'])) {
        $titleBonus = (int) $title['bonus_points'];
    }
}

$calculatedScore = $baseScore * $cardMultiplier * $winTypeMultiplier + $titleBonus;
$winnerName = $round->winner_name ?? ($winnerParticipant ? $winnerParticipant->getDisplayName() : 'بازیکن مهمان');
?>

<div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200/80 overflow-hidden">

    <!-- هدر دور (بدون برچسب "امتیاز") -->
    <div class="px-3.5 py-2.5 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200/60 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="text-base font-black text-indigo-600">#<?= $round->round_number ?></span>
            <span class="text-[10px] text-gray-400 bg-white/70 px-2 py-0.5 rounded-full"><?= date('H:i:s', strtotime($round->created_at)) ?></span>
        </div>
        <div class="text-red-600 font-black text-base sm:text-lg">
            +<?= number_format($calculatedScore, 2) ?>
        </div>
    </div>

    <!-- بدنه -->
    <div class="p-3.5 space-y-3">

        <!-- برنده با آیکون 🏆 کنار نام -->
        <div class="flex items-center gap-3">
            <?php if ($winnerParticipant && $winnerParticipant->avatar_path): ?>
                <img src="/storage/uploads/avatars/<?= htmlspecialchars($winnerParticipant->avatar_path) ?>"
                    class="!w-11 !h-11 aspect-square rounded-full object-cover border-2 border-yellow-400 shadow-sm flex-shrink-0">
            <?php elseif ($winnerParticipant): ?>
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-yellow-100 to-amber-100 flex items-center justify-center text-2xl border-2 border-yellow-400 shadow-sm flex-shrink-0">
                    👤
                </div>
            <?php endif; ?>
            <div>
                <div class="font-bold text-gray-800 text-sm sm:text-base flex items-center gap-1.5">
                    <span><?= htmlspecialchars($winnerName) ?></span>
                    <span class="text-yellow-500 text-lg">🏆</span>
                </div>
                <?php if ($winnerParticipant && !empty($winnerParticipant->real_name)): ?>
                    <div class="text-[10px] text-gray-400"><?= htmlspecialchars($winnerParticipant->real_name) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- کارت و نوع برد -->
        <?php if ($cardInfo || $winTypeInfo): ?>
            <div class="flex flex-wrap gap-2">
                <?php if ($cardInfo): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-100 text-purple-800 rounded-full text-[11px] font-bold border border-purple-200">
                        <span class="text-sm"><?= $cardInfo['emoji'] ?></span>
                        <span><?= htmlspecialchars($cardInfo['name']) ?></span>
                        <span class="bg-purple-200 px-1.5 py-0.5 rounded-full text-[9px]">×<?= number_format($cardInfo['multiplier'], 2) ?></span>
                    </span>
                <?php endif; ?>

                <?php if ($winTypeInfo): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[11px] font-bold border border-emerald-200">
                        <span class="text-sm"><?= $winTypeInfo['icon'] ?></span>
                        <span><?= htmlspecialchars($winTypeInfo['name']) ?></span>
                        <span class="bg-emerald-200 px-1.5 py-0.5 rounded-full text-[9px]">×<?= number_format($winTypeInfo['multiplier'], 2) ?></span>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- فرمول -->
        <div class="bg-gray-50 rounded-lg p-2 font-mono text-[10px] sm:text-[11px] overflow-x-auto whitespace-nowrap border border-gray-200/60">
            <span class="text-blue-600 font-bold"><?= number_format($baseScore, 2) ?></span>
            <span class="text-gray-400 mx-0.5">×</span>
            <span class="text-purple-600 font-bold"><?= number_format($cardMultiplier, 2) ?></span>
            <span class="text-gray-400 mx-0.5">×</span>
            <span class="text-emerald-600 font-bold"><?= number_format($winTypeMultiplier, 2) ?></span>
            <span class="text-gray-400 mx-0.5">+</span>
            <span class="text-amber-600 font-bold"><?= number_format($titleBonus, 2) ?></span>
            <span class="text-gray-400 mx-0.5">=</span>
            <span class="text-red-600 font-bold"><?= number_format($calculatedScore, 2) ?></span>
        </div>
    </div>
</div>