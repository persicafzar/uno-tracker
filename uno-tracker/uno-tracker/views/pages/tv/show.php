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

<!-- ======= محتوای خالص بازی ======= -->
<?php include __DIR__ . '/partials/game-content.php'; ?>

<!-- ======= اسکریپت‌های اولیه ======= -->
<script>
    window.BASE_URL = '<?= base_url() ?>';
    window.TV_MODE = true;
    window.GAME_CONFIG = {
        gameId: <?= (int)($game->id ?? 0) ?>,
        currentUserId: <?= (int)($currentUser['id'] ?? 0) ?>,
        isReferee: <?= ($isReferee ?? false) ? 'true' : 'false' ?>,
        maxWins: <?= (int)($maxWins ?? 0) ?>,
        participants: <?= json_encode(
                            array_map(fn($p) => [
                                'id' => $p->id,
                                'user_id' => $p->user_id,
                                'name' => $p->getDisplayName(),
                                'team_id' => $p->team_id,
                            ], $game->participants ?? [])
                        ) ?>
    };
    console.log('🎮 TV GAME_CONFIG initialized:', window.GAME_CONFIG);
</script>

<!-- ======= TV SSE ======= -->
<script src="/assets/js/tv-sse.js"></script>

<!-- ======= رفرش خودکار هوشمند ======= -->
<script>
    (function() {
        const gameConfig = window.GAME_CONFIG || {};
        const gameStatus = '<?= $game->status ?>';
        const gameId = gameConfig.gameId || 0;

        const AUTO_REFRESH_STATUSES = ['active', 'paused'];
        const shouldAutoRefresh = AUTO_REFRESH_STATUSES.includes(gameStatus);

        let refreshTimer = null;
        let lastEventTime = Date.now();
        const REFRESH_INTERVAL = 10000;

        console.log(`📺 TV Auto-Refresh: gameId=${gameId}, status=${gameStatus}, shouldRefresh=${shouldAutoRefresh}`);

        if (!shouldAutoRefresh) {
            console.log('⏹️ Auto-refresh disabled');
            return;
        }

        function refreshPage() {
            const timeSinceLastEvent = (Date.now() - lastEventTime) / 1000;
            if (timeSinceLastEvent > 12) {
                console.log('🔄 Auto-refreshing TV page');
                location.reload();
            } else {
                console.log('⏳ SSE active, skipping auto-refresh');
                resetTimer();
            }
        }

        function resetTimer() {
            if (refreshTimer) {
                clearTimeout(refreshTimer);
                refreshTimer = null;
            }
            refreshTimer = setTimeout(refreshPage, REFRESH_INTERVAL);
        }

        document.addEventListener('game_updated', function() {
            lastEventTime = Date.now();
            console.log('📨 game_updated received');
            resetTimer();
        });

        resetTimer();

        window.addEventListener('beforeunload', function() {
            if (refreshTimer) clearTimeout(refreshTimer);
        });
    })();
</script>