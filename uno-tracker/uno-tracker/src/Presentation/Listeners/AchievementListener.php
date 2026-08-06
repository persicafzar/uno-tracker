<?php

namespace Presentation\Listeners;

use Core\Database;

class AchievementListener
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * بررسی نشان‌ها پس از هر دور
     */
    public function checkAchievements(array $data): void
    {
        $userId = $data['winner_user_id'] ?? null;
        if (!$userId) {
            return;
        }

        $achievements = $this->db->fetchAll(
            "SELECT * FROM achievements WHERE is_active = 1"
        );

        foreach ($achievements as $achievement) {
            // بررسی اینکه کاربر قبلاً این نشان را نگرفته باشد
            $hasAchievement = $this->db->fetchOne(
                "SELECT id FROM user_achievements 
                 WHERE user_id = ? AND achievement_id = ? AND is_completed = 1",
                [$userId, $achievement['id']]
            );

            if ($hasAchievement) {
                continue;
            }

            // 🆕 اصلاح: استفاده از condition_type و condition_value
            $conditionType = $achievement['condition_type'] ?? null;
            $conditionValue = (int)($achievement['condition_value'] ?? 0);

            if (!$conditionType || $conditionValue <= 0) {
                continue;
            }

            // محاسبه مقدار فعلی کاربر
            $currentValue = $this->calculateCurrentValue($conditionType, $userId);

            // به‌روزرسانی پیشرفت
            $this->updateProgress($userId, $achievement['id'], $currentValue);

            // بررسی تکمیل
            if ($currentValue >= $conditionValue) {
                $this->grantAchievement($achievement, $userId, $currentValue);
            }
        }
    }

    /**
     * بررسی نشان‌های پایان بازی
     */
    public function checkGameAchievements(array $data): void
    {
        // همان منطق checkAchievements
        $this->checkAchievements($data);
    }

    /**
     * محاسبه مقدار فعلی کاربر بر اساس نوع شرط
     */
    private function calculateCurrentValue(string $conditionType, int $userId): int
    {
        switch ($conditionType) {
            case 'total_games':
                $result = $this->db->fetchOne(
                    "SELECT COUNT(DISTINCT game_id) as count 
                     FROM game_participants 
                     WHERE user_id = ?",
                    [$userId]
                );
                return $result ? (int)$result['count'] : 0;

            case 'total_wins':
                $result = $this->db->fetchOne(
                    "SELECT COUNT(*) as count 
                     FROM game_participants gp
                     JOIN games g ON gp.game_id = g.id
                     WHERE gp.user_id = ? AND g.winner_participant_id = gp.id",
                    [$userId]
                );
                return $result ? (int)$result['count'] : 0;

            case 'total_points':
                $result = $this->db->fetchOne(
                    "SELECT SUM(total_score) as total 
                     FROM game_participants 
                     WHERE user_id = ?",
                    [$userId]
                );
                return $result ? (int)$result['total'] : 0;

            case 'team_games':
                $result = $this->db->fetchOne(
                    "SELECT COUNT(DISTINCT gp.game_id) as count 
                     FROM game_participants gp
                     JOIN games g ON gp.game_id = g.id
                     WHERE gp.user_id = ? AND g.game_mode = 'friendly'",
                    [$userId]
                );
                return $result ? (int)$result['count'] : 0;

            case 'team_wins':
                $result = $this->db->fetchOne(
                    "SELECT COUNT(*) as count 
                     FROM game_participants gp
                     JOIN games g ON gp.game_id = g.id
                     WHERE gp.user_id = ? 
                     AND g.game_mode = 'friendly'
                     AND g.winner_participant_id = gp.id",
                    [$userId]
                );
                return $result ? (int)$result['count'] : 0;

            case 'best_streak':
                $result = $this->db->fetchOne(
                    "SELECT best_streak FROM user_streaks WHERE user_id = ?",
                    [$userId]
                );
                return $result ? (int)$result['best_streak'] : 0;

            case 'current_streak':
                $result = $this->db->fetchOne(
                    "SELECT current_streak FROM user_streaks WHERE user_id = ?",
                    [$userId]
                );
                return $result ? (int)$result['current_streak'] : 0;

            default:
                return 0;
        }
    }

    /**
     * به‌روزرسانی پیشرفت نشان
     */
    private function updateProgress(int $userId, int $achievementId, int $currentValue): void
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM user_achievements 
             WHERE user_id = ? AND achievement_id = ?",
            [$userId, $achievementId]
        );

        if ($existing) {
            $this->db->update(
                'user_achievements',
                ['progress' => $currentValue],
                'user_id = ? AND achievement_id = ?',
                [$userId, $achievementId]
            );
        } else {
            $this->db->insert('user_achievements', [
                'user_id' => $userId,
                'achievement_id' => $achievementId,
                'progress' => $currentValue,
                'is_completed' => 0,
            ]);
        }
    }

    /**
     * اعطای نشان به کاربر
     */
    private function grantAchievement(array $achievement, int $userId, int $finalValue): void
    {
        // به‌روزرسانی به تکمیل شده
        $this->db->update(
            'user_achievements',
            [
                'is_completed' => 1,
                'progress' => $finalValue,
                'unlocked_at' => date('Y-m-d H:i:s'),
            ],
            'user_id = ? AND achievement_id = ?',
            [$userId, $achievement['id']]
        );

        // دادن XP
        $xpReward = (int)($achievement['xp_reward'] ?? 10);
        
        // به‌روزرسانی user_xp
        $currentXP = $this->db->fetchOne(
            "SELECT total_xp FROM user_xp WHERE user_id = ?",
            [$userId]
        );

        if ($currentXP) {
            $this->db->update(
                'user_xp',
                ['total_xp' => (int)$currentXP['total_xp'] + $xpReward],
                'user_id = ?',
                [$userId]
            );
        } else {
            $this->db->insert('user_xp', [
                'user_id' => $userId,
                'total_xp' => $xpReward,
                'current_level' => 1,
                'xp_to_next_level' => 100,
            ]);
        }

        // Dispatch رویداد
        \Core\EventDispatcher::getInstance()->dispatch('achievement_unlocked', [
            'user_id' => $userId,
            'achievement_id' => $achievement['id'],
            'achievement_name' => $achievement['name'],
            'xp_reward' => $xpReward,
        ]);
    }
}