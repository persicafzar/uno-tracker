<?php

namespace Application\Services;

use Infrastructure\Repositories\GamificationRepository;
use Domain\Notification;

class NotificationService
{
    private GamificationRepository $repo;

    public function __construct()
    {
        $this->repo = new GamificationRepository();
    }

    public function notifyAchievementUnlocked(int $userId, string $achievementName, string $icon, int $xpReward): int
    {
        return $this->repo->createNotification(
            $userId,
            Notification::TYPE_ACHIEVEMENT,
            "مدال جدید: {$achievementName}",
            "تبریک! شما مدال {$icon} {$achievementName} را کسب کردید و {$xpReward} امتیاز تجربه دریافت کردید.",
            $icon,
            '/achievements#achievements-section'
        );
    }

    public function notifyTitleUnlocked(int $userId, string $titleName, string $icon): int
    {
        return $this->repo->createNotification(
            $userId,
            Notification::TYPE_TITLE,
            "عنوان جدید: {$titleName}",
            "شما عنوان {$icon} {$titleName} را کسب کردید!",
            $icon,
            '/achievements#achievements-section'
        );
    }

    public function notifyLevelUp(int $userId, int $newLevel, string $levelTitle, string $icon): int
    {
        return $this->repo->createNotification(
            $userId,
            Notification::TYPE_LEVEL_UP,
            "ارتقا به سطح {$newLevel}!",
            "تبریک! شما به سطح {$newLevel} ({$levelTitle}) رسیدید {$icon}",
            $icon,
            '/achievements#level-section'
        );
    }


    public function notifyStreak(int $userId, int $streakCount): int
    {
        $icon = '🔥';
        $title = '';
        $message = '';

        if ($streakCount >= 10) {
            $title = "زنجیره افسانه‌ای: {$streakCount} برد!";
            $message = "باورنکردنی! شما {$streakCount} برد متوالی داشته‌اید!";
        } elseif ($streakCount >= 5) {
            $title = "زنجیره عالی: {$streakCount} برد!";
            $message = "ادامه بده! {$streakCount} برد متوالی داشته‌اید.";
        } elseif ($streakCount >= 3) {
            $title = "زنجیره خوب: {$streakCount} برد!";
            $message = "شروع خوبی است! {$streakCount} برد متوالی.";
        } else {
            $title = "زنجیره جدید: {$streakCount} برد";
            $message = "شما {$streakCount} برد متوالی دارید.";
        }

        return $this->repo->createNotification(
            $userId,
            Notification::TYPE_STREAK,
            $title,
            $message,
            $icon,
            '/achievements#streak-section'
        );
    }

    public function notifySystem(int $userId, string $title, string $message, string $icon = '🔔', ?string $link = null): int
    {
        return $this->repo->createNotification(
            $userId,
            Notification::TYPE_SYSTEM,
            $title,
            $message,
            $icon,
            $link
        );
    }

    public function getUnreadNotifications(int $userId, int $limit = 10): array
    {
        return $this->repo->getUnreadNotifications($userId, $limit);
    }

    public function getAllNotifications(int $userId, int $limit = 50): array
    {
        return $this->repo->getUserNotifications($userId, $limit);
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->repo->getUnreadCount($userId);
    }

    public function markAsRead(int $notificationId, int $userId): void
    {
        $this->repo->markNotificationAsRead($notificationId, $userId);
    }

    public function markAllAsRead(int $userId): void
    {
        $this->repo->markAllNotificationsAsRead($userId);
    }
}
