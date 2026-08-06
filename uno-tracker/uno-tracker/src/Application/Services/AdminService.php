<?php

namespace Application\Services;

use Core\Database;
use Infrastructure\Repositories\AdminRepository;

class AdminService
{
    private AdminRepository $repo;
    private Database $db;

    public function __construct()
    {
        $this->repo = new AdminRepository();
        $this->db = Database::getInstance();
    }

    // ============================================
    // Dashboard
    // ============================================

    public function getDashboardData(): array
    {
        return [
            'stats' => $this->repo->getDashboardStats(),
            'userRegistrationStats' => $this->repo->getUserRegistrationStats(30),
            'gamesStats' => $this->repo->getGamesStats(30),
            'gameModeDistribution' => $this->repo->getGameModeDistribution(),
        ];
    }

    // ============================================
    // Users
    // ============================================

    public function getUsers(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $result = $this->repo->getUsers($filters, $perPage, $offset);

        return [
            'users' => $result['users'],
            'total' => $result['total'],
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($result['total'] / $perPage),
        ];
    }

    public function banUser(int $userId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $user = $this->repo->getUser($userId);
        if (!$user || $user['status'] === 'banned') return false;
        // 🆕 محافظت از super_admin
        if ($user['role'] === 'super_admin') {
            log_message("⚠️ Attempt to ban super_admin blocked for user #{$userId}");
            return false;
        }
        $result = $this->repo->updateUserStatus($userId, 'banned');

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'user_ban',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "مسدود کردن کاربر: {$user['nickname']}",
                'new_data' => ['status' => 'banned'],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $result;
    }

    public function unbanUser(int $userId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $user = $this->repo->getUser($userId);
        if (!$user || $user['status'] !== 'banned') return false;

        $result = $this->repo->updateUserStatus($userId, 'active');

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'user_unban',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "فعال‌سازی کاربر: {$user['nickname']}",
                'new_data' => ['status' => 'active'],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $result;
    }

    public function changeUserRole(int $userId, string $newRole, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $user = $this->repo->getUser($userId);
        if (!$user) return false;

        // 🆕 محافظت از super_admin
        if ($user['role'] === 'super_admin') {
            log_message("⚠️ Attempt to change super_admin role blocked for user #{$userId}");
            return false;
        }

        $oldRole = $user['role'];
        if ($oldRole === $newRole) return false;

        $result = $this->repo->updateUserRole($userId, $newRole);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'user_role_change',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "تغییر نقش کاربر {$user['nickname']} از {$oldRole} به {$newRole}",
                'old_data' => ['role' => $oldRole],
                'new_data' => ['role' => $newRole],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $result;
    }

    public function deleteUser(int $userId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $user = $this->repo->getUser($userId);
        if (!$user) return false;
        // 🆕 محافظت از super_admin
        if ($user['role'] === 'super_admin') {
            log_message("⚠️ Attempt to delete super_admin blocked for user #{$userId}");
            return false;
        }
        // 🆕 مرحله ۱: شناسایی تمام بازیکنانی که با کاربر حذف‌شده بازی کرده‌اند
        $affectedUsers = $this->db->fetchAll(
            "SELECT DISTINCT gp2.user_id 
         FROM game_participants gp1
         INNER JOIN game_participants gp2 ON gp1.game_id = gp2.game_id
         WHERE gp1.user_id = ? 
           AND gp2.user_id != ?
           AND gp2.user_id IS NOT NULL",
            [$userId, $userId]
        );

        // 🆕 مرحله ۲: حذف کاربر
        $result = $this->repo->deleteUser($userId);

        if ($result) {
            // 🆕 مرحله ۳: باز محاسبه آمار تمام بازیکنان تحت تأثیر
            $recalcService = new \Application\Services\RecalculateUserService();
            foreach ($affectedUsers as $affectedUser) {
                if ($affectedUser['user_id']) {
                    try {
                        $recalcService->recalculateAll((int)$affectedUser['user_id']);
                    } catch (\Throwable $e) {
                        log_message("Error recalculating user {$affectedUser['user_id']} after delete: " . $e->getMessage());
                    }
                }
            }

            // 🆕 مرحله ۴: ثبت لاگ
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'user_delete',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "حذف کاربر: {$user['nickname']} (باز محاسبه آمار " . count($affectedUsers) . " بازیکن تحت تأثیر)",
                'old_data' => $user,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $result;
    }

    // ============================================
    // Games
    // ============================================

    public function getGames(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $result = $this->repo->getGames($filters, $perPage, $offset);

        return [
            'games' => $result['games'],
            'total' => $result['total'],
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($result['total'] / $perPage),
        ];
    }


    public function deleteGame(int $gameId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $game = $this->db->fetchOne("SELECT * FROM games WHERE id = ?", [$gameId]);
        if (!$game) return false;

        // ۱. شناسایی تمام کاربران واقعی شرکت‌کننده در این بازی قبل از حذف
        $participants = $this->db->fetchAll(
            "SELECT DISTINCT user_id FROM game_participants WHERE game_id = ? AND user_id IS NOT NULL",
            [$gameId]
        );

        // 🆕 ۲. حذف رکوردهای مشکوک مرتبط با این بازی (قبل از حذف بازی)
        $this->db->delete('suspicious_games', 'game_id = ?', [$gameId]);

        // ۳. حذف بازی (Cascade به طور خودکار game_participants و game_rounds را حذف می‌کند)
        $result = $this->repo->deleteGame($gameId);

        if ($result) {
            // ۴. باز محاسبه کامل آمار، XP، مدال‌ها و القاب برای تمام شرکت‌کنندگان
            $recalcService = new \Application\Services\RecalculateUserService();
            foreach ($participants as $p) {
                if ($p['user_id']) {
                    $recalcService->recalculateAll((int)$p['user_id']);
                }
            }

            // ۵. ثبت در لاگ ادمین
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'game_delete',
                'target_type' => 'game',
                'target_id' => $gameId,
                'description' => "حذف بازی تقلبی/مشکوک و باز محاسبه خودکار آمار، XP، مدال و القاب تمام شرکت‌کنندگان - بازی: " . ($game['name'] ?: "بازی #{$gameId}"),
                'old_data' => $game,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $result;
    }

    // ============================================
    // Logs
    // ============================================

    public function getLogs(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;
        $result = $this->repo->getLogs($filters, $perPage, $offset);

        return [
            'logs' => $result['logs'],
            'total' => $result['total'],
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($result['total'] / $perPage),
        ];
    }

    // ============================================
    // Settings
    // ============================================

    public function getSettings(?string $category = null): array
    {
        $settings = $this->repo->getSettings($category);

        // گروه‌بندی بر اساس category
        $grouped = [];
        foreach ($settings as $setting) {
            $grouped[$setting['category']][] = $setting;
        }

        return $grouped;
    }

    public function updateSetting(string $key, string $value, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $setting = $this->repo->getSetting($key);
        if (!$setting) return false;

        $oldValue = $setting['setting_value'];
        if ($oldValue === $value) return true;

        $result = $this->repo->updateSetting($key, $value, $adminId);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'setting_change',
                'target_type' => 'setting',
                'target_id' => $setting['id'],
                'description' => "تغییر تنظیم {$key}",
                'old_data' => ['value' => $oldValue],
                'new_data' => ['value' => $value],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $result;
    }

    // ============================================
    // Achievements
    // ============================================

    public function getAchievements(): array
    {
        return $this->repo->getAchievements();
    }

    public function updateAchievement(int $id, array $data): bool
    {
        return $this->repo->updateAchievement($id, $data);
    }


    /**
     * گرفتن کاربر با آمار کامل
     */
    public function getUserWithStats(int $userId): ?array
    {
        $user = $this->repo->getUser($userId);
        if (!$user) return null;

        // 🆕 استفاده از UserStatsService
        $statsService = new UserStatsService();
        $stats = $statsService->getDetailedStats($userId);

        // آمار گیمیفیکیشن
        $gamificationStats = $this->db->fetchOne(
            "SELECT 
            (SELECT COUNT(*) FROM user_achievements WHERE user_id = ? AND is_completed = 1) as achievements_count,
            (SELECT total_xp FROM user_xp WHERE user_id = ?) as total_xp,
            (SELECT current_level FROM user_xp WHERE user_id = ?) as current_level,
            (SELECT best_streak FROM user_streaks WHERE user_id = ?) as best_streak",
            [$userId, $userId, $userId, $userId]
        );

        // آخرین بازی‌ها
        $recentGames = $this->db->fetchAll(
            "SELECT g.id, g.name, g.game_mode, g.status, g.created_at,
            gp.wins_count, gp.total_score, gp.is_winner
         FROM games g
         INNER JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ?
         ORDER BY g.created_at DESC
         LIMIT 10",
            [$userId]
        );

        return array_merge($user, [
            'games_stats' => $stats,
            'gamification_stats' => $gamificationStats,
            'recent_games' => $recentGames,
        ]);
    }


    /**
     * گرفتن بازی با جزئیات کامل
     */

    public function getGameWithDetails(int $gameId): ?array
    {
        $game = $this->db->fetchOne(
            "SELECT g.*,
                (SELECT u.nickname FROM users u WHERE u.id = g.referee_id) as referee_name,
                (SELECT COALESCE(u.nickname, gp2.guest_name)
                 FROM game_participants gp2
                 LEFT JOIN users u ON gp2.user_id = u.id
                 WHERE gp2.game_id = g.id AND gp2.is_winner = 1
                 LIMIT 1) as winner_name,
                (SELECT gp2.user_id 
                 FROM game_participants gp2
                 WHERE gp2.game_id = g.id AND gp2.is_winner = 1
                 LIMIT 1) as winner_user_id,
                (SELECT COALESCE(u.nickname, gp3.guest_name)
                 FROM game_participants gp3
                 LEFT JOIN users u ON gp3.user_id = u.id
                 WHERE gp3.id = g.first_player_participant_id
                 LIMIT 1) as first_player_name,
                (SELECT gp3.user_id
                 FROM game_participants gp3
                 WHERE gp3.id = g.first_player_participant_id
                 LIMIT 1) as first_player_user_id
         FROM games g
         WHERE g.id = ?",
            [$gameId]
        );

        if (!$game) return null;

        // شرکت‌کنندگان
        $participants = $this->db->fetchAll(
            "SELECT gp.id as participant_id, gp.game_id, gp.user_id, gp.team_id, 
                gp.guest_name, gp.wins_count, gp.total_score, gp.is_winner, gp.joined_at,
                u.nickname, u.avatar_path, u.real_name,
                t.name as team_name
         FROM game_participants gp
         LEFT JOIN users u ON gp.user_id = u.id
         LEFT JOIN teams t ON gp.team_id = t.id
         WHERE gp.game_id = ?
         ORDER BY gp.is_winner DESC, gp.total_score DESC",
            [$gameId]
        );

        // تیم‌ها
        $teams = $this->db->fetchAll(
            "SELECT t.id, t.name, t.game_id,
                (SELECT COUNT(*) FROM game_participants gp WHERE gp.team_id = t.id) as members_count,
                (SELECT SUM(gp.total_score) FROM game_participants gp WHERE gp.team_id = t.id) as team_score
         FROM teams t
         WHERE t.game_id = ?
         ORDER BY t.id",
            [$gameId]
        );

        // دورها
        $rounds = $this->db->fetchAll(
            "SELECT gr.id, gr.game_id, gr.round_number, gr.winner_participant_id, 
                gr.winner_team_name, gr.winning_card_id, gr.win_type_id, 
                gr.calculated_score, gr.created_at,
                COALESCE(u.nickname, gp2.guest_name) as winner_name,
                c.name as card_name,
                wt.name as win_type_name
         FROM game_rounds gr
         LEFT JOIN game_participants gp2 ON gr.winner_participant_id = gp2.id
         LEFT JOIN users u ON gp2.user_id = u.id
         LEFT JOIN cards c ON gr.winning_card_id = c.id
         LEFT JOIN win_types wt ON gr.win_type_id = wt.id
         WHERE gr.game_id = ?
         ORDER BY gr.round_number ASC",
            [$gameId]
        );

        return array_merge($game, [
            'participants' => $participants,
            'teams' => $teams,
            'rounds' => $rounds,
        ]);
    }

    /**
     * گرفتن لیست کاربران برای انتخاب
     */
    public function getUsersForSelect(): array
    {
        return $this->repo->getUsersForSelect();
    }

// ============================================
// 🆕 مدیریت بازی‌ها (ویرایش)
// ============================================

    /**
     * تغییر وضعیت بازی
     */
    public function updateGameStatus(int $gameId, string $status, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $game = $this->repo->getGame($gameId);
        if (!$game) return false;

        $oldStatus = $game['status'];
        if ($oldStatus === $status) return true;

        $result = $this->repo->updateGameStatus($gameId, $status);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'game_status_change',
                'target_type' => 'game',
                'target_id' => $gameId,
                'description' => "تغییر وضعیت بازی #{$gameId} از {$oldStatus} به {$status}",
                'old_data' => ['status' => $oldStatus],
                'new_data' => ['status' => $status],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $result;
    }

    /**
     * تغییر داور بازی
     */
    public function updateGameReferee(int $gameId, int $newRefereeId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $game = $this->repo->getGame($gameId);
        if (!$game) return false;

        $oldRefereeId = $game['referee_id'];
        if ($oldRefereeId === $newRefereeId) return true;

        $result = $this->repo->updateGameReferee($gameId, $newRefereeId);

        if ($result) {
            $oldReferee = $this->repo->getUser($oldRefereeId);
            $newReferee = $this->repo->getUser($newRefereeId);

            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'game_referee_change',
                'target_type' => 'game',
                'target_id' => $gameId,
                'description' => "تغییر داور بازی #{$gameId} از {$oldReferee['nickname']} به {$newReferee['nickname']}",
                'old_data' => ['referee_id' => $oldRefereeId],
                'new_data' => ['referee_id' => $newRefereeId],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $result;
    }

    /**
     * ویرایش تعداد دورهای بازی
     */
    public function updateGameRounds(int $gameId, int $totalRounds, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $game = $this->repo->getGame($gameId);
        if (!$game) return false;

        $oldRounds = $game['total_rounds_played'];
        if ($oldRounds === $totalRounds) return true;

        $result = $this->repo->updateGameRounds($gameId, $totalRounds);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'game_rounds_change',
                'target_type' => 'game',
                'target_id' => $gameId,
                'description' => "تغییر تعداد دورهای بازی #{$gameId} از {$oldRounds} به {$totalRounds}",
                'old_data' => ['total_rounds_played' => $oldRounds],
                'new_data' => ['total_rounds_played' => $totalRounds],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $result;
    }

    /**
     * افزودن بازیکن به بازی
     */
    public function addParticipant(int $gameId, array $data, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $game = $this->repo->getGame($gameId);
        if (!$game) return false;

        try {
            $participantId = $this->repo->addParticipant($gameId, $data);

            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'game_participant_add',
                'target_type' => 'game',
                'target_id' => $gameId,
                'description' => "افزودن بازیکن به بازی #{$gameId}",
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            return true;
        } catch (\Throwable $e) {
            log_message("Error adding participant: " . $e->getMessage());
            return false;
        }
    }

    /**
     * حذف بازیکن از بازی
     */
    public function removeParticipant(int $participantId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        try {
            $result = $this->repo->removeParticipant($participantId);

            if ($result) {
                $this->repo->createLog([
                    'admin_id' => $adminId,
                    'action_type' => 'game_participant_remove',
                    'target_type' => 'participant',
                    'target_id' => $participantId,
                    'description' => "حذف بازیکن #{$participantId} از بازی",
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                ]);
            }

            return $result;
        } catch (\Throwable $e) {
            log_message("Error removing participant: " . $e->getMessage());
            return false;
        }
    }

    /**
     * عملیات گروهی روی بازی‌ها
     */
    public function bulkAction(array $gameIds, string $action, int $adminId, ?string $ip, ?string $userAgent): int
    {
        $affected = 0;

        // 🆕 اگر عملیات حذف است، ابتدا باید کاربران شرکت‌کننده را برای باز محاسبه پیدا کنیم
        $usersToRecalculate = [];
        if ($action === 'delete') {
            $placeholders = implode(',', array_fill(0, count($gameIds), '?'));
            $participants = $this->db->fetchAll(
                "SELECT DISTINCT user_id FROM game_participants WHERE game_id IN ($placeholders) AND user_id IS NOT NULL",
                $gameIds
            );
            foreach ($participants as $p) {
                if ($p['user_id'] && !in_array($p['user_id'], $usersToRecalculate)) {
                    $usersToRecalculate[] = (int)$p['user_id'];
                }
            }
        }

        switch ($action) {
            case 'delete':
                $affected = $this->repo->bulkDeleteGames($gameIds);
                break;
            case 'cancel':
                $affected = $this->repo->bulkUpdateGames($gameIds, ['status' => 'cancelled']);
                break;
            case 'finish':
                $affected = $this->repo->bulkUpdateGames($gameIds, [
                    'status' => 'finished',
                    'finished_at' => date('Y-m-d H:i:s')
                ]);
                break;
        }

        // 🆕 اگر حذف انجام شد، آمار تمام کاربران شرکت‌کننده را باز محاسبه کن
        if ($action === 'delete' && $affected > 0) {
            $recalcService = new \Application\Services\RecalculateUserService();
            foreach ($usersToRecalculate as $uid) {
                $recalcService->recalculateAll($uid);
            }
        }

        if ($affected > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'game_bulk_action',
                'target_type' => 'game',
                'description' => "عملیات گروهی {$action} روی {$affected} بازی",
                'new_data' => ['game_ids' => $gameIds, 'action' => $action],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $affected;
    }
    // ============================================
    // 🆕 کارت‌ها
    // ============================================

    public function getCards(): array
    {
        return $this->repo->getCards();
    }

    public function createCard(array $data, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $cardId = $this->repo->createCard($data);

        if ($cardId) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'card_create',
                'target_type' => 'card',
                'target_id' => $cardId,
                'description' => "ایجاد کارت: {$data['name']}",
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    public function updateCard(int $cardId, array $data, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $oldCard = $this->db->fetchOne("SELECT * FROM cards WHERE id = ?", [$cardId]);
        if (!$oldCard) return false;

        $result = $this->repo->updateCard($cardId, $data);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'card_edit',
                'target_type' => 'card',
                'target_id' => $cardId,
                'description' => "ویرایش کارت: {$oldCard['name']}",
                'old_data' => $oldCard,
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    public function toggleCardActive(int $cardId, int $isActive): bool
    {
        return $this->repo->updateCard($cardId, ['is_active' => $isActive]);
    }

    public function deleteCard(int $cardId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $card = $this->db->fetchOne("SELECT * FROM cards WHERE id = ?", [$cardId]);
        if (!$card) return false;

        $result = $this->repo->deleteCard($cardId);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'card_delete',
                'target_type' => 'card',
                'target_id' => $cardId,
                'description' => "حذف کارت: {$card['name']}",
                'old_data' => $card,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    // ============================================
    // 🆕 انواع برد
    // ============================================

    public function getWinTypes(): array
    {
        return $this->repo->getWinTypes();
    }

    public function createWinType(array $data, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $winTypeId = $this->repo->createWinType($data);

        if ($winTypeId) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'win_type_create',
                'target_type' => 'win_type',
                'target_id' => $winTypeId,
                'description' => "ایجاد نوع برد: {$data['name']}",
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    public function updateWinType(int $winTypeId, array $data, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $oldWinType = $this->db->fetchOne("SELECT * FROM win_types WHERE id = ?", [$winTypeId]);
        if (!$oldWinType) return false;

        $result = $this->repo->updateWinType($winTypeId, $data);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'win_type_edit',
                'target_type' => 'win_type',
                'target_id' => $winTypeId,
                'description' => "ویرایش نوع برد: {$oldWinType['name']}",
                'old_data' => $oldWinType,
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    public function toggleWinTypeActive(int $winTypeId, int $isActive): bool
    {
        return $this->repo->updateWinType($winTypeId, ['is_active' => $isActive]);
    }

    public function deleteWinType(int $winTypeId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $winType = $this->db->fetchOne("SELECT * FROM win_types WHERE id = ?", [$winTypeId]);
        if (!$winType) return false;

        $result = $this->repo->deleteWinType($winTypeId);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'win_type_delete',
                'target_type' => 'win_type',
                'target_id' => $winTypeId,
                'description' => "حذف نوع برد: {$winType['name']}",
                'old_data' => $winType,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }
    // ============================================
    // 🆕 عناوین
    // ============================================

    public function getTitles(): array
    {
        return $this->repo->getTitles();
    }

    public function createTitle(array $data, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $titleId = $this->repo->createTitle($data);

        if ($titleId) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'title_create',
                'target_type' => 'title',
                'target_id' => $titleId,
                'description' => "ایجاد عنوان: {$data['name']}",
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    public function updateTitle(int $titleId, array $data, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $oldTitle = $this->db->fetchOne("SELECT * FROM titles WHERE id = ?", [$titleId]);

        if (!$oldTitle) return false;

        $result = $this->repo->updateTitle($titleId, $data);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'title_edit',
                'target_type' => 'title',
                'target_id' => $titleId,
                'description' => "ویرایش عنوان: {$oldTitle['name']}",
                'old_data' => $oldTitle,
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    public function deleteTitle(int $titleId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $title = $this->db->fetchOne("SELECT * FROM titles WHERE id = ?", [$titleId]);

        if (!$title) return false;

        $result = $this->repo->deleteTitle($titleId);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'title_delete',
                'target_type' => 'title',
                'target_id' => $titleId,
                'description' => "حذف عنوان: {$title['name']}",
                'old_data' => $title,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    // در فایل src/Application/Services/AdminService.php این متد را اضافه کنید:

    public function recalculateAndLogUserStats(int $userId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        // ۱. محاسبه آمار واقعی از روی بازی‌های پایان‌یافته
        $stats = $this->db->fetchOne(
            "SELECT
            COUNT(DISTINCT g.id) as total_games,
            SUM(CASE WHEN g.winner_participant_id = gp.id THEN 1 ELSE 0 END) as total_wins,
            SUM(gp.total_score) as total_points
         FROM games g
         INNER JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ? AND g.status = 'finished'",
            [$userId]
        );

        $totalGames = (int)($stats['total_games'] ?? 0);
        $totalWins = (int)($stats['total_wins'] ?? 0);
        $totalPoints = (float)($stats['total_points'] ?? 0);

        // ۲. محاسبه XP جدید بر اساس فرمول سیستم
        $xpMultiplier = (float) ($this->db->fetchOne("SELECT setting_value FROM system_settings WHERE setting_key = 'scoring_xp_multiplier'")['setting_value'] ?? 2.0);
        $gameBonus = (int) ($this->db->fetchOne("SELECT setting_value FROM system_settings WHERE setting_key = 'scoring_game_bonus'")['setting_value'] ?? 5);
        $winBonus = (int) ($this->db->fetchOne("SELECT setting_value FROM system_settings WHERE setting_key = 'scoring_win_bonus'")['setting_value'] ?? 15);

        $newXP = (int)(($totalPoints * $xpMultiplier) + ($totalGames * $gameBonus) + ($totalWins * $winBonus));

        // محاسبه سطح تقریبی (یا می‌توانید از LevelService استفاده کنید)
        $newLevel = max(1, (int)floor($newXP / 100) + 1);

        // ۳. به‌روزرسانی دیتابیس
        $this->db->update('user_xp', [
            'total_xp' => $newXP,
            'current_level' => $newLevel,
        ], 'user_id = ?', [$userId]);

        // ۴. ثبت لاگ از طریق ریپازیتوری (همانطور که شما اشاره کردید)
        $this->repo->createLog([
            'admin_id' => $adminId,
            'action_type' => 'user_stats_recalculated',
            'target_type' => 'user',
            'target_id' => $userId,
            'description' => "بازمحاسبه آمار و XP کاربر به دلیل احتمال تقلب",
            'new_data' => [
                'total_games' => $totalGames,
                'total_wins' => $totalWins,
                'total_points' => $totalPoints,
                'new_xp' => $newXP
            ],
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        return true;
    }

        // ============================================
    // 🆕 مدیریت سطوح (Player Levels)
    // ============================================

    /**
     * گرفتن تمام سطوح
     */
    public function getAllLevels(): array
    {
        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();
        return $gamificationRepo->getLevels();
    }

    /**
     * ایجاد سطح جدید
     */
    public function createLevel(array $data, int $adminId, ?string $ip, ?string $userAgent): array
    {
        // ۱. اعتبارسنجی (منطق تجاری - متعلق به Service)
        $validation = $this->validateLevelData($data);
        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }

        // ۲. استفاده از Repository برای بررسی تداخل (نه کوئری مستقیم)
        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();

        $overlap = $gamificationRepo->checkXpRangeOverlap($data['min_xp'], $data['max_xp']);
        if ($overlap) {
            return [
                'success' => false,
                'errors' => ['xp_range' => "محدوده XP با سطح {$overlap['level']} ({$overlap['title']}) تداخل دارد"]
            ];
        }

        // ۳. بررسی تکراری نبودن شماره سطح (از Repository)
        $existingLevel = $gamificationRepo->getLevelByNumber($data['level']);
        if ($existingLevel) {
            return [
                'success' => false,
                'errors' => ['level' => "سطح {$data['level']} قبلاً وجود دارد"]
            ];
        }

        // ۴. ایجاد سطح (از Repository)
        $levelId = $gamificationRepo->createLevel($data);

        if ($levelId) {
            // ۵. لاگ‌گیری (از AdminRepository)
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'level_create',
                'target_type' => 'level',
                'target_id' => $levelId,
                'description' => "ایجاد سطح {$data['level']}: {$data['title']}",
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return ['success' => true, 'message' => 'سطح با موفقیت ایجاد شد'];
        }

        return ['success' => false, 'errors' => ['general' => 'خطا در ایجاد سطح']];
    }

    /**
     * به‌روزرسانی سطح
     */
    public function updateLevel(int $id, array $data, int $adminId, ?string $ip, ?string $userAgent): array
    {
        // اعتبارسنجی (ارسال $id به عنوان excludeId)
        $validation = $this->validateLevelData($data, $id);
        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }

        // 🆕 اصلاح کوئری تداخل: مقایسه id به جای level
        $overlap = $this->db->fetchOne(
            "SELECT level, title FROM player_levels 
             WHERE id != ? AND (
                (min_xp <= ? AND max_xp >= ?) 
                OR (min_xp <= ? AND max_xp >= ?)
                OR (min_xp >= ? AND max_xp <= ?)
             )",
            [
                $id, // ✅ اصلاح شد
                (int)$data['max_xp'],
                (int)$data['min_xp'],
                (int)$data['min_xp'],
                (int)$data['max_xp'],
                (int)$data['min_xp'],
                (int)$data['max_xp']
            ]
        );

        if ($overlap) {
            return [
                'success' => false,
                'errors' => ['xp_range' => "محدوده XP با سطح {$overlap['level']} ({$overlap['title']}) تداخل دارد"]
            ];
        }

        $oldLevel = $this->db->fetchOne("SELECT * FROM player_levels WHERE id = ?", [$id]);

        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();
        $result = $gamificationRepo->updateLevel($id, $data);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'level_update',
                'target_type' => 'level',
                'target_id' => $id,
                'description' => "ویرایش سطح {$oldLevel['level']}: {$data['title']}",
                'old_data' => $oldLevel,
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return ['success' => true, 'message' => 'سطح با موفقیت ویرایش شد'];
        }

        return ['success' => false, 'errors' => ['general' => 'خطا در ویرایش سطح']];
    }

    /**
     * حذف سطح
     */
    public function deleteLevel(int $level, int $adminId, ?string $ip, ?string $userAgent): array
    {
        $level = $this->db->fetchOne("SELECT * FROM player_levels WHERE level = ?", [$level]);
        if (!$level) {
            return ['success' => false, 'errors' => ['general' => 'سطح یافت نشد']];
        }

        // بررسی اینکه کاربران در این سطح وجود ندارند
        $usersInLevel = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM user_xp WHERE current_level = ?",
            [$level['level']]
        );

        if ($usersInLevel && $usersInLevel['count'] > 0) {
            return [
                'success' => false,
                'errors' => ['general' => "نمی‌توان این سطح را حذف کرد زیرا {$usersInLevel['count']} کاربر در این سطح قرار دارند"]
            ];
        }

        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();
        $result = $gamificationRepo->deleteLevel($level['level']);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'level_delete',
                'target_type' => 'level',
                'target_id' => $level['level'],
                'description' => "حذف سطح {$level['level']}: {$level['title']}",
                'old_data' => $level,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return ['success' => true, 'message' => 'سطح با موفقیت حذف شد'];
        }

        return ['success' => false, 'errors' => ['general' => 'خطا در حذف سطح']];
    }

    /**
     * اعتبارسنجی داده‌های سطح
     */
    private function validateLevelData(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        // فقط در حالت ایجاد (Create) فیلد level الزامی است
        if ($excludeId === null && empty($data['level'])) {
            $errors['level'] = 'شماره سطح الزامی است';
        }

        if (empty($data['min_xp']) && $data['min_xp'] !== '0') {
            $errors['min_xp'] = 'حداقل XP الزامی است';
        }

        if (empty($data['max_xp'])) {
            $errors['max_xp'] = 'حداکثر XP الزامی است';
        }

        // بررسی عددی بودن
        if (!empty($data['level']) && !is_numeric($data['level'])) {
            $errors['level'] = 'شماره سطح باید عددی باشد';
        }

        if (!is_numeric($data['min_xp'] ?? '')) {
            $errors['min_xp'] = 'حداقل XP باید عددی باشد';
        }

        if (!is_numeric($data['max_xp'] ?? '')) {
            $errors['max_xp'] = 'حداکثر XP باید عددی باشد';
        }

        // بررسی منطقی min و max
        if (is_numeric($data['min_xp'] ?? '') && is_numeric($data['max_xp'] ?? '')) {
            if ((int)$data['min_xp'] > (int)$data['max_xp']) {
                $errors['max_xp'] = 'حداکثر XP باید بزرگتر از حداقل XP باشد';
            }
        }

        // بررسی سطح معتبر
        if (!empty($data['level']) && (int)$data['level'] < 1) {
            $errors['level'] = 'شماره سطح باید حداقل ۱ باشد';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

        // ============================================
    // 🆕 مدیریت نشان‌ها (Achievements CRUD)
    // ============================================

    /**
     * گرفتن لیست نشان‌ها برای پنل ادمین
     */
    public function getAchievementsForAdmin(): array
    {
        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();
        return $gamificationRepo->findAllAchievementsForAdmin();
    }

    /**
     * ایجاد نشان جدید با ثبت لاگ
     */
    public function createAchievementWithLog(array $data, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        // اعتبارسنجی
        if (empty($data['code']) || empty($data['name']) || empty($data['condition_type']) || empty($data['condition_value'])) {
            return false;
        }

        // بررسی تکراری نبودن code
        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();
        $existing = $gamificationRepo->findAchievementByCode($data['code']);
        if ($existing) {
            return false;
        }

        $achievementId = $gamificationRepo->createAchievementRecord($data);

        if ($achievementId) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'achievement_create',
                'target_type' => 'achievement',
                'target_id' => $achievementId,
                'description' => "ایجاد نشان: {$data['name']}",
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    /**
     * به‌روزرسانی نشان با ثبت لاگ
     */
    public function updateAchievementWithLog(int $id, array $data, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();
        $oldAchievement = $gamificationRepo->findAchievementById($id);

        if (!$oldAchievement) {
            return false;
        }

        // بررسی تکراری نبودن code (اگر تغییر کرده)
        if ($data['code'] !== $oldAchievement['code']) {
            $existing = $gamificationRepo->findAchievementByCode($data['code']);
            if ($existing && $existing['id'] !== $id) {
                return false;
            }
        }

        $result = $gamificationRepo->updateAchievementRecord($id, $data);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'achievement_update',
                'target_type' => 'achievement',
                'target_id' => $id,
                'description' => "ویرایش نشان: {$data['name']}",
                'old_data' => $oldAchievement,
                'new_data' => $data,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    /**
     * حذف نشان با ثبت لاگ
     */
    public function deleteAchievementWithLog(int $id, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();
        $achievement = $gamificationRepo->findAchievementById($id);

        if (!$achievement) {
            return false;
        }

        $result = $gamificationRepo->deleteAchievementRecord($id);

        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'achievement_delete',
                'target_type' => 'achievement',
                'target_id' => $id,
                'description' => "حذف نشان: {$achievement['name']}",
                'old_data' => $achievement,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }

    /**
     * تغییر وضعیت فعال/غیرفعال نشان با ثبت لاگ
     */
    public function toggleAchievementActiveWithLog(int $id, int $isActive, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();
        $achievement = $gamificationRepo->findAchievementById($id);

        if (!$achievement) {
            return false;
        }

        $result = $gamificationRepo->toggleAchievementActiveRecord($id, $isActive);

        if ($result) {
            $statusText = $isActive ? 'فعال' : 'غیرفعال';
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'achievement_toggle',
                'target_type' => 'achievement',
                'target_id' => $id,
                'description' => "تغییر وضعیت نشان {$achievement['name']} به {$statusText}",
                'old_data' => ['is_active' => $achievement['is_active']],
                'new_data' => ['is_active' => $isActive],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            return true;
        }

        return false;
    }
    // ============================================
    // 🆕 مجوزهای بازی و ریست پسورد
    // ============================================

    public function banFromCreatingGame(int $userId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $user = $this->repo->getUser($userId);
        if (!$user) return false;
        $result = $this->repo->updateUserGamePermissions($userId, ['can_create_game' => 0]);
        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'ban_create_game',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "سلب مجوز ساخت بازی از {$user['nickname']}",
                'new_data' => ['can_create_game' => 0],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }
        return $result;
    }

    public function allowCreateGame(int $userId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $user = $this->repo->getUser($userId);
        if (!$user) return false;
        $result = $this->repo->updateUserGamePermissions($userId, ['can_create_game' => 1]);
        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'allow_create_game',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "اعطای مجوز ساخت بازی به {$user['nickname']}",
                'new_data' => ['can_create_game' => 1],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }
        return $result;
    }

    public function banFromJoiningGame(int $userId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $user = $this->repo->getUser($userId);
        if (!$user) return false;
        // 🆕 منطق: can_join_game = 0 → can_create_game هم 0 شود
        $result = $this->repo->updateUserGamePermissions($userId, [
            'can_join_game' => 0,
            'can_create_game' => 0,
        ]);
        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'ban_join_game',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "سلب مجوز شرکت در بازی از {$user['nickname']} (ساخت بازی هم سلب شد)",
                'new_data' => ['can_join_game' => 0, 'can_create_game' => 0],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }
        return $result;
    }

    public function allowJoinGame(int $userId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $user = $this->repo->getUser($userId);
        if (!$user) return false;
        // 🆕 منطق: can_join_game = 1 → can_create_game تغییر نکند
        $result = $this->repo->updateUserGamePermissions($userId, ['can_join_game' => 1]);
        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'allow_join_game',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "اعطای مجوز شرکت در بازی به {$user['nickname']}",
                'new_data' => ['can_join_game' => 1],
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }
        return $result;
    }

    public function resetUserPassword(int $userId, int $adminId, ?string $ip, ?string $userAgent): bool
    {
        $user = $this->repo->getUser($userId);
        if (!$user) return false;
        $newHash = password_hash('123456', PASSWORD_BCRYPT);
        $result = $this->repo->resetUserPassword($userId, $newHash);
        if ($result) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'reset_password',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "ریست پسورد کاربر {$user['nickname']} به 123456",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }
        return $result;
    }
    // ============================================
// 🆕 مدیریت اعلان‌ها (Notifications)
// ============================================

    /**
     * گرفتن لیست اعلان‌ها
     */
    public function getNotifications(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;
        $result = $this->repo->getNotifications($filters, $perPage, $offset);

        return [
            'notifications' => $result['notifications'],
            'total' => $result['total'],
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($result['total'] / $perPage),
        ];
    }

    /**
     * حذف اعلان‌های قدیمی
     */
    public function deleteOldNotifications(int $daysOld, int $adminId, ?string $ip, ?string $userAgent): int
    {
        $deletedCount = $this->repo->deleteOldNotifications($daysOld);

        if ($deletedCount > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'cleanup_notifications',
                'target_type' => 'notification',
                'description' => "حذف {$deletedCount} اعلان قدیمی‌تر از {$daysOld} روز",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $deletedCount;
    }

    /**
     * حذف همه اعلان‌ها
     */
    public function deleteAllNotifications(int $adminId, ?string $ip, ?string $userAgent): int
    {
        $deletedCount = $this->repo->deleteAllNotifications();

        if ($deletedCount > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'cleanup_all_notifications',
                'target_type' => 'notification',
                'description' => "حذف همه اعلان‌ها ({$deletedCount} مورد)",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $deletedCount;
    }

// ============================================
// 🆕 پاک‌سازی جداول لاگ
// ============================================

    /**
     * گرفتن آمار جداول لاگ
     */
    public function getLogsStats(): array
    {
        return $this->repo->getLogsStats();
    }

    /**
     * حذف لاگ‌های ادمین قدیمی
     */
    public function deleteOldAdminLogs(int $daysOld, int $adminId, ?string $ip, ?string $userAgent): int
    {
        $deletedCount = $this->repo->deleteOldAdminLogs($daysOld);

        if ($deletedCount > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'cleanup_admin_logs',
                'target_type' => 'admin_log',
                'description' => "حذف {$deletedCount} لاگ ادمین قدیمی‌تر از {$daysOld} روز",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $deletedCount;
    }

    /**
     * حذف همه لاگ‌های ادمین
     */
    public function deleteAllAdminLogs(int $adminId, ?string $ip, ?string $userAgent): int
    {
        $deletedCount = $this->repo->deleteAllAdminLogs();

        if ($deletedCount > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'cleanup_all_admin_logs',
                'target_type' => 'admin_log',
                'description' => "حذف همه لاگ‌های ادمین ({$deletedCount} مورد)",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $deletedCount;
    }

    /**
     * حذف referee_actions_log قدیمی
     */
    public function deleteOldRefereeActionsLog(int $daysOld, int $adminId, ?string $ip, ?string $userAgent): int
    {
        $deletedCount = $this->repo->deleteOldRefereeActionsLog($daysOld);

        if ($deletedCount > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'cleanup_referee_actions',
                'target_type' => 'referee_action',
                'description' => "حذف {$deletedCount} لاگ داور قدیمی‌تر از {$daysOld} روز",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $deletedCount;
    }

    /**
     * حذف همه referee_actions_log
     */
    public function deleteAllRefereeActionsLog(int $adminId, ?string $ip, ?string $userAgent): int
    {
        $deletedCount = $this->repo->deleteAllRefereeActionsLog();

        if ($deletedCount > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'cleanup_all_referee_actions',
                'target_type' => 'referee_action',
                'description' => "حذف همه لاگ‌های داور ({$deletedCount} مورد)",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $deletedCount;
    }

    /**
     * حذف sse_events قدیمی
     */
    public function deleteOldSseEvents(int $hoursOld, int $adminId, ?string $ip, ?string $userAgent): int
    {
        $deletedCount = $this->repo->deleteOldSseEvents($hoursOld);

        if ($deletedCount > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'cleanup_sse_events',
                'target_type' => 'sse_event',
                'description' => "حذف {$deletedCount} رویداد SSE قدیمی‌تر از {$hoursOld} ساعت",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $deletedCount;
    }

    /**
     * حذف همه sse_events
     */
    public function deleteAllSseEvents(int $adminId, ?string $ip, ?string $userAgent): int
    {
        $deletedCount = $this->repo->deleteAllSseEvents();

        if ($deletedCount > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'cleanup_all_sse_events',
                'target_type' => 'sse_event',
                'description' => "حذف همه رویدادهای SSE ({$deletedCount} مورد)",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $deletedCount;
    }
    /**
     * علامت‌گذاری همه اعلان‌ها به عنوان خوانده شده
     */
    public function markAllNotificationsAsRead(int $adminId, ?string $ip, ?string $userAgent): int
    {
        $updatedCount = $this->repo->markAllNotificationsAsRead();

        if ($updatedCount > 0) {
            $this->repo->createLog([
                'admin_id' => $adminId,
                'action_type' => 'setting_change',
                'target_type' => 'notification',
                'description' => "علامت‌گذاری {$updatedCount} اعلان به عنوان خوانده شده",
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        }

        return $updatedCount;
    }
}
