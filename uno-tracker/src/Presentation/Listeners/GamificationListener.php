<?php

namespace Presentation\Listeners;

use Application\Services\GamificationService;
use Application\Services\UserService;
use Core\Database;
use Infrastructure\Repositories\ParticipantRepository;

class GamificationListener
{
    private GamificationService $gamificationService;
    private UserService $userService;
    private Database $db;

    public function __construct()
    {
        $this->gamificationService = new GamificationService();
        $this->userService = new UserService();
        $this->db = Database::getInstance();
    }

    /**
     * رسیدگی به رویداد پایان بازی
     * - پردازش گیمیفیکیشن (XP، استریک، مدال‌ها، القاب)
     * - به‌روزرسانی leaderboard_cache (با refreshLeaderboardCache)
     */
    public function handleGameFinished(array $data): void
    {
        $gameId = $data['game_id'] ?? null;
        $winnerId = $data['winner_id'] ?? null;
        $winnerTeamId = $data['winner_team_id'] ?? null;

        if (!$gameId) {
            log_message("❌ GamificationListener: game_id not provided");
            return;
        }

        log_message("🎮 GamificationListener: Processing game {$gameId}");

        // دریافت شرکت‌کنندگان با is_winner
        $participants = $this->db->fetchAll(
            "SELECT gp.id, gp.user_id, gp.wins_count, gp.total_score, gp.team_id, gp.is_winner
         FROM game_participants gp
         WHERE gp.game_id = ? AND gp.user_id IS NOT NULL",
            [$gameId]
        );

        if (empty($participants)) {
            log_message("⚠️ GamificationListener: No participants found for game {$gameId}");
            return;
        }

        $game = $this->db->fetchOne(
            "SELECT game_mode FROM games WHERE id = ?",
            [$gameId]
        );
        $isTeamGame = ($game['game_mode'] ?? '') === 'friendly';

        $statsService = new \Application\Services\UserStatsService();

        foreach ($participants as $participant) {
            $userId = (int) $participant['user_id'];
            $isWinner = (bool) $participant['is_winner'];

            log_message("🎯 Processing user {$userId} - Winner: " . ($isWinner ? 'Yes' : 'No'));

            try {
                // پردازش گیمیفیکیشن (XP، استریک، مدال‌ها، القاب)
                $result = $this->gamificationService->processGameEnd(
                    $userId,
                    $isWinner,
                    $isTeamGame
                );

                log_message("✅ User {$userId} - XP: {$result['xp_gained']}, Achievements: " .
                    count($result['achievements_unlocked']));

                // 🆕 به‌روزرسانی leaderboard_cache بعد از به‌روز شدن استریک
                $statsService->refreshLeaderboardCache($userId);
            } catch (\Throwable $e) {
                log_message("❌ Error processing user {$userId}: " . $e->getMessage());
            }
        }
    }

    /**
     * رسیدگی به رویداد ثبت دور
     */
    public function handleRoundRecorded(array $data): void
    {
        // در حال حاضر نیازی به پردازش خاص نیست
        // چون processGameEnd همه چیز را مدیریت می‌کند
        // اما می‌توانیم در آینده برای نشان‌های خاص استفاده کنیم

        $winnerUserId = $data['winner_user_id'] ?? null;
        $cardId = $data['card_id'] ?? null;

        if ($winnerUserId && $cardId) {
            // در آینده: بررسی نشان‌های خاص مثل "برد با کارت خاص"
            log_message("🎯 Round recorded - Winner: {$winnerUserId}, Card: {$cardId}");
        }
    }

    /**
     * رسیدگی به رویداد شروع بازی
     */
    public function handleGameStarted(array $data): void
    {
        // در حال حاضر نیازی به پردازش نیست
        $gameId = $data['game_id'] ?? null;
        log_message("🎮 Game started: {$gameId}");
    }

    /**
     * رسیدگی به رویداد لغو بازی
     */
    public function handleGameCancelled(array $data): void
    {
        // در صورت لغو بازی، هیچ XP یا پاداشی داده نمی‌شود
        $gameId = $data['game_id'] ?? null;
        log_message("❌ Game cancelled: {$gameId} - No rewards given");
    }
}
