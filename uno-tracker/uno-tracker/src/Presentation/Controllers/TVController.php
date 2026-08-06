<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Core\Database;
use Application\Services\AuthService;
use Application\Services\GameService;
use Application\Services\UserService;
use Infrastructure\Repositories\GameRepository;
use Infrastructure\Repositories\CardRepository;
use Infrastructure\Repositories\WinTypeRepository;

class TVController
{
    private AuthService $auth;
    private GameService $gameService;
    private GameRepository $gameRepo;
    private UserService $userService;
    private Response $response;
    private Database $db;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->gameService = new GameService();
        $this->gameRepo = new GameRepository();
        $this->userService = new UserService();
        $this->response = new Response();
        $this->db = Database::getInstance();
    }

    /**
     * 📺 لیست بازی‌ها برای نمایش در تلویزیون
     */
    public function list(Request $request): void
    {
        $currentUser = $this->auth->user();

        if (!$currentUser) {
            $this->response->redirect('/login');
            return;
        }

        $page = max(1, (int) $request->get('page', 1));

        $filters = [
            'search' => $request->get('search', ''),
            'status' => $request->get('status', ''),
        ];

        $result = $this->gameService->getGamesList(null, $filters, $page, 20);

        $canCreate = isset($currentUser['can_create_game']) ? (bool)$currentUser['can_create_game'] : true;

        $html = View::make('pages.tv.list', [
            'title' => 'نمایش تلویزیون - لیست بازی‌ها',
            'currentUser' => $currentUser,
            'games' => $result['games'],
            'total' => $result['total'],
            'page' => $result['page'],
            'totalPages' => $result['totalPages'],
            'filters' => $filters,
            'canCreate' => $canCreate,
        ], 'tv');

        $this->response->html($html);
    }

    /**
     * 📺 نمایش یک بازی در تلویزیون
     */
    public function show(Request $request, array $params): void
    {
        $currentUser = $this->auth->user();

        if (!$currentUser) {
            $this->response->redirect('/login');
            return;
        }

        $gameId = (int) $params['id'];
        $game = $this->gameService->getGameWithDetails($gameId);

        if (!$game) {
            $this->response->status(404)->html('<h1 style="color:white;text-align:center;padding:50px;">بازی یافت نشد</h1>');
            return;
        }

        $isReferee = $game->referee_id === (int)$currentUser['id'];

        // دریافت شروع‌کننده (first_player_participant_id)
        $firstPlayer = null;
        if (!empty($game->first_player_participant_id)) {
            $firstPlayerData = $this->db->fetchOne(
                "SELECT gp.id, gp.user_id, gp.guest_name, gp.team_id, 
                        u.nickname, u.avatar_path 
                 FROM game_participants gp
                 LEFT JOIN users u ON gp.user_id = u.id 
                 WHERE gp.id = ?",
                [$game->first_player_participant_id]
            );
            if ($firstPlayerData) {
                $firstPlayer = (object) $firstPlayerData;
                // اگر کاربر مهمان است و nickname ندارد
                if (empty($firstPlayer->nickname) && !empty($firstPlayer->guest_name)) {
                    $firstPlayer->nickname = $firstPlayer->guest_name;
                }
            }
        }

        // دریافت آخرین دور و اطلاعات کارت و نوع برد
        $lastRound = null;
        $lastRoundWinType = null;
        if (!empty($game->rounds)) {
            $lastRound = end($game->rounds);

            // دریافت کارت
            if ($lastRound && $lastRound->winning_card_id) {
                $cardRepo = new CardRepository();
                $card = $cardRepo->findById($lastRound->winning_card_id);
                if ($card) {
                    $lastRound->card = $card;
                }
            }

            // دریافت نوع برد
            if ($lastRound && $lastRound->win_type_id) {
                $winTypeRepo = new WinTypeRepository();
                $winType = $winTypeRepo->findById($lastRound->win_type_id);
                if ($winType) {
                    $lastRoundWinType = (object) $winType;
                }
            }
        }
        // دریافت لقب برای هر شرکت‌کننده
        $participantTitles = [];
        foreach ($game->participants as $participant) {
            if ($participant->user_id) {
                $titleData = $this->db->fetchOne(
                    "SELECT t.id, t.name, t.icon, t.bonus_points 
             FROM titles t
             JOIN users u ON u.current_title_id = t.id
             WHERE u.id = ?",
                    [$participant->user_id]
                );
                if ($titleData) {
                    $participantTitles[$participant->id] = (object) $titleData;
                }
            }
        }
        // دریافت سطح کاربران شرکت‌کننده
        $participantLevels = [];
        foreach ($game->participants as $participant) {
            if ($participant->user_id) {
                $levelData = $this->db->fetchOne(
                    "SELECT ux.current_level, ux.total_xp, 
                    pl.title as level_title, pl.icon as level_icon, pl.color as level_color
             FROM user_xp ux
             LEFT JOIN player_levels pl ON ux.current_level = pl.level
             WHERE ux.user_id = ?",
                    [$participant->user_id]
                );
                if ($levelData) {
                    $participantLevels[$participant->id] = (object) $levelData;
                }
            }
        }
        // محاسبه maxWins
        $maxWins = $this->db->fetchOne(
            "SELECT MAX(wins_count) as max_wins FROM game_participants WHERE game_id = ?",
            [$gameId]
        );
        $maxWinsValue = max(0, (int)($maxWins['max_wins'] ?? 0));

        $html = View::make('pages.tv.show', [
            'title' => 'نمایش تلویزیون - ' . ($game->name ?: 'بازی #' . $gameId),
            'game' => $game,
            'currentUser' => $currentUser,
            'isReferee' => $isReferee,
            'maxWins' => $maxWinsValue,
            'lastRound' => $lastRound,
            'lastRoundWinType' => $lastRoundWinType,
            'firstPlayer' => $firstPlayer,
            'participantTitles' => $participantTitles,
            'participantLevels' => $participantLevels,

        ], 'tv');

        $this->response->html($html);
    }


    /**
     * 📺 دریافت partial بازی برای TV (AJAX)
     * ✅ فقط محتوای خالص (بدون اسکریپت) برمی‌گرداند
     */
    public function partial(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $game = $this->gameService->getGameWithDetails($gameId);

        if (!$game) {
            $this->response->status(404)->html('بازی یافت نشد');
            return;
        }

        $currentUser = $this->auth->user();
        $isReferee = $game->referee_id === (int)$currentUser['id'];

        // دریافت شروع‌کننده
        $firstPlayer = null;
        if (!empty($game->first_player_participant_id)) {
            $firstPlayerData = $this->db->fetchOne(
                "SELECT gp.id, gp.user_id, gp.guest_name, gp.team_id, 
                    u.nickname, u.avatar_path 
             FROM game_participants gp
             LEFT JOIN users u ON gp.user_id = u.id 
             WHERE gp.id = ?",
                [$game->first_player_participant_id]
            );
            if ($firstPlayerData) {
                $firstPlayer = (object) $firstPlayerData;
                if (empty($firstPlayer->nickname) && !empty($firstPlayer->guest_name)) {
                    $firstPlayer->nickname = $firstPlayer->guest_name;
                }
            }
        }

        // آخرین دور
        $lastRound = null;
        $lastRoundWinType = null;
        if (!empty($game->rounds)) {
            $lastRound = end($game->rounds);
            if ($lastRound && $lastRound->winning_card_id) {
                $cardRepo = new CardRepository();
                $card = $cardRepo->findById($lastRound->winning_card_id);
                if ($card) {
                    $lastRound->card = $card;
                }
            }
            if ($lastRound && $lastRound->win_type_id) {
                $winTypeRepo = new WinTypeRepository();
                $winType = $winTypeRepo->findById($lastRound->win_type_id);
                if ($winType) {
                    $lastRoundWinType = (object) $winType;
                }
            }
        }

        // دریافت لقب‌ها و سطوح
        $participantTitles = [];
        $participantLevels = [];
        foreach ($game->participants as $participant) {
            if ($participant->user_id) {
                $titleData = $this->db->fetchOne(
                    "SELECT t.id, t.name, t.icon, t.bonus_points 
                 FROM titles t
                 JOIN users u ON u.current_title_id = t.id
                 WHERE u.id = ?",
                    [$participant->user_id]
                );
                if ($titleData) {
                    $participantTitles[$participant->id] = (object) $titleData;
                }
                $levelData = $this->db->fetchOne(
                    "SELECT ux.current_level, ux.total_xp, 
                        pl.title as level_title, pl.icon as level_icon, pl.color as level_color
                 FROM user_xp ux
                 LEFT JOIN player_levels pl ON ux.current_level = pl.level
                 WHERE ux.user_id = ?",
                    [$participant->user_id]
                );
                if ($levelData) {
                    $participantLevels[$participant->id] = (object) $levelData;
                }
            }
        }

        $maxWins = $this->db->fetchOne(
            "SELECT MAX(wins_count) as max_wins FROM game_participants WHERE game_id = ?",
            [$gameId]
        );
        $maxWinsValue = max(0, (int)($maxWins['max_wins'] ?? 0));

        // ✅ فقط محتوای خالص (بدون اسکریپت) را رندر کن
        $html = View::render('pages.tv.partials.game-content', [
            'game' => $game,
            'currentUser' => $currentUser,
            'isReferee' => $isReferee,
            'maxWins' => $maxWinsValue,
            'lastRound' => $lastRound,
            'lastRoundWinType' => $lastRoundWinType,
            'firstPlayer' => $firstPlayer,
            'participantTitles' => $participantTitles,
            'participantLevels' => $participantLevels,
        ]);

        $this->response->html($html);
    }
}
