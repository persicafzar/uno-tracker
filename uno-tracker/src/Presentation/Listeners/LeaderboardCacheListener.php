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
     * به‌روزرسانی آمار پس از هر دور
     */
    public function updateStats(array $data): void
    {
        $userId = $data['winner_user_id'] ?? null;
        if (!$userId) {
            return;
        }

        $this->recalculateUserStats($userId);
    }

    /**
     * نهایی‌سازی آمار پس از پایان بازی
     */
    public function finalizeGame(array $data): void
    {
        $gameId = $data['game_id'] ?? null;
        if (!$gameId) {
            return;
        }

        // گرفتن تمام شرکت‌کنندگان
        $participants = $this->db->fetchAll(
            "SELECT user_id FROM game_participants WHERE game_id = ? AND user_id IS NOT NULL",
            [$gameId]
        );

        foreach ($participants as $participant) {
            $this->recalculateUserStats($participant['user_id']);
        }
    }

    /**
     * علامت‌گذاری شروع بازی
     */
    public function markGameStarted(array $data): void
    {
        // در آینده می‌توان برای آمار آنلاین استفاده کرد
    }

    /**
     * مدیریت لغو بازی
     */
    public function handleCancellation(array $data): void
    {
        $gameId = $data['game_id'] ?? null;
        if (!$gameId) {
            return;
        }

        // گرفتن شرکت‌کنندگان
        $participants = $this->db->fetchAll(
            "SELECT user_id FROM game_participants WHERE game_id = ? AND user_id IS NOT NULL",
            [$gameId]
        );

        foreach ($participants as $participant) {
            $this->recalculateUserStats($participant['user_id']);
        }
    }

    /**
     * محاسبه مجدد تمام آمار یک کاربر
     */
    private function recalculateUserStats(int $userId): void
    {
        // آمار کلی از بازی‌های تمام شده
        $stats = $this->db->fetchOne(
            "SELECT 
                COUNT(DISTINCT g.id) as total_games,
                SUM(CASE WHEN gp.is_winner = 1 THEN 1 ELSE 0 END) as total_wins,
                SUM(gp.total_score) as total_points
             FROM game_participants gp
             JOIN games g ON gp.game_id = g.id
             WHERE gp.user_id = ? AND g.status = 'finished'",
            [$userId]
        );

        $totalGames = (int) ($stats['total_games'] ?? 0);
        $totalWins = (int) ($stats['total_wins'] ?? 0);
        $totalPoints = (int) ($stats['total_points'] ?? 0);
        $totalLosses = $totalGames - $totalWins;

        // محاسبه نرخ برد
        $winRate = $totalGames > 0 ? ($totalWins / $totalGames) * 100 : 0;

        // محاسبه PPG
        $ppg = $totalGames > 0 ? $totalPoints / $totalGames : 0;

        // محاسبه ضریب اطمینان
        $minGames = (int) \Application\Services\SettingsRepository::getInstance()->get('min_games_for_leaderboard', 5);
        $confidence = min(1.0, $totalGames / max(1, $minGames * 2));

        // محاسبه امتیاز نهایی رتبه‌بندی
        $finalScore = ($ppg * $confidence) + ($winRate * 0.5);

        // محاسبه streak فعلی
        $currentStreak = $this->calculateCurrentStreak($userId);
        $bestStreak = $this->calculateBestStreak($userId);

        // آپدیت یا ایجاد رکورد در leaderboard_cache
        $exists = $this->db->fetchOne(
            "SELECT user_id FROM leaderboard_cache WHERE user_id = ?",
            [$userId]
        );

        if ($exists) {
            $this->db->update('leaderboard_cache', [
                'total_games' => $totalGames,
                'total_wins' => $totalWins,
                'total_losses' => $totalLosses,
                'total_points' => $totalPoints,
                'win_rate' => round($winRate, 2),
                'points_per_game' => round($ppg, 2),
                'confidence_factor' => round($confidence, 2),
                'final_rank_score' => round($finalScore, 2),
                'current_streak' => $currentStreak,
                'best_streak' => $bestStreak,
            ], 'user_id = ?', [$userId]);
        } else {
            $this->db->insert('leaderboard_cache', [
                'user_id' => $userId,
                'total_games' => $totalGames,
                'total_wins' => $totalWins,
                'total_losses' => $totalLosses,
                'total_points' => $totalPoints,
                'win_rate' => round($winRate, 2),
                'points_per_game' => round($ppg, 2),
                'confidence_factor' => round($confidence, 2),
                'final_rank_score' => round($finalScore, 2),
                'current_streak' => $currentStreak,
                'best_streak' => $bestStreak,
            ]);
        }
    }

    /**
     * محاسبه streak فعلی کاربر
     */
    private function calculateCurrentStreak(int $userId): int
    {
        $recentGames = $this->db->fetchAll(
            "SELECT gp.is_winner 
             FROM game_participants gp
             JOIN games g ON gp.game_id = g.id
             WHERE gp.user_id = ? AND g.status = 'finished'
             ORDER BY g.finished_at DESC
             LIMIT 50",
            [$userId]
        );

        $streak = 0;
        foreach ($recentGames as $game) {
            if ($game['is_winner']) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * محاسبه بهترین streak کاربر
     */
    private function calculateBestStreak(int $userId): int
    {
        $games = $this->db->fetchAll(
            "SELECT gp.is_winner 
             FROM game_participants gp
             JOIN games g ON gp.game_id = g.id
             WHERE gp.user_id = ? AND g.status = 'finished'
             ORDER BY g.finished_at ASC",
            [$userId]
        );

        $bestStreak = 0;
        $currentStreak = 0;

        foreach ($games as $game) {
            if ($game['is_winner']) {
                $currentStreak++;
                $bestStreak = max($bestStreak, $currentStreak);
            } else {
                $currentStreak = 0;
            }
        }

        return $bestStreak;
    }
}