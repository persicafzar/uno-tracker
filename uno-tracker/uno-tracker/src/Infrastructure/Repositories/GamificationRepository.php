<?php

namespace Infrastructure\Repositories;

use Core\Database;
use Domain\Achievement;
use Domain\Title;
use Domain\PlayerLevel;
use Domain\UserXP;
use Domain\Notification;
use Domain\UserStreak;

class GamificationRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ============================================
    // Achievements
    // ============================================

    /**
     * گرفتن همه نشان‌های فعال
     */
    public function findAllAchievements(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM achievements WHERE is_active = 1 ORDER BY category, condition_value ASC"
        );
        return array_map(fn($row) => new Achievement($row), $rows);
    }

    /**
     * گرفتن نشان بر اساس کد
     */
    public function findAchievementByCode(string $code): ?Achievement
    {
        $row = $this->db->fetchOne(
            "SELECT * FROM achievements WHERE code = ?",
            [$code]
        );
        return $row ? new Achievement($row) : null;
    }

    /**
     * گرفتن نشان‌های یک کاربر با وضعیت پیشرفت
     */
    public function getUserAchievements(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT a.*, 
                    ua.progress as user_progress, 
                    ua.is_completed as user_completed, 
                    ua.unlocked_at as user_unlocked_at
             FROM achievements a
             LEFT JOIN user_achievements ua ON a.id = ua.achievement_id AND ua.user_id = ?
             WHERE a.is_active = 1
             ORDER BY a.category, a.condition_value ASC",
            [$userId]
        );
        return array_map(fn($row) => new Achievement($row), $rows);
    }

    /**
     * گرفتن نشان‌های تکمیل شده کاربر
     */
    public function getUserCompletedAchievements(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT a.*, ua.progress as user_progress, ua.is_completed as user_completed, ua.unlocked_at as user_unlocked_at
             FROM achievements a
             INNER JOIN user_achievements ua ON a.id = ua.achievement_id
             WHERE ua.user_id = ? AND ua.is_completed = 1
             ORDER BY ua.unlocked_at DESC",
            [$userId]
        );
        return array_map(fn($row) => new Achievement($row), $rows);
    }

    /**
     * به‌روزرسانی پیشرفت نشان
     */
    public function updateUserAchievementProgress(int $userId, int $achievementId, int $progress): void
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM user_achievements WHERE user_id = ? AND achievement_id = ?",
            [$userId, $achievementId]
        );

        if ($existing) {
            $this->db->update('user_achievements', [
                'progress' => $progress,
            ], 'user_id = ? AND achievement_id = ?', [$userId, $achievementId]);
        } else {
            $this->db->insert('user_achievements', [
                'user_id' => $userId,
                'achievement_id' => $achievementId,
                'progress' => $progress,
                'is_completed' => 0,
            ]);
        }
    }

    /**
     * تکمیل نشان
     */
    public function completeUserAchievement(int $userId, int $achievementId, int $progress): void
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM user_achievements WHERE user_id = ? AND achievement_id = ?",
            [$userId, $achievementId]
        );

        if ($existing) {
            $this->db->update('user_achievements', [
                'progress' => $progress,
                'is_completed' => 1,
                'unlocked_at' => date('Y-m-d H:i:s'),
            ], 'user_id = ? AND achievement_id = ?', [$userId, $achievementId]);
        } else {
            $this->db->insert('user_achievements', [
                'user_id' => $userId,
                'achievement_id' => $achievementId,
                'progress' => $progress,
                'is_completed' => 1,
                'unlocked_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * بررسی اینکه آیا نشان قبلاً تکمیل شده
     */
    public function isAchievementCompleted(int $userId, int $achievementId): bool
    {
        $row = $this->db->fetchOne(
            "SELECT is_completed FROM user_achievements WHERE user_id = ? AND achievement_id = ?",
            [$userId, $achievementId]
        );
        return $row && (bool)$row['is_completed'];
    }

    
    // ============================================
    // Titles
    // ============================================

    /**
     * گرفتن همه القاب
     */
    public function findAllTitles(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM titles WHERE is_active = 1 ORDER BY priority DESC"
        );
        return array_map(fn($row) => new Title($row), $rows);
    }

    /**
     * گرفتن القاب یک کاربر
     */
    public function getUserTitles(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT t.*, 
                    1 as user_unlocked, 
                    ut.is_active as user_active, 
                    ut.unlocked_at as user_unlocked_at
             FROM titles t
             INNER JOIN user_titles ut ON t.id = ut.title_id
             WHERE ut.user_id = ? AND t.is_active = 1
             ORDER BY t.priority DESC",
            [$userId]
        );
        return array_map(fn($row) => new Title($row), $rows);
    }

    /**
     * گرفتن لقب فعال کاربر
     */
    public function getUserActiveTitle(int $userId): ?Title
    {
        $row = $this->db->fetchOne(
            "SELECT t.*, 1 as user_unlocked, ut.is_active as user_active, ut.unlocked_at as user_unlocked_at
             FROM titles t
             INNER JOIN user_titles ut ON t.id = ut.title_id
             WHERE ut.user_id = ? AND ut.is_active = 1
             LIMIT 1",
            [$userId]
        );
        return $row ? new Title($row) : null;
    }
    /**
     * 🆕 گرفتن لقب فعال کاربر به صورت آرایه (برای دسترسی به تمام فیلدها)
     */
    public function getUserActiveTitleArray(int $userId): ?array
    {
        return $this->db->fetchOne(
            "SELECT t.id, t.code, t.name, t.description, t.icon, 
                t.condition_type, t.condition_value, t.bonus_points, t.priority, t.is_active,
                ut.is_active as user_active, ut.unlocked_at as user_unlocked_at
         FROM titles t
         INNER JOIN user_titles ut ON t.id = ut.title_id
         WHERE ut.user_id = ? AND ut.is_active = 1
         LIMIT 1",
            [$userId]
        );
    }
    /**
     * اعطای لقب به کاربر
     */
    public function grantTitle(int $userId, int $titleId): bool
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM user_titles WHERE user_id = ? AND title_id = ?",
            [$userId, $titleId]
        );

        if ($existing) {
            return false; // قبلاً دارد
        }

        $this->db->insert('user_titles', [
            'user_id' => $userId,
            'title_id' => $titleId,
            'is_active' => 0,
        ]);

        return true;
    }

    /**
     * تنظیم لقب فعال
     */
    public function setActiveTitle(int $userId, int $titleId): void
    {
        // غیرفعال کردن همه القاب
        $this->db->query(
            "UPDATE user_titles SET is_active = 0 WHERE user_id = ?",
            [$userId]
        );

        // فعال کردن لقب انتخاب شده
        $this->db->update('user_titles', [
            'is_active' => 1,
        ], 'user_id = ? AND title_id = ?', [$userId, $titleId]);
    }

    // ============================================
    // Levels & XP
    // ============================================

    /**
     * گرفتن همه سطوح
     */
    public function findAllLevels(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM player_levels ORDER BY level ASC"
        );
        return array_map(fn($row) => new PlayerLevel($row), $rows);
    }

    /**
     * گرفتن سطح بر اساس XP
     */
    public function getLevelByXp(int $xp): ?PlayerLevel
    {
        $row = $this->db->fetchOne(
            "SELECT * FROM player_levels WHERE min_xp <= ? AND max_xp >= ?",
            [$xp, $xp]
        );
        return $row ? new PlayerLevel($row) : null;
    }

    /**
     * گرفتن XP کاربر
     */
    public function getUserXp(int $userId): ?UserXP
    {
        $row = $this->db->fetchOne(
            "SELECT * FROM user_xp WHERE user_id = ?",
            [$userId]
        );
        return $row ? new UserXP($row) : null;
    }

    /**
     * ایجاد یا به‌روزرسانی XP کاربر
     */
    public function upsertUserXp(int $userId, int $totalXp, int $currentLevel, int $xpToNextLevel): void
    {
        $existing = $this->db->fetchOne(
            "SELECT user_id FROM user_xp WHERE user_id = ?",
            [$userId]
        );

        if ($existing) {
            $this->db->update('user_xp', [
                'total_xp' => $totalXp,
                'current_level' => $currentLevel,
                'xp_to_next_level' => $xpToNextLevel,
            ], 'user_id = ?', [$userId]);
        } else {
            $this->db->insert('user_xp', [
                'user_id' => $userId,
                'total_xp' => $totalXp,
                'current_level' => $currentLevel,
                'xp_to_next_level' => $xpToNextLevel,
            ]);
        }
    }

    /**
     * اضافه کردن XP
     */
    public function addXp(int $userId, int $amount): int
    {
        $xp = $this->getUserXp($userId);
        $currentXp = $xp ? $xp->total_xp : 0;
        return $currentXp + $amount;
    }



    // ============================================
    // Notifications
    // ============================================

    /**
     * ایجاد اعلان
     */
    public function createNotification(int $userId, string $type, string $title, ?string $message = null, ?string $icon = null, ?string $link = null): int
    {
        return $this->db->insert('notifications', [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon ?? Notification::getDefaultIcon($type),
            'link' => $link,
            'is_read' => 0,
        ]);
    }

    /**
     * گرفتن اعلان‌های خوانده نشده
     */
    public function getUnreadNotifications(int $userId, int $limit = 10): array
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
        return array_map(fn($row) => new Notification($row), $rows);
    }

    /**
     * گرفتن همه اعلان‌ها
     */
    public function getUserNotifications(int $userId, int $limit = 50): array
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
        return array_map(fn($row) => new Notification($row), $rows);
    }

    /**
     * علامت‌گذاری به عنوان خوانده شده
     */
    public function markNotificationAsRead(int $notificationId, int $userId): void
    {
        $this->db->update('notifications', [
            'is_read' => 1,
        ], 'id = ? AND user_id = ?', [$notificationId, $userId]);
    }

    /**
     * علامت‌گذاری همه به عنوان خوانده شده
     */
    public function markAllNotificationsAsRead(int $userId): void
    {
        $this->db->update('notifications', [
            'is_read' => 1,
        ], 'user_id = ? AND is_read = 0', [$userId]);
    }

    /**
     * تعداد اعلان‌های خوانده نشده
     */
    public function getUnreadCount(int $userId): int
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
        return (int)($row['count'] ?? 0);
    }

    // ============================================
    // Streaks
    // ============================================

    /**
     * گرفتن استریک کاربر
     */
    public function getUserStreak(int $userId): ?UserStreak
    {
        $row = $this->db->fetchOne(
            "SELECT * FROM user_streaks WHERE user_id = ?",
            [$userId]
        );
        return $row ? new UserStreak($row) : null;
    }

    /**
     * ایجاد یا به‌روزرسانی استریک
     */
    public function upsertUserStreak(int $userId, int $currentStreak, int $bestStreak, ?string $lastWinAt, ?string $streakBrokenAt = null): void
    {
        $existing = $this->db->fetchOne(
            "SELECT user_id FROM user_streaks WHERE user_id = ?",
            [$userId]
        );

        if ($existing) {
            $data = [
                'current_streak' => $currentStreak,
                'best_streak' => $bestStreak,
                'last_win_at' => $lastWinAt,
            ];
            if ($streakBrokenAt !== null) {
                $data['streak_broken_at'] = $streakBrokenAt;
            }
            $this->db->update('user_streaks', $data, 'user_id = ?', [$userId]);
        } else {
            $this->db->insert('user_streaks', [
                'user_id' => $userId,
                'current_streak' => $currentStreak,
                'best_streak' => $bestStreak,
                'last_win_at' => $lastWinAt,
                'streak_broken_at' => $streakBrokenAt,
            ]);
        }
    }

    // ============================================
    // آمار کلی کاربر (برای بررسی شرایط)
    // ============================================

    /**
     * گرفتن آمار کلی کاربر
     */
    public function getUserStats(int $userId): array
    {
        // کل بازی‌ها
        $totalGames = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT g.id) as count 
             FROM games g 
             JOIN game_participants gp ON g.id = gp.game_id 
             WHERE gp.user_id = ? AND g.status = 'finished'",
            [$userId]
        );

        // کل بردها
        $totalWins = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT g.id) as count 
             FROM games g 
             JOIN game_participants gp ON g.id = gp.game_id 
             WHERE gp.user_id = ? 
             AND g.status = 'finished'
             AND g.winner_participant_id = gp.id",
            [$userId]
        );

        // امتیاز کل
        $totalPoints = $this->db->fetchOne(
            "SELECT COALESCE(SUM(gp.total_score), 0) as total 
             FROM game_participants gp 
             JOIN games g ON gp.game_id = g.id 
             WHERE gp.user_id = ? AND g.status = 'finished'",
            [$userId]
        );

        // بازی‌های تیمی
        $teamGames = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT g.id) as count 
             FROM games g 
             JOIN game_participants gp ON g.id = gp.game_id 
             WHERE gp.user_id = ? 
             AND g.game_mode = 'friendly'
             AND g.status = 'finished'",
            [$userId]
        );

        // بردهای تیمی
        $teamWins = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT g.id) as count 
             FROM games g 
             JOIN game_participants gp ON g.id = gp.game_id 
             WHERE gp.user_id = ? 
             AND g.game_mode = 'friendly'
             AND g.status = 'finished'
             AND g.winner_participant_id = gp.id",
            [$userId]
        );

        return [
            'total_games' => (int)($totalGames['count'] ?? 0),
            'total_wins' => (int)($totalWins['count'] ?? 0),
            'total_points' => (int)($totalPoints['total'] ?? 0),
            'team_games' => (int)($teamGames['count'] ?? 0),
            'team_wins' => (int)($teamWins['count'] ?? 0),
        ];
    }
        // ============================================
    // 🆕 مدیریت سطوح (Player Levels)
    // ============================================

    /**
     * گرفتن تمام سطوح
     */
    public function getLevels(): array
    {
        return $this->db->fetchAll(
            "SELECT pl.*, 
                    (SELECT COUNT(*) FROM user_xp WHERE current_level = pl.level) as users_count
             FROM player_levels pl
             ORDER BY pl.level ASC"
        );
    }

    /**
     * گرفتن یک سطح با id
     */
    public function getLevelById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM player_levels WHERE id = ?",
            [$id]
        );
    }
    /**
     * گرفتن سطح بر اساس شماره سطح
     */
    public function getLevelByNumber(int $level): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM player_levels WHERE level = ?",
            [$level]
        );
    }
    /**
     * ایجاد سطح جدید
     */
    public function createLevel(array $data): int|false
    {
        return $this->db->insert('player_levels', [
            'level' => (int) $data['level'],
            'min_xp' => (int) $data['min_xp'],
            'max_xp' => (int) $data['max_xp'],
            'title' => $data['title'] ?? null,
            'color' => $data['color'] ?? '#6366f1',
            'icon' => $data['icon'] ?? '⭐',
        ]);
    }

    /**
     * به‌روزرسانی سطح
     */
    public function updateLevel(int $id, array $data): bool
    {
        return $this->db->update(
            'player_levels',
            [
                'min_xp' => (int) $data['min_xp'],
                'max_xp' => (int) $data['max_xp'],
                'title' => $data['title'] ?? null,
                'color' => $data['color'] ?? '#6366f1',
                'icon' => $data['icon'] ?? '⭐',
            ],
            'id = ?',
            [$id]
        );
    }

    /**
     * حذف سطح
     */
    public function deleteLevel(int $id): bool
    {
        // بررسی اینکه کاربری در این سطح نباشد
        $level = $this->getLevelById($id);
        if (!$level) return false;

        $usersInLevel = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM user_xp WHERE current_level = ?",
            [$level['level']]
        );

        if ($usersInLevel && $usersInLevel['count'] > 0) {
            throw new \Exception("نمی‌توان این سطح را حذف کرد زیرا {$usersInLevel['count']} کاربر در این سطح قرار دارند.");
        }

        return $this->db->delete('player_levels', 'id = ?', [$id]);
    }

    /**
     * بررسی تداخل محدوده XP
     */
    public function checkXpRangeOverlap(int $minXp, int $maxXp, ?int $excludeId = null): ?array
    {
        $excludeCondition = $excludeId ? " AND id != {$excludeId}" : "";

        return $this->db->fetchOne(
            "SELECT level, min_xp, max_xp, title 
             FROM player_levels 
             WHERE (min_xp <= ? AND max_xp >= ?) 
                OR (min_xp <= ? AND max_xp >= ?)
                OR (min_xp >= ? AND max_xp <= ?)
                {$excludeCondition}",
            [$maxXp, $minXp, $minXp, $maxXp, $minXp, $maxXp]
        );
    }

        // ============================================
    // 🆕 مدیریت نشان‌ها (Achievements CRUD)
    // ============================================

    /**
     * گرفتن همه نشان‌ها برای پنل ادمین (با آمار کسب شده)
     */
    public function findAllAchievementsForAdmin(): array
    {
        return $this->db->fetchAll(
            "SELECT a.*,
                (SELECT COUNT(*) FROM user_achievements ua 
                 WHERE ua.achievement_id = a.id AND ua.is_completed = 1) as unlocked_count
             FROM achievements a
             ORDER BY a.category, a.condition_value ASC"
        );
    }

    /**
     * پیدا کردن نشان با ID
     */
    public function findAchievementById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM achievements WHERE id = ?",
            [$id]
        );
    }

    /**
     * ایجاد رکورد نشان جدید
     */
    public function createAchievementRecord(array $data): int|false
    {
        return $this->db->insert('achievements', [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? '🏅',
            'category' => $data['category'] ?? 'general',
            'rarity' => $data['rarity'] ?? 'common',
            'xp_reward' => (int) ($data['xp_reward'] ?? 10),
            'condition_type' => $data['condition_type'],
            'condition_value' => (int) $data['condition_value'],
            'is_active' => (int) ($data['is_active'] ?? 1),
        ]);
    }

    /**
     * به‌روزرسانی رکورد نشان
     */
    public function updateAchievementRecord(int $id, array $data): bool
    {
        return $this->db->update('achievements', [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? '🏅',
            'category' => $data['category'] ?? 'general',
            'rarity' => $data['rarity'] ?? 'common',
            'xp_reward' => (int) ($data['xp_reward'] ?? 10),
            'condition_type' => $data['condition_type'],
            'condition_value' => (int) $data['condition_value'],
            'is_active' => (int) ($data['is_active'] ?? 1),
        ], 'id = ?', [$id]);
    }

    /**
     * حذف رکورد نشان
     */
    public function deleteAchievementRecord(int $id): bool
    {
        // حذف user_achievements مرتبط
        $this->db->delete('user_achievements', 'achievement_id = ?', [$id]);
        return $this->db->delete('achievements', 'id = ?', [$id]);
    }

    /**
     * تغییر وضعیت فعال/غیرفعال نشان
     */
    public function toggleAchievementActiveRecord(int $id, int $isActive): bool
    {
        return $this->db->update('achievements', [
            'is_active' => $isActive,
        ], 'id = ?', [$id]);
    }
}
