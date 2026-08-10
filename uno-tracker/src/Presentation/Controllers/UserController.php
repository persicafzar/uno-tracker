<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Core\Database;
use Application\Services\AuthService;
use Application\Services\UserService;
use Application\Services\UserStatsService;
use Application\Services\GamificationService;

class UserController
{
    private AuthService $auth;
    private UserService $userService;
    private Response $response;
    private Database $db;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->userService = new UserService();
        $this->response = new Response();
        $this->db = Database::getInstance();
    }

    /**
     * نمایش پروفایل کاربر دیگر
     */
    public function show(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $currentUserId = $this->auth->id();

        // کاربر نمی‌تواند پروفایل خودش را از این route ببیند
        if ($userId === $currentUserId) {
            $this->response->redirect('/profile');
            return;
        }

        $profile = $this->userService->getUserProfile($userId);

        if (!$profile) {
            $this->response->status(404)->html('<h1>کاربر یافت نشد</h1>');
            return;
        }
        // ✅ اضافه کردن مجوزهای کاربر
        $permissions = $this->db->fetchOne(
            "SELECT can_create_game, can_join_game FROM users WHERE id = ?",
            [$userId]
        );
        $profile['can_create_game'] = (int)($permissions['can_create_game'] ?? 0);
        $profile['can_join_game'] = (int)($permissions['can_join_game'] ?? 1);
        // گرفتن دستاوردها
        $gamificationService = new GamificationService();
        $achievements = $gamificationService->getAchievementService()->getCompletedAchievements($userId);
        $profile['achievements'] = $achievements;

        // 🆕 گرفتن اطلاعات عنوان فعلی کاربر
        $titleInfo = $this->db->fetchOne(
            "SELECT t.id, t.name, t.icon, t.bonus_points, t.description
             FROM users u
             LEFT JOIN titles t ON u.current_title_id = t.id
             WHERE u.id = ?",
            [$userId]
        );
        $profile['title_info'] = $titleInfo;

        // 🆕 گرفتن همه عناوین کسب شده توسط کاربر
        $userTitles = $this->db->fetchAll(
            "SELECT t.id, t.name, t.icon, t.bonus_points, t.description, ut.unlocked_at
             FROM user_titles ut
             JOIN titles t ON ut.title_id = t.id
             WHERE ut.user_id = ?
             ORDER BY t.priority DESC, ut.unlocked_at ASC",
            [$userId]
        );
        $profile['user_titles'] = $userTitles;

        // 🆕 گرفتن آمار برای نمودارها
        $statsService = new UserStatsService();
        $roundStats = $statsService->getRoundStats($userId);
        $profile['stats_by_status'] = $statsService->getStatsByStatus($userId);
        $profile['stats_by_mode'] = $statsService->getStatsByMode($userId);
        $profile['daily_stats'] = $statsService->getDailyStats($userId, 30);
        $profile['day_of_week_stats'] = $statsService->getDayOfWeekStats($userId);
        $profile['card_stats'] = $statsService->getCardUsageStats($userId);

        // گرفتن همه بازی‌ها با Pagination
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $allGames = $this->db->fetchAll(
            "SELECT
                g.id,
                g.name,
                g.game_mode,
                g.target_wins,
                g.status,
                g.created_at,
                g.finished_at,
                g.winner_participant_id,
                gp.wins_count,
                gp.total_score,
                gp.id as participant_id,
                (SELECT COUNT(*) FROM game_participants gp2 WHERE gp2.game_id = g.id) as total_players,
                (SELECT COUNT(*) FROM teams t WHERE t.game_id = g.id) as total_teams,
                (g.winner_participant_id = gp.id) as is_winner
             FROM games g
             INNER JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ?
             ORDER BY g.created_at DESC
             LIMIT ? OFFSET ?",
            [$userId, $perPage + 1, $offset]
        );

        $hasMore = count($allGames) > $perPage;
        $games = array_slice($allGames, 0, $perPage);
        $history = $games;

        foreach ($history as &$game) {
            if (!isset($game['is_winner'])) {
                $game['is_winner'] = false;
            }
        }

        $html = View::make('pages.profile.show', [
            'title' => 'پروفایل ' . ($profile['nickname'] ?? ''),
            'profile' => $profile,
            'isOwn' => false,
            'stats' => [
                'total_games' => $profile['total_games'],
                'total_wins' => $profile['total_wins'],
            ],
            'history' => $history,
            'games' => $games,
            'page' => $page,
            'hasMore' => $hasMore,
            'achievements' => $achievements,
            'roundStats' => $roundStats,
        ], 'main');

        $this->response->html($html);
    }

    /**
     * نمایش پروفایل خلاصه کاربر (برای Drawer)
     */
    public function partial(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $gameId = (int) $request->get('game_id', 0); // 🆕 دریافت game_id از query string

        $profile = $this->userService->getUserProfile($userId);

        if (!$profile) {
            $this->response->status(404)->html('<div class="text-center text-red-600 py-8">کاربر یافت نشد</div>');
            return;
        }

        // 🆕 گرفتن اطلاعات عنوان فعلی کاربر
        $titleInfo = $this->db->fetchOne(
            "SELECT t.id, t.name, t.icon, t.bonus_points
         FROM users u
         LEFT JOIN titles t ON u.current_title_id = t.id
         WHERE u.id = ?",
            [$userId]
        );
        $profile['title_info'] = $titleInfo;

        // 🆕 گرفتن اطلاعات بازی فعلی کاربر
        // اگر game_id داده شده است، همان بازی را در اولویت قرار بده (حتی اگر تمام شده باشد)
        if ($gameId > 0) {
            $currentGame = $this->db->fetchOne(
                "SELECT 
                g.id, g.name, g.game_mode, g.target_wins, g.status,
                gp.wins_count, gp.total_score
            FROM games g
            JOIN game_participants gp ON g.id = gp.game_id
            WHERE gp.user_id = ? AND g.id = ?
            LIMIT 1",
                [$userId, $gameId]
            );
        } else {
            // اگر game_id داده نشده، آخرین بازی فعال یا متوقف را بگیر
            $currentGame = $this->db->fetchOne(
                "SELECT 
                g.id, g.name, g.game_mode, g.target_wins, g.status,
                gp.wins_count, gp.total_score
            FROM games g
            JOIN game_participants gp ON g.id = gp.game_id
            WHERE gp.user_id = ? AND g.status IN ('active', 'paused')
            ORDER BY g.created_at DESC
            LIMIT 1",
                [$userId]
            );
        }

        if ($currentGame) {
            $profile['current_game'] = [
                'name' => $currentGame['name'] ?? 'بازی بدون نام',
                'mode' => $currentGame['game_mode'],
                'target_wins' => $currentGame['target_wins'],
                'status' => $currentGame['status'],
                'wins_in_game' => (int) $currentGame['wins_count'],
                'score_in_game' => (float) $currentGame['total_score'],
            ];
        } else {
            $profile['current_game'] = null;
        }

        $html = View::render('pages.partials.user-profile-partial', [
            'profile' => $profile,
        ]);

        $this->response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $this->response->header('Pragma', 'no-cache');
        $this->response->header('Expires', '0');
        $this->response->html($html);
    }
    /**
     * 🆕 نمایش لیست بازیکنان
     */
    public function list(Request $request): void
    {
        $filters = [
            'search' => $request->get('search', ''),
            'level' => $request->get('level', ''),
            'status' => $request->get('status', ''),
        ];

        $sortBy = $request->get('sort', 'newest');
        $page = max(1, (int) $request->get('page', 1));

        $result = $this->userService->getUsersList($filters, $sortBy, $page, 20);
        $allLevels = $this->userService->getAllLevels();

        $html = View::make('pages.users.list', [
            'title' => 'لیست بازیکنان',
            'users' => $result['users'],
            'total' => $result['total'],
            'page' => $result['page'],
            'totalPages' => $result['totalPages'],
            'filters' => $filters,
            'sortBy' => $sortBy,
            'allLevels' => $allLevels,
            'currentUserId' => $this->auth->id(),
        ], 'main');

        $this->response->html($html);
    }
}
