<?php

namespace Presentation\Listeners;

use Core\Database;
use Application\Services\AntiCheatService;

class AntiCheatListener
{
    private Database $db;
    private AntiCheatService $antiCheatService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->antiCheatService = new AntiCheatService();
    }

    /**
     * بررسی بازی هنگام ایجاد
     */
    public function onGameCreated(array $data): void
    {
        $gameId = $data['game_id'] ?? null;
        $userId = $data['user_id'] ?? null;

        if (!$gameId || !$userId) return;

        // بررسی بازی‌های فقط مهمان
        $guestOnlyCheck = $this->antiCheatService->checkGuestOnlyGame($gameId);

        if ($guestOnlyCheck['is_suspicious']) {
            $this->antiCheatService->recordSuspiciousGame(
                $gameId,
                $userId,
                ['guest_only_game'],
                'high',
                ['guest_only' => $guestOnlyCheck]
            );

            log_message("🛡️ Suspicious game detected: Guest-only game #{$gameId} created by user #{$userId}");
        }

        // 🆕 بررسی حلقه تبانی هنگام ایجاد بازی
        $collusionCheck = $this->antiCheatService->checkCollusionLoop($userId);

        if ($collusionCheck['is_suspicious']) {
            $this->antiCheatService->recordSuspiciousGame(
                $gameId,
                $userId,
                ['collusion_loop'],
                'critical',
                ['collusion' => $collusionCheck]
            );

            log_message("🛡️ Collusion Loop detected for user #{$userId}: {$collusionCheck['total_games']} games with only {$collusionCheck['unique_opponents']} unique opponent(s)");
        }
    }

    /**
     * بررسی بازی هنگام پایان
     */
    public function onGameFinished(array $data): void
    {
        $gameId = $data['game_id'] ?? null;

        if (!$gameId) return;

        // ✅ تغییر: متغیر $game را قبل از بلوک if تعریف می‌کنیم
        $game = $this->db->fetchOne(
            "SELECT referee_id FROM games WHERE id = ?",
            [$gameId]
        );

        if (!$game) {
            return; // بازی وجود ندارد
        }

        $userId = $game['referee_id'] ?? null;

        // بررسی کامل بازی
        $checkResult = $this->antiCheatService->checkGame($gameId);

        if ($checkResult['is_suspicious']) {
            $this->antiCheatService->recordSuspiciousGame(
                $gameId,
                $userId,
                $checkResult['cheat_types'],
                $checkResult['risk_level'],
                $checkResult['details']
            );

            log_message("🛡️ Suspicious game detected: Game #{$gameId} - " . implode(', ', $checkResult['cheat_types']));
        }

        // 🆕 بررسی حلقه تبانی بعد از پایان بازی
        if (isset($game['referee_id'])) {
            $collusionCheck = $this->antiCheatService->checkCollusionLoop($game['referee_id']);

            if ($collusionCheck['is_suspicious']) {
                $this->antiCheatService->recordSuspiciousGame(
                    $gameId,
                    $game['referee_id'],
                    ['collusion_loop'],
                    'critical',
                    ['collusion' => $collusionCheck]
                );

                log_message("🛡️ Collusion Loop detected for user #{$game['referee_id']}: {$collusionCheck['total_games']} games with only {$collusionCheck['unique_opponents']} unique opponent(s)");
            }
        }
    }
}
