<?php

use Core\JalaliDate;

$isTeamMode = $game->isTeamMode();
$winner = $game->getWinner();
$statusLabels = [
    'pending' => ['label' => 'در انتظار', 'color' => 'pending'],
    'active' => ['label' => 'در حال بازی', 'color' => 'active'],
    'paused' => ['label' => 'متوقف', 'color' => 'paused'],
    'finished' => ['label' => 'پایان یافته', 'color' => 'finished'],
    'cancelled' => ['label' => 'لغو شده', 'color' => 'cancelled'],
];
$status = $statusLabels[$game->status] ?? $statusLabels['pending'];

$lastRound = $lastRound ?? null;
$lastRoundWinType = $lastRoundWinType ?? null;
$participantTitles = $participantTitles ?? [];
$participantLevels = $participantLevels ?? [];

// دریافت داور
$db = \Core\Database::getInstance();
$referee = $db->fetchOne(
    "SELECT id, nickname, avatar_path FROM users WHERE id = ?",
    [$game->referee_id]
);

// شروع‌کننده (از کنترلر ارسال شده)
$firstPlayer = $firstPlayer ?? null;

// اطلاعات برنده‌ی آخرین دور
$lastWinnerInfo = null;
if ($lastRound) {
    $winnerParticipant = null;
    foreach ($game->participants as $p) {
        if ($p->id === $lastRound->winner_participant_id) {
            $winnerParticipant = $p;
            break;
        }
    }
    if ($winnerParticipant) {
        $lastWinnerInfo = [
            'participant' => $winnerParticipant,
            'card' => $lastRound->card ?? null,
            'card_multiplier' => $lastRound->card->score_multiplier ?? 1,
            'win_type' => $lastRoundWinType,
            'score' => $lastRound->calculated_score ?? 0,
            'round_number' => $lastRound->round_number,
        ];
    }
}
?>

<div id="tv-game-content" class="space-y-4">

    <!-- ======= هدر بازی ======= -->
    <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-r from-indigo-900/60 via-purple-900/60 to-pink-900/60 border border-white/10 shadow-xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-2xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight drop-shadow">
                    <?= htmlspecialchars($game->name ?: 'بازی #' . $game->id) ?>
                </h1>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="status-badge status-<?= $status['color'] ?> text-base px-4 py-1.5">
                        <?= $status['label'] ?>
                    </span>
                    
                    <!-- هدف و دورها -->
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-1.5 border border-white/10">
                        <span class="text-3xl">🎯</span>
                        <span class="text-gray-300 text-lg">هدف:</span>
                        <span class="text-4xl font-black text-white"><?= $game->target_wins ?></span>
                        <span class="text-gray-400 text-lg">برد</span>
                    </div>
                    
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-1.5 border border-white/10">
                        <span class="text-3xl">🔄</span>
                        <span class="text-gray-300 text-lg">دورها:</span>
                        <span class="text-4xl font-black text-white"><?= $game->total_rounds_played ?></span>
                    </div>

                    <?php if ($winner): ?>
                        <div class="flex items-center gap-2 bg-yellow-500/20 backdrop-blur-sm rounded-xl px-4 py-1.5 border border-yellow-500/30">
                            <span class="text-2xl">🏆</span>
                            <span class="text-gray-300 text-lg">برنده:</span>
                            <span class="text-2xl font-black text-yellow-400"><?= htmlspecialchars($winner->getDisplayName()) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ======= کارت‌های داور و شروع‌کننده - بزرگ‌تر ======= -->
            <div class="flex items-center gap-5 flex-wrap">
                <!-- داور -->
                <?php if ($referee): ?>
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-xl px-5 py-3 border border-white/15 shadow-lg">
                        <?php if (!empty($referee['avatar_path'])): ?>
                            <img src="/storage/uploads/avatars/<?= htmlspecialchars($referee['avatar_path']) ?>" class="w-16 h-16 rounded-full object-cover border-2 border-white/30 shadow">
                        <?php else: ?>
                            <div class="w-16 h-16 rounded-full bg-gray-700 flex items-center justify-center text-3xl border-2 border-white/20">👤</div>
                        <?php endif; ?>
                        <div>
                            <div class="text-gray-400 text-base font-bold">داور</div>
                            <div class="text-white font-bold text-2xl drop-shadow"><?= htmlspecialchars($referee['nickname'] ?? 'نامشخص') ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- شروع‌کننده -->
                <?php if ($firstPlayer): ?>
                    <div class="flex items-center gap-4 bg-amber-500/10 backdrop-blur-sm rounded-xl px-5 py-3 border border-amber-500/20 shadow-lg">
                        <?php if (!empty($firstPlayer->avatar_path)): ?>
                            <img src="/storage/uploads/avatars/<?= htmlspecialchars($firstPlayer->avatar_path) ?>" class="w-16 h-16 rounded-full object-cover border-2 border-amber-500/30 shadow">
                        <?php else: ?>
                            <div class="w-16 h-16 rounded-full bg-gray-700 flex items-center justify-center text-3xl border-2 border-amber-500/20">👤</div>
                        <?php endif; ?>
                        <div>
                            <div class="text-amber-400 text-base font-bold">شروع‌کننده</div>
                            <div class="text-white font-bold text-2xl drop-shadow">
                                <?= htmlspecialchars($firstPlayer->nickname ?? $firstPlayer->guest_name ?? 'مهمان') ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ======= آخرین دور ======= -->
    <?php if ($lastWinnerInfo): ?>
        <div class="relative overflow-hidden rounded-2xl p-4 bg-gradient-to-r from-emerald-900/30 via-teal-900/30 to-cyan-900/30 border border-emerald-500/30 shadow-lg">
            <div class="absolute top-0 right-0 w-48 h-48 bg-emerald-500/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-xl"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-teal-500/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-xl"></div>

            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- ستون راست: دور + برنده -->
                <div class="flex flex-wrap items-center justify-start gap-4">
                    <div>
                        <div class="text-gray-400 text-sm font-bold">آخرین دور</div>
                        <div class="text-2xl font-black text-white drop-shadow">#<?= $lastWinnerInfo['round_number'] ?></div>
                    </div>

                    <div class="flex items-center gap-4">
                        <?php if ($lastWinnerInfo['participant']->avatar_path): ?>
                            <img src="/storage/uploads/avatars/<?= htmlspecialchars($lastWinnerInfo['participant']->avatar_path) ?>" class="w-28 h-28 rounded-full object-cover border-2 border-yellow-400/50 shadow-lg">
                        <?php else: ?>
                            <div class="w-28 h-28 rounded-full bg-gray-700 flex items-center justify-center text-3xl border-2 border-yellow-400/50">👤</div>
                        <?php endif; ?>
                        <div>
                            <div class="text-gray-400 text-sm font-bold">برنده</div>
                            <div class="text-2xl font-black text-green-400 flex items-center gap-2 drop-shadow">
                                <?= htmlspecialchars($lastWinnerInfo['participant']->getDisplayName()) ?>
                                <span class="text-yellow-400 text-base">⭐</span>
                            </div>
                            <div class="text-gray-300"><?= $lastWinnerInfo['participant']->wins_count ?? 0 ?> برد</div>
                        </div>
                    </div>

                    <!-- سطح XP -->
                    <?php
                    $winnerLevel = $participantLevels[$lastWinnerInfo['participant']->id] ?? null;
                    if ($winnerLevel):
                    ?>
                        <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-2 border border-white/10">
                            <span class="text-3xl"><?= htmlspecialchars($winnerLevel->level_icon ?? '⭐') ?></span>
                            <div>
                                <div class="text-gray-400 text-xs">سطح</div>
                                <div class="text-white font-bold text-lg drop-shadow flex items-center gap-2">
                                    <span><?= htmlspecialchars($winnerLevel->current_level ?? '?') ?></span>
                                    <span class="text-indigo-300 text-lg font-bold"><?= htmlspecialchars($winnerLevel->level_title ?? '') ?></span>
                                </div>
                                <div class="text-yellow-300 text-lg drop-shadow"><?= number_format($winnerLevel->total_xp ?? 0) ?> XP</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ستون چپ: لقب + کارت + نوع برد + امتیاز -->
                <div class="flex flex-wrap items-center justify-start md:justify-end gap-4">

                    <!-- لقب برنده -->
                    <?php
                    $winnerTitle = $participantTitles[$lastWinnerInfo['participant']->id] ?? null;
                    if ($winnerTitle):
                    ?>
                        <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-2 border border-white/10">
                            <span class="text-4xl"><?= htmlspecialchars($winnerTitle->icon ?? '🏆') ?></span>
                            <div>
                                <div class="text-gray-400 text-xs">لقب برنده</div>
                                <div class="text-white font-bold text-lg drop-shadow"><?= htmlspecialchars($winnerTitle->name ?? '') ?></div>
                                <div class="text-yellow-400 font-bold text-lg drop-shadow">×<?= number_format($winnerTitle->bonus_points ?? 1, 1) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- کارت برنده -->
                    <?php if ($lastWinnerInfo['card']): ?>
                        <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-2 border border-white/10">
                            <span class="text-4xl"><?= htmlspecialchars($lastWinnerInfo['card']->emoji ?? '🃏') ?></span>
                            <div>
                                <div class="text-gray-400 text-xs">کارت برنده</div>
                                <div class="text-white font-bold text-lg drop-shadow"><?= htmlspecialchars($lastWinnerInfo['card']->name ?? '') ?></div>
                                <div class="text-yellow-400 font-bold text-lg drop-shadow">×<?= number_format($lastWinnerInfo['card_multiplier'], 1) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- نوع برد -->
                    <?php if ($lastWinnerInfo['win_type']): ?>
                        <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-2 border border-white/10">
                            <span class="text-3xl"><?= htmlspecialchars($lastWinnerInfo['win_type']->icon ?? '⚡') ?></span>
                            <div>
                                <div class="text-gray-400 text-xs">نوع برد</div>
                                <div class="text-white font-bold text-lg drop-shadow"><?= htmlspecialchars($lastWinnerInfo['win_type']->name ?? '') ?></div>
                                <div class="text-indigo-400 font-bold text-lg drop-shadow">×<?= number_format($lastWinnerInfo['win_type']->score_multiplier ?? 1, 1) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- امتیاز دور -->
                    <div>
                        <div class="text-gray-400 text-sm font-bold">امتیاز دور</div>
                        <div class="text-4xl font-black text-yellow-400 drop-shadow"><?= number_format($lastWinnerInfo['score'], 1) ?></div>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ======= تابلو امتیازات ======= -->
    <div class="relative overflow-hidden rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 shadow-lg">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative z-10 p-4">
            <?php include __DIR__ . '/scoreboard-tv.php'; ?>
        </div>
    </div>

</div>

<style>
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .animate-shimmer {
        animation: shimmer 2s infinite;
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 15px rgba(234, 179, 8, 0.2); }
        50% { box-shadow: 0 0 40px rgba(234, 179, 8, 0.6); }
    }
    .pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite;
    }
</style>