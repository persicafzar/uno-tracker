<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Application\Services\AuthService;
use Application\Services\GameService;
use Application\Services\UserService;
use Infrastructure\Repositories\GameRepository;

class GameController
{
    private AuthService $auth;
    private GameService $gameService;
    private GameRepository $gameRepo;
    private UserService $userService;
    private Response $response;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->gameService = new GameService();
        $this->gameRepo = new GameRepository();
        $this->userService = new UserService();
        $this->response = new Response();
    }

    /**
     * نمایش فرم ایجاد بازی
     */
    public function create(Request $request): void
    {
        $allUsers = $this->userService->getUsersForGameSelection();
        $currentUser = $this->auth->user();
        // 🆕 بررسی مجوز ساخت بازی
        if (empty($currentUser['can_create_game'])) {
            $request->flash('error', 'شما مجوز ساخت بازی ندارید. لطفاً با مدیر سیستم تماس بگیرید.');
            $this->response->redirect('/dashboard');
            return;
        }
        $old = $request->getFlash('old', []);
        $error = $request->getFlash('error');

        $old = array_merge([
            'game_name' => '',
            'game_mode' => 'solo',
            'target_wins' => 10,
            'player_ids' => [],
            'guest_players' => [],
            'team_names' => [],
            'team_algorithm' => 'manual',
            'player_teams' => [],
        ], $old);

        $html = View::make('pages.game.create', [
            'title' => 'ایجاد بازی جدید',
            'players' => $allUsers,
            'currentUser' => $currentUser,
            'old' => $old,
            'error' => $error,
        ], 'main');

        $this->response->html($html);
    }

    /**
     * پردازش ایجاد بازی
     */
    public function store(Request $request): void
    {
        $currentUser = $this->auth->user();

        $playerIds = $request->post('player_ids', []);
        if (!is_array($playerIds)) $playerIds = [];
        $playerIds = array_map('intval', $playerIds);

        $guestPlayersRaw = $request->post('guest_players', '[]');
        $guestPlayers = json_decode($guestPlayersRaw, true);
        if (!is_array($guestPlayers)) $guestPlayers = [];

        $teamNames = $request->post('team_names', []);
        if (!is_array($teamNames)) $teamNames = [];

        $playerTeamsRaw = $request->post('player_teams', '{}');
        $playerTeams = json_decode($playerTeamsRaw, true);
        if (!is_array($playerTeams)) $playerTeams = [];

        // 🆕 دریافت ترکیب تیم‌های پیش‌نمایش شده
        $teamAssignmentsRaw = $request->post('team_assignments', '[]');
        $teamAssignments = json_decode($teamAssignmentsRaw, true);
        if (!is_array($teamAssignments)) $teamAssignments = [];

        $data = [
            'referee_id' => $currentUser['id'],
            'game_name' => trim($request->post('game_name', '')),
            'game_mode' => $request->post('game_mode', 'solo'),
            'target_wins' => (int) $request->post('target_wins', 10),
            'player_ids' => $playerIds,
            'team_names' => $teamNames,
            'team_algorithm' => $request->post('team_algorithm', 'manual'),
            'guest_players' => $guestPlayers,
            'player_teams' => $playerTeams,
            'team_assignments' => $teamAssignments, // 🆕 اضافه شد
        ];

        $result = $this->gameService->createGame($data);

        if ($result['success']) {
            if ($request->isHtmx()) {
                $this->response->htmxRedirect('/game/' . $result['game_id']);
            } else {
                $this->response->redirect('/game/' . $result['game_id']);
            }
        } else {
            $oldData = [
                'game_name' => $data['game_name'],
                'game_mode' => $data['game_mode'],
                'target_wins' => $data['target_wins'],
                'player_ids' => $data['player_ids'],
                'guest_players' => $data['guest_players'],
                'team_names' => $data['team_names'],
                'team_algorithm' => $data['team_algorithm'],
                'player_teams' => $data['player_teams'],
            ];

            if ($request->isHtmx()) {
                $html = View::render('pages.game.create', [
                    'players' => $this->userService->getUsersForGameSelection(),
                    'currentUser' => $currentUser,
                    'error' => $result['error'],
                    'old' => $oldData,
                ]);
                $this->response->status(400)->html($html);
            } else {
                $request->flash('error', $result['error']);
                $request->flash('old', $oldData);
                $this->response->redirect('/game/create');
            }
        }
    }

    /**
     * نمایش جزئیات بازی
     */
    public function show(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $game = $this->gameService->getGameWithDetails($gameId);

        if (!$game) {
            $this->response->status(404)->html('<h1>بازی یافت نشد</h1>');
            return;
        }

        $currentUser = $this->auth->user();
        $isReferee = $game->referee_id === $currentUser['id'];
        $isAdmin = $this->auth->isAdmin();

        // 🆕 محاسبه maxWins
        $db = \Core\Database::getInstance();
        $maxWins = $db->fetchOne(
            "SELECT MAX(wins_count) as max_wins FROM game_participants WHERE game_id = ?",
            [$gameId]
        );
        $maxWinsValue = max(0, (int)($maxWins['max_wins'] ?? 0));

        // 🆕 گرفتن تنظیمات امتیازدهی
        $scoringService = new \Application\Services\ScoringService();
        $scoringSettings = $scoringService->getScoringSettings();

        $isPartial = $request->get('partial') === '1' || $request->isHtmx();
        $viewPath = $isPartial ? 'pages.game.partials.game-content' : 'pages.game.show';
        $layout = $isPartial ? null : 'main';

        $html = View::make($viewPath, [
            'title' => $game->name ?: 'بازی #' . $gameId,
            'game' => $game,
            'isReferee' => $isReferee,
            'isAdmin' => $isAdmin,
            'currentUser' => $currentUser,
            'maxWins' => $maxWinsValue,
            'scoringSettings' => $scoringSettings, // 🆕 اضافه شده
        ], $layout);

        $this->response->html($html);
    }


    /**
     * لیست بازی‌های فعال
     */
    public function activeGames(Request $request): void
    {
        $games = $this->gameRepo->findActiveGames();

        if ($request->isHtmx()) {
            $html = View::render('pages.game.partials.active_list', [
                'games' => $games,
            ]);
            $this->response->html($html);
        } else {
            $html = View::make('pages.game.active_list', [
                'title' => 'بازی‌های فعال',
                'games' => $games,
            ], 'main');
            $this->response->html($html);
        }
    }

    /**
     * 🆕 لیست بازی‌ها (برای کاربران عادی)
     */
    public function list(Request $request): void
    {
        $currentUser = $this->auth->user();
        $page = max(1, (int) $request->get('page', 1));

        // گرفتن player_id از query string
        $playerId = $request->get('player_id');
        $playerId = $playerId ? (int) $playerId : null;

        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'mode' => $request->get('mode'),
            'role' => $request->get('role'),
            'result' => $request->get('result'),
        ];

        // 🆕 اصلاح: حذف آرگومان اضافی $currentUser['id']
        $result = $this->gameService->getGamesList($playerId, $filters, $page, 20);

        // گرفتن اطلاعات بازیکن انتخاب شده
        $selectedPlayer = null;
        if ($playerId) {
            $selectedPlayer = $this->gameService->getPlayerInfo($playerId);
        }

        $html = View::make('pages.game.list', [
            'title' => 'بازی‌ها',
            'currentUser' => $currentUser,
            'games' => $result['games'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['perPage'],
            'totalPages' => $result['totalPages'],
            'filters' => $filters,
            'selectedPlayer' => $selectedPlayer,
            'selectedPlayerId' => $playerId,
        ], 'main');

        $this->response->html($html);
    }

    /**
     * 🆕 AJAX: گرفتن لیست بازیکنان برای Modal
     */
    public function players(Request $request): void
    {
        $search = $request->get('search', '');
        $players = $this->gameService->getPlayersList($search, 20);

        $this->response->json(['players' => $players]);
    }

    /**
     * 🆕 پیش‌نمایش تیم‌ها (AJAX)
     */
    public function previewTeams(Request $request): void
    {
        // 🆕 دریافت player_ids - ممکن است JSON string یا array باشد
        $playerIdsRaw = $request->post('player_ids', []);

        // اگر JSON string است، decode کن
        if (is_string($playerIdsRaw)) {
            $playerIds = json_decode($playerIdsRaw, true);
            if (!is_array($playerIds)) {
                $playerIds = [];
            }
        } else {
            $playerIds = $playerIdsRaw;
        }

        // تبدیل به آرایه‌ای از اعداد
        $playerIds = array_map('intval', $playerIds);
        $playerIds = array_filter($playerIds); // حذف مقادیر خالی

        $algorithm = $request->post('algorithm', 'random');
        $teamSize = (int) $request->post('team_size', 2);

        if (empty($playerIds)) {
            $this->response->json(['success' => false, 'error' => 'بازیکنی انتخاب نشده است']);
            return;
        }

        if (count($playerIds) < 4) {
            $this->response->json(['success' => false, 'error' => 'حداقل ۴ بازیکن نیاز است']);
            return;
        }

        try {
            $teamBuilder = new \Application\Services\TeamBuilderService();
            $teams = $teamBuilder->buildTeams($playerIds, $algorithm, $teamSize);

            $this->response->json([
                'success' => true,
                'teams' => $teams,
            ]);
        } catch (\Throwable $e) {
            log_message("❌ Team preview error: " . $e->getMessage());
            $this->response->json([
                'success' => false,
                'error' => 'خطا در گروه‌بندی: ' . $e->getMessage()
            ]);
        }
    }
}
