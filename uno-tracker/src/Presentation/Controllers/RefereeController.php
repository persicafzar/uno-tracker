<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Application\Services\AuthService;
use Application\Services\GameService;
use Application\Services\RefereeService;
use Application\Services\SSEService;

class RefereeController
{
    private AuthService $auth;
    private GameService $gameService;
    private RefereeService $refereeService;
    private Response $response;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->gameService = new GameService();
        $this->refereeService = new RefereeService();
        $this->response = new Response();
    }

    /**
     * 🆕 متد کمکی برای محاسبه maxWins با پشتیبانی از بازی تیمی
     * 
     * در بازی تیمی: مجموع بردهای بازیکنان هر تیم را محاسبه می‌کند
     * در بازی انفرادی: بیشترین برد یک بازیکن را برمی‌گرداند
     */
    private function calculateMaxWins(int $gameId): int
    {
        $db = \Core\Database::getInstance();

        // بررسی نوع بازی
        $gameMode = $db->fetchOne(
            "SELECT game_mode FROM games WHERE id = ?",
            [$gameId]
        );

        if ($gameMode && $gameMode['game_mode'] === 'friendly') {
            // 🆕 بازی تیمی: مجموع بردهای هر تیم را محاسبه کن
            $maxTeamWins = $db->fetchOne(
                "SELECT MAX(team_total_wins) as max_wins FROM (
                    SELECT team_id, SUM(wins_count) as team_total_wins
                    FROM game_participants
                    WHERE game_id = ? AND team_id IS NOT NULL
                    GROUP BY team_id
                ) as team_wins",
                [$gameId]
            );
            return (int)($maxTeamWins['max_wins'] ?? 0);
        } else {
            // بازی انفرادی: بیشترین برد یک بازیکن
            $maxWinsResult = $db->fetchOne(
                "SELECT MAX(wins_count) as max_wins FROM game_participants WHERE game_id = ?",
                [$gameId]
            );
            return (int)($maxWinsResult['max_wins'] ?? 0);
        }
    }

    /**
     * 🆕 متد کمکی برای رندر partial با همه متغیرهای لازم
     */
    private function renderGamePartial(int $gameId, array $extraData = []): string
    {
        $game = $this->gameService->getGameWithDetails($gameId);
        $currentUser = $this->auth->user();
        $isReferee = $game && $game->referee_id === $currentUser['id'];
        $isAdmin = $this->auth->isAdmin();

        // 🆕 محاسبه maxWins
        $db = \Core\Database::getInstance();
        $maxWinsResult = $db->fetchOne(
            "SELECT MAX(wins_count) as max_wins FROM game_participants WHERE game_id = ?",
            [$gameId]
        );
        $maxWins = (int)($maxWinsResult['max_wins'] ?? 0);

        // 🆕 گرفتن تنظیمات امتیازدهی
        $scoringService = new \Application\Services\ScoringService();
        $scoringSettings = $scoringService->getScoringSettings();

        return View::render('pages.game.partials.game-content', array_merge([
            'game' => $game,
            'isReferee' => $isReferee,
            'isAdmin' => $isAdmin,
            'currentUser' => $currentUser,
            'maxWins' => $maxWins,
            'scoringSettings' => $scoringSettings, // 🆕 اضافه شده
        ], $extraData));
    }

    /**
     * شروع بازی
     */
    public function start(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $refereeId = $this->auth->id();
        $result = $this->gameService->startGame($gameId, $refereeId);
        if ($result['success']) {
            $html = $this->renderGamePartial($gameId, [
                'success' => $result['message'] . ' - اولین بازیکن: ' . $result['first_player'],
            ]);
            $this->response->html($html);
        } else {
            $html = $this->renderGamePartial($gameId, [
                'error' => $result['error'],
            ]);
            $this->response->status(400)->html($html);
        }
    }

    /**
     * توقف بازی
     */
    public function pause(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $refereeId = $this->auth->id();
        $result = $this->gameService->pauseGame($gameId, $refereeId);
        if ($result['success']) {
            $html = $this->renderGamePartial($gameId, [
                'success' => $result['message'],
            ]);
            $this->response->html($html);
        } else {
            $html = $this->renderGamePartial($gameId, [
                'error' => $result['error'],
            ]);
            $this->response->status(400)->html($html);
        }
    }

    /**
     * ادامه بازی
     */
    public function resume(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $refereeId = $this->auth->id();
        $result = $this->gameService->resumeGame($gameId, $refereeId);
        if ($result['success']) {
            $html = $this->renderGamePartial($gameId, [
                'success' => $result['message'],
            ]);
            $this->response->html($html);
        } else {
            $html = $this->renderGamePartial($gameId, [
                'error' => $result['error'],
            ]);
            $this->response->status(400)->html($html);
        }
    }

    /**
     * پایان بازی
     */
    public function finish(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $refereeId = $this->auth->id();
        $result = $this->gameService->finishGame($gameId, $refereeId);
        if ($result['success']) {
            $html = $this->renderGamePartial($gameId, [
                'success' => $result['message'] . ' - برنده: ' . $result['winner'],
            ]);
            $this->response->html($html);
        } else {
            $html = $this->renderGamePartial($gameId, [
                'error' => $result['error'],
            ]);
            $this->response->status(400)->html($html);
        }
    }

    /**
     * لغو بازی
     */
    public function cancel(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $refereeId = $this->auth->id();
        $result = $this->gameService->cancelGame($gameId, $refereeId);
        if ($result['success']) {
            $html = $this->renderGamePartial($gameId, [
                'success' => $result['message'],
            ]);
            $this->response->html($html);
        } else {
            $html = $this->renderGamePartial($gameId, [
                'error' => $result['error'],
            ]);
            $this->response->status(400)->html($html);
        }
    }

    /**
     * ثبت نتیجه دور
     */
    public function recordRound(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $refereeId = $this->auth->id();
        $data = [
            'winner_participant_id' => $request->post('winner_participant_id'),
            'winning_card_id' => $request->post('winning_card_id'),
            'win_type_id' => $request->post('win_type_id'),
        ];
        $result = $this->refereeService->recordRound($gameId, $refereeId, $data);
        if ($result['success']) {
            $html = $this->renderGamePartial($gameId, [
                'success' => $result['message'],
            ]);
            $this->response->html($html);
        } else {
            $html = $this->renderGamePartial($gameId, [
                'error' => $result['error'],
            ]);
            $this->response->status(400)->html($html);
        }
    }

    /**
     * لغو آخرین دور
     */
    public function undoLastRound(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $refereeId = $this->auth->id();
        $result = $this->refereeService->undoLastRound($gameId, $refereeId);
        if ($result['success']) {
            $html = $this->renderGamePartial($gameId, [
                'success' => $result['message'],
            ]);
            $this->response->html($html);
        } else {
            $html = $this->renderGamePartial($gameId, [
                'error' => $result['error'],
            ]);
            $this->response->status(400)->html($html);
        }
    }

    /**
     * 🆕 ویرایش هدف برد بازی (توسط داور) - با حداقل مطلق
     */
    public function editRounds(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $refereeId = $this->auth->id();
        $newTargetWins = (int) $request->post('target_wins');
        log_message("🎯 editRounds called: gameId={$gameId}, refereeId={$refereeId}, newTargetWins={$newTargetWins}");

        // بررسی دسترسی داور
        $game = $this->gameService->getGameWithDetails($gameId);
        if (!$game || $game->referee_id !== $refereeId) {
            $this->response->status(403)->html('شما اجازه ویرایش این بازی را ندارید');
            return;
        }

        // بررسی اینکه بازی هنوز تمام نشده
        if ($game->isFinished() || $game->isCancelled()) {
            $this->response->status(400)->html('بازی پایان یافته و قابل ویرایش نیست');
            return;
        }

        // 🆕 محاسبه دقیق maxWins با پشتیبانی از بازی تیمی
        $maxWinsValue = $this->calculateMaxWins($gameId);

        // 🆕 گرفتن حداقل مطلق از تنظیمات
        $settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
        $minTargetWins = (int) $settingsRepo->get('min_target_wins', 3);

        // 🆕 حداقل هدف مجاز = max(minTargetWins, maxWins)
        $minTarget = max($minTargetWins, $maxWinsValue);

        // حداکثر هدف مجاز
        $maxTarget = 100;

        log_message("🎯 Validation: newTargetWins={$newTargetWins}, minTarget={$minTarget}, maxTarget={$maxTarget}, maxWinsValue={$maxWinsValue}, minTargetWins={$minTargetWins}");

        // 🆕 اعتبارسنجی دقیق
        if ($newTargetWins < $minTarget) {
            if ($maxWinsValue > 0 && $maxWinsValue >= $minTargetWins) {
                // 🆕 نمایش نوع بازی در پیام خطا
                $gameType = $game->isTeamMode() ? 'تیم' : 'بازیکن';
                $errorMessage = "هدف جدید نمی‌تواند کمتر از بالاترین تعداد برد فعلی {$gameType}‌ها (بالاترین تعداد برد فعلی: {$maxWinsValue}) باشد";
            } else {
                $errorMessage = "هدف برد نمی‌تواند کمتر از حداقل مجاز ({$minTargetWins}) باشد";
            }
            $html = $this->renderGamePartial($gameId, [
                'error' => $errorMessage,
            ]);
            $this->response->status(400)->html($html);
            return;
        }

        if ($newTargetWins > $maxTarget) {
            $errorMessage = "هدف برد نمی‌تواند بیشتر از {$maxTarget} باشد";
            $html = $this->renderGamePartial($gameId, [
                'error' => $errorMessage,
            ]);
            $this->response->status(400)->html($html);
            return;
        }

        // به‌روزرسانی
        $currentTarget = $game->target_wins;
        try {
            $stmt = $db = \Core\Database::getInstance();
            $stmt = $db->query(
                "UPDATE games SET target_wins = ? WHERE id = ?",
                [$newTargetWins, $gameId]
            );
            if ($stmt === false) {
                throw new \Exception('Query failed');
            }
        } catch (\Throwable $e) {
            log_message("❌ Error in editRounds: " . $e->getMessage());
            $html = $this->renderGamePartial($gameId, [
                'error' => 'خطا در به‌روزرسانی هدف برد: ' . $e->getMessage(),
            ]);
            $this->response->status(500)->html($html);
            return;
        }

        // پاک کردن کش بازی
        $this->gameService->clearGameCache($gameId);

        // 🆕 Broadcast به SSE
        try {
            $sseService = new \Application\Services\SSEService();
            $sseService->broadcastToGame($gameId, 'game_target_changed', [
                'game_id' => $gameId,
                'old_target' => $currentTarget,
                'new_target' => $newTargetWins,
                'min_target' => $minTarget,
                'max_wins' => $maxWinsValue,
                'changed_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message("❌ SSE broadcast error (game_target_changed): " . $e->getMessage());
        }

        // 🆕 بارگذاری مجدد بازی
        $successMsg = "هدف برد با موفقیت از {$currentTarget} به {$newTargetWins} تغییر کرد";
        $html = $this->renderGamePartial($gameId, [
            'success' => $successMsg,
        ]);
        $this->response->html($html);
    }

    /**
     * 🆕 انتقال نقش داور به کاربر دیگر (توسط داور فعلی)
     */
    public function transferReferee(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $currentRefereeId = $this->auth->id();
        $newRefereeId = (int) $request->post('new_referee_id');
        log_message("👤 transferReferee called: gameId={$gameId}, from={$currentRefereeId}, to={$newRefereeId}");

        // بررسی دسترسی داور
        $game = $this->gameService->getGameWithDetails($gameId);
        if (!$game || $game->referee_id !== $currentRefereeId) {
            $this->response->status(403)->html('شما اجازه انتقال نقش داور را ندارید');
            return;
        }

        // بررسی اینکه بازی هنوز تمام نشده
        if ($game->isFinished() || $game->isCancelled()) {
            $this->response->status(400)->html('بازی پایان یافته و قابل ویرایش نیست');
            return;
        }

        // بررسی وجود کاربر جدید
        $db = \Core\Database::getInstance();
        $newReferee = $db->fetchOne(
            "SELECT id, nickname FROM users WHERE id = ? AND status = 'active'",
            [$newRefereeId]
        );
        if (!$newReferee) {
            $this->response->status(400)->html('کاربر انتخاب شده معتبر نیست');
            return;
        }

        // 🆕 انتقال نقش داور با query مستقیم
        try {
            $stmt = $db->query(
                "UPDATE games SET referee_id = ? WHERE id = ?",
                [$newRefereeId, $gameId]
            );
            if ($stmt === false) {
                throw new \Exception('Query failed');
            }

            // 🆕 بررسی اینکه واقعاً تغییر کرده است
            $updatedGame = $db->fetchOne(
                "SELECT referee_id FROM games WHERE id = ?",
                [$gameId]
            );
            if (!$updatedGame || (int)$updatedGame['referee_id'] !== $newRefereeId) {
                throw new \Exception('انتقال نقش داور ناموفق بود');
            }
            log_message("✅ transferReferee success: referee_id changed to {$newRefereeId}");
        } catch (\Throwable $e) {
            log_message("❌ Error in transferReferee: " . $e->getMessage());
            $html = $this->renderGamePartial($gameId, [
                'error' => 'خطا در انتقال نقش داور: ' . $e->getMessage(),
            ]);
            $this->response->status(500)->html($html);
            return;
        }

        // پاک کردن کش بازی
        $this->gameService->clearGameCache($gameId);

        // ثبت در لاگ
        try {
            $db->insert('referee_actions_log', [
                'game_id' => $gameId,
                'referee_id' => $currentRefereeId,
                'action_type' => 'transfer_referee',
                'target_type' => 'user',
                'target_id' => $newRefereeId,
                'old_value' => json_encode(['referee_id' => $currentRefereeId]),
                'new_value' => json_encode(['referee_id' => $newRefereeId]),
            ]);
        } catch (\Throwable $e) {
            log_message("⚠️ Failed to log transfer: " . $e->getMessage());
        }

        // 🆕 Broadcast به SSE
        try {
            $sseService = new SSEService();
            $sseService->broadcastToGame($gameId, 'game_referee_changed', [
                'game_id' => $gameId,
                'old_referee_id' => $currentRefereeId,
                'new_referee_id' => $newRefereeId,
                'new_referee_name' => $newReferee['nickname'],
                'changed_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message("❌ SSE broadcast error (game_referee_changed): " . $e->getMessage());
        }

        // 🆕 رندر partial با موفقیت
        $html = $this->renderGamePartial($gameId, [
            'success' => 'نقش داور با موفقیت به ' . htmlspecialchars($newReferee['nickname']) . ' منتقل شد',
        ]);
        $this->response->html($html);
    }

    /**
     * 🆕 ویرایش امتیاز بازیکن (توسط داور)
     */
    public function editParticipantScore(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $participantId = (int) $request->post('participant_id');
        $newScore = (int) $request->post('new_score');
        $refereeId = $this->auth->id();
        $game = $this->gameService->getGameWithDetails($gameId);
        if (!$game || $game->referee_id !== $refereeId) {
            $this->response->status(403)->html('شما اجازه ویرایش این بازی را ندارید');
            return;
        }
        if ($game->isFinished() || $game->isCancelled()) {
            $this->response->status(400)->html('بازی پایان یافته و قابل ویرایش نیست');
            return;
        }
        $db = \Core\Database::getInstance();
        $db->query(
            "UPDATE game_participants SET total_score = ? WHERE id = ? AND game_id = ?",
            [$newScore, $participantId, $gameId]
        );
        $this->gameService->clearGameCache($gameId);
        $html = $this->renderGamePartial($gameId, [
            'success' => 'امتیاز بازیکن با موفقیت ویرایش شد',
        ]);
        $this->response->html($html);
    }

    /**
     * 🆕 ویرایش تعداد بردهای بازیکن (توسط داور)
     */
    public function editParticipantWins(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $participantId = (int) $request->post('participant_id');
        $newWins = (int) $request->post('new_wins');
        $refereeId = $this->auth->id();
        $game = $this->gameService->getGameWithDetails($gameId);
        if (!$game || $game->referee_id !== $refereeId) {
            $this->response->status(403)->html('شما اجازه ویرایش این بازی را ندارید');
            return;
        }
        if ($game->isFinished() || $game->isCancelled()) {
            $this->response->status(400)->html('بازی پایان یافته و قابل ویرایش نیست');
            return;
        }
        $db = \Core\Database::getInstance();
        $db->query(
            "UPDATE game_participants SET wins_count = ? WHERE id = ? AND game_id = ?",
            [$newWins, $participantId, $gameId]
        );
        $this->gameService->clearGameCache($gameId);
        $html = $this->renderGamePartial($gameId, [
            'success' => 'تعداد بردهای بازیکن با موفقیت ویرایش شد',
        ]);
        $this->response->html($html);
    }
}
