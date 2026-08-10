<?php

namespace Application\Services;

use Infrastructure\Repositories\GamificationRepository;

class GamificationService
{
    private GamificationRepository $repo;
    private AchievementService $achievementService;
    private LevelService $levelService;
    private StreakService $streakService;
    private NotificationService $notificationService;


    public function __construct()
    {
        $this->repo = new GamificationRepository();
        $this->notificationService = new NotificationService();
        $this->levelService = new LevelService();
        $this->streakService = new StreakService();
        $this->achievementService = new AchievementService();
        // 🗑️ ChallengeService حذف شد
    }

    /**
     * پردازش پایان بازی - 🆕 نسخه نهایی با پشتیبانی از القاب
     */
    public function processGameEnd(int $userId, bool $isWinner, bool $isTeamGame = false): array
    {
        $results = [
            'xp_gained' => 0,
            'achievements_unlocked' => [],
            'titles_unlocked' => [],      // 🆕 القاب جدید کسب شده
            'new_active_title' => null,   // 🆕 لقب فعال جدید
            'level_up' => false,
            'streak_info' => null,
        ];

        // ۱. XP برای شرکت در بازی
        $xpResult = $this->levelService->rewardForGamePlayed($userId);
        $results['xp_gained'] += $xpResult['xp_gained'] ?? 0;

        // ۲. در صورت برد
        if ($isWinner) {
            $winXpResult = $this->levelService->rewardForWin($userId, $isTeamGame);
            $results['xp_gained'] += $winXpResult['xp_gained'] ?? 0;
            if (!empty($winXpResult['leveled_up'])) {
                $results['level_up'] = true;
            }

            $streakResult = $this->streakService->recordWin($userId);
            $results['streak_info'] = $streakResult;
        } else {
            $streakResult = $this->streakService->recordLoss($userId);
            $results['streak_info'] = $streakResult;
        }

        // ۳. بررسی نشان‌ها (Achievements)
        $newAchievements = $this->achievementService->checkAndUpdateAchievements($userId);
        $results['achievements_unlocked'] = $newAchievements;
        foreach ($newAchievements as $achievement) {
            $results['xp_gained'] += $achievement->xp_reward;
        }

        // 🆕 ۴. بررسی و اعطای القاب (Titles) - بخش جدید
        $titleResult = $this->checkAndUpdateTitles($userId);
        $results['titles_unlocked'] = $titleResult['new_titles'] ?? [];
        $results['new_active_title'] = $titleResult['active_title'] ?? null;

        // 🆕 ۵. ارسال نوتیفیکیشن برای القاب جدید
        foreach ($results['titles_unlocked'] as $title) {
            $this->repo->createNotification(
                $userId,
                'title_unlocked',
                '🏆 لقب جدید: ' . ($title['name'] ?? ''),
                'شما لقب "' . ($title['name'] ?? '') . '" را کسب کردید!' .
                    (($title['bonus_points'] ?? 0) > 0 ? ' (+' . $title['bonus_points'] . ' امتیاز بونوس در هر برد)' : ''),
                $title['icon'] ?? '🏆',
                '/achievements#titles'
            );
        }

        return $results;
    }

    /**
     * 🆕 بررسی و اعطای القاب (Titles) برای کاربر
     * 
     * این متد منطق RecalculateUserService::recalculateAll را برای القاب پیاده‌سازی می‌کند
     * تا القاب در لحظه (real-time) اعطا شوند.
     * 
     * @param int $userId شناسه کاربر
     * @return array شامل new_titles (القای جدید) و active_title (لقب فعال فعلی)
     */
    public function checkAndUpdateTitles(int $userId): array
    {
        $db = \Core\Database::getInstance();

        // ۱. گرفتن همه القاب فعال
        $titles = $db->fetchAll(
            "SELECT * FROM titles WHERE is_active = 1 ORDER BY bonus_points DESC, priority DESC"
        );

        if (empty($titles)) {
            return ['new_titles' => [], 'active_title' => null];
        }

        $validTitles = [];
        $newTitles = [];

        // ۲. بررسی هر لقب
        foreach ($titles as $title) {
            $currentValue = $this->getCurrentValueForTitle($title['condition_type'], $userId);

            if ($currentValue >= $title['condition_value']) {
                $validTitles[] = $title;

                // بررسی اینکه آیا قبلاً این لقب را داشته یا نه
                $existing = $db->fetchOne(
                    "SELECT id FROM user_titles WHERE user_id = ? AND title_id = ?",
                    [$userId, $title['id']]
                );

                if (!$existing) {
                    // 🆕 اعطای لقب جدید
                    $db->insert('user_titles', [
                        'user_id' => $userId,
                        'title_id' => $title['id'],
                        'is_active' => 0,
                        'unlocked_at' => date('Y-m-d H:i:s'),
                    ]);
                    $newTitles[] = $title;
                    error_log("🏆 Title awarded to user {$userId}: {$title['name']} (current: {$currentValue}, required: {$title['condition_value']})");
                } else {
                    // اطمینان از اینکه unlocked_at ست شده
                    if (empty($existing['unlocked_at'] ?? null)) {
                        $db->update(
                            'user_titles',
                            ['unlocked_at' => date('Y-m-d H:i:s')],
                            'user_id = ? AND title_id = ?',
                            [$userId, $title['id']]
                        );
                    }
                }
            } else {
                // 🆕 حذف لقب‌هایی که دیگر شرایطشان برآورده نمی‌شود
                $db->query(
                    "DELETE FROM user_titles WHERE user_id = ? AND title_id = ?",
                    [$userId, $title['id']]
                );
            }
        }

        // ۳. انتخاب بهترین لقب معتبر به عنوان لقب فعال
        $activeTitle = null;
        if (!empty($validTitles)) {
            // Sort: بالاترین bonus_points، سپس بالاترین priority، سپس بالاترین ID
            usort($validTitles, function ($a, $b) {
                if ($a['bonus_points'] != $b['bonus_points']) {
                    return $b['bonus_points'] <=> $a['bonus_points'];
                }
                if ($a['priority'] != $b['priority']) {
                    return $b['priority'] <=> $a['priority'];
                }
                return $b['id'] <=> $a['id'];
            });

            $bestTitle = $validTitles[0];
            $activeTitle = $bestTitle;

            // غیرفعال کردن همه لقب‌ها
            $db->query(
                "UPDATE user_titles SET is_active = 0 WHERE user_id = ?",
                [$userId]
            );

            // فعال کردن بهترین
            $db->query(
                "UPDATE user_titles SET is_active = 1 WHERE user_id = ? AND title_id = ?",
                [$userId, $bestTitle['id']]
            );

            // به‌روزرسانی users.current_title_id
            $db->update(
                'users',
                ['current_title_id' => $bestTitle['id']],
                'id = ?',
                [$userId]
            );

            error_log("⭐ Active title set for user {$userId}: {$bestTitle['name']} (bonus: {$bestTitle['bonus_points']})");
        } else {
            // هیچ لقبی معتبر نیست - غیرفعال کردن همه
            $db->query(
                "UPDATE user_titles SET is_active = 0 WHERE user_id = ?",
                [$userId]
            );
            $db->update(
                'users',
                ['current_title_id' => null],
                'id = ?',
                [$userId]
            );
        }

        return [
            'new_titles' => $newTitles,
            'active_title' => $activeTitle,
        ];
    }

    /**
     * 🆕 گرفتن مقدار فعلی برای شرط لقب (با پشتیبانی کامل از حالت تیمی)
     * 
     * این متد دقیقاً الگوی RecalculateUserService::getCurrentValue را پیاده‌سازی می‌کند
     */
    private function getCurrentValueForTitle(string $conditionType, int $userId): int
    {
        $db = \Core\Database::getInstance();

        switch ($conditionType) {
            case 'total_games':
                $res = $db->fetchOne(
                    "SELECT COUNT(DISTINCT g.id) as count 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? AND g.status = 'finished'",
                    [$userId]
                );
                return (int)($res['count'] ?? 0);

            case 'total_wins':
                // 🎯 محاسبه صحیح کل بردها (Solo + Team)
                $res = $db->fetchOne(
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
                $res = $db->fetchOne(
                    "SELECT SUM(gp.total_score) as total 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? AND g.status = 'finished'",
                    [$userId]
                );
                return (int)($res['total'] ?? 0);

            case 'best_streak':
                $res = $db->fetchOne(
                    "SELECT best_streak FROM user_streaks WHERE user_id = ?",
                    [$userId]
                );
                return (int)($res['best_streak'] ?? 0);

            case 'current_streak':
            case 'win_streak':
                $res = $db->fetchOne(
                    "SELECT current_streak FROM user_streaks WHERE user_id = ?",
                    [$userId]
                );
                return (int)($res['current_streak'] ?? 0);

            case 'team_wins':
                // 🎯 محاسبه صحیح بردهای تیمی
                $res = $db->fetchOne(
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

            case 'solo_wins':
                $res = $db->fetchOne(
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

            case 'team_games':
                $res = $db->fetchOne(
                    "SELECT COUNT(DISTINCT g.id) as count 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? 
                 AND g.game_mode = 'friendly' 
                 AND g.status = 'finished'",
                    [$userId]
                );
                return (int)($res['count'] ?? 0);

            case 'solo_games':
                $res = $db->fetchOne(
                    "SELECT COUNT(DISTINCT g.id) as count 
                 FROM games g 
                 JOIN game_participants gp ON g.id = gp.game_id 
                 WHERE gp.user_id = ? 
                 AND g.game_mode = 'solo' 
                 AND g.status = 'finished'",
                    [$userId]
                );
                return (int)($res['count'] ?? 0);

            default:
                error_log("⚠️ Unknown title condition_type: {$conditionType}");
                return 0;
        }
    }

    /**
     * گرفتن همه اطلاعات گیمیفیکیشن کاربر
     */
    public function getUserGamificationData(int $userId): array
    {
        return [
            'xp_info' => $this->levelService->getUserXpInfo($userId),
            'streak_info' => $this->streakService->getUserStreakInfo($userId),
            'achievements_stats' => $this->achievementService->getAchievementsStats($userId),
            'notifications_count' => $this->notificationService->getUnreadCount($userId),
        ];
    }

    public function getUserAchievements(int $userId): array
    {
        return $this->achievementService->getUserAchievements($userId);
    }

    // 🗑️ متدهای getUserChallenges و getAllUserChallenges حذف شدند

    public function getUserNotifications(int $userId, int $limit = 50): array
    {
        return $this->notificationService->getAllNotifications($userId, $limit);
    }

    public function getUnreadNotificationsCount(int $userId): int
    {
        return $this->notificationService->getUnreadCount($userId);
    }

    public function markAllNotificationsAsRead(int $userId): void
    {
        $this->notificationService->markAllAsRead($userId);
    }

    public function getUserLevelInfo(int $userId): array
    {
        return $this->levelService->getUserXpInfo($userId);
    }

    public function getUserStreakInfo(int $userId): array
    {
        return $this->streakService->getUserStreakInfo($userId);
    }

    // Getters
    public function getAchievementService(): AchievementService
    {
        return $this->achievementService;
    }
    public function getLevelService(): LevelService
    {
        return $this->levelService;
    }
    public function getStreakService(): StreakService
    {
        return $this->streakService;
    }
    // 🗑️ getChallengeService حذف شد
    public function getNotificationService(): NotificationService
    {
        return $this->notificationService;
    }

    /**
     * 🆕 گرفتن لقب فعال کاربر
     */
    public function getUserActiveTitle(int $userId): ?\Domain\Title
    {
        return $this->repo->getUserActiveTitle($userId);
    }
    /**
     * 🆕 گرفتن لقب فعال کاربر
     */
    public function getUserActiveTitleArray(int $userId): ?array
    {
        return $this->repo->getUserActiveTitleArray($userId);
    }
}
