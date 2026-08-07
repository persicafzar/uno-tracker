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
     * گرفتن لیست بازیکنان با اطلاعات کامل
     */
    public function getUsersList(array $filters = [], string $sortBy = 'newest', int $page = 1, int $perPage = 20): array
    {
        $where = ['u.status = ?', 'u.status != ?'];
        $params = ['active', 'banned'];

        // فیلتر جستجو
        if (!empty($filters['search'])) {
            $where[] = "(u.nickname LIKE ? OR u.real_name LIKE ? OR u.phone LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // فیلتر سطح
        if (!empty($filters['level'])) {
            $where[] = "COALESCE(ux.current_level, 1) = ?";
            $params[] = (int) $filters['level'];
        }

        $whereClause = implode(' AND ', $where);

        // مرتب‌سازی
        $orderBy = match ($sortBy) {
            'xp' => 'COALESCE(ux.total_xp, 0) DESC',
            'points' => 'COALESCE(lc.total_points, 0) DESC',
            'wins' => 'COALESCE(lc.total_wins, 0) DESC',
            'games' => 'COALESCE(lc.total_games, 0) DESC',
            'win_rate' => 'COALESCE(lc.win_rate, 0) DESC',
            default => 'u.created_at DESC',
        };

        $offset = ($page - 1) * $perPage;

        // 🆕 Query اصلاح شده با محاسبه سطح از روی XP
        $users = $this->db->fetchAll(
            "SELECT 
            u.id,
            u.nickname,
            u.real_name,
            u.avatar_path,
            u.created_at,
            COALESCE(ux.total_xp, 0) as total_xp,
            -- 🆕 محاسبه سطح از روی XP به جای استفاده از current_level ذخیره شده
            COALESCE((
                SELECT pl.level 
                FROM player_levels pl 
                WHERE COALESCE(ux.total_xp, 0) BETWEEN pl.min_xp AND pl.max_xp 
                LIMIT 1
            ), 1) as current_level,
            -- 🆕 گرفتن عنوان سطح
            COALESCE((
                SELECT pl.title 
                FROM player_levels pl 
                WHERE COALESCE(ux.total_xp, 0) BETWEEN pl.min_xp AND pl.max_xp 
                LIMIT 1
            ), 'تازه‌کار') as level_title,
            -- 🆕 گرفتن رنگ سطح
            COALESCE((
                SELECT pl.color 
                FROM player_levels pl 
                WHERE COALESCE(ux.total_xp, 0) BETWEEN pl.min_xp AND pl.max_xp 
                LIMIT 1
            ), '#6366f1') as level_color,
            COALESCE(lc.total_points, 0) as total_points,
            COALESCE(lc.total_games, 0) as total_games,
            COALESCE(lc.total_wins, 0) as total_wins,
            COALESCE(lc.win_rate, 0) as win_rate,
            t.name as current_title,
            t.icon as title_icon
         FROM users u
         LEFT JOIN user_xp ux ON u.id = ux.user_id
         LEFT JOIN leaderboard_cache lc ON u.id = lc.user_id
         LEFT JOIN titles t ON u.current_title_id = t.id
         WHERE {$whereClause}
         ORDER BY {$orderBy}
         LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        // تعداد کل
        $total = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM users u WHERE {$whereClause}",
            $params
        )['count'];

        return [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
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
