<?php

namespace Presentation\Listeners;

use Core\Database;
use Infrastructure\Repositories\TitleRepository;

/**
 * 🏆 Listener بررسی و اعطای عناوین
 * 
 * منطق انتخاب عنوان فعال:
 * ۱. بالاترین bonus_points (بونوس مهم‌تر است)
 * ۲. بالاترین priority (به عنوان tie-breaker)
 */
class TitleListener
{
    private Database $db;
    private TitleRepository $titleRepo;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->titleRepo = new TitleRepository();
    }

    /**
     * بررسی القاب پس از هر دور
     */
    public function checkTitles(array $data): void
    {
        $userId = $data['winner_user_id'] ?? null;
        if (!$userId) {
            return;
        }

        $this->checkAndGrantTitles($userId);
    }

    /**
     * بررسی القاب پس از پایان بازی
     */
    public function checkGameTitles(array $data): void
    {
        // بررسی برای همه شرکت‌کنندگان
        $gameId = $data['game_id'] ?? null;
        if (!$gameId) return;

        $participants = $this->db->fetchAll(
            "SELECT user_id FROM game_participants 
             WHERE game_id = ? AND user_id IS NOT NULL",
            [$gameId]
        );

        foreach ($participants as $p) {
            $this->checkAndGrantTitles((int) $p['user_id']);
        }
    }

    /**
     * بررسی و اعطای عناوین واجد شرایط
     */
    private function checkAndGrantTitles(int $userId): void
    {
        // گرفتن آمار کاربر
        $stats = $this->getUserStats($userId);

        // گرفتن همه عناوین فعال
        $titles = $this->titleRepo->findAllActive();

        $hasNewTitle = false;

        foreach ($titles as $title) {
            // بررسی اینکه کاربر قبلاً این عنوان را دارد
            $hasTitle = $this->db->fetchOne(
                "SELECT id FROM user_titles WHERE user_id = ? AND title_id = ?",
                [$userId, $title['id']]
            );

            if ($hasTitle) {
                continue; // قبلاً دارد
            }

            // بررسی شرط
            $conditionMet = $this->checkCondition(
                $title['condition_type'],
                $title['condition_value'],
                $stats
            );

            if ($conditionMet) {
                // اعطای عنوان
                $this->db->insert('user_titles', [
                    'user_id' => $userId,
                    'title_id' => $title['id'],
                    'is_active' => 0, // بعداً فعال می‌شود
                    'unlocked_at' => date('Y-m-d H:i:s'),
                ]);

                $hasNewTitle = true;

                // ارسال نوتیفیکیشن
                $this->sendTitleNotification($userId, $title);

                log_message("🏆 عنوان '{$title['name']}' به کاربر {$userId} اعطا شد");
            }
        }

        // اگر عنوان جدیدی اعطا شد، عنوان فعال را به‌روز کن
        if ($hasNewTitle) {
            $this->updateActiveTitle($userId);
        }
    }

    /**
     * بررسی شرط عنوان
     */
    private function checkCondition(string $conditionType, int $conditionValue, array $stats): bool
    {
        $currentValue = match ($conditionType) {
            'total_games' => $stats['total_games'] ?? 0,
            'total_wins' => $stats['total_wins'] ?? 0,
            'best_streak' => $stats['best_streak'] ?? 0,
            'current_streak' => $stats['current_streak'] ?? 0,
            'team_wins' => $stats['team_wins'] ?? 0,
            'team_games' => $stats['team_games'] ?? 0,
            'total_points' => $stats['total_points'] ?? 0,
            default => 0,
        };

        return $currentValue >= $conditionValue;
    }

    /**
     * گرفتن آمار کاربر
     */
    private function getUserStats(int $userId): array
    {
        $stats = $this->db->fetchOne(
            "SELECT
                COUNT(DISTINCT g.id) as total_games,
                SUM(CASE WHEN g.winner_participant_id = gp.id THEN 1 ELSE 0 END) as total_wins,
                SUM(gp.total_score) as total_points,
                SUM(CASE WHEN g.game_mode = 'friendly' THEN 1 ELSE 0 END) as team_games,
                SUM(CASE WHEN g.game_mode = 'friendly' AND g.winner_participant_id = gp.id THEN 1 ELSE 0 END) as team_wins
             FROM games g
             JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? AND g.status = 'finished'",
            [$userId]
        );

        // گرفتن استریک
        $streak = $this->db->fetchOne(
            "SELECT current_streak, best_streak FROM user_streaks WHERE user_id = ?",
            [$userId]
        );

        return [
            'total_games' => (int) ($stats['total_games'] ?? 0),
            'total_wins' => (int) ($stats['total_wins'] ?? 0),
            'total_points' => (int) ($stats['total_points'] ?? 0),
            'team_games' => (int) ($stats['team_games'] ?? 0),
            'team_wins' => (int) ($stats['team_wins'] ?? 0),
            'current_streak' => (int) ($streak['current_streak'] ?? 0),
            'best_streak' => (int) ($streak['best_streak'] ?? 0),
        ];
    }

    /**
     * 🆕 به‌روزرسانی عنوان فعال
     * 
     * منطق انتخاب:
     * ۱. بالاترین bonus_points (بونوس مهم‌تر است)
     * ۲. بالاترین priority (به عنوان tie-breaker)
     */
    private function updateActiveTitle(int $userId): void
    {
        // غیرفعال کردن همه عناوین
        $this->db->update(
            'user_titles',
            ['is_active' => 0],
            'user_id = ?',
            [$userId]
        );

        // پیدا کردن بهترین عنوان
        // اول بر اساس bonus_points DESC، سپس priority DESC
        $bestTitle = $this->db->fetchOne(
            "SELECT ut.title_id, t.bonus_points, t.priority
             FROM user_titles ut
             JOIN titles t ON ut.title_id = t.id
             WHERE ut.user_id = ?
             ORDER BY t.bonus_points DESC, t.priority DESC
             LIMIT 1",
            [$userId]
        );

        if ($bestTitle) {
            // فعال کردن بهترین عنوان
            $this->db->update(
                'user_titles',
                ['is_active' => 1],
                'user_id = ? AND title_id = ?',
                [$userId, $bestTitle['title_id']]
            );

            // به‌روزرسانی users.current_title_id
            $this->db->update(
                'users',
                ['current_title_id' => $bestTitle['title_id']],
                'id = ?',
                [$userId]
            );

            log_message("🏆 عنوان فعال کاربر {$userId} به title_id={$bestTitle['title_id']} تغییر کرد");
        }
    }

    /**
     * ارسال نوتیفیکیشن کسب عنوان
     */
    private function sendTitleNotification(int $userId, array $title): void
    {
        try {
            $this->db->insert('notifications', [
                'user_id' => $userId,
                'type' => 'title',
                'title' => '🏆 عنوان جدید!',
                'message' => "تبریک! شما عنوان «{$title['name']}» را کسب کردید." . 
                            ($title['bonus_points'] > 0 ? " بونوس: +{$title['bonus_points']} امتیاز در هر برد" : ''),
                'icon' => $title['icon'] ?? '🏆',
                'link' => '/achievements',
                'is_read' => 0,
            ]);
        } catch (\Throwable $e) {
            log_message("⚠️ Title notification error: " . $e->getMessage());
        }
    }
}