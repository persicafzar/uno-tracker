<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Application\Services\AuthService;
use Application\Services\SSEService;
use Application\Services\EventBroadcaster;

class SSEController
{
    private AuthService $auth;
    private SSEService $sseService;
    private EventBroadcaster $broadcaster;
    private Response $response;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->sseService = new SSEService();
        $this->broadcaster = EventBroadcaster::getInstance();
        $this->response = new Response();
    }

    /**
     * 🆕 اتصال SSE برای بازی خاص
     */
    public function game(Request $request, array $params): void
    {
        if (!$this->auth->check()) {
            $this->response->status(401)->html('Unauthorized');
            return;
        }

        $gameId = (int) ($params['id'] ?? 0);
        if ($gameId <= 0) {
            $this->response->status(400)->html('Invalid game ID');
            return;
        }

        // 🆕 ذخیره user_id در session قبل از بستن session
        $userId = $this->auth->id();
        if ($userId && !isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $userId;
        }

        // 🆕 گرفتن lastEventId از query string یا header
        $lastEventId = (int) ($request->get('last_event_id') ?? 0);
        if ($lastEventId === 0) {
            $lastEventId = (int) ($request->server('HTTP_LAST_EVENT_ID') ?? 0);
        }

        $channel = "game_{$gameId}";

        log_message("🔌 SSE game request: channel={$channel}, lastEventId={$lastEventId}");

        // 🆕 بستن session قبل از شروع stream
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $this->sseService->startStream($channel, $lastEventId);
    }

    /**
     * 🆕 اتصال SSE برای نوتیفیکیشن‌های کاربر
     */
    public function notifications(Request $request): void
    {
        if (!$this->auth->check()) {
            $this->response->status(401)->html('Unauthorized');
            return;
        }

        $userId = $this->auth->id();

        // 🆕 ذخیره user_id در session قبل از بستن session
        if ($userId && !isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $userId;
        }

        // 🆕 گرفتن lastEventId از query string یا header
        $lastEventId = (int) ($request->get('last_event_id') ?? 0);
        if ($lastEventId === 0) {
            $lastEventId = (int) ($request->server('HTTP_LAST_EVENT_ID') ?? 0);
        }

        $channel = "user_{$userId}";

        log_message("🔌 SSE notifications request: channel={$channel}, lastEventId={$lastEventId}");

        // 🆕 بستن session قبل از شروع stream
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $this->sseService->startStream($channel, $lastEventId);
    }

    /**
     * 🆕 اتصال SSE عمومی
     */
    public function publicStream(Request $request): void
    {
        $lastEventId = (int) ($request->server('HTTP_LAST_EVENT_ID') ?? 0);

        // 🆕 بستن session قبل از شروع stream
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $this->sseService->startStream('public', $lastEventId);
    }

    /**
     * 🆕 گرفتن کاربران آنلاین
     */
    public function onlineUsers(Request $request): void
    {
        if (!$this->auth->check()) {
            $this->response->json(['error' => 'Unauthorized'], 401);
            return;
        }

        $users = $this->broadcaster->getOnlineUsers(5);
        $this->response->json([
            'users' => $users,
            'count' => count($users),
        ]);
    }

    /**
     * 🆕 به‌روزرسانی session کاربر (Heartbeat)
     */
    public function heartbeat(Request $request): void
    {
        if (!$this->auth->check()) {
            $this->response->json(['error' => 'Unauthorized'], 401);
            return;
        }

        $userId = $this->auth->id();
        $sessionId = session_id();
        $page = $request->post('page', '');

        $this->broadcaster->updateSession($userId, $sessionId, $page);

        $this->response->json([
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }


    /**
     * 🆕 تست اتصال SSE
     */
    public function test(Request $request): void
    {
        // 🆕 بستن session قبل از شروع stream
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        ob_implicit_flush(true);

        set_time_limit(0);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        flush();

        for ($i = 1; $i <= 5; $i++) {
            if (connection_aborted()) break;

            echo "id: {$i}\n";
            echo "event: test\n";
            echo "data: " . json_encode([
                'message' => "Test message #{$i}",
                'timestamp' => date('Y-m-d H:i:s'),
            ], JSON_UNESCAPED_UNICODE) . "\n\n";

            flush();
            sleep(2);
        }

        echo ": test completed\n\n";
        flush();
    }

    /**
     * 🆕 اتصال SSE برای داور (بدون نیاز به بازیکن بودن)
     */
    public function referee(Request $request): void
    {
        if (!$this->auth->check()) {
            $this->response->status(401)->html('Unauthorized');
            return;
        }

        $userId = $this->auth->id();

        // 🆕 ذخیره user_id در session قبل از بستن session
        if ($userId && !isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $userId;
        }

        $lastEventId = (int) ($request->server('HTTP_LAST_EVENT_ID') ?? 0);

        // 🆕 کانال شخصی برای داور
        $channel = "user_{$userId}";

        // 🆕 بستن session قبل از شروع stream
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $this->sseService->startStream($channel, $lastEventId);
    }
}
