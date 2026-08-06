<?php

namespace Application\Services;

use Core\Database;

/**
 * سرویس broadcast رویدادها به SSE
 */
class EventBroadcaster
{
    private Database $db;
    private static ?EventBroadcaster $instance = null;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * ارسال رویداد به یک کانال
     */
    public function broadcast(string $channel, string $eventType, array $data): int
    {
        try {
            $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
            log_message("📡 SSE Broadcast: channel={$channel}, event={$eventType}, data=" . substr($jsonData, 0, 200));

            $eventId = $this->db->insert('sse_events', [
                'channel' => $channel,
                'event_type' => $eventType,
                'data' => $jsonData,
            ]);

            log_message("✅ SSE Broadcast success: id={$eventId}");
            return $eventId;
        } catch (\Throwable $e) {
            log_message("❌ SSE Broadcast FAILED: " . $e->getMessage());
            log_message("📋 Trace: " . $e->getTraceAsString());
            throw $e;
        }
    }



    /**
     * ارسال رویداد به بازی خاص
     */
    // src/Application/Services/EventBroadcaster.php

    public function broadcastToGame(int $gameId, string $eventType, array $data): int
    {
        // 🆕 اگر source_user_id در data نبود، از session بگیر
        if (!isset($data['source_user_id']) || empty($data['source_user_id'])) {
            if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user_id'])) {
                $data['source_user_id'] = (int) $_SESSION['user_id'];
            } elseif (isset($_SESSION['user']['id'])) {
                $data['source_user_id'] = (int) $_SESSION['user']['id'];
            }
        }

        // 🆕 اگر هنوز source_user_id نداریم، از REFERER یا IP استفاده کن (اختیاری)
        if (!isset($data['source_user_id']) || empty($data['source_user_id'])) {
            // می‌توانیم از IP یا شناسه‌ی دیگری استفاده کنیم، اما بهتر است null بگذاریم
            $data['source_user_id'] = 0; // یا null
        }

        return $this->broadcast("game_{$gameId}", $eventType, array_merge($data, [
            'game_id' => $gameId,
        ]));
    }

    /**
     * ارسال رویداد به کاربر خاص
     */
    public function broadcastToUser(int $userId, string $eventType, array $data): int
    {
        if (!isset($data['source_user_id']) && isset($_SESSION['user_id'])) {
            $data['source_user_id'] = $_SESSION['user_id'];
        }
        return $this->broadcast("user_{$userId}", $eventType, array_merge($data, [
            'user_id' => $userId,
        ]));
    }

    /**
     * ارسال رویداد عمومی (به همه)
     */
    public function broadcastPublic(string $eventType, array $data): int
    {
        return $this->broadcast('public', $eventType, $data);
    }

    public function getNewEvents(string $channel, int $lastEventId = 0, int $limit = 50): array
    {
        if ($lastEventId > 0) {
            log_message("📥 getNewEvents (existing): channel={$channel}, lastEventId={$lastEventId}");
            $events = $this->db->fetchAll(
                "SELECT id, event_type, data, created_at 
             FROM sse_events 
             WHERE channel = ? AND id > ?
             ORDER BY id ASC
             LIMIT ?",
                [$channel, $lastEventId, $limit]
            );
            if (!empty($events)) {
                log_message("📥 Found " . count($events) . " new events");
            }
            return $events;
        }

        log_message("📥 getNewEvents (new connection): channel={$channel}, returning last 5 minutes");
        return $this->db->fetchAll(
            "SELECT id, event_type, data, created_at 
         FROM sse_events 
         WHERE channel = ? 
         AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
         ORDER BY id ASC
         LIMIT ?",
            [$channel, $limit]
        );
    }

    /**
     * پاکسازی رویدادهای قدیمی
     */
    public function cleanup(int $hours = 1): int
    {
        $this->db->query(
            "DELETE FROM sse_events WHERE created_at < DATE_SUB(NOW(), INTERVAL ? HOUR)",
            [$hours]
        );
        return $this->db->getAffectedRows();
    }

    // ============================================
    // Session Management
    // ============================================

    /**
     * به‌روزرسانی session کاربر
     */
    public function updateSession(int $userId, string $sessionId, ?string $page = null): void
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM user_sessions WHERE session_id = ?",
            [$sessionId]
        );

        $data = [
            'user_id' => $userId,
            'session_id' => $sessionId,
            'page' => $page,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'last_activity' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->db->update('user_sessions', $data, 'id = ?', [$existing['id']]);
        } else {
            $this->db->insert('user_sessions', $data);
        }
    }

    /**
     * گرفتن کاربران آنلاین
     */
    public function getOnlineUsers(int $minutes = 5): array
    {
        return $this->db->fetchAll(
            "SELECT us.user_id, u.nickname, u.avatar_path, us.page, us.last_activity
             FROM user_sessions us
             JOIN users u ON us.user_id = u.id
             WHERE us.last_activity >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
             ORDER BY us.last_activity DESC",
            [$minutes]
        );
    }

    /**
     * پاکسازی session های قدیمی
     */
    public function cleanupSessions(int $minutes = 30): int
    {
        $this->db->query(
            "DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$minutes]
        );
        return $this->db->getAffectedRows();
    }
}
