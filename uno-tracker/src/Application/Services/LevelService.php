<?php

namespace Application\Services;

use Infrastructure\Repositories\GamificationRepository;
use Domain\PlayerLevel;
use Domain\UserXP;

class LevelService
{
    private GamificationRepository $repo;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->repo = new GamificationRepository();
        $this->notificationService = new NotificationService();
    }

    /**
     * اضافه کردن XP به کاربر
     * @return array ['old_level' => int, 'new_level' => int, 'leveled_up' => bool, 'total_xp' => int]
     */
    public function addXp(int $userId, int $amount, bool $sendNotification = true): array
    {
        if ($amount <= 0) {
            return ['error' => 'XP باید مثبت باشد'];
        }

        // گرفتن XP فعلی
        $userXp = $this->repo->getUserXp($userId);
        $currentXp = $userXp ? $userXp->total_xp : 0;
        $oldLevel = $userXp ? $userXp->current_level : 1;

        // XP جدید
        $newXp = $currentXp + $amount;

        // محاسبه سطح جدید
        $newLevelData = $this->repo->getLevelByXp($newXp);
        $newLevel = $newLevelData ? $newLevelData->level : 1;

        // محاسبه XP مورد نیاز برای سطح بعدی
        $xpToNext = $newLevelData ? $newLevelData->getXpToNextLevel($newXp) : 0;

        // ذخیره
        $this->repo->upsertUserXp($userId, $newXp, $newLevel, $xpToNext);

        $leveledUp = $newLevel > $oldLevel;

        // ارسال اعلان در صورت ارتقا سطح
        if ($leveledUp && $sendNotification && $newLevelData) {
            $this->notificationService->notifyLevelUp(
                $userId,
                $newLevel,
                $newLevelData->title ?? "سطح {$newLevel}",
                $newLevelData->icon
            );
        }

        return [
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'leveled_up' => $leveledUp,
            'total_xp' => $newXp,
            'xp_gained' => $amount,
            'xp_to_next_level' => $xpToNext,
        ];
    }

    /**
     * گرفتن اطلاعات XP و سطح کاربر - 🆕 اصلاح شده
     */
    public function getUserXpInfo(int $userId): array
    {
        $userXp = $this->repo->getUserXp($userId);

        if (!$userXp) {
            // کاربر هنوز XP ندارد
            $firstLevel = $this->repo->getLevelByXp(0);
            return [
                'total_xp' => 0,
                'current_level' => 1,
                'level_data' => $firstLevel,
                'xp_to_next_level' => $firstLevel ? $firstLevel->getXpToNextLevel(0) : 100,
                'progress_percentage' => 0,
            ];
        }

        // 🆕 همیشه سطح را از روی XP محاسبه کن (نه از current_level ذخیره شده)
        $levelData = $this->repo->getLevelByXp($userXp->total_xp);

        // اگر سطحی پیدا نشد، از سطح 1 استفاده کن
        if (!$levelData) {
            $levelData = $this->repo->getLevelByXp(0);
        }

        // 🆕 محاسبه درصد پیشرفت
        $progress = $levelData
            ? PlayerLevel::calculateProgress($userXp->total_xp, $levelData->min_xp, $levelData->max_xp)
            : 100;

        // 🆕 سطح صحیح از level_data
        $correctLevel = $levelData ? $levelData->level : 1;

        return [
            'total_xp' => $userXp->total_xp,
            'current_level' => $correctLevel, // 🆕 استفاده از سطح محاسبه شده
            'level_data' => $levelData,
            'xp_to_next_level' => $levelData ? $levelData->getXpToNextLevel($userXp->total_xp) : 100,
            'progress_percentage' => round($progress, 1),
        ];
    }

    /**
     * گرفتن همه سطوح
     */
    public function getAllLevels(): array
    {
        return $this->repo->findAllLevels();
    }

    /**
     * گرفتن سطح بر اساس XP
     */
    public function getLevelByXp(int $xp): ?PlayerLevel
    {
        return $this->repo->getLevelByXp($xp);
    }

    /**
     * XP پاداش برای فعالیت‌های مختلف
     */
    public const XP_FOR_GAME_PLAYED = 5;
    public const XP_FOR_WIN = 15;
    public const XP_FOR_TEAM_WIN = 20;
    public const XP_FOR_STREAK_3 = 10;
    public const XP_FOR_STREAK_5 = 25;
    public const XP_FOR_STREAK_10 = 50;

    /**
     * دادن XP برای بازی
     */
    public function rewardForGamePlayed(int $userId): array
    {
        return $this->addXp($userId, self::XP_FOR_GAME_PLAYED);
    }

    /**
     * دادن XP برای برد
     */
    public function rewardForWin(int $userId, bool $isTeamGame = false): array
    {
        $xp = $isTeamGame ? self::XP_FOR_TEAM_WIN : self::XP_FOR_WIN;
        return $this->addXp($userId, $xp);
    }
}
