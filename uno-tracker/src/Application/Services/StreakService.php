<?php

namespace Application\Services;

use Core\Database;
use Infrastructure\Repositories\GamificationRepository;
use Domain\UserStreak;

class StreakService
{
    private GamificationRepository $repo;
    private NotificationService $notificationService;
    private Database $db; // 🆕 اضافه شد

    public function __construct()
    {
        $this->repo = new GamificationRepository();
        $this->notificationService = new NotificationService();
        $this->db = Database::getInstance(); // 🆕 مقداردهی
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
        // 🆕 خواندن مدت زمان مجاز از تنظیمات
        $settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
        $resetHours = (int) $settingsRepo->get('streak_reset_hours', 24); // پیش‌فرض ۲۴ ساعت

        if ($lastWinAt) {
            $hoursDiff = (strtotime($now) - strtotime($lastWinAt)) / 3600;
            if ($hoursDiff > $resetHours) {
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

        // 🆕 خواندن تنظیمات
        $settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
        $resetHours = (int) $settingsRepo->get('streak_reset_hours', 24);

        if (!$streak) {
            return [
                'current_streak' => 0,
                'best_streak' => 0,
                'is_active' => false,
                'is_broken' => false,
                'last_win_at' => null,
                'hours_since_last_win' => null,
                'reset_hours' => $resetHours, // 🆕
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
            'reset_hours' => $resetHours, // 🆕
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

    /**
     * بازمحاسبه کامل زنجیره پیروزی یک کاربر از روی تاریخچه بازی‌های باقی‌مانده
     * 
     * این متد پس از حذف بازی‌های تقلبی یا تغییرات دستی در آمار، برای ترمیم زنجیره استفاده می‌شود.
     * 
     * @param int $userId شناسه کاربر
     * @return array ['current_streak' => int, 'best_streak' => int, 'last_win_at' => string|null]
     */

    /**
     * بازمحاسبه کامل زنجیره پیروزی با در نظر گرفتن تاریخ ریست
     */
    public function recalculateStreak(int $userId): array
    {
        // خواندن تاریخ ریست از تنظیمات
        $settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
        $resetDate = $settingsRepo->get('streak_reset_date', null);

        $params = [$userId];
        $resetCondition = '';
        if ($resetDate) {
            $resetCondition = " AND g.finished_at >= ?";
            $params[] = $resetDate;
        }

        // دریافت بازی‌های بعد از تاریخ ریست
        $games = $this->db->fetchAll(
            "SELECT 
            g.id,
            g.finished_at,
            gp.is_winner
         FROM games g
         INNER JOIN game_participants gp ON g.id = gp.game_id
         WHERE gp.user_id = ? 
         AND g.status = 'finished'
         AND g.finished_at IS NOT NULL
         {$resetCondition}
         ORDER BY g.finished_at ASC",
            $params
        );

        $currentStreak = 0;
        $bestStreak = 0;
        $lastWinAt = null;

        // خواندن مدت زمان مجاز از تنظیمات
        $resetHours = (int) $settingsRepo->get('streak_reset_hours', 24);
        $resetSeconds = $resetHours * 3600;

        foreach ($games as $game) {
            if ($game['is_winner']) {
                if ($lastWinAt === null) {
                    $currentStreak = 1;
                } else {
                    $timeDiff = strtotime($game['finished_at']) - strtotime($lastWinAt);
                    if ($timeDiff > $resetSeconds) {
                        $currentStreak = 1;
                    } else {
                        $currentStreak++;
                    }
                }
                $lastWinAt = $game['finished_at'];
                $bestStreak = max($bestStreak, $currentStreak);
            } else {
                $currentStreak = 0;
                $lastWinAt = null; // باخت باعث ریست کامل می‌شود
            }
        }

        // ذخیره در دیتابیس
        $existing = $this->db->fetchOne(
            "SELECT user_id FROM user_streaks WHERE user_id = ?",
            [$userId]
        );

        $data = [
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak,
            'last_win_at' => $lastWinAt,
            'streak_broken_at' => ($currentStreak == 0 && !empty($games)) ? date('Y-m-d H:i:s') : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->db->update('user_streaks', $data, 'user_id = ?', [$userId]);
        } else {
            $data['user_id'] = $userId;
            $this->db->insert('user_streaks', $data);
        }

        return [
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak,
            'last_win_at' => $lastWinAt,
        ];
    }
}
