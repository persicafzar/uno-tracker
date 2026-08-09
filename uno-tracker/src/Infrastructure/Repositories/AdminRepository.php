<?php

namespace Infrastructure\Repositories;

use Core\Database;

class AdminRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ============================================
    // آمار کلی
    // ============================================

    public function getDashboardStats(): array
    {
        $stats = [];

        // تعداد کل کاربران
        $stats['total_users'] = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM users"
        )['count'];

        // کاربران فعال
        $stats['active_users'] = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM users WHERE status = 'active'"
        )['count'];

        // کاربران مسدود
        $stats['banned_users'] = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM users WHERE status = 'banned'"
        )['count'];

        // کل بازی‌ها
        $stats['total_games'] = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM games"
        )['count'];

        // بازی‌های فعال
        $stats['active_games'] = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM games WHERE status = 'active'"
        )['count'];

        // بازی‌های پایان یافته
        $stats['finished_games'] = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM games WHERE status = 'finished'"
        )['count'];

        // بازی‌های امروز
        $stats['today_games'] = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM games WHERE DATE(created_at) = CURDATE()"
        )['count'];

        // کل دورهای ثبت شده
        $stats['total_rounds'] = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM game_rounds"
        )['count'];

        // نشان‌های کسب شده
        $stats['total_achievements_unlocked'] = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM user_achievements WHERE is_completed = 1"
        )['count'];


        return $stats;
    }

    /**
     * آمار ثبت‌نام کاربران در ۳۰ روز اخیر
     */
    public function getUserRegistrationStats(int $days = 30): array
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $rows = $this->db->fetchAll(
            "SELECT DATE(created_at) as date, COUNT(*) as count
             FROM users
             WHERE created_at >= ?
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [$startDate]
        );

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = $row['date'];
            $data[] = (int) $row['count'];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * آمار بازی‌ها در ۳۰ روز اخیر
     */
    public function getGamesStats(int $days = 30): array
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $rows = $this->db->fetchAll(
            "SELECT DATE(created_at) as date, COUNT(*) as count
             FROM games
             WHERE created_at >= ?
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [$startDate]
        );

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = $row['date'];
            $data[] = (int) $row['count'];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * توزیع حالت بازی‌ها
     */
    public function getGameModeDistribution(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT game_mode, COUNT(*) as count
             FROM games
             GROUP BY game_mode"
        );

        return array_map(fn($row) => [
            'mode' => $row['game_mode'] === 'solo' ? 'انفرادی' : 'تیمی',
            'count' => (int) $row['count']
        ], $rows);
    }

    // ============================================
    // کاربران
    // ============================================

    public function getUsers(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            // 🆕 اصلاح: جستجو بر اساس nickname, real_name, phone (نه email)
            $where[] = "(nickname LIKE ? OR real_name LIKE ? OR phone LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }

        $whereClause = implode(' AND ', $where);

        $users = $this->db->fetchAll(
            "SELECT u.*,
                (SELECT COUNT(*) FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = u.id) as total_games,
                (SELECT COUNT(*) FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = u.id AND g.winner_participant_id = gp.id) as total_wins
         FROM users u
         WHERE {$whereClause}
         ORDER BY u.created_at DESC
         LIMIT ? OFFSET ?",
            array_merge($params, [$limit, $offset])
        );

        $total = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM users u WHERE {$whereClause}",
            $params
        )['count'];

        return ['users' => $users, 'total' => $total];
    }

    public function getUser(int $userId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE id = ?",
            [$userId]
        );
    }

    public function updateUserStatus(int $userId, string $status): bool
    {
        return $this->db->update(
            'users',
            ['status' => $status],
            'id = ?',
            [$userId]
        );
    }

    public function updateUserRole(int $userId, string $role): bool
    {
        return $this->db->update(
            'users',
            ['role' => $role],
            'id = ?',
            [$userId]
        );
    }

    public function deleteUser(int $userId): bool
    {
        return $this->db->delete('users', 'id = ?', [$userId]);
    }

    // ============================================
    // بازی‌ها
    // ============================================

    public function getGames(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(g.name LIKE ? OR g.id = ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = (int) $filters['search'];
        }

        if (!empty($filters['status'])) {
            $where[] = "g.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['mode'])) {
            $where[] = "g.game_mode = ?";
            $params[] = $filters['mode'];
        }

        $whereClause = implode(' AND ', $where);

        // 🆕 اصلاح: استفاده از COALESCE برای نمایش نام مهمان
        $games = $this->db->fetchAll(
            "SELECT g.*,
                (SELECT COUNT(*) FROM game_participants gp WHERE gp.game_id = g.id) as total_players,
                (SELECT u.nickname FROM users u WHERE u.id = g.referee_id) as referee_name,
                (SELECT COALESCE(u.nickname, gp3.guest_name) 
                 FROM game_participants gp3
                 LEFT JOIN users u ON gp3.user_id = u.id
                 WHERE gp3.game_id = g.id AND gp3.is_winner = 1 
                 LIMIT 1) as winner_name,
                (SELECT gp3.user_id 
                 FROM game_participants gp3
                 WHERE gp3.game_id = g.id AND gp3.is_winner = 1 
                 LIMIT 1) as winner_user_id
         FROM games g
         WHERE {$whereClause}
         ORDER BY g.created_at DESC
         LIMIT ? OFFSET ?",
            array_merge($params, [$limit, $offset])
        );

        $total = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM games g WHERE {$whereClause}",
            $params
        )['count'];

        return ['games' => $games, 'total' => $total];
    }

    public function deleteGame(int $gameId): bool
    {
        return $this->db->delete('games', 'id = ?', [$gameId]);
    }

    // ============================================
    // لاگ‌ها
    // ============================================

    public function createLog(array $data): int
    {
        return $this->db->insert('admin_logs', [
            'admin_id' => $data['admin_id'],
            'action_type' => $data['action_type'],
            'target_type' => $data['target_type'] ?? null,
            'target_id' => $data['target_id'] ?? null,
            'description' => $data['description'] ?? null,
            'old_data' => isset($data['old_data']) ? json_encode($data['old_data'], JSON_UNESCAPED_UNICODE) : null,
            'new_data' => isset($data['new_data']) ? json_encode($data['new_data'], JSON_UNESCAPED_UNICODE) : null,
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
        ]);
    }

    public function getLogs(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['admin_id'])) {
            $where[] = "al.admin_id = ?";
            $params[] = (int) $filters['admin_id'];
        }

        if (!empty($filters['action_type'])) {
            $where[] = "al.action_type = ?";
            $params[] = $filters['action_type'];
        }

        if (!empty($filters['target_type'])) {
            $where[] = "al.target_type = ?";
            $params[] = $filters['target_type'];
        }

        if (!empty($filters['from_date'])) {
            $where[] = "al.created_at >= ?";
            $params[] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $where[] = "al.created_at <= ?";
            $params[] = $filters['to_date'] . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $logs = $this->db->fetchAll(
            "SELECT al.*, u.nickname as admin_name
             FROM admin_logs al
             LEFT JOIN users u ON al.admin_id = u.id
             WHERE {$whereClause}
             ORDER BY al.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$limit, $offset])
        );

        $total = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM admin_logs al WHERE {$whereClause}",
            $params
        )['count'];

        return ['logs' => $logs, 'total' => $total];
    }

    // ============================================
    // تنظیمات
    // ============================================

    public function getSettings(?string $category = null): array
    {
        if ($category) {
            return $this->db->fetchAll(
                "SELECT * FROM system_settings WHERE category = ? ORDER BY setting_key",
                [$category]
            );
        }

        return $this->db->fetchAll(
            "SELECT * FROM system_settings ORDER BY category, setting_key"
        );
    }

    public function getSetting(string $key): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM system_settings WHERE setting_key = ?",
            [$key]
        );
    }

    public function updateSetting(string $key, string $value, int $updatedBy): bool
    {
        return $this->db->update(
            'system_settings',
            [
                'setting_value' => $value,
                'updated_by' => $updatedBy,
            ],
            'setting_key = ?',
            [$key]
        );
    }

    // ============================================
    // نشان‌ها و ماموریت‌ها
    // ============================================

    public function getAchievements(): array
    {
        return $this->db->fetchAll(
            "SELECT a.*,
                    (SELECT COUNT(*) FROM user_achievements ua WHERE ua.achievement_id = a.id AND ua.is_completed = 1) as unlocked_count
             FROM achievements a
             ORDER BY a.category, a.condition_value"
        );
    }

    public function updateAchievement(int $id, array $data): bool
    {
        return $this->db->update('achievements', $data, 'id = ?', [$id]);
    }

    public function createAchievement(array $data): int
    {
        return $this->db->insert('achievements', $data);
    }

    public function deleteAchievement(int $id): bool
    {
        return $this->db->delete('achievements', 'id = ?', [$id]);
    }


// ============================================
// 🆕 مدیریت بازی‌ها (ویرایش)
// ============================================

    /**
     * گرفتن یک بازی با جزئیات
     */
    public function getGame(int $gameId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM games WHERE id = ?",
            [$gameId]
        );
    }

    /**
     * به‌روزرسانی وضعیت بازی
     */
    public function updateGameStatus(int $gameId, string $status): bool
    {
        $data = ['status' => $status];

        // تنظیم زمان‌ها بر اساس وضعیت
        if ($status === 'active') {
            $game = $this->getGame($gameId);
            if ($game && empty($game['started_at'])) {
                $data['started_at'] = date('Y-m-d H:i:s');
            }
        } elseif ($status === 'finished') {
            $data['finished_at'] = date('Y-m-d H:i:s');
        }

        return $this->db->update('games', $data, 'id = ?', [$gameId]);
    }

    /**
     * به‌روزرسانی داور بازی
     */
    public function updateGameReferee(int $gameId, int $newRefereeId): bool
    {
        return $this->db->update(
            'games',
            ['referee_id' => $newRefereeId],
            'id = ?',
            [$gameId]
        );
    }

    /**
     * به‌روزرسانی تعداد دورهای بازی
     */
    public function updateGameRounds(int $gameId, int $totalRounds): bool
    {
        return $this->db->update(
            'games',
            ['total_rounds_played' => $totalRounds],
            'id = ?',
            [$gameId]
        );
    }

    /**
     * به‌روزرسانی هدف برد بازی
     */
    public function updateGameTargetWins(int $gameId, int $targetWins): bool
    {
        return $this->db->update(
            'games',
            ['target_wins' => $targetWins],
            'id = ?',
            [$gameId]
        );
    }

    /**
     * افزودن بازیکن به بازی
     */
    public function addParticipant(int $gameId, array $data): int
    {
        $data['game_id'] = $gameId;
        return $this->db->insert('game_participants', $data);
    }

    /**
     * حذف بازیکن از بازی
     */
    public function removeParticipant(int $participantId): bool
    {
        return $this->db->delete('game_participants', 'id = ?', [$participantId]);
    }


    /**
     * عملیات گروهی روی بازی‌ها
     */
    public function bulkUpdateGames(array $gameIds, array $data): int
    {
        $affected = 0;
        foreach ($gameIds as $gameId) {
            if ($this->db->update('games', $data, 'id = ?', [$gameId])) {
                $affected++;
            }
        }
        return $affected;
    }

    /**
     * حذف گروهی بازی‌ها
     */
    public function bulkDeleteGames(array $gameIds): int
    {
        $affected = 0;
        foreach ($gameIds as $gameId) {
            if ($this->db->delete('games', 'id = ?', [$gameId])) {
                $affected++;
            }
        }
        return $affected;
    }
    /**
     * به‌روزرسانی امتیاز بازیکن
     */
    public function updateParticipantScore(int $participantId, int $score): bool
    {
        return $this->db->update(
            'game_participants',
            ['total_score' => $score],
            'id = ?',
            [$participantId]
        );
    }

    /**
     * به‌روزرسانی تعداد بردهای بازیکن
     */
    public function updateParticipantWins(int $participantId, int $wins): bool
    {
        return $this->db->update(
            'game_participants',
            ['wins_count' => $wins],
            'id = ?',
            [$participantId]
        );
    }

    /**
     * گرفتن لیست کاربران برای انتخاب داور
     */
    public function getUsersForSelect(): array
    {
        return $this->db->fetchAll(
            "SELECT id, nickname, real_name, role 
         FROM users 
         WHERE status = 'active' 
         ORDER BY nickname ASC"
        );
    }

    // ============================================
    // 🆕 کارت‌ها
    // ============================================

    public function getCards(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM cards ORDER BY score_multiplier, rarity, name"
        );
    }

    public function createCard(array $data): int
    {
        return $this->db->insert('cards', $data);
    }

    public function updateCard(int $id, array $data): bool
    {
        return $this->db->update('cards', $data, 'id = ?', [$id]);
    }

    public function deleteCard(int $id): bool
    {
        return $this->db->delete('cards', 'id = ?', [$id]);
    }

    // ============================================
    // 🆕 انواع برد
    // ============================================

    public function getWinTypes(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM win_types ORDER BY score_multiplier, name"
        );
    }

    public function createWinType(array $data): int
    {
        return $this->db->insert('win_types', $data);
    }

    public function updateWinType(int $id, array $data): bool
    {
        return $this->db->update('win_types', $data, 'id = ?', [$id]);
    }

    public function deleteWinType(int $id): bool
    {
        return $this->db->delete('win_types', 'id = ?', [$id]);
    }
    // ============================================
    // 🆕 عناوین
    // ============================================

    public function getTitles(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM titles ORDER BY bonus_points DESC, condition_value ASC"
        );
    }

    public function createTitle(array $data): int
    {
        return $this->db->insert('titles', $data);
    }

    public function updateTitle(int $id, array $data): bool
    {
        return $this->db->update('titles', $data, 'id = ?', [$id]);
    }

    public function deleteTitle(int $id): bool
    {
        return $this->db->delete('titles', 'id = ?', [$id]);
    }
    // ============================================
    // 🆕 مجوزهای بازی و ریست پسورد
    // ============================================

    public function updateUserGamePermissions(int $userId, array $permissions): bool
    {
        $data = [];
        if (array_key_exists('can_create_game', $permissions)) {
            $data['can_create_game'] = (int) $permissions['can_create_game'];
        }
        if (array_key_exists('can_join_game', $permissions)) {
            $data['can_join_game'] = (int) $permissions['can_join_game'];
        }
        if (empty($data)) return false;
        return $this->db->update('users', $data, 'id = ?', [$userId]);
    }

    public function resetUserPassword(int $userId, string $newPasswordHash): bool
    {
        return $this->db->update('users', [
            'password_hash' => $newPasswordHash,
        ], 'id = ?', [$userId]);
    }

    public function getUserGamePermissions(int $userId): ?array
    {
        return $this->db->fetchOne(
            "SELECT can_create_game, can_join_game FROM users WHERE id = ?",
            [$userId]
        );
    }

    // ============================================
// 🆕 مدیریت اعلان‌ها (Notifications)
// ============================================

    /**
     * گرفتن لیست اعلان‌ها با فیلتر
     */
    public function getNotifications(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = 'n.user_id = ?';
            $params[] = (int) $filters['user_id'];
        }

        if (!empty($filters['type'])) {
            $where[] = 'n.type = ?';
            $params[] = $filters['type'];
        }

        // 🆕 اصلاح: استفاده از isset به جای empty برای is_read
        if (isset($filters['is_read']) && $filters['is_read'] !== '') {
            $where[] = 'n.is_read = ?';
            $params[] = (int) $filters['is_read'];
        }

        if (!empty($filters['from_date'])) {
            $where[] = 'n.created_at >= ?';
            $params[] = $filters['from_date'] . ' 00:00:00';
        }

        if (!empty($filters['to_date'])) {
            $where[] = 'n.created_at <= ?';
            $params[] = $filters['to_date'] . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $notifications = $this->db->fetchAll(
            "SELECT n.*, u.nickname as user_nickname
         FROM notifications n
         LEFT JOIN users u ON n.user_id = u.id
         WHERE {$whereClause}
         ORDER BY n.created_at DESC
         LIMIT ? OFFSET ?",
            array_merge($params, [$limit, $offset])
        );

        $total = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM notifications n WHERE {$whereClause}",
            $params
        )['count'];

        return ['notifications' => $notifications, 'total' => $total];
    }
    /**
     * گرفتن آمار جداول لاگ
     */
    public function getLogsStats(): array
    {
        $adminLogs = $this->db->fetchOne("SELECT COUNT(*) as count, MIN(created_at) as oldest FROM admin_logs");
        $notifications = $this->db->fetchOne("SELECT COUNT(*) as count, MIN(created_at) as oldest FROM notifications");
        $refereeActions = $this->db->fetchOne("SELECT COUNT(*) as count, MIN(created_at) as oldest FROM referee_actions_log");
        $sseEvents = $this->db->fetchOne("SELECT COUNT(*) as count, MIN(created_at) as oldest FROM sse_events");

        return [
            'admin_logs' => [
                'count' => (int) ($adminLogs['count'] ?? 0),
                'oldest' => $adminLogs['oldest'] ?? null,
            ],
            'notifications' => [
                'count' => (int) ($notifications['count'] ?? 0),
                'oldest' => $notifications['oldest'] ?? null,
            ],
            'referee_actions_log' => [
                'count' => (int) ($refereeActions['count'] ?? 0),
                'oldest' => $refereeActions['oldest'] ?? null,
            ],
            'sse_events' => [
                'count' => (int) ($sseEvents['count'] ?? 0),
                'oldest' => $sseEvents['oldest'] ?? null,
            ],
        ];
    }



    // ============================================
// 🆕 مدیریت اعلان‌ها (Notifications)
// ============================================

    /**
     * حذف اعلان‌های قدیمی
     * 🆕 اصلاح: استفاده از strtotime به جای INTERVAL (چون INTERVAL با PDO bind کار نمی‌کند)
     */
    public function deleteOldNotifications(int $daysOld = 30): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));

        // 🆕 ابتدا تعداد رکوردهایی که قرار است حذف شوند را بشمار
        $countResult = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM notifications WHERE created_at < ?",
            [$cutoffDate]
        );
        $count = (int) ($countResult['count'] ?? 0);

        // 🆕 سپس حذف کن (فقط اگر رکوردی وجود داشته باشد)
        if ($count > 0) {
            $this->db->query(
                "DELETE FROM notifications WHERE created_at < ?",
                [$cutoffDate]
            );
        }

        return $count;
    }


    /**
     * حذف همه اعلان‌ها
     */
    public function deleteAllNotifications(): int
    {
        $countResult = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM notifications"
        );
        $count = (int) ($countResult['count'] ?? 0);

        if ($count > 0) {
            $this->db->query("DELETE FROM notifications");
        }

        return $count;
    }

    /**
     * علامت‌گذاری همه اعلان‌ها به عنوان خوانده شده
     */
    public function markAllNotificationsAsRead(): int
    {
        $countResult = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0"
        );
        $count = (int) ($countResult['count'] ?? 0);

        if ($count > 0) {
            $this->db->query(
                "UPDATE notifications SET is_read = 1 WHERE is_read = 0"
            );
        }

        return $count;
    }

// ============================================
// 🆕 پاک‌سازی جداول لاگ
// ============================================

    /**
     * حذف لاگ‌های ادمین قدیمی
     */
    public function deleteOldAdminLogs(int $daysOld = 30): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));

        $countResult = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM admin_logs WHERE created_at < ?",
            [$cutoffDate]
        );
        $count = (int) ($countResult['count'] ?? 0);

        if ($count > 0) {
            $this->db->query(
                "DELETE FROM admin_logs WHERE created_at < ?",
                [$cutoffDate]
            );
        }

        return $count;
    }

    /**
     * حذف همه لاگ‌های ادمین
     */
    public function deleteAllAdminLogs(): int
    {
        $countResult = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM admin_logs"
        );
        $count = (int) ($countResult['count'] ?? 0);

        if ($count > 0) {
            $this->db->query("DELETE FROM admin_logs");
        }

        return $count;
    }

    /**
     * حذف referee_actions_log قدیمی
     */
    public function deleteOldRefereeActionsLog(int $daysOld = 30): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));

        $countResult = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM referee_actions_log WHERE created_at < ?",
            [$cutoffDate]
        );
        $count = (int) ($countResult['count'] ?? 0);

        if ($count > 0) {
            $this->db->query(
                "DELETE FROM referee_actions_log WHERE created_at < ?",
                [$cutoffDate]
            );
        }

        return $count;
    }

    /**
     * حذف همه referee_actions_log
     */
    public function deleteAllRefereeActionsLog(): int
    {
        $countResult = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM referee_actions_log"
        );
        $count = (int) ($countResult['count'] ?? 0);

        if ($count > 0) {
            $this->db->query("DELETE FROM referee_actions_log");
        }

        return $count;
    }

    /**
     * حذف sse_events قدیمی
     */
    public function deleteOldSseEvents(int $hoursOld = 24): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$hoursOld} hours"));

        $countResult = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM sse_events WHERE created_at < ?",
            [$cutoffDate]
        );
        $count = (int) ($countResult['count'] ?? 0);

        if ($count > 0) {
            $this->db->query(
                "DELETE FROM sse_events WHERE created_at < ?",
                [$cutoffDate]
            );
        }

        return $count;
    }

    /**
     * حذف همه sse_events
     */
    public function deleteAllSseEvents(): int
    {
        $countResult = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM sse_events"
        );
        $count = (int) ($countResult['count'] ?? 0);

        if ($count > 0) {
            $this->db->query("DELETE FROM sse_events");
        }

        return $count;
    }
}
