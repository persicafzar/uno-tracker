<?php
$isTeamMode = $game->isTeamMode();

// پیدا کردن آخرین دور
$lastRound = null;
if (!empty($game->rounds)) {
    $lastRound = end($game->rounds);
}
$lastWinnerId = $lastRound ? $lastRound->winner_participant_id : null;

// ======= بررسی وجود card =======
$lastCard = null;
$lastCardEmoji = null;
$lastCardMultiplier = null;
if ($lastRound && isset($lastRound->card) && $lastRound->card) {
    $lastCard = $lastRound->card;
    $lastCardEmoji = $lastCard->emoji ?? '';
    $lastCardMultiplier = $lastCard->score_multiplier ?? 1;
}

// اطلاعات لقب‌ها (از کنترلر ارسال شده)
$participantTitles = $participantTitles ?? [];
?>

<div class="space-y-3">

    <!-- ======= هدر تابلو ======= -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-2 pb-2 border-b-2 border-white/10">
        <div class="flex items-center gap-3">
            <span class="text-3xl">📊</span>
            <h2 class="text-2xl font-black text-white tracking-tight">تابلو امتیازات</h2>
        </div>
        <div class="flex items-center gap-4 bg-white/5 rounded-xl px-3 py-1.5 border border-white/10">
            <span class="text-gray-400 text-lg">🎯 هدف:</span>
            <span class="text-3xl font-black text-white"><?= $game->target_wins ?></span>
            <span class="text-gray-400 text-lg">برد</span>
        </div>
    </div>

    <?php if ($isTeamMode && !empty($game->teams)): ?>
        <!-- ======= حالت تیمی ======= -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($game->teams as $team):
                $teamTotalWins = $team->getTotalWins();
                $progress = min(100, ($teamTotalWins / $game->target_wins) * 100);
                $isWinner = $teamTotalWins >= $game->target_wins;
                // ======= محاسبه Match Point برای تیم =======
                $isMatchPoint = (!$isWinner && ($game->target_wins - $teamTotalWins) === 1);
                $teamColor = htmlspecialchars($team->color_hex);
                $members = $team->getMembers();
            ?>
                <div class="rounded-xl p-4 <?= $isWinner ? 'border-4 border-yellow-400 shadow-2xl shadow-yellow-500/50 pulse-glow animate-pulse' : ($isMatchPoint ? 'border-4 border-orange-400 shadow-2xl shadow-orange-500/40 animate-pulse' : 'border border-white/10 shadow') ?> backdrop-blur-sm" style="background: linear-gradient(145deg, <?= $teamColor ?>30, <?= $teamColor ?>15);">
                    <!-- هدر تیم -->
                    <div class="flex items-center justify-between mb-3 pb-2 border-b-2" style="border-color: <?= $teamColor ?>60;">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-black text-2xl shadow" style="background-color: <?= $teamColor ?>">
                                <?= array_search($team, $game->teams) + 1 ?>
                            </div>
                            <span class="text-3xl font-black text-white drop-shadow" style="color: <?= $teamColor ?>">
                                <?= htmlspecialchars($team->name) ?>
                            </span>
                        </div>
                        <div class="text-left">
                            <span class="text-4xl font-black text-white"><?= $teamTotalWins ?></span>
                            <span class="text-xl text-gray-400"> / <?= $game->target_wins ?></span>
                        </div>
                    </div>

                    <!-- نوار پیشرفت -->
                    <div class="w-full bg-gray-800/80 rounded-full h-3 overflow-hidden mb-3 shadow-inner">
                        <div class="h-full transition-all duration-700 rounded-full" style="width: <?= $progress ?>%; background: linear-gradient(90deg, <?= $teamColor ?>, <?= $teamColor ?>cc);"></div>
                    </div>

                    <!-- ======= نشان Match Point برای تیم ======= -->
                    <?php if ($isMatchPoint): ?>
                        <div class="mb-3 p-2 bg-orange-500/30 border-2 border-orange-400 rounded-xl text-center shadow-lg shadow-orange-500/20 animate-pulse">
                            <span class="text-sm font-black text-orange-400 drop-shadow">🔥 MATCH POINT</span>
                        </div>
                    <?php endif; ?>

                    <!-- اعضای تیم -->
                    <div class="space-y-2">
                        <?php foreach ($members as $member): ?>
                            <?php
                            $isLastWinner = $lastWinnerId && $member->id === $lastWinnerId;
                            $isGameWinner = $isWinner && $member->id === $game->winner_participant_id;
                            $title = $participantTitles[$member->id] ?? null;
                            ?>
                            <div class="flex items-center justify-between p-2.5 rounded-xl transition-all duration-200 
                                <?= $isLastWinner ? 'bg-yellow-500/30 border-2 border-yellow-400/50 shadow-lg shadow-yellow-500/30' : 'bg-white/10 border border-white/10 hover:bg-white/15' ?> 
                                <?= $isGameWinner ? '!bg-gradient-to-r !from-yellow-500/50 !to-amber-500/40 border-2 border-yellow-400 shadow-lg shadow-yellow-500/40' : '' ?>">

                                <div class="flex items-center gap-3">
                                    <!-- تصویر پروفایل -->
                                    <?php if ($member->avatar_path): ?>
                                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($member->avatar_path) ?>" class="w-24 h-24 rounded-full object-cover border-2 border-white/30 shadow-lg">
                                    <?php else: ?>
                                        <div class="w-24 h-24 rounded-full bg-gray-700 flex items-center justify-center text-4xl border-2 border-white/20">👤</div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="text-xl font-bold text-white <?= $isLastWinner ? 'text-yellow-300' : '' ?> <?= $isGameWinner ? 'text-white' : '' ?> drop-shadow">
                                            <?= htmlspecialchars($member->getDisplayName()) ?>
                                            <?php if ($isLastWinner): ?>
                                                <span class="text-yellow-300 text-base">⭐</span>
                                            <?php endif; ?>
                                        </div>
                                        <!-- ======= برد با کنتراست بالا ======= -->
                                        <div class="text-base text-white font-bold drop-shadow-lg">🏆 <?= $member->wins_count ?> برد</div>
                                        <?php if ($title): ?>
                                            <div class="flex items-center gap-2 mt-1 text-lg">
                                                <span class="text-2xl"><?= htmlspecialchars($title->icon ?? '🏆') ?></span>
                                                <span class="text-indigo-300 font-bold drop-shadow"><?= htmlspecialchars($title->name ?? '') ?></span>
                                                <span class="text-yellow-300 drop-shadow">×<?= number_format($title->bonus_points ?? 1, 1) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <!-- ======= امتیاز با کنتراست بالا ======= -->
                                    <div class="text-4xl font-black text-white drop-shadow-2xl"><?= number_format($member->total_score, 1) ?></div>
                                    <div class="text-sm text-gray-300 font-bold">امتیاز</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($isWinner): ?>
                        <div class="mt-3 p-2 bg-yellow-500/20 border-2 border-yellow-500/30 rounded-xl text-center">
                            <span class="text-2xl font-black text-yellow-400 animate-pulse">🏆 برنده بازی</span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <!-- ======= حالت انفرادی ======= -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <?php foreach ($game->participants as $participant):
                $progress = min(100, ($participant->wins_count / $game->target_wins) * 100);
                $isWinner = $participant->wins_count >= $game->target_wins;
                // ======= محاسبه Match Point برای بازیکن =======
                $isMatchPoint = (!$isWinner && ($game->target_wins - $participant->wins_count) === 1);
                $isLastWinner = $lastWinnerId && $participant->id === $lastWinnerId;
                $title = $participantTitles[$participant->id] ?? null;
            ?>
                <div class="relative rounded-xl p-4 text-center transition-all duration-300 hover:scale-[1.02]
                    <?= $isWinner ? '!bg-gradient-to-br !from-yellow-500/40 !via-amber-500/30 !to-orange-500/20 border-4 border-yellow-400 shadow-2xl shadow-yellow-500/50 pulse-glow animate-pulse' : ($isMatchPoint ? '!bg-gradient-to-br !from-orange-500/30 !to-red-500/10 border-4 border-orange-400 shadow-2xl shadow-orange-500/40 animate-pulse' : 'border border-white/10 shadow bg-white/5 hover:bg-white/10') ?> 
                    <?= $isLastWinner ? '!bg-gradient-to-br !from-yellow-500/30 !to-amber-500/20 ring-2 ring-yellow-400/50 ring-offset-2 shadow-lg shadow-yellow-500/30' : '' ?>">

                    <!-- تصویر پروفایل -->
                    <?php if ($participant->avatar_path): ?>
                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($participant->avatar_path) ?>" class="w-28 h-28 rounded-full mx-auto object-cover border-2 border-white/20 shadow-2xl">
                    <?php else: ?>
                        <div class="w-28 h-28 rounded-full bg-gray-700 mx-auto flex items-center justify-center text-5xl border-2 border-white/20">👤</div>
                    <?php endif; ?>

                    <div class="mt-2">
                        <div class="text-xl font-bold text-white <?= $isLastWinner ? 'text-yellow-300' : '' ?> <?= $isWinner ? 'text-2xl text-white' : '' ?> drop-shadow">
                            <?= htmlspecialchars($participant->getDisplayName()) ?>
                            <?php if ($isLastWinner): ?>
                                <span class="text-yellow-300 text-base">⭐</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($participant->team_name): ?>
                            <div class="text-base text-gray-300"><?= htmlspecialchars($participant->team_name) ?></div>
                        <?php endif; ?>
                        <?php if ($title): ?>
                            <div class="flex items-center justify-center gap-2 mt-1 text-lg">
                                <span class="text-2xl"><?= htmlspecialchars($title->icon ?? '🏆') ?></span>
                                <span class="text-indigo-300 font-bold drop-shadow"><?= htmlspecialchars($title->name ?? '') ?></span>
                                <span class="text-yellow-300 font-bold drop-shadow">×<?= number_format($title->bonus_points ?? 1, 1) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- نوار پیشرفت -->
                    <div class="w-full bg-gray-800/80 rounded-full h-3 overflow-hidden mt-2 shadow-inner">
                        <div class="h-full transition-all duration-700 rounded-full" style="width: <?= $progress ?>%; background: linear-gradient(90deg, #6366f1, #8b5cf6);"></div>
                    </div>

                    <!-- ======= نمایش Match Point ======= -->
                    <?php if ($isMatchPoint): ?>
                        <div class="mt-2 p-1.5 bg-orange-500/30 border-2 border-orange-400 rounded-xl text-center shadow-lg shadow-orange-500/20 animate-pulse">
                            <span class="text-sm font-black text-orange-400 drop-shadow">🔥 MATCH POINT</span>
                        </div>
                    <?php endif; ?>

                    <!-- ======= برد و امتیاز با کنتراست بالا ======= -->
                    <div class="flex items-center justify-between mt-2 text-lg">
                        <span class="text-gray-400">برد</span>
                        <span class="text-white font-black text-2xl drop-shadow-2xl"><?= $participant->wins_count ?></span>
                    </div>

                    <div class="flex items-center justify-between mt-1 text-lg">
                        <span class="text-gray-400">امتیاز</span>
                        <span class="text-white font-black text-3xl drop-shadow-2xl"><?= number_format($participant->total_score, 1) ?></span>
                    </div>

                    <?php if ($isWinner): ?>
                        <div class="mt-2 text-yellow-400 font-black text-lg animate-pulse drop-shadow">🏆 برنده</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 15px rgba(234, 179, 8, 0.2);
        }

        50% {
            box-shadow: 0 0 40px rgba(234, 179, 8, 0.6);
        }
    }

    .pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite;
    }
</style>