<?php

namespace Application\Services;

use Core\Database;

class UserService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * گرفتن تمام کاربران
     */
    public function getAllActiveUsers(): array
    {
        return $this->db->fetchAll(
            "SELECT id, nickname, real_name, avatar_path 
             FROM users 
             ORDER BY nickname ASC"
        );
    }

    /**
     * گرفتن کاربران مجاز برای انتخاب در بازی
     * فقط کاربرانی که هم فعال هستند و هم مجوز شرکت در بازی دارند
     */
    public function getUsersForGameSelection(): array
    {
        return $this->db->fetchAll(
            "SELECT id, nickname, real_name, avatar_path 
         FROM users 
         WHERE status = 'active' AND can_join_game = 1
         ORDER BY nickname ASC"
        );
    }

    /**
     * گرفتن کاربر بر اساس ID
     */
    public function getById(int $userId): ?array
    {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
    }

    /**
     * گرفتن پروفایل کامل کاربر (با آمار بازی‌ها)
     */
    public function getUserProfile(int $userId): ?array
    {
        $user = $this->db->fetchOne(
            "SELECT id, nickname, real_name, tagline, avatar_path, phone, created_at 
         FROM users 
         WHERE id = ?",
            [$userId]
        );

        if (!$user) {
            return null;
        }

        $user['user_id'] = $userId;

        // 🆕 استفاده از UserStatsService برای محاسبه آمار
        $statsService = new UserStatsService();
        $stats = $statsService->getDetailedStats($userId);

        $user['total_games'] = $stats['total_games'];
        $user['total_wins'] = $stats['total_wins'];
        $user['total_losses'] = $stats['total_losses'];
        $user['total_points'] = $stats['total_points'];
        $user['win_rate'] = $stats['win_rate'];
        $user['points_per_game'] = $stats['points_per_game'];
        $user['current_streak'] = $stats['current_streak'];
        $user['best_streak'] = $stats['best_streak'];

        // گرفتن آخرین بازی‌های کاربر
        $user['recent_games'] = $this->db->fetchAll(
            "SELECT 
            g.id,
            g.name,
            g.game_mode,
            g.target_wins,
            g.status,
            g.created_at,
            g.finished_at,
            g.winner_participant_id,
            gp.wins_count,
            gp.total_score,
            gp.id as participant_id,
            (SELECT COUNT(*) FROM game_participants gp2 WHERE gp2.game_id = g.id) as total_players,
            (g.winner_participant_id = gp.id) as is_winner
         FROM games g
         INNER JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ?
         ORDER BY g.created_at DESC
         LIMIT 10",
            [$userId]
        );

        return $user;
    }

    /**
     * بررسی تکراری نبودن nickname
     */
    public function isNicknameTaken(string $nickname, ?int $excludeUserId = null): bool
    {
        $sql = "SELECT id FROM users WHERE nickname = ?";
        $params = [$nickname];

        if ($excludeUserId) {
            $sql .= " AND id != ?";
            $params[] = $excludeUserId;
        }

        $existing = $this->db->fetchOne($sql, $params);
        return $existing !== null;
    }

    /**
     * بررسی تکراری نبودن شماره تماس
     */
    public function isPhoneTaken(string $phone, ?int $excludeUserId = null): bool
    {
        $sql = "SELECT id FROM users WHERE phone = ?";
        $params = [$phone];

        if ($excludeUserId) {
            $sql .= " AND id != ?";
            $params[] = $excludeUserId;
        }

        $existing = $this->db->fetchOne($sql, $params);
        return $existing !== null;
    }
    /**
     * 🆕 گرفتن لیست بازیکنان با فیلتر، جستجو و مرتب‌سازی
     */
    public function getUsersList(array $filters = [], string $sortBy = 'newest', int $page = 1, int $perPage = 20): array
    {
        $where = ["u.status = 'active'"];
        $params = [];

        // فیلتر جستجو
        if (!empty($filters['search'])) {
            $where[] = "(u.nickname LIKE ? OR u.real_name LIKE ? OR u.phone LIKE ?)";
            $searchParam = '%' . $filters['search'] . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        // فیلتر بر اساس سطح
        if (!empty($filters['level'])) {
            $where[] = "COALESCE(ux.current_level, 1) = ?";
            $params[] = (int) $filters['level'];
        }

        // فیلتر بر اساس وضعیت
        if (!empty($filters['status'])) {
            $where[] = "u.status = ?";
            $params[] = $filters['status'];
        }

        // مرتب‌سازی
        $orderBy = match ($sortBy) {
            'xp_desc' => 'COALESCE(ux.total_xp, 0) DESC',
            'xp_asc' => 'COALESCE(ux.total_xp, 0) ASC',
            'points_desc' => 'COALESCE(lc.total_points, 0) DESC',  // 🆕
            'points_asc' => 'COALESCE(lc.total_points, 0) ASC',    // 🆕
            'name_asc' => 'u.nickname ASC',
            'name_desc' => 'u.nickname DESC',
            'oldest' => 'u.created_at ASC',
            'level_desc' => 'COALESCE(ux.current_level, 1) DESC',
            'newest' => 'u.created_at DESC',
            default => 'u.created_at DESC',
        };

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        // گرفتن لیست کاربران
        $users = $this->db->fetchAll(
            "SELECT 
                u.id,
                u.nickname,
                u.real_name,
                u.avatar_path,
                u.created_at,
                u.status,
                COALESCE(ux.total_xp, 0) as total_xp,
                COALESCE(ux.current_level, 1) as current_level,
                t.name as current_title,
                t.icon as title_icon,
                pl.title as level_title,
                pl.color as level_color,
                COALESCE(lc.total_games, 0) as total_games,
                COALESCE(lc.total_wins, 0) as total_wins,
                COALESCE(lc.total_points, 0) as total_points,
                COALESCE(lc.win_rate, 0) as win_rate
             FROM users u
             LEFT JOIN user_xp ux ON u.id = ux.user_id
             LEFT JOIN titles t ON u.current_title_id = t.id
             LEFT JOIN player_levels pl ON ux.current_level = pl.level
             LEFT JOIN leaderboard_cache lc ON u.id = lc.user_id
             WHERE {$whereClause}
             ORDER BY {$orderBy}
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        // تعداد کل برای Pagination
        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as count
             FROM users u
             LEFT JOIN user_xp ux ON u.id = ux.user_id
             WHERE {$whereClause}",
            $params
        );

        return [
            'users' => $users,
            'total' => (int) ($total['count'] ?? 0),
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil(($total['count'] ?? 0) / $perPage),
        ];
    }

    /**
     * 🆕 گرفتن تمام سطوح برای فیلتر
     */
    public function getAllLevels(): array
    {
        return $this->db->fetchAll(
            "SELECT level, title, color, icon, min_xp, max_xp 
             FROM player_levels 
             ORDER BY level ASC"
        );
    }
}
