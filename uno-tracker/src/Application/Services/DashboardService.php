<?php

namespace Application\Services;

use Core\Database;
use Core\JalaliDate;
use Infrastructure\Repositories\CardRepository;
use Infrastructure\Repositories\WinTypeRepository;

class DashboardService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * گرفتن آمار کلی کاربر
     */
    public function getUserStats(int $userId): array
    {
        // 🆕 استفاده از UserStatsService
        $statsService = new UserStatsService();
        return $statsService->getDetailedStats($userId);
    }

    /**
     * بازی‌های اخیر کاربر
     */
    public function getRecentGames(int $userId, int $limit = 10): array
    {
        $gameService = new \Application\Services\GameService();
        return $gameService->getRecentGamesWithRole($userId, $limit);
    }

    /**
     * گرفتن داده‌های نمودار پیشرفت - 🆕 با پشتیبانی از حالت تیمی
     */
    public function getProgressData(int $userId, int $days = 30): array
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $games = $this->db->fetchAll(
            "SELECT 
            DATE(g.created_at) as date,
            COUNT(*) as games_count,
            SUM(gp.total_score) as total_points,
            SUM(CASE 
                WHEN g.game_mode = 'solo' AND g.winner_participant_id = gp.id THEN 1
                WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                     AND g.winner_team_id = gp.team_id THEN 1
                ELSE 0 
            END) as wins
         FROM games g
         JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ? 
         AND g.created_at >= ?
         AND g.status = 'finished'
         GROUP BY DATE(g.created_at)
         ORDER BY date ASC",
            [$userId, $startDate]
        );

        $labels = [];
        $pointsData = [];
        $winsData = [];
        $gamesData = [];

        foreach ($games as $game) {
            $jDate = JalaliDate::fromGregorian($game['date']);
            $labels[] = $jDate['month'] . '/' . $jDate['day'];
            $pointsData[] = (int) $game['total_points'];
            $winsData[] = (int) $game['wins'];
            $gamesData[] = (int) $game['games_count'];
        }

        return [
            'labels' => $labels,
            'points' => $pointsData,
            'wins' => $winsData,
            'games' => $gamesData,
        ];
    }

    /**
     * گرفتن توزیع بردها - 🆕 با پشتیبانی از حالت تیمی
     */
    public function getWinDistribution(int $userId): array
    {
        $distribution = $this->db->fetchAll(
            "SELECT 
            CASE 
                WHEN g.game_mode = 'solo' THEN 'انفرادی'
                WHEN g.game_mode = 'friendly' THEN 'تیمی'
            END as mode,
            COUNT(*) as count
         FROM games g
         JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ? 
         AND g.status = 'finished'
         AND (
             (g.game_mode = 'solo' AND g.winner_participant_id = gp.id)
             OR (g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                 AND g.winner_team_id = gp.team_id)
         )
         GROUP BY g.game_mode",
            [$userId]
        );

        $result = [];
        foreach ($distribution as $item) {
            $result[] = [
                'label' => $item['mode'],
                'value' => (int) $item['count'],
            ];
        }

        return $result;
    }

    /**
     * گرفتن خلاصه ماهانه بازی‌ها - 🆕 با پشتیبانی از حالت تیمی
     */
    public function getMonthlySummary(int $userId, int $months = 6): array
    {
        $startDate = date('Y-m-d', strtotime("-{$months} months"));

        $games = $this->db->fetchAll(
            "SELECT 
            g.id,
            g.name,
            g.game_mode,
            g.created_at,
            g.winner_participant_id,
            g.winner_team_id,
            gp.wins_count,
            gp.total_score,
            gp.team_id,
            CASE 
                WHEN g.game_mode = 'solo' AND g.winner_participant_id = gp.id THEN 1
                WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                     AND g.winner_team_id = gp.team_id THEN 1
                ELSE 0
            END as is_winner,
            (SELECT COUNT(*) FROM game_participants gp2 WHERE gp2.game_id = g.id) as total_players,
            (SELECT COUNT(*) FROM teams t WHERE t.game_id = g.id) as total_teams
         FROM games g
         JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ? 
         AND g.created_at >= ?
         AND g.status = 'finished'
         ORDER BY g.created_at DESC",
            [$userId, $startDate]
        );

        $monthlyData = [];
        foreach ($games as $game) {
            $jDate = JalaliDate::fromGregorian(date('Y-m-d', strtotime($game['created_at'])));
            $monthKey = sprintf('%04d-%02d', $jDate['year'], $jDate['month']);
            $monthName = JalaliDate::getMonthName($jDate['month']);
            $label = $monthName . ' ' . $jDate['year'];

            if (!isset($monthlyData[$monthKey])) {
                $monthlyData[$monthKey] = [
                    'label' => $label,
                    'year' => $jDate['year'],
                    'month' => $jDate['month'],
                    'games' => 0,
                    'wins' => 0,
                    'losses' => 0,
                    'points' => 0,
                    'team_games' => 0,
                    'team_wins' => 0,
                    'solo_games' => 0,
                    'solo_wins' => 0,
                    'games_list' => [],
                ];
            }

            $monthlyData[$monthKey]['games']++;
            $monthlyData[$monthKey]['points'] += (int) $game['total_score'];

            if (!empty($game['is_winner'])) {
                $monthlyData[$monthKey]['wins']++;
            } else {
                $monthlyData[$monthKey]['losses']++;
            }

            if ($game['game_mode'] === 'friendly') {
                $monthlyData[$monthKey]['team_games']++;
                if (!empty($game['is_winner'])) {
                    $monthlyData[$monthKey]['team_wins']++;
                }
            } else {
                $monthlyData[$monthKey]['solo_games']++;
                if (!empty($game['is_winner'])) {
                    $monthlyData[$monthKey]['solo_wins']++;
                }
            }

            if (count($monthlyData[$monthKey]['games_list']) < 3) {
                $monthlyData[$monthKey]['games_list'][] = [
                    'name' => $game['name'] ?: 'بازی #' . $game['id'],
                    'is_winner' => !empty($game['is_winner']),
                    'mode' => $game['game_mode'],
                ];
            }
        }

        foreach ($monthlyData as &$data) {
            $data['win_rate'] = $data['games'] > 0
                ? round(($data['wins'] / $data['games']) * 100, 1)
                : 0;
            $data['points_per_game'] = $data['games'] > 0
                ? round($data['points'] / $data['games'], 1)
                : 0;
        }

        krsort($monthlyData);

        return array_values($monthlyData);
    }

    /**
     * 🆕 گرفتن مقایسه با رقبا - اصلاح شده با پشتیبانی از حالت تیمی
     */
    public function getFriendsComparison(int $userId, string $period = 'all', string $mode = 'rivals'): array
    {
        // محاسبه تاریخ شروع بر اساس period
        $startDate = null;
        $periodLabel = 'از ابتدا';

        switch ($period) {
            case 'month':
                $startDate = date('Y-m-d', strtotime('-30 days'));
                $periodLabel = '۳۰ روز اخیر';
                break;
            case '3months':
                $startDate = date('Y-m-d', strtotime('-90 days'));
                $periodLabel = '۳ ماه اخیر';
                break;
            case '6months':
                $startDate = date('Y-m-d', strtotime('-180 days'));
                $periodLabel = '۶ ماه اخیر';
                break;
            case 'all':
            default:
                $startDate = null;
                $periodLabel = 'ابتدا تاکنون';
                break;
        }

        // 🆕 Query اصلاح شده برای شمارش بردها (پشتیبانی از تیمی)
        $winCaseSql = "SUM(CASE 
        WHEN g.game_mode = 'solo' AND g.winner_participant_id = gp.id THEN 1
        WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
             AND g.winner_team_id = gp.team_id THEN 1
        ELSE 0 
    END)";

        if ($mode === 'rivals') {
            // حالت ۱: فقط کاربرانی که با این کاربر بازی کرده‌اند
            $params = [$userId];
            $dateCondition = '';

            if ($startDate) {
                $dateCondition = " AND g.created_at >= ?";
                $params[] = $startDate;
            }

            $userGames = $this->db->fetchAll(
                "SELECT g.id FROM games g
             JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? {$dateCondition} AND g.status = 'finished'",
                $params
            );

            if (empty($userGames)) {
                return [
                    'friends' => [],
                    'user_rank' => null,
                    'total_players' => 0,
                    'mode' => 'rivals',
                    'period' => $period,
                    'period_label' => $periodLabel,
                ];
            }

            $gameIds = array_column($userGames, 'id');
            $gameIdsStr = implode(',', array_map('intval', $gameIds));

            // 🆕 Query اصلاح شده
            $allPlayers = $this->db->fetchAll(
                "SELECT 
                u.id,
                u.nickname,
                u.avatar_path,
                COUNT(DISTINCT g.id) as games_played,
                SUM(gp.total_score) as total_points,
                {$winCaseSql} as total_wins,
                ROUND(
                    ({$winCaseSql} * 100.0) / 
                    NULLIF(COUNT(DISTINCT g.id), 0), 
                    1
                ) as win_rate
             FROM users u
             JOIN game_participants gp ON u.id = gp.user_id
             JOIN games g ON gp.game_id = g.id
             WHERE g.id IN ({$gameIdsStr})
             AND g.status = 'finished'
             GROUP BY u.id, u.nickname, u.avatar_path
             HAVING COUNT(DISTINCT g.id) >= 1
             ORDER BY total_points DESC, total_wins DESC, win_rate DESC",
                []
            );
        } else {
            // حالت ۲: همه کاربران سایت
            $params = [];
            $dateCondition = '';

            if ($startDate) {
                $dateCondition = " AND g.created_at >= ?";
                $params[] = $startDate;
            }

            // 🆕 Query اصلاح شده
            $allPlayers = $this->db->fetchAll(
                "SELECT 
                u.id,
                u.nickname,
                u.avatar_path,
                COUNT(DISTINCT g.id) as games_played,
                SUM(gp.total_score) as total_points,
                {$winCaseSql} as total_wins,
                ROUND(
                    ({$winCaseSql} * 100.0) / 
                    NULLIF(COUNT(DISTINCT g.id), 0), 
                    1
                ) as win_rate
             FROM users u
             JOIN game_participants gp ON u.id = gp.user_id
             JOIN games g ON gp.game_id = g.id
             WHERE g.status = 'finished' {$dateCondition}
             GROUP BY u.id, u.nickname, u.avatar_path
             HAVING COUNT(DISTINCT g.id) >= 1
             ORDER BY total_points DESC, total_wins DESC, win_rate DESC",
                $params
            );
        }

        // محاسبه رتبه کاربر از بین همه
        $userRank = null;
        $totalPlayers = count($allPlayers);

        foreach ($allPlayers as $index => $player) {
            if ((int)$player['id'] === $userId) {
                $userRank = $index + 1;
                break;
            }
        }

        // فقط ۱۰ نفر اول را برای نمایش ببر
        $players = array_slice($allPlayers, 0, 10);

        return [
            'friends' => $players,
            'user_rank' => $userRank,
            'total_players' => $totalPlayers,
            'mode' => $mode,
            'period' => $period,
            'period_label' => $periodLabel,
        ];
    }

    /**
     * گرفتن آمار هفتگی - 🆕 با پشتیبانی از حالت تیمی
     */
    public function getWeeklyStats(int $userId): array
    {
        $stats = [];
        $jalaliDayNames = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];

        for ($i = 6; $i >= 0; $i--) {
            $timestamp = strtotime("-{$i} days");
            $gDate = getdate($timestamp);

            $dayOfWeekIndex = ($gDate['wday'] + 1) % 7;
            $dayName = $jalaliDayNames[$dayOfWeekIndex];

            $dateStr = date('Y-m-d', $timestamp);

            $dayStats = $this->db->fetchOne(
                "SELECT 
                COUNT(*) as games,
                SUM(CASE 
                    WHEN g.game_mode = 'solo' AND g.winner_participant_id = gp.id THEN 1
                    WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                         AND g.winner_team_id = gp.team_id THEN 1
                    ELSE 0 
                END) as wins,
                SUM(gp.total_score) as points
             FROM games g
             JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? 
             AND DATE(g.created_at) = ?
             AND g.status = 'finished'",
                [$userId, $dateStr]
            );

            $stats[] = [
                'day' => $dayName,
                'date' => $dateStr,
                'games' => (int)($dayStats['games'] ?? 0),
                'wins' => (int)($dayStats['wins'] ?? 0),
                'points' => (int)($dayStats['points'] ?? 0),
            ];
        }

        return $stats;
    }
    /**
     * 🆕 گرفتن ۱۰ بازیکن اخیر عضو شده
     */
    public function getRecentPlayers(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT 
                u.id,
                u.nickname,
                u.real_name,
                u.avatar_path,
                u.created_at,
                COALESCE(ux.total_xp, 0) as total_xp,
                COALESCE(ux.current_level, 1) as current_level,
                t.name as current_title,
                t.icon as title_icon,
                pl.title as level_title,
                pl.color as level_color
             FROM users u
             LEFT JOIN user_xp ux ON u.id = ux.user_id
             LEFT JOIN titles t ON u.current_title_id = t.id
             LEFT JOIN player_levels pl ON ux.current_level = pl.level
             WHERE u.status = 'active'
             ORDER BY u.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
    /**
     * 🆕 گرفتن بازیکنان با بالاترین امتیاز (Top Players) - اصلاح شده
     */
    public function getTopPlayers(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT 
            u.id,
            u.nickname,
            u.real_name,
            u.tagline,
            u.avatar_path,
            u.created_at,
            COALESCE(ux.total_xp, 0) as total_xp,
            -- 🆕 محاسبه سطح از روی XP
            COALESCE((
                SELECT pl.level 
                FROM player_levels pl 
                WHERE COALESCE(ux.total_xp, 0) BETWEEN pl.min_xp AND pl.max_xp 
                LIMIT 1
            ), 1) as current_level,
            -- 🆕 عنوان سطح
            COALESCE((
                SELECT pl.title 
                FROM player_levels pl 
                WHERE COALESCE(ux.total_xp, 0) BETWEEN pl.min_xp AND pl.max_xp 
                LIMIT 1
            ), 'تازه‌کار') as level_title,
            -- 🆕 رنگ سطح
            COALESCE((
                SELECT pl.color 
                FROM player_levels pl 
                WHERE COALESCE(ux.total_xp, 0) BETWEEN pl.min_xp AND pl.max_xp 
                LIMIT 1
            ), '#6366f1') as level_color,
            COALESCE(lc.total_points, 0) as total_points,
            COALESCE(lc.total_games, 0) as total_games,
            COALESCE(lc.total_wins, 0) as total_wins,
            t.name as current_title,
            t.icon as title_icon
         FROM users u
         LEFT JOIN user_xp ux ON u.id = ux.user_id
         LEFT JOIN titles t ON u.current_title_id = t.id
         LEFT JOIN leaderboard_cache lc ON u.id = lc.user_id
         WHERE u.status = 'active'
         ORDER BY COALESCE(lc.total_points, 0) DESC, COALESCE(ux.total_xp, 0) DESC
         LIMIT ?",
            [$limit]
        );
    }
    /**
     * 🃏 گرفتن لیست کارت‌های فعال
     */
    public function getCards(): array
    {
        $cardRepo = new CardRepository();
        $cards = $cardRepo->findAllActive();

        return array_map(fn($card) => [
            'id' => $card->id,
            'name' => $card->name,
            'emoji' => $card->emoji,
            'rarity' => $card->rarity,
            'score_multiplier' => $card->score_multiplier,
            'description' => $card->description,
            'is_active' => $card->is_active,
        ], $cards);
    }

    /**
     * ⚡ گرفتن لیست نوع‌های برد فعال
     */
    public function getWinTypes(): array
    {
        $winTypeRepo = new WinTypeRepository();
        return $winTypeRepo->findAllActive();
    }
}
