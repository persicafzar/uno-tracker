<?php
namespace Application\Services;

use Core\Database;
use Infrastructure\Repositories\CardRepository;
use Infrastructure\Repositories\SettingsRepository;

/**
 * 🎯 سرویس محاسبه امتیاز و XP
 *
 * فرمول امتیاز دور:
 * calculatedScore = baseScore × cardMultiplier × winTypeMultiplier × teamMultiplier + titleBonus
 *
 * فرمول XP:
 * playerXP = (totalScore × xpMultiplier) + gameBonus + winBonus
 * winnerXP = playerXP + winnerBonus
 */
class ScoringService
{
    private Database $db;
    private SettingsRepository $settings;
    private CardRepository $cardRepo;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->settings = SettingsRepository::getInstance();
        $this->cardRepo = new CardRepository();
    }

    /**
     * 🎯 محاسبه امتیاز یک دور
     *
     * @param int|null $winningCardId شناسه کارت برنده
     * @param int|null $winTypeId شناسه نوع برد
     * @param int|null $userId شناسه کاربر (برای titleBonus)
     * @param bool $isTeamGame آیا بازی تیمی است؟
     * @return float امتیاز محاسبه شده
     */
    public function calculateRoundScore(
        ?int $winningCardId = null,
        ?int $winTypeId = null,
        ?int $userId = null,
        bool $isTeamGame = false
    ): float {
        // ۱. امتیاز پایه
        $baseScore = (float) $this->settings->get('scoring_base_score', 1.0);

        // ۲. ضریب کارت
        $cardMultiplier = 1.0;
        if ($winningCardId) {
            $card = $this->cardRepo->findById($winningCardId);
            if ($card && $card->is_active) {
                $cardMultiplier = (float) $card->score_multiplier;
            }
        }

        // ۳. ضریب نوع برد
        $winTypeMultiplier = 1.0;
        if ($winTypeId) {
            $winType = $this->db->fetchOne(
                "SELECT score_multiplier FROM win_types WHERE id = ? AND is_active = 1",
                [$winTypeId]
            );
            if ($winType) {
                $winTypeMultiplier = (float) $winType['score_multiplier'];
            }
        }

        // ۴. ضریب بازی تیمی
        $teamMultiplier = 1.0;
        if ($isTeamGame) {
            $teamMultiplier = (float) $this->settings->get('scoring_team_multiplier', 1.5);
        }

        // ۵. بونوس لقب
        $titleBonus = 0;
        if ($userId) {
            $titleBonus = $this->getTitleBonus($userId);
        }

        // ۶. محاسبه نهایی
        $calculatedScore = $baseScore * $cardMultiplier * $winTypeMultiplier * $teamMultiplier + $titleBonus;

        // ۷. گرد کردن به ۲ رقم اعشار
        return round($calculatedScore, 2);
    }

    /**
     * ⭐ محاسبه XP برای یک بازیکن
     *
     * @param float $totalScore امتیاز کل بازیکن
     * @param bool $isWinner آیا بازیکن برنده شده؟
     * @param bool $isGameWinner آیا برنده کل بازی است؟
     * @return int XP محاسبه شده
     */
    public function calculatePlayerXP(
        float $totalScore,
        bool $isWinner = false,
        bool $isGameWinner = false
    ): int {
        // ۱. ضریب XP
        $xpMultiplier = (float) $this->settings->get('scoring_xp_multiplier', 2.0);

        // ۲. XP از امتیاز
        $scoreXP = (int) ($totalScore * $xpMultiplier);

        // ۳. XP شرکت در بازی
        $gameBonus = (int) $this->settings->get('scoring_game_bonus', 5);

        // ۴. XP برد (اگر در دور برنده شده)
        $winBonus = 0;
        if ($isWinner) {
            $winBonus = (int) $this->settings->get('scoring_win_bonus', 15);
        }

        // ۵. XP برنده بازی
        $winnerBonus = 0;
        if ($isGameWinner) {
            $winnerBonus = (int) $this->settings->get('scoring_winner_bonus', 50);
        }

        // ۶. مجموع XP
        $totalXP = $scoreXP + $gameBonus + $winBonus + $winnerBonus;

        return $totalXP;
    }

    /**
     * 🏆 محاسبه XP برنده بازی
     *
     * @param float $totalScore امتیاز کل برنده
     * @return int XP محاسبه شده
     */
    public function calculateWinnerXP(float $totalScore): int
    {
        return $this->calculatePlayerXP($totalScore, true, true);
    }

    /**
     * 🎖️ گرفتن بونوس لقب کاربر
     */
    private function getTitleBonus(int $userId): int
    {
        // 🆕 بررسی وجود ستون bonus_points در جدول titles
        try {
            $title = $this->db->fetchOne(
                "SELECT t.bonus_points
                 FROM users u
                 LEFT JOIN titles t ON u.current_title_id = t.id
                 WHERE u.id = ?",
                [$userId]
            );

            if ($title && isset($title['bonus_points']) && $title['bonus_points']) {
                return (int) $title['bonus_points'];
            }
        } catch (\Throwable $e) {
            // 🆕 اگر ستون bonus_points وجود ندارد، ۰ برگردان
            log_message("⚠️ getTitleBonus error: " . $e->getMessage());
        }

        return 0;
    }

    /**
     * 📈 به‌روزرسانی XP کاربر
     */
    public function updateUserXP(int $userId, int $xpToAdd): array
    {
        // ۱. گرفتن XP فعلی
        $currentXP = $this->db->fetchOne(
            "SELECT total_xp, current_level FROM user_xp WHERE user_id = ?",
            [$userId]
        );

        if (!$currentXP) {
            // ایجاد رکورد جدید
            $this->db->insert('user_xp', [
                'user_id' => $userId,
                'total_xp' => $xpToAdd,
                'current_level' => 1,
                'xp_to_next_level' => 100,
            ]);
            $currentXP = [
                'total_xp' => $xpToAdd,
                'current_level' => 1,
            ];
        } else {
            // به‌روزرسانی
            $newTotalXP = (int) $currentXP['total_xp'] + $xpToAdd;
            $this->db->update(
                'user_xp',
                ['total_xp' => $newTotalXP],
                'user_id = ?',
                [$userId]
            );
            $currentXP['total_xp'] = $newTotalXP;
        }

        // ۲. بررسی ارتقاء سطح
        $levelUp = $this->checkLevelUp($userId, (int) $currentXP['total_xp']);

        return [
            'total_xp' => (int) $currentXP['total_xp'],
            'current_level' => (int) $currentXP['current_level'],
            'level_up' => $levelUp,
        ];
    }

    /**
     * 🎖️ بررسی ارتقاء سطح
     */
    private function checkLevelUp(int $userId, int $totalXP): bool
    {
        $currentLevel = $this->db->fetchOne(
            "SELECT * FROM player_levels
             WHERE ? BETWEEN min_xp AND max_xp
             ORDER BY level DESC
             LIMIT 1",
            [$totalXP]
        );

        if (!$currentLevel) return false;

        $userXP = $this->db->fetchOne(
            "SELECT current_level FROM user_xp WHERE user_id = ?",
            [$userId]
        );

        if (!$userXP) return false;

        $oldLevel = (int) $userXP['current_level'];
        $newLevel = (int) $currentLevel['level'];

        if ($newLevel > $oldLevel) {
            $this->db->update(
                'user_xp',
                [
                    'current_level' => $newLevel,
                    'xp_to_next_level' => $this->getXPForNextLevel($newLevel),
                ],
                'user_id = ?',
                [$userId]
            );

            // Dispatch رویداد
            $events = \Core\EventDispatcher::getInstance();
            $events->dispatch('level_up', [
                'user_id' => $userId,
                'old_level' => $oldLevel,
                'new_level' => $newLevel,
                'title' => $currentLevel['title'],
            ]);

            return true;
        }

        return false;
    }

    /**
     * 📊 گرفتن XP برای سطح بعدی
     */
    private function getXPForNextLevel(int $currentLevel): int
    {
        $nextLevel = $this->db->fetchOne(
            "SELECT min_xp FROM player_levels WHERE level = ?",
            [$currentLevel + 1]
        );

        return $nextLevel ? (int) $nextLevel['min_xp'] : 999999;
    }

    /**
     * 🃏 گرفتن اطلاعات کارت
     */
    public function getCardInfo(?int $cardId): ?array
    {
        if (!$cardId) return null;

        $card = $this->cardRepo->findById($cardId);
        if (!$card) return null;

        return [
            'id' => $card->id,
            'name' => $card->name,
            'emoji' => $card->emoji,
            'multiplier' => (float) $card->score_multiplier,
            'rarity' => $card->rarity,
        ];
    }

    /**
     * 🏆 گرفتن اطلاعات نوع برد
     */
    public function getWinTypeInfo(?int $winTypeId): ?array
    {
        if (!$winTypeId) return null;

        $winType = $this->db->fetchOne(
            "SELECT * FROM win_types WHERE id = ? AND is_active = 1",
            [$winTypeId]
        );

        if (!$winType) return null;

        return [
            'id' => $winType['id'],
            'name' => $winType['name'],
            'icon' => $winType['icon'],
            'multiplier' => (float) $winType['score_multiplier'],
        ];
    }

    /**
     * 📊 گرفتن همه تنظیمات امتیازدهی
     */
    public function getScoringSettings(): array
    {
        return [
            'base_score' => (float) $this->settings->get('scoring_base_score', 1.0),
            'xp_multiplier' => (float) $this->settings->get('scoring_xp_multiplier', 2.0),
            'win_bonus' => (int) $this->settings->get('scoring_win_bonus', 15),
            'game_bonus' => (int) $this->settings->get('scoring_game_bonus', 5),
            'team_multiplier' => (float) $this->settings->get('scoring_team_multiplier', 1.5),
            'winner_bonus' => (int) $this->settings->get('scoring_winner_bonus', 50),
            'min_target_wins' => (int) $this->settings->get('scoring_min_target_wins', 3),
        ];
    }

    /**
     * 🆕 اضافه کردن امتیاز به شرکت‌کننده
     * 
     * این متد برای سازگاری با کدهایی که از ScoringService استفاده می‌کنند
     * اضافه شده است.
     *
     * @param int $participantId شناسه شرکت‌کننده
     * @param float $score امتیاز اضافه شده
     * @return void
     */
    public function addScoreToParticipant(int $participantId, float $score): void
    {
        // گرد کردن به ۲ رقم اعشار
        $roundedScore = round($score, 2);

        $this->db->query(
            "UPDATE game_participants SET total_score = total_score + ? WHERE id = ?",
            [$roundedScore, $participantId]
        );
    }
}