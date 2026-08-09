<?php
$isTeamMode = $game->isTeamMode();

// پیدا کردن برنده آخرین دور
$lastWinnerId = null;
if (!empty($game->rounds)) {
    $lastRound = end($game->rounds);
    $lastWinnerId = $lastRound->winner_participant_id ?? null;
}
?>
<div id="scoreboard-content" class="space-y-4">

    <!-- هدر -->
    <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
        <span class="text-xl sm:text-2xl">📊</span>
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">تابلو امتیازات</h2>
        <span class="mr-auto text-xs bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full font-medium">
            هدف: <?= $game->target_wins ?> برد
        </span>
    </div>

    <?php if ($isTeamMode && !empty($game->teams)): ?>
        <!-- ======= حالت تیمی ======= -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php foreach ($game->teams as $team):
                $teamTotalWins = $team->getTotalWins();
                $progress = ($teamTotalWins / $game->target_wins) * 100;
                $isWinner = $teamTotalWins >= $game->target_wins;
                $isMatchPoint = ($game->target_wins - $teamTotalWins) === 1;
                $teamColor = htmlspecialchars($team->color_hex);
            ?>
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100 overflow-hidden 
                            <?= $isWinner ? 'ring-2 ring-yellow-400 ring-offset-1' : ($isMatchPoint ? 'ring-2 ring-orange-400 ring-offset-1 animate-pulse' : '') ?>">

                    <div class="px-3 py-2 flex items-center justify-between" style="background-color: <?= $teamColor ?>20; border-bottom: 2px solid <?= $teamColor ?>40;">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm"
                                style="background-color: <?= $teamColor ?>">
                                <?= array_search($team, $game->teams) + 1 ?>
                            </div>
                            <span class="font-bold text-sm text-gray-800" style="color: <?= $teamColor ?>">
                                <?= htmlspecialchars($team->name) ?>
                            </span>
                        </div>
                        <div class="text-left">
                            <span class="text-xl font-black text-indigo-600"><?= $teamTotalWins ?></span>
                            <span class="text-xs text-gray-500"> / <?= $game->target_wins ?></span>
                        </div>
                    </div>

                    <div class="p-3 space-y-2">
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="h-full transition-all duration-700 ease-out rounded-full"
                                style="width: <?= min($progress, 100) ?>%; background: linear-gradient(90deg, <?= $teamColor ?>, <?= $teamColor ?>cc);">
                            </div>
                        </div>

                        <!-- اعضای تیم -->
                        <div class="space-y-1">
                            <?php foreach ($team->getMembers() as $member): ?>
                                <?php
                                $isLastWinner = ($lastWinnerId && $member->id === $lastWinnerId);
                                // اگر برنده بازی باشد، استایل برنده بازی اولویت دارد
                                $isGameWinner = $isWinner && $member->id === $game->winner_participant_id;
                                ?>
                                <div class="flex items-center justify-between text-xs p-1 rounded-lg hover:bg-gray-50 transition-colors duration-150
                                    <?= $isGameWinner ? 'bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-yellow-400' : '' ?>
                                    <?= (!$isGameWinner && $isLastWinner) ? 'bg-amber-50/60 border-l-4 border-amber-300' : '' ?>
                                    <?= (!$isGameWinner && !$isLastWinner) ? 'bg-white' : '' ?>"
                                    <?php if ($member->user_id): ?>
                                    onclick="openProfile('/users/<?= $member->user_id ?>/partial?game_id=<?= $game->id ?>'); return false;"
                                    style="cursor: pointer;"
                                    <?php else: ?>
                                    style="cursor: default; opacity: 0.8;"
                                    <?php endif; ?>>
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <?php if ($member->avatar_path): ?>
                                            <img src="/storage/uploads/avatars/<?= htmlspecialchars($member->avatar_path) ?>"
                                                class="!w-9 !h-9 aspect-square rounded-full object-cover border border-gray-200 flex-shrink-0">
                                        <?php else: ?>
                                            <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-sm text-gray-500 flex-shrink-0">
                                                👤
                                            </div>
                                        <?php endif; ?>

                                        <div class="min-w-0 flex flex-col gap-0 leading-none">
                                            <?php if ($member->user_id): ?>
                                                <span class="mb-1 font-semibold text-gray-700 hover:text-indigo-600 transition text-sm inline-flex items-center truncate max-w-full leading-none">
                                                    <?= htmlspecialchars($member->getDisplayName()) ?>
                                                    <?php if ($isLastWinner): ?>
                                                        <span class="text-amber-500 text-base mr-0.5" title="برنده آخرین دور">⭐</span>
                                                    <?php endif; ?>
                                                    <?php if ($isGameWinner): ?>
                                                        <span class="text-yellow-500 text-base mr-0.5" title="برنده بازی">👑</span>
                                                    <?php endif; ?>
                                                </span>
                                                <?php if (!empty($member->real_name)): ?>
                                                    <span class="text-[10px] text-gray-400 truncate leading-none"><?= htmlspecialchars($member->real_name) ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="mb-1 font-semibold text-gray-700 text-sm inline-flex items-center truncate max-w-full leading-none">
                                                    <?= htmlspecialchars($member->getDisplayName()) ?>
                                                    <?php if ($isLastWinner): ?>
                                                        <span class="text-amber-500 text-base mr-0.5" title="برنده آخرین دور">⭐</span>
                                                    <?php endif; ?>
                                                    <?php if ($isGameWinner): ?>
                                                        <span class="text-yellow-500 text-base mr-0.5" title="برنده بازی">👑</span>
                                                    <?php endif; ?>
                                                </span>
                                                <span class="text-[10px] text-gray-400 leading-none">مهمان</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="text-[10px] text-gray-500"><?= $member->wins_count ?> برد</span>
                                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">
                                            ⭐ <?= number_format($member->total_score, 1) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($isMatchPoint): ?>
                            <div class="bg-orange-100 text-orange-700 text-[10px] font-bold text-center py-1 rounded-lg flex items-center justify-center gap-1">
                                <span class="text-sm">🔥</span> MATCH POINT
                            </div>
                        <?php endif; ?>
                        <?php if ($isWinner): ?>
                            <div class="bg-yellow-100 text-yellow-700 text-[10px] font-bold text-center py-1 rounded-lg flex items-center justify-center gap-1">
                                <span class="text-sm">👑</span> برنده‌ی بازی
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <!-- ======= حالت انفرادی ======= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php foreach ($game->participants as $participant):
                $progress = ($participant->wins_count / $game->target_wins) * 100;
                $isWinner = $participant->wins_count >= $game->target_wins;
                $isMatchPoint = ($game->target_wins - $participant->wins_count) === 1;
                $isLastWinner = ($lastWinnerId && $participant->id === $lastWinnerId);
            ?>
                <div class="rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden
                            <?= $isWinner ? 'ring-2 ring-yellow-400 ring-offset-1' : '' ?>
                            <?= (!$isWinner && $isMatchPoint) ? 'ring-2 ring-orange-400 ring-offset-1 animate-pulse' : '' ?>
                            <?= (!$isWinner && !$isMatchPoint && $isLastWinner) ? 'border bg-amber-50/60 border-l-4 border-amber-300' : '' ?>
                            <?= (!$isWinner && !$isMatchPoint && !$isLastWinner) ? 'bg-white border border-gray-100' : '' ?>
                            <?php if ($participant->user_id): ?> hover:scale-[1.02] <?php endif; ?>"
                    <?php if ($participant->user_id): ?>
                    onclick="openProfile('/users/<?= $participant->user_id ?>/partial?game_id=<?= $game->id ?>'); return false;"
                    style="cursor: pointer;"
                    <?php else: ?>
                    style="cursor: default; opacity: 0.9;"
                    <?php endif; ?>>
                    <div class="p-3 flex items-center justify-between border-b border-gray-100">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <?php if ($participant->avatar_path): ?>
                                <img src="/storage/uploads/avatars/<?= htmlspecialchars($participant->avatar_path) ?>"
                                    class="!w-11 !h-11 sm:!w-12 sm:!h-12 aspect-square rounded-full object-cover border-2 border-gray-200 flex-shrink-0">
                            <?php else: ?>
                                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-indigo-100 to-violet-100 flex items-center justify-center text-xl flex-shrink-0 border-2 border-gray-200">
                                    👤
                                </div>
                            <?php endif; ?>

                            <div class="min-w-0 flex flex-col gap-0 leading-none">
                                <?php if ($participant->user_id): ?>
                                    <span class="mb-1 font-bold text-gray-800 hover:text-indigo-600 transition text-sm inline-flex items-center truncate max-w-full leading-none">
                                        <?= htmlspecialchars($participant->getDisplayName()) ?>
                                        <?php if ($isWinner): ?>
                                            <span class="text-yellow-500 inline-block mr-1">👑</span>
                                        <?php endif; ?>
                                        <?php if ($isLastWinner && !$isWinner): ?>
                                            <span class="text-amber-500 text-base mr-0.5" title="برنده آخرین دور">⭐</span>
                                        <?php endif; ?>
                                    </span>
                                    <?php if (!empty($participant->real_name)): ?>
                                        <span class="text-[10px] text-gray-400 truncate leading-none"><?= htmlspecialchars($participant->real_name) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="mb-1 font-bold text-gray-800 text-sm inline-flex items-center truncate max-w-full leading-none">
                                        <?= htmlspecialchars($participant->getDisplayName()) ?>
                                        <?php if ($isWinner): ?>
                                            <span class="text-yellow-500 inline-block mr-1">👑</span>
                                        <?php endif; ?>
                                        <?php if ($isLastWinner && !$isWinner): ?>
                                            <span class="text-amber-500 text-base mr-0.5" title="برنده آخرین دور">⭐</span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="text-[10px] text-gray-400 leading-none">مهمان</span>
                                <?php endif; ?>
                                <?php if ($participant->team_name): ?>
                                    <span class="text-[10px] text-gray-500 truncate leading-none"><?= htmlspecialchars($participant->team_name) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-left flex-shrink-0">
                            <span class="text-xl font-black text-indigo-600"><?= $participant->wins_count ?></span>
                            <span class="text-xs text-gray-500"> / <?= $game->target_wins ?></span>
                        </div>
                    </div>

                    <div class="p-3 space-y-2">
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="h-full transition-all duration-700 ease-out rounded-full"
                                style="width: <?= min($progress, 100) ?>%; background: linear-gradient(90deg, #6366f1, #8b5cf6);">
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-600 mt-1">
                            <span class="font-medium">امتیاز کل</span>
                            <span class="font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                                ⭐ <?= number_format($participant->total_score, 1) ?>
                            </span>
                        </div>

                        <?php if ($isMatchPoint && !$isWinner): ?>
                            <div class="bg-orange-100 text-orange-700 text-[10px] font-bold text-center py-1 rounded-lg flex items-center justify-center gap-1">
                                <span class="text-sm">🔥</span> MATCH POINT
                            </div>
                        <?php endif; ?>
                        <?php if ($isWinner): ?>
                            <div class="bg-yellow-100 text-yellow-700 text-[10px] font-bold text-center py-1 rounded-lg flex items-center justify-center gap-1">
                                <span class="text-sm">🏆</span> برنده‌ی بازی
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- دکمه Undo -->
    <?php
    $currentUser = $currentUser ?? $user ?? null;
    $isCurrentReferee = $currentUser && $game->referee_id === (int)$currentUser['id'];
    ?>
    <?php if ($isCurrentReferee && $game->isActive() && !empty($game->rounds)): ?>
        <div class="mt-4 pt-3 border-t border-gray-200 flex justify-center sm:justify-start">
            <button type="button"
                onclick="confirmUndo(<?= $game->id ?>)"
                class="px-5 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg font-semibold text-sm shadow hover:shadow-md transition-all duration-200 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                </svg>
                لغو آخرین دور
            </button>
        </div>
    <?php endif; ?>
</div>