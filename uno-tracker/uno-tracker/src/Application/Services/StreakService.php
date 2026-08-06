<?php

namespace Application\Services;

use Infrastructure\Repositories\GamificationRepository;
use Domain\UserStreak;

class StreakService
{
    private GamificationRepository $repo;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->repo = new GamificationRepository();
        $this->notificationService = new NotificationService();
    }

    /**
     * ثبت برد و به‌روزرسانی استریک
     */
    public function recordWin(int $userId): array
    {
        $streak = $this->repo->getUserStreak($userId);
        
        $currentStreak = $streak ? $streak->current_streak : 0;
        $bestStreak = $streak ? $streak->best_streak : 0;
        $lastWinAt = $streak ? $streak->last_win_at : null;
        
        $now = date('Y-m-d H:i:s');
        $streakBroken = false;

        // بررسی اینکه آیا استریک شکسته شده (بیش از ۲۴ ساعت از آخرین برد)
        if ($lastWinAt) {
            $hoursDiff = (strtotime($now) - strtotime($lastWinAt)) / 3600;
            if ($hoursDiff > 24) {
                $streakBroken = true;
                $currentStreak = 0;
            }
        }

        // افزایش استریک
        $currentStreak++;
        $bestStreak = max($bestStreak, $currentStreak);

        // ذخیره
        $this->repo->upsertUserStreak(
            $userId,
            $currentStreak,
            $bestStreak,
            $now,
            $streakBroken ? $now : null
        );

        // ارسال اعلان برای استریک‌های مهم
        $shouldNotify = in_array($currentStreak, [3, 5, 10, 15, 20, 25, 50, 100]);
        if ($shouldNotify) {
            $this->notificationService->notifyStreak($userId, $currentStreak);
        }

        return [
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak,
            'streak_broken' => $streakBroken,
            'is_milestone' => $shouldNotify,
        ];
    }

    /**
     * ثبت باخت و شکستن استریک
     */
    public function recordLoss(int $userId): array
    {
        $streak = $this->repo->getUserStreak($userId);
        
        $currentStreak = $streak ? $streak->current_streak : 0;
        $bestStreak = $streak ? $streak->best_streak : 0;
        $lastWinAt = $streak ? $streak->last_win_at : null;

        // اگر استریک فعال بود، شکسته می‌شود
        $wasActive = $streak && $streak->isActive();

        $this->repo->upsertUserStreak(
            $userId,
            0, // استریک فعلی ریست می‌شود
            $bestStreak,
            $lastWinAt,
            $wasActive ? date('Y-m-d H:i:s') : null
        );

        return [
            'current_streak' => 0,
            'best_streak' => $bestStreak,
            'streak_broken' => $wasActive,
        ];
    }

    /**
     * گرفتن اطلاعات استریک کاربر
     */
    public function getUserStreakInfo(int $userId): array
    {
        $streak = $this->repo->getUserStreak($userId);

        if (!$streak) {
            return [
                'current_streak' => 0,
                'best_streak' => 0,
                'is_active' => false,
                'is_broken' => false,
                'last_win_at' => null,
                'hours_since_last_win' => null,
            ];
        }

        $hoursSinceLastWin = null;
        if ($streak->last_win_at) {
            $hoursSinceLastWin = round((time() - strtotime($streak->last_win_at)) / 3600, 1);
        }

        return [
            'current_streak' => $streak->current_streak,
            'best_streak' => $streak->best_streak,
            'is_active' => $streak->isActive(),
            'is_broken' => $streak->isBroken(),
            'last_win_at' => $streak->last_win_at,
            'hours_since_last_win' => $hoursSinceLastWin,
        ];
    }

    /**
     * بررسی اینکه آیا استریک هنوز فعال است
     */
    public function isStreakActive(int $userId): bool
    {
        $streak = $this->repo->getUserStreak($userId);
        return $streak && $streak->isActive();
    }
}