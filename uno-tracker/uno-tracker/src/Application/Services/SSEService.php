<?php

namespace Application\Services;

use Core\Database;

class SSEService
{
    private Database $db;
    private EventBroadcaster $broadcaster;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->broadcaster = EventBroadcaster::getInstance();
    }

    /**
     * شروع یک اتصال SSE - 🆕 نسخه بهبود یافته با poll interval کمتر
     */
    public function startStream(string $channel, int $lastEventId = 0): void
    {
        log_message("🔌 SSE Stream started: channel={$channel}, lastEventId={$lastEventId}");

        // 🆕 غیرفعال کردن همه لایه‌های buffering
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        ob_implicit_flush(true);
        
        // 🆕 بستن session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        
        // 🆕 تنظیم time limit
        set_time_limit(0);
        ini_set('max_execution_time', '0');
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', '0');
        
        // 🆕 تنظیم هدرهای SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        
        // 🆕 flush اولیه
        flush();
        
        // 🆕 ارسال کامنت اولیه
        echo ": SSE stream started for channel: {$channel}\n";
        echo "retry: 3000\n\n";
        flush();
        
        $heartbeatInterval = 15; // ثانیه
        $pollInterval = 0.5; // 🆕 کاهش به ۰.۵ ثانیه برای پاسخ‌دهی سریع‌تر
        $lastHeartbeat = time();
        $maxExecutionTime = 600; // ۱۰ دقیقه
        $startTime = time();
        
        // 🆕 حلقه اصلی
        while (!connection_aborted() && (time() - $startTime) < $maxExecutionTime) {
            
            try {
                // گرفتن رویدادهای جدید
                $events = $this->broadcaster->getNewEvents($channel, $lastEventId);
                
                if (!empty($events)) {
                    log_message("📨 SSE Events found: " . count($events) . " for channel {$channel} (lastEventId: {$lastEventId})");
                }
                
                foreach ($events as $event) {
                    $this->sendEvent(
                        $event['event_type'],
                        json_decode($event['data'], true),
                        (int) $event['id']
                    );
                    $lastEventId = (int) $event['id'];
                }
                
                // 🆕 Heartbeat
                if (time() - $lastHeartbeat >= $heartbeatInterval) {
                    echo ": heartbeat " . time() . "\n\n";
                    flush();
                    $lastHeartbeat = time();
                }
                
                // 🆕 بررسی اتصال
                if (connection_aborted()) {
                    break;
                }
                
            } catch (\Exception $e) {
                log_message("SSE Error: " . $e->getMessage());
                echo ": error: " . $e->getMessage() . "\n\n";
                flush();
            }
            
            // 🆕 استفاده از usleep برای کاهش مصرف CPU
            usleep((int)($pollInterval * 1000000));
        }
        
        // 🆕 پاکسازی نهایی
        echo ": stream closed\n\n";
        flush();
    }

    /**
     * ارسال یک رویداد SSE
     */
    private function sendEvent(string $type, array $data, int $id = null): void
    {
        if ($id !== null) {
            echo "id: {$id}\n";
        }
        echo "event: {$type}\n";
        echo "data: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";
        flush();
    }

    // ============================================
    // Broadcast Helpers
    // ============================================

    public function broadcastGameStarted(int $gameId, array $gameData): void
    {
        $this->broadcaster->broadcastToGame($gameId, 'game_started', $gameData);
    }

    public function broadcastRoundRecorded(int $gameId, array $roundData): void
    {
        $this->broadcaster->broadcastToGame($gameId, 'round_recorded', $roundData);
    }

    public function broadcastRoundUndone(int $gameId, array $data): void
    {
        $this->broadcaster->broadcastToGame($gameId, 'round_undone', $data);
    }

    public function broadcastGameFinished(int $gameId, array $gameData): void
    {
        $this->broadcaster->broadcastToGame($gameId, 'game_finished', $gameData);
    }

    public function broadcastGameStatusChanged(int $gameId, string $status, array $data = []): void
    {
        $this->broadcaster->broadcastToGame($gameId, 'game_status_changed', array_merge($data, [
            'status' => $status,
        ]));
    }

    public function broadcastScoreUpdated(int $gameId, array $scores): void
    {
        $this->broadcaster->broadcastToGame($gameId, 'score_updated', [
            'scores' => $scores,
        ]);
    }

    public function broadcastNotification(int $userId, array $notification): void
    {
        $this->broadcaster->broadcastToUser($userId, 'notification', $notification);
    }

    public function broadcastSystemMessage(string $message, string $type = 'info'): void
    {
        $this->broadcaster->broadcastPublic('system_message', [
            'message' => $message,
            'type' => $type,
        ]);
    }

    /**
     * 🆕 Broadcast به کانال بازی
     */
    public function broadcastToGame(int $gameId, string $eventType, array $data): void
    {
        $this->broadcaster->broadcastToGame($gameId, $eventType, $data);
    }
}