<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Application\Services\AuthService;
use Application\Services\GamificationService;
use Application\Services\UserService;

class AchievementsController
{
    private AuthService $auth;
    private GamificationService $gamificationService;
    private UserService $userService;
    private Response $response;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->gamificationService = new GamificationService();
        $this->userService = new UserService();
        $this->response = new Response();
    }

    /**
     * نمایش صفحه دستاوردها
     */
    public function index(Request $request): void
    {
        $userId = $this->auth->id();

        $user = $this->userService->getById($userId);
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        // گرفتن همه اطلاعات گیمیفیکیشن
        $gamificationData = $this->gamificationService->getUserGamificationData($userId);

        // گرفتن نشان‌ها با گروه‌بندی
        $achievements = $this->gamificationService->getUserAchievements($userId);
        $groupedAchievements = $this->gamificationService->getAchievementService()->groupByCategory($achievements);

        // گرفتن همه سطوح
        $allLevels = $this->gamificationService->getLevelService()->getAllLevels();
        // 🆕 گرفتن لقب فعال کاربر
        $activeTitle = $this->gamificationService->getUserActiveTitleArray($userId);

        $html = View::make('pages.achievements.index', [
            'title' => 'دستاوردها',
            'user' => $user,
            'gamificationData' => $gamificationData,
            'achievements' => $achievements,
            'groupedAchievements' => $groupedAchievements,
            'allLevels' => $allLevels,
            'activeTitle' => $activeTitle,  // 🆕 اضافه شد

        ], 'main');

        $this->response->html($html);
    }

    /**
     * API: گرفتن اعلان‌ها (AJAX)
     */
    public function notifications(Request $request): void
    {
        $userId = $this->auth->id();
        $limit = (int) $request->get('limit', 20);

        $notifications = $this->gamificationService->getUserNotifications($userId, $limit);

        $html = View::render('pages.achievements.partials.notifications-list', [
            'notifications' => $notifications,
        ]);

        $this->response->html($html);
    }

    /**
     * API: علامت‌گذاری اعلان به عنوان خوانده شده (AJAX)
     */
    public function markNotificationRead(Request $request): void
    {
        $userId = $this->auth->id();
        $notificationId = (int) $request->post('notification_id');

        $this->gamificationService->getNotificationService()->markAsRead($notificationId, $userId);

        $this->response->json(['success' => true]);
    }

    /**
     * API: علامت‌گذاری همه اعلان‌ها به عنوان خوانده شده (AJAX)
     */
    public function markAllNotificationsRead(Request $request): void
    {
        $userId = $this->auth->id();

        $this->gamificationService->markAllNotificationsAsRead($userId);

        $this->response->json(['success' => true]);
    }

    /**
     * API: گرفتن تعداد اعلان‌های خوانده نشده (AJAX)
     */
    public function unreadCount(Request $request): void
    {
        $userId = $this->auth->id();
        $count = $this->gamificationService->getUnreadNotificationsCount($userId);

        $this->response->json(['count' => $count]);
    }
}
