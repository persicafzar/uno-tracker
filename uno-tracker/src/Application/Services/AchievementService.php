<?php

namespace Application\Services;

use Core\Database;
use Infrastructure\Repositories\GamificationRepository;
use Domain\Achievement;

class AchievementService
{
    private GamificationRepository $repo;
    private NotificationService $notificationService;
    private LevelService $levelService;
    private Database $db;

    public function __construct()
    {
        $this->repo = new GamificationRepository();
        $this->notificationService = new NotificationService();
        $this->levelService = new LevelService();
        $this->db = Database::getInstance();
    }

    /**
     * بررسی و به‌روزرسانی نشان‌ها - 🆕 با فراخوانی titles در انتها
     */
    public function checkAndUpdateAchievements(int $userId): array
    {
        $achievements = $this->repo->findAllAchievements();
        $unlocked = [];
        $userStats = $this->getUserStats($userId);

        foreach ($achievements as $achievement) {
            if ($this->repo->isAchievementCompleted($userId, $achievement->id)) {
                continue;
            }

            $currentValue = $this->getCurrentValueForCondition($achievement->condition_type, $userId);

            if ($currentValue >= $achievement->condition_value) {
                $this->repo->completeUserAchievement($userId, $achievement->id, $currentValue);
                $achievement->user_completed = true;
                $achievement->user_progress = $currentValue;
                $achievement->user_unlocked_at = date('Y-m-d H:i:s');
                $unlocked[] = $achievement;

                error_log("🏅 Achievement unlocked: {$achievement->name} for user {$userId}");
            } else {
                $this->repo->updateUserAchievementProgress($userId, $achievement->id, $currentValue);
            }
        }

        // 🆕 فراخوانی titles بررسی هم انجام شود (backup)
        // این کار باعث می‌شود حتی اگر در processGameEnd فراخوانی نشود،
        // titles در checkAndUpdateAchievements بررسی شوند
        try {
            $gamificationService = new \Application\Services\GamificationService();
            $gamificationService->checkAndUpdateTitles($userId);
        } catch (\Throwable $e) {
            error_log("⚠️ Error checking titles in checkAndUpdateAchievements: " . $e->getMessage());
        }

        return $unlocked;
    }

    /**
     * گرفتن آمار کاربر - 🆕 اصلاح شده برای پشتیبانی از تیمی
     */
    private function getUserStats(int $userId): array
    {
        $stats = $this->db->fetchOne(
            "SELECT
            COUNT(DISTINCT gp.game_id) as total_games,
            -- 🆕 محاسبه صحیح کل بردها (شامل Solo و Team)
            SUM(CASE 
                WHEN g.game_mode = 'solo' AND g.winner_participant_id = gp.id THEN 1
                WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                     AND g.winner_team_id = gp.team_id THEN 1
                ELSE 0 
            END) as total_wins,
            SUM(gp.total_score) as total_points,
            SUM(CASE WHEN g.game_mode = 'friendly' THEN 1 ELSE 0 END) as team_games,
            -- 🆕 محاسبه صحیح بردهای تیمی
            SUM(CASE 
                WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                     AND g.winner_team_id = gp.team_id THEN 1
                ELSE 0 
            END) as team_wins
         FROM game_participants gp
         JOIN games g ON gp.game_id = g.id
         WHERE gp.user_id = ?
         AND g.status = 'finished'",
            [$userId]
        );

        // گرفتن استریک
        $streak = $this->db->fetchOne(
            "SELECT current_streak, best_streak FROM user_streaks WHERE user_id = ?",
            [$userId]
        );

        return [
            'total_games' => (int)($stats['total_games'] ?? 0),
            'total_wins' => (int)($stats['total_wins'] ?? 0),
            'total_points' => (int)($stats['total_points'] ?? 0),
            'team_games' => (int)($stats['team_games'] ?? 0),
            'team_wins' => (int)($stats['team_wins'] ?? 0),
            'current_streak' => (int)($streak['current_streak'] ?? 0),
            'best_streak' => (int)($streak['best_streak'] ?? 0),
        ];
    }

    /**
     * گرفتن مقدار فعلی برای شرط نشان
     */
    private function getCurrentValueForCondition(string $conditionType, int $userId): int
    {
        $stats = $this->getUserStats($userId);

        return match ($conditionType) {
            'total_games' => $stats['total_games'],
            'total_wins' => $stats['total_wins'],
            'total_points' => $stats['total_points'],
            'team_games' => $stats['team_games'],
            'team_wins' => $stats['team_wins'],
            'best_streak' => $stats['best_streak'],
            'current_streak' => $stats['current_streak'],
            default => 0,
        };
    }

    /**
     * به‌روزرسانی پیشرفت نشان
     */
    private function updateProgress(int $userId, int $achievementId, int $progress): void
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM user_achievements 
             WHERE user_id = ? AND achievement_id = ?",
            [$userId, $achievementId]
        );

        if ($existing) {
            $this->db->update(
                'user_achievements',
                ['progress' => $progress],
                'user_id = ? AND achievement_id = ?',
                [$userId, $achievementId]
            );
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
    private function completeAchievement(int $userId, int $achievementId, int $finalValue): void
    {
        $this->db->update(
            'user_achievements',
            [
                'is_completed' => 1,
                'progress' => $finalValue,
                'unlocked_at' => date('Y-m-d H:i:s'),
            ],
            'user_id = ? AND achievement_id = ?',
            [$userId, $achievementId]
        );
    }

    /**
     * گرفتن همه نشان‌های کاربر با وضعیت پیشرفت
     */
    public function getUserAchievements(int $userId): array
    {
        $achievements = $this->repo->getUserAchievements($userId);
        $stats = $this->getUserStats($userId);

        // به‌روزرسانی پیشرفت در memory (برای نمایش)
        foreach ($achievements as $achievement) {
            if (!$achievement->user_completed) {
                $conditionType = $achievement->condition_type ?? null;
                if ($conditionType) {
                    $currentValue = $this->getCurrentValueForCondition($conditionType, $userId);
                    $achievement->user_progress = min($currentValue, $achievement->condition_value);
                }
            }
        }

        return $achievements;
    }

    /**
     * گرفتن نشان‌های تکمیل شده
     */
    public function getCompletedAchievements(int $userId): array
    {
        return $this->repo->getUserCompletedAchievements($userId);
    }

    /**
     * گروه‌بندی نشان‌ها بر اساس دسته‌بندی
     */
    public function groupByCategory(array $achievements): array
    {
        $grouped = [];

        foreach ($achievements as $achievement) {
            $category = $achievement->category;

            if (!isset($grouped[$category])) {
                $grouped[$category] = [
                    'name' => $achievement->getCategoryName(),
                    'achievements' => [],
                    'completed_count' => 0,
                    'total_count' => 0,
                ];
            }

            $grouped[$category]['achievements'][] = $achievement;
            $grouped[$category]['total_count']++;

            if ($achievement->user_completed) {
                $grouped[$category]['completed_count']++;
            }
        }

        return $grouped;
    }

    /**
     * آمار کلی نشان‌ها
     */
    public function getAchievementsStats(int $userId): array
    {
        $achievements = $this->repo->getUserAchievements($userId);

        $total = count($achievements);
        $completed = 0;
        $totalXpEarned = 0;
        $totalXpPossible = 0;

        foreach ($achievements as $achievement) {
            $totalXpPossible += $achievement->xp_reward;

            if ($achievement->user_completed) {
                $completed++;
                $totalXpEarned += $achievement->xp_reward;
            }
        }

        return [
            'total' => $total,
            'completed' => $completed,
            'locked' => $total - $completed,
            'completion_percentage' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            'xp_earned' => $totalXpEarned,
            'xp_possible' => $totalXpPossible,
        ];
    }
}
