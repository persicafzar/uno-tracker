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
     * پردازش پایان بازی
     */
    public function processGameEnd(int $userId, bool $isWinner, bool $isTeamGame = false): array
    {
        $results = [
            'xp_gained' => 0,
            'achievements_unlocked' => [],
            'level_up' => false,
            'streak_info' => null,
        ];

        // ۱. XP برای شرکت در بازی
        $xpResult = $this->levelService->rewardForGamePlayed($userId);
        $results['xp_gained'] += $xpResult['xp_gained'] ?? 0;

        // 🗑️ بخش چالش‌های بازی انجام شده حذف شد

        // ۲. در صورت برد
        if ($isWinner) {
            // XP برای برد
            $winXpResult = $this->levelService->rewardForWin($userId, $isTeamGame);
            $results['xp_gained'] += $winXpResult['xp_gained'] ?? 0;
            if (!empty($winXpResult['leveled_up'])) {
                $results['level_up'] = true;
            }

            // زنجیره پیروزی
            $streakResult = $this->streakService->recordWin($userId);
            $results['streak_info'] = $streakResult;

            // 🗑️ بخش چالش‌های برد حذف شد
            // 🗑️ بخش چالش زنجیره پیروزی حذف شد
        } else {
            // باخت: شکستن زنجیره پیروزی
            $streakResult = $this->streakService->recordLoss($userId);
            $results['streak_info'] = $streakResult;
        }

        // ۳. بررسی مدال‌ها
        $newAchievements = $this->achievementService->checkAndUpdateAchievements($userId);
        $results['achievements_unlocked'] = $newAchievements;
        foreach ($newAchievements as $achievement) {
            $results['xp_gained'] += $achievement->xp_reward;
        }

        return $results;
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
