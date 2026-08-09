<?php

namespace Application\Services;

use Core\Database;

class UserStatsService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * گرفتن آمار کامل کاربر - 🆕 با پشتیبانی از تیمی
     */
    public function getUserStats(int $userId): array
    {
        $stats = $this->db->fetchOne(
            "SELECT 
            COUNT(DISTINCT g.id) as total_games,
            SUM(CASE 
                WHEN g.game_mode = 'solo' AND g.winner_participant_id = gp.id THEN 1
                WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                     AND g.winner_team_id = gp.team_id THEN 1
                ELSE 0 
            END) as total_wins,
            CAST(COALESCE(SUM(gp.total_score), 0) AS DECIMAL(10, 2)) as total_points
         FROM games g
         INNER JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ? 
         AND g.status = 'finished'
         AND gp.user_id IS NOT NULL",
            [$userId]
        );

        $totalGames = (int)($stats['total_games'] ?? 0);
        $totalWins = (int)($stats['total_wins'] ?? 0);
        $totalPoints = (float)($stats['total_points'] ?? 0);

        $winRate = $totalGames > 0
            ? round(($totalWins / $totalGames) * 100, 1)
            : 0;

        $pointsPerGame = $totalGames > 0
            ? round($totalPoints / $totalGames, 2)
            : 0;

        return [
            'total_games' => $totalGames,
            'total_wins' => $totalWins,
            'total_losses' => max(0, $totalGames - $totalWins),
            'total_points' => $totalPoints,
            'win_rate' => $winRate,
            'points_per_game' => $pointsPerGame,
        ];
    }

    /**
     * 🆕 گرفتن آمار تفصیلی - اصلاح شده برای تیمی
     */
    public function getDetailedStats(int $userId): array
    {
        $basicStats = $this->getUserStats($userId);

        // آمار بر اساس وضعیت بازی
        $statusStats = $this->db->fetchOne(
            "SELECT 
            COUNT(DISTINCT CASE WHEN g.status = 'active' THEN g.id END) as active_games,
            COUNT(DISTINCT CASE WHEN g.status = 'pending' THEN g.id END) as pending_games,
            COUNT(DISTINCT CASE WHEN g.status = 'paused' THEN g.id END) as paused_games,
            COUNT(DISTINCT CASE WHEN g.status = 'cancelled' THEN g.id END) as cancelled_games
         FROM games g
         INNER JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ? AND gp.user_id IS NOT NULL",
            [$userId]
        );

        // 🆕 آمار بر اساس حالت بازی - اصلاح شده
        $modeStats = $this->db->fetchOne(
            "SELECT 
            COUNT(DISTINCT CASE WHEN g.game_mode = 'solo' THEN g.id END) as solo_games,
            COUNT(DISTINCT CASE WHEN g.game_mode = 'friendly' THEN g.id END) as team_games,
            SUM(CASE WHEN g.game_mode = 'solo' AND g.winner_participant_id = gp.id THEN 1 ELSE 0 END) as solo_wins,
            SUM(CASE WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                     AND g.winner_team_id = gp.team_id THEN 1 ELSE 0 END) as team_wins
         FROM games g
         INNER JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ? 
         AND g.status = 'finished'
         AND gp.user_id IS NOT NULL",
            [$userId]
        );

        // گرفتن استریک
        $streakData = $this->db->fetchOne(
            "SELECT current_streak, best_streak FROM user_streaks WHERE user_id = ?",
            [$userId]
        );

        return array_merge($basicStats, [
            'active_games' => (int)($statusStats['active_games'] ?? 0),
            'pending_games' => (int)($statusStats['pending_games'] ?? 0),
            'paused_games' => (int)($statusStats['paused_games'] ?? 0),
            'cancelled_games' => (int)($statusStats['cancelled_games'] ?? 0),
            'solo_games' => (int)($modeStats['solo_games'] ?? 0),
            'team_games' => (int)($modeStats['team_games'] ?? 0),
            'solo_wins' => (int)($modeStats['solo_wins'] ?? 0),
            'team_wins' => (int)($modeStats['team_wins'] ?? 0),
            'current_streak' => (int)($streakData['current_streak'] ?? 0),
            'best_streak' => (int)($streakData['best_streak'] ?? 0),
        ]);
    }

    /**
     * 🆕 گرفتن آمار بر اساس وضعیت (برای نمودار)
     */
    public function getStatsByStatus(int $userId): array
    {
        $stats = $this->db->fetchAll(
            "SELECT 
                g.status,
                COUNT(DISTINCT g.id) as count
             FROM games g
             INNER JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? AND gp.user_id IS NOT NULL
             GROUP BY g.status
             ORDER BY count DESC",
            [$userId]
        );

        $statusLabels = [
            'finished' => 'پایان یافته',
            'active' => 'در حال بازی',
            'pending' => 'در انتظار',
            'paused' => 'متوقف',
            'cancelled' => 'لغو شده',
        ];

        $result = [];
        foreach ($stats as $stat) {
            $result[] = [
                'status' => $stat['status'],
                'label' => $statusLabels[$stat['status']] ?? $stat['status'],
                'count' => (int) $stat['count'],
            ];
        }

        return $result;
    }

    /**
     * 🆕 گرفتن آمار بر اساس حالت بازی (برای نمودار) - اصلاح شده
     */
    public function getStatsByMode(int $userId): array
    {
        $stats = $this->db->fetchAll(
            "SELECT 
            g.game_mode,
            COUNT(DISTINCT g.id) as total_games,
            SUM(CASE 
                WHEN g.game_mode = 'solo' AND g.winner_participant_id = gp.id THEN 1
                WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                     AND g.winner_team_id = gp.team_id THEN 1
                ELSE 0 
            END) as total_wins,
            CAST(COALESCE(SUM(gp.total_score), 0) AS DECIMAL(10, 2)) as total_points
         FROM games g
         INNER JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ? 
         AND g.status = 'finished'
         AND gp.user_id IS NOT NULL
         GROUP BY g.game_mode",
            [$userId]
        );

        $modeLabels = [
            'solo' => 'انفرادی',
            'friendly' => 'تیمی',
        ];

        $result = [];
        foreach ($stats as $stat) {
            $totalGames = (int) $stat['total_games'];
            $totalWins = (int) $stat['total_wins'];
            $result[] = [
                'mode' => $stat['game_mode'],
                'label' => $modeLabels[$stat['game_mode']] ?? $stat['game_mode'],
                'total_games' => $totalGames,
                'total_wins' => $totalWins,
                'total_points' => (float) $stat['total_points'],
                'win_rate' => $totalGames > 0 ? round(($totalWins / $totalGames) * 100, 1) : 0,
            ];
        }

        return $result;
    }

    /**
     * 🆕 گرفتن آمار روزانه برای نمودار (۳۰ روز اخیر)
     */
    public function getDailyStats(int $userId, int $days = 30): array
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $stats = $this->db->fetchAll(
            "SELECT 
                DATE(g.created_at) as date,
                COUNT(DISTINCT g.id) as games_count,
                SUM(CASE WHEN gp.is_winner = 1 THEN 1 ELSE 0 END) as wins_count,
                COALESCE(SUM(gp.total_score), 0) as points
             FROM games g
             INNER JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? 
             AND g.status = 'finished'
             AND g.created_at >= ?
             AND gp.user_id IS NOT NULL
             GROUP BY DATE(g.created_at)
             ORDER BY date ASC",
            [$userId, $startDate]
        );

        return array_map(fn($stat) => [
            'date' => $stat['date'],
            'games' => (int) $stat['games_count'],
            'wins' => (int) $stat['wins_count'],
            'points' => (float) $stat['points'],
        ], $stats);
    }

    /**
     * 🆕 گرفتن آمار ساعتی برای نمودار (۲۴ ساعت اخیر)
     */
    public function getHourlyStats(int $userId): array
    {
        $stats = $this->db->fetchAll(
            "SELECT 
                HOUR(g.created_at) as hour,
                COUNT(DISTINCT g.id) as games_count
             FROM games g
             INNER JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? 
             AND g.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             AND gp.user_id IS NOT NULL
             GROUP BY HOUR(g.created_at)
             ORDER BY hour ASC",
            [$userId]
        );

        // پر کردن ساعت‌های خالی
        $hourlyData = array_fill(0, 24, 0);
        foreach ($stats as $stat) {
            $hourlyData[(int) $stat['hour']] = (int) $stat['games_count'];
        }

        return $hourlyData;
    }

    /**
     * 🆕 گرفتن آمار روز هفته برای نمودار
     */
    public function getDayOfWeekStats(int $userId): array
    {
        $stats = $this->db->fetchAll(
            "SELECT 
                DAYOFWEEK(g.created_at) as day_of_week,
                COUNT(DISTINCT g.id) as games_count,
                SUM(CASE WHEN gp.is_winner = 1 THEN 1 ELSE 0 END) as wins_count
             FROM games g
             INNER JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? 
             AND g.status = 'finished'
             AND gp.user_id IS NOT NULL
             GROUP BY DAYOFWEEK(g.created_at)
             ORDER BY day_of_week ASC",
            [$userId]
        );

        // DAYOFWEEK: 1=Sunday, 2=Monday, ..., 7=Saturday
        // تبدیل به شنبه=0، یکشنبه=1، ...، جمعه=6
        $dayLabels = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
        $dayData = array_fill(0, 7, ['games' => 0, 'wins' => 0]);

        foreach ($stats as $stat) {
            $dayOfWeek = (int) $stat['day_of_week'];
            // تبدیل: 1(Sun)->6, 2(Mon)->0, 3(Tue)->1, ..., 7(Sat)->5
            $index = ($dayOfWeek + 5) % 7;
            $dayData[$index] = [
                'games' => (int) $stat['games_count'],
                'wins' => (int) $stat['wins_count'],
            ];
        }

        $result = [];
        foreach ($dayData as $index => $data) {
            $result[] = [
                'day' => $dayLabels[$index],
                'games' => $data['games'],
                'wins' => $data['wins'],
            ];
        }

        return $result;
    }

    /**
     * به‌روزرسانی leaderboard_cache
     */
    public function refreshLeaderboardCache(int $userId): void
    {
        $stats = $this->getUserStats($userId);
        $detailedStats = $this->getDetailedStats($userId);

        try {
            $existing = $this->db->fetchOne(
                "SELECT user_id FROM leaderboard_cache WHERE user_id = ?",
                [$userId]
            );

            // 🆕 استفاده از last_updated به جای updated_at
            $data = [
                'user_id' => $userId,
                'total_games' => $stats['total_games'],
                'total_wins' => $stats['total_wins'],
                'total_losses' => $stats['total_losses'],
                'total_points' => $stats['total_points'],
                'win_rate' => $stats['win_rate'],
                'points_per_game' => $stats['points_per_game'],
                'current_streak' => $detailedStats['current_streak'],
                'best_streak' => $detailedStats['best_streak'],
                'last_updated' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $this->db->update(
                    'leaderboard_cache',
                    $data,
                    'user_id = ?',
                    [$userId]
                );
            } else {
                $this->db->insert('leaderboard_cache', $data);
            }
        } catch (\PDOException $e) {
            log_message("⚠️ Leaderboard cache error: " . $e->getMessage());
        }
    }


    /**
     * 🆕 گرفتن آمار استفاده از کارت‌ها (اصلاح شده)
     */
    public function getCardUsageStats(int $userId): array
    {
        // کارت‌هایی که کاربر با آنها برنده شده
        $winningCards = $this->db->fetchAll(
            "SELECT c.id, c.name, c.emoji, c.rarity, COUNT(gr.id) as usage_count
         FROM game_rounds gr
         JOIN game_participants gp ON gr.winner_participant_id = gp.id
         JOIN cards c ON gr.winning_card_id = c.id
         WHERE gp.user_id = ?
         AND gr.winning_card_id IS NOT NULL
         GROUP BY c.id, c.name, c.emoji, c.rarity
         ORDER BY usage_count DESC
         LIMIT 10",
            [$userId]
        );

        return array_map(fn($card) => [
            'id' => (int) $card['id'],
            'name' => $card['name'],
            // 🆕 اگر emoji خالی بود، حرف اول نام را قرار بده
            'emoji' => !empty($card['emoji']) ? $card['emoji'] : mb_substr($card['name'], 0, 1),
            'rarity' => $card['rarity'] ?? 'common',
            'usage_count' => (int) $card['usage_count'],
        ], $winningCards);
    }
}
