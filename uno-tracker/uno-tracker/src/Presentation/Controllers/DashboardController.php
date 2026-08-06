<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Application\Services\AuthService;
use Application\Services\DashboardService;
use Application\Services\UserService;
use Application\Services\GamificationService;


class DashboardController
{
    private AuthService $auth;
    private DashboardService $dashboardService;
    private UserService $userService;
    private Response $response;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->dashboardService = new DashboardService();
        $this->userService = new UserService();
        $this->response = new Response();
    }

    /**
     * نمایش داشبورد اصلی
     */
    public function index(Request $request): void
    {
        $userId = $this->auth->id();

        $user = $this->userService->getById($userId);

        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        $stats = $this->dashboardService->getUserStats($userId);
        $recentGames = $this->dashboardService->getRecentGames($userId, 10);
        $progressData = $this->dashboardService->getProgressData($userId, 30);
        $winDistribution = $this->dashboardService->getWinDistribution($userId);
        $weeklyStats = $this->dashboardService->getWeeklyStats($userId);
        $monthlySummary = $this->dashboardService->getMonthlySummary($userId, 6);
        $friendsComparison = $this->dashboardService->getFriendsComparison($userId, 'all', 'rivals');

        $gamificationService = new GamificationService();
        $gamificationData = $gamificationService->getUserGamificationData($userId);

        $recentPlayers = $this->dashboardService->getTopPlayers(10);

        // 🆕 گرفتن کارت‌ها و نوع‌های برد
        $cards = $this->dashboardService->getCards();
        $winTypes = $this->dashboardService->getWinTypes();

        $html = View::make('pages.dashboard.index', [
            'title' => 'داشبورد',
            'user' => $user,
            'stats' => $stats,
            'recentGames' => $recentGames,
            'progressData' => $progressData,
            'winDistribution' => $winDistribution,
            'weeklyStats' => $weeklyStats,
            'monthlySummary' => $monthlySummary,
            'friendsComparison' => $friendsComparison,
            'currentUserId' => $userId,
            'gamificationData' => $gamificationData,
            'recentPlayers' => $recentPlayers,
            'cards' => $cards,          // 🆕
            'winTypes' => $winTypes,    // 🆕
        ], 'main');

        $this->response->html($html);
    }

    /**
     * 🆕 API: گرفتن مقایسه با رقبا (AJAX)
     */
    public function friendsComparison(Request $request): void
    {
        $userId = $this->auth->id();
        $period = $request->get('period', 'all');
        $mode = $request->get('mode', 'rivals');

        // 🆕 اعتبارسنجی period
        if (!in_array($period, ['all', 'month', '3months', '6months'])) {
            $period = 'all';
        }

        // 🆕 اعتبارسنجی mode
        if (!in_array($mode, ['rivals', 'all'])) {
            $mode = 'rivals';
        }

        $friendsComparison = $this->dashboardService->getFriendsComparison($userId, $period, $mode);

        $html = View::render('pages.dashboard.partials.friends-comparison', [
            'friendsComparison' => $friendsComparison,
            'currentUserId' => $userId,
        ]);

        $this->response->html($html);
    }

    /**
     * API: گرفتن خلاصه ماهانه (AJAX)
     */
    public function monthlySummary(Request $request): void
    {
        $userId = $this->auth->id();
        $months = (int) $request->get('months', 6);

        $monthlySummary = $this->dashboardService->getMonthlySummary($userId, $months);

        $html = View::render('pages.dashboard.partials.monthly-summary', [
            'monthlySummary' => $monthlySummary,
        ]);

        $this->response->html($html);
    }
}
