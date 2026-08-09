<?php

namespace Presentation\Listeners;

use Core\Database;

class LeaderboardListener
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * رسیدگی به رویداد شروع بازی
     */
    public function markGameStarted(array $data): void
    {
        $gameId = $data['game_id'] ?? null;
        $firstPlayerId = $data['first_player_id'] ?? null;
        $firstPlayerName = $data['first_player_name'] ?? 'ناشناس';
        log_message("🏆 LeaderboardListener: بازی {$gameId} شروع شد. اولین بازیکن: {$firstPlayerName}");
    }

    /**
     * به‌روزرسانی آمار پس از ثبت دور
     */
    public function updateStats(array $data): void
    {
        $gameId = $data['game_id'] ?? null;
        $winnerUserId = $data['winner_user_id'] ?? null;
        $winnerName = $data['winner_name'] ?? 'ناشناس';
        $score = (float)($data['score'] ?? 1); // 🆕 تبدیل به float
        
        log_message("🏆 LeaderboardListener: دور {$data['round_number']} بازی {$gameId} - برنده: {$winnerName}");
        
        if ($winnerUserId) {
            $this->updateLeaderboardCache($winnerUserId, $score);
        }
    }

    /**
     * نهایی‌سازی بازی پس از پایان
     */
    public function finalizeGame(array $data): void
    {
        $gameId = $data['game_id'] ?? null;
        $winnerId = $data['winner_id'] ?? null;
        $winnerName = $data['winner_name'] ?? 'ناشناس';
        $totalRounds = $data['total_rounds'] ?? 0;
        
        log_message("🏆 LeaderboardListener: بازی {$gameId} پایان یافت. برنده: {$winnerName} ({$totalRounds} دور)");
        
        if ($winnerId) {
            $this->updateWinnerStats($winnerId);
        }
        
        $this->updateLosersStats($gameId, $winnerId);
    }

    /**
     * رسیدگی به لغو بازی
     */
    public function handleCancellation(array $data): void
    {
        $gameId = $data['game_id'] ?? null;
        log_message("🏆 LeaderboardListener: بازی {$gameId} لغو شد - آمار به‌روز نمی‌شود");
    }

    /**
     * 🆕 به‌روزرسانی cache لیدربورد برای برنده یک دور
     * تغییر نوع $score از int به float
     */
    private function updateLeaderboardCache(int $userId, float $score): void
    {
        try {
            $existing = $this->db->fetchOne(
                "SELECT * FROM leaderboard_cache WHERE user_id = ?",
                [$userId]
            );
            
            if ($existing) {
                $this->db->query(
                    "UPDATE leaderboard_cache 
                     SET total_points = total_points + ?,
                         total_games = total_games + 1
                     WHERE user_id = ?",
                    [$score, $userId]
                );
            } else {
                $this->db->insert('leaderboard_cache', [
                    'user_id' => $userId,
                    'total_points' => $score,
                    'total_games' => 1,
                    'total_wins' => 0,
                    'total_losses' => 0,
                    'win_rate' => 0,
                    'points_per_game' => $score,
                    'current_streak' => 0,
                    'best_streak' => 0,
                ]);
            }
        } catch (\Throwable $e) {
            log_message("❌ Error updating leaderboard cache: " . $e->getMessage());
        }
    }

    /**
     * 🆕 به‌روزرسانی آمار برنده بازی
     */
    private function updateWinnerStats(int $winnerId): void
    {
        try {
            $this->db->query(
                "UPDATE leaderboard_cache 
                 SET total_wins = total_wins + 1,
                     win_rate = (total_wins * 100.0) / GREATEST(total_games, 1),
                     points_per_game = total_points / GREATEST(total_games, 1)
                 WHERE user_id = ?",
                [$winnerId]
            );
        } catch (\Throwable $e) {
            log_message("❌ Error updating winner stats: " . $e->getMessage());
        }
    }

    /**
     * 🆕 به‌روزرسانی آمار بازندگان
     */
    private function updateLosersStats(int $gameId, ?int $winnerId): void
    {
        try {
            $participants = $this->db->fetchAll(
                "SELECT user_id FROM game_participants WHERE game_id = ? AND user_id IS NOT NULL",
                [$gameId]
            );
            
            foreach ($participants as $participant) {
                $userId = (int) $participant['user_id'];
                if ($userId !== $winnerId) {
                    $this->db->query(
                        "UPDATE leaderboard_cache 
                         SET total_losses = total_losses + 1,
                             win_rate = (total_wins * 100.0) / GREATEST(total_games, 1)
                         WHERE user_id = ?",
                        [$userId]
                    );
                }
            }
        } catch (\Throwable $e) {
            log_message("❌ Error updating losers stats: " . $e->getMessage());
        }
    }
}