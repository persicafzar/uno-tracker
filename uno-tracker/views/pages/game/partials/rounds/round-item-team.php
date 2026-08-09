<?php

/**
 * نمایش یک دور - بازی تیمی
 */

$winnerTeam = null;
$teamMembers = [];
if ($winnerParticipant && $winnerParticipant->team_id) {
    foreach ($game->teams as $team) {
        if ($team->id === $winnerParticipant->team_id) {
            $winnerTeam = $team;
            $teamMembers = $team->getMembers();
            break;
        }
    }
}

$cardMultiplier = $cardInfo ? $cardInfo['multiplier'] : 1.0;
$winTypeMultiplier = $winTypeInfo ? $winTypeInfo['multiplier'] : 1.0;
?>

<div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200/80 overflow-hidden">

    <!-- هدر دور -->
    <div class="px-3.5 py-2.5 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200/60 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="text-base font-black text-indigo-600">#<?= $round->round_number ?></span>
            <span class="text-[10px] text-gray-400 bg-white/70 px-2 py-0.5 rounded-full"><?= date('H:i:s', strtotime($round->created_at)) ?></span>
        </div>
        <?php if ($winnerTeam): ?>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-gray-600">برنده:</span>
                <span class="text-sm font-black" style="color: <?= htmlspecialchars($winnerTeam->color_hex) ?>">
                    <?= htmlspecialchars($winnerTeam->name) ?>
                </span>
                <span class="text-base">🏆</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- بدنه -->
    <div class="p-3.5 space-y-3">

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

        <div class="space-y-2.5">
            <?php foreach ($teamMembers as $member): ?>
                <?php
                $memberTitleBonus = 0;
                if ($member->user_id) {
                    $title = \Core\Database::getInstance()->fetchOne(
                        "SELECT t.bonus_points FROM users u LEFT JOIN titles t ON u.current_title_id = t.id WHERE u.id = ?",
                        [$member->user_id]
                    );
                    if ($title && isset($title['bonus_points'])) {
                        $memberTitleBonus = (int) $title['bonus_points'];
                    }
                }

                $isMainWinner = ($member->id == $round->winner_participant_id);

                if ($isMainWinner) {
                    $memberScore = $baseScore * $cardMultiplier * $winTypeMultiplier * $teamMultiplier + $memberTitleBonus;
                    $memberCardMultiplier = $cardMultiplier;
                    $memberWinTypeMultiplier = $winTypeMultiplier;
                } else {
                    $memberScore = $baseScore * 1.0 * 1.0 * $teamMultiplier + $memberTitleBonus;
                    $memberCardMultiplier = 1.0;
                    $memberWinTypeMultiplier = 1.0;
                }
                ?>

                <div class="bg-gray-50/80 rounded-lg p-2.5 border border-gray-200/60 hover:bg-gray-100/80 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <?php if ($member->avatar_path): ?>
                                <img src="/storage/uploads/avatars/<?= htmlspecialchars($member->avatar_path) ?>"
                                    class="!w-7 !h-7 aspect-square rounded-full object-cover border border-gray-200 flex-shrink-0">
                            <?php else: ?>
                                <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-sm flex-shrink-0">👤</div>
                            <?php endif; ?>
                            <span class="font-semibold text-gray-800 text-sm truncate">
                                <?= htmlspecialchars($member->getDisplayName()) ?>
                            </span>
                            <?php if ($isMainWinner): ?>
                                <span class="text-yellow-500 text-base">🏆</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-red-600 font-black text-sm flex-shrink-0">
                            +<?= number_format($memberScore, 2) ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-md p-1.5 font-mono text-[10px] sm:text-[11px] overflow-x-auto whitespace-nowrap">
                        <span class="text-blue-600 font-bold"><?= number_format($baseScore, 2) ?></span>
                        <span class="text-gray-400 mx-0.5">×</span>
                        <span class="text-purple-600 font-bold"><?= number_format($memberCardMultiplier, 2) ?></span>
                        <span class="text-gray-400 mx-0.5">×</span>
                        <span class="text-emerald-600 font-bold"><?= number_format($memberWinTypeMultiplier, 2) ?></span>
                        <span class="text-gray-400 mx-0.5">×</span>
                        <span class="text-orange-600 font-bold"><?= number_format($teamMultiplier, 2) ?></span>
                        <span class="text-gray-400 mx-0.5">+</span>
                        <span class="text-amber-600 font-bold"><?= number_format($memberTitleBonus, 2) ?></span>
                        <span class="text-gray-400 mx-0.5">=</span>
                        <span class="text-red-600 font-bold"><?= number_format($memberScore, 2) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>