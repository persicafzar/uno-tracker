<?php

namespace Application\Services;

use Core\Database;

class RecalculateUserService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * باز محاسبه کامل آمار، XP، مدال‌ها و القاب یک کاربر از صفر (بر اساس بازی‌های معتبر باقی‌مانده)
     */
    public function recalculateAll(int $userId): void
    {
        $this->db->beginTransaction();
        try {
            // ۱. 🆕 محاسبه آمار پایه با پشتیبانی کامل از تیمی
            $stats = $this->db->fetchOne(
                "SELECT 
                COUNT(DISTINCT g.id) as total_games,
                SUM(CASE 
                    WHEN g.game_mode = 'solo' AND g.winner_participant_id = gp.id THEN 1
                    WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                         AND g.winner_team_id = gp.team_id THEN 1
                    ELSE 0 
                END) as total_wins,
                SUM(CASE 
                    WHEN g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                         AND g.winner_team_id = gp.team_id THEN 1
                    ELSE 0 
                END) as team_wins,
                COALESCE(SUM(gp.total_score), 0) as total_points
             FROM games g
             INNER JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? AND g.status = 'finished'",
                [$userId]
            );

            $totalGames = (int)($stats['total_games'] ?? 0);
            $totalWins = (int)($stats['total_wins'] ?? 0);
            $totalPoints = (float)($stats['total_points'] ?? 0);
            $teamWins = (int)($stats['team_wins'] ?? 0);

            // ۲. محاسبه XP و سطح جدید
            $xpMultiplier = (float)($this->db->fetchOne("SELECT setting_value FROM system_settings WHERE setting_key = 'scoring_xp_multiplier'")['setting_value'] ?? 2.0);
            $gameBonus = (int)($this->db->fetchOne("SELECT setting_value FROM system_settings WHERE setting_key = 'scoring_game_bonus'")['setting_value'] ?? 5);
            $winBonus = (int)($this->db->fetchOne("SELECT setting_value FROM system_settings WHERE setting_key = 'scoring_win_bonus'")['setting_value'] ?? 15);

            $newXP = (int)(($totalPoints * $xpMultiplier) + ($totalGames * $gameBonus) + ($totalWins * $winBonus));

            $level = $this->db->fetchOne("SELECT level FROM player_levels WHERE ? BETWEEN min_xp AND max_xp ORDER BY level DESC LIMIT 1", [$newXP]);
            $newLevel = $level ? (int)$level['level'] : 1;
            $nextLevelXp = $this->db->fetchOne("SELECT min_xp FROM player_levels WHERE level = ?", [$newLevel + 1]);
            $xpToNext = $nextLevelXp ? (int)$nextLevelXp['min_xp'] : 999999;

            $this->db->update('user_xp', [
                'total_xp' => $newXP,
                'current_level' => $newLevel,
                'xp_to_next_level' => $xpToNext,
            ], 'user_id = ?', [$userId]);

            // ۳. به‌روزرسانی لیدربورد
            $this->db->update('leaderboard_cache', [
                'total_games' => $totalGames,
                'total_wins' => $totalWins,
                'total_points' => $totalPoints,
                'win_rate' => $totalGames > 0 ? ($totalWins / $totalGames) * 100 : 0,
            ], 'user_id = ?', [$userId]);

            // ۴. 🆕 باز محاسبه مدال‌ها (Achievements)
            $achievements = $this->db->fetchAll("SELECT * FROM achievements WHERE is_active = 1");
            foreach ($achievements as $ach) {
                $currentValue = $this->getCurrentValue($ach['condition_type'], $userId);
                if ($currentValue >= $ach['condition_value']) {
                    $this->db->query(
                        "INSERT INTO user_achievements (user_id, achievement_id, progress, is_completed, unlocked_at)
                     VALUES (?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE progress = VALUES(progress), is_completed = 1, unlocked_at = NOW()",
                        [$userId, $ach['id'], $currentValue]
                    );
                } else {
                    $this->db->query(
                        "UPDATE user_achievements SET progress = ?, is_completed = 0, unlocked_at = NULL WHERE user_id = ? AND achievement_id = ?",
                        [$currentValue, $userId, $ach['id']]
                    );
                }
            }
            // ۵. باز محاسبه القاب (Titles) - نسخه بهبود یافته
            $titles = $this->db->fetchAll("SELECT * FROM titles WHERE is_active = 1");
            $validTitles = [];

            foreach ($titles as $title) {
                $currentValue = $this->getCurrentValue($title['condition_type'], $userId);

                // 🆕 لاگ برای debug
                error_log("Title check: {$title['name']} - current: {$currentValue}, required: {$title['condition_value']}");

                if ($currentValue >= $title['condition_value']) {
                    $validTitles[] = $title;

                    // اطمینان از وجود در user_titles
                    $this->db->query(
                        "INSERT INTO user_titles (user_id, title_id, is_active, unlocked_at)
             VALUES (?, ?, 0, NOW()) 
             ON DUPLICATE KEY UPDATE unlocked_at = COALESCE(unlocked_at, NOW())",
                        [$userId, $title['id']]
                    );
                } else {
                    // حذف لقب‌های نامعتبر
                    $this->db->query("DELETE FROM user_titles WHERE user_id = ? AND title_id = ?", [$userId, $title['id']]);
                }
            }

            // 🆕 فعال‌سازی بهترین لقب معتبر - نسخه بهبود یافته
            if (!empty($validTitles)) {
                // 🎯 Sort: اول bonus_points (نزولی)، سپس priority (نزولی)، سپس ID (نزولی برای ثبات)
                usort($validTitles, function ($a, $b) {
                    if ($a['bonus_points'] != $b['bonus_points']) {
                        return $b['bonus_points'] <=> $a['bonus_points'];
                    }
                    if ($a['priority'] != $b['priority']) {
                        return $b['priority'] <=> $a['priority'];
                    }
                    return $b['id'] <=> $a['id']; // 🆕 Tie-breaker
                });

                $bestTitle = $validTitles[0];

                // 🆕 لاگ برای debug
                error_log("Best title selected: {$bestTitle['name']} with bonus: {$bestTitle['bonus_points']}");

                // 🆕 غیرفعال کردن همه لقب‌های دیگر
                $this->db->query(
                    "UPDATE user_titles SET is_active = 0 WHERE user_id = ? AND title_id != ?",
                    [$userId, $bestTitle['id']]
                );

                // فعال کردن بهترین
                $this->db->query(
                    "UPDATE user_titles SET is_active = 1, unlocked_at = COALESCE(unlocked_at, NOW()) 
         WHERE user_id = ? AND title_id = ?",
                    [$userId, $bestTitle['id']]
                );

                // به‌روزرسانی users.current_title_id
                $this->db->update('users', ['current_title_id' => $bestTitle['id']], 'id = ?', [$userId]);
            } else {
                $this->db->query("UPDATE user_titles SET is_active = 0 WHERE user_id = ?", [$userId]);
                $this->db->update('users', ['current_title_id' => null], 'id = ?', [$userId]);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }


    private function getCurrentValue(string $conditionType, int $userId): int
    {
        switch ($conditionType) {
            case 'total_games':
                $res = $this->db->fetchOne(
                    "SELECT COUNT(DISTINCT g.id) as count 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? AND g.status = 'finished'",
                    [$userId]
                );
                return (int)($res['count'] ?? 0);

            case 'total_wins':
                // 🆕 محاسبه صحیح کل بردها (شامل Solo و Team)
                $res = $this->db->fetchOne(
                    "SELECT COUNT(DISTINCT g.id) as count 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? 
                 AND g.status = 'finished'
                 AND (
                     (g.game_mode = 'solo' AND g.winner_participant_id = gp.id)
                     OR (g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL 
                         AND g.winner_team_id = gp.team_id)
                 )",
                    [$userId]
                );
                return (int)($res['count'] ?? 0);

            case 'total_points':
                $res = $this->db->fetchOne(
                    "SELECT SUM(gp.total_score) as total 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? AND g.status = 'finished'",
                    [$userId]
                );
                return (int)($res['total'] ?? 0);

            case 'best_streak':
                $res = $this->db->fetchOne("SELECT best_streak FROM user_streaks WHERE user_id = ?", [$userId]);
                return (int)($res['best_streak'] ?? 0);

            case 'team_wins':
                // 🆕 محاسبه صحیح بردهای تیمی
                $res = $this->db->fetchOne(
                    "SELECT COUNT(DISTINCT g.id) as count 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? 
                 AND g.game_mode = 'friendly' 
                 AND g.status = 'finished' 
                 AND g.winner_team_id IS NOT NULL
                 AND g.winner_team_id = gp.team_id",
                    [$userId]
                );
                return (int)($res['count'] ?? 0);

            case 'team_games':
                // 🆕 تعداد بازی‌های تیمی
                $res = $this->db->fetchOne(
                    "SELECT COUNT(DISTINCT g.id) as count 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? 
                 AND g.game_mode = 'friendly' 
                 AND g.status = 'finished'",
                    [$userId]
                );
                return (int)($res['count'] ?? 0);

            case 'solo_wins':
                // 🆕 محاسبه صحیح بردهای انفرادی
                $res = $this->db->fetchOne(
                    "SELECT COUNT(DISTINCT g.id) as count 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? 
                 AND g.game_mode = 'solo' 
                 AND g.status = 'finished' 
                 AND g.winner_participant_id = gp.id",
                    [$userId]
                );
                return (int)($res['count'] ?? 0);

            default:
                return 0;
        }
    }
}
