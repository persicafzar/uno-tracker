<?php

namespace Application\Services;

use Core\Database;

class RuleEvaluator
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * ارزیابی یک شرط JSON
     * 
     * @param array $conditionJson ساختار شرط
     * @param array $context اطلاعات زمینه (user_id, game_id, etc.)
     * @return bool آیا شرط برقرار است؟
     */
    public function evaluate(array $conditionJson, array $context): bool
    {
        $rule = $conditionJson['rule'] ?? null;
        
        if (!$rule) {
            return false;
        }

        switch ($rule) {
            case 'max_consecutive_wins_with_card':
                return $this->evaluateConsecutiveWinsWithCard($conditionJson, $context);
            
            case 'total_wins_with_card':
                return $this->evaluateTotalWinsWithCard($conditionJson, $context);
            
            case 'win_by_rarity_percentage':
                return $this->evaluateWinByRarityPercentage($conditionJson, $context);
            
            case 'win_by_card_type':
                return $this->evaluateWinByCardType($conditionJson, $context);
            
            case 'win_streak':
                return $this->evaluateWinStreak($conditionJson, $context);
            
            case 'total_games_played':
                return $this->evaluateTotalGamesPlayed($conditionJson, $context);
            
            case 'combo_wins':
                return $this->evaluateComboWins($conditionJson, $context);
            
            default:
                return false;
        }
    }

    /**
     * بررسی برد متوالی با یک کارت خاص
     * شرط: {"rule": "max_consecutive_wins_with_card", "card_slug": "king", "min_streak": 3}
     */
    private function evaluateConsecutiveWinsWithCard(array $condition, array $context): bool
    {
        $userId = $context['user_id'] ?? null;
        $cardSlug = $condition['card_slug'] ?? null;
        $minStreak = $condition['min_streak'] ?? 3;

        if (!$userId || !$cardSlug) {
            return false;
        }

        // گرفتن کارت
        $card = $this->db->fetchOne(
            "SELECT id FROM cards WHERE slug = ?",
            [$cardSlug]
        );

        if (!$card) {
            return false;
        }

        // گرفتن رکورد فعلی کاربر با این کارت
        $mastery = $this->db->fetchOne(
            "SELECT max_streak FROM card_mastery WHERE user_id = ? AND card_id = ?",
            [$userId, $card['id']]
        );

        return $mastery && (int) $mastery['max_streak'] >= $minStreak;
    }

    /**
     * بررسی تعداد کل برد با یک کارت
     * شرط: {"rule": "total_wins_with_card", "card_slug": "shuffle", "min_wins": 10}
     */
    private function evaluateTotalWinsWithCard(array $condition, array $context): bool
    {
        $userId = $context['user_id'] ?? null;
        $cardSlug = $condition['card_slug'] ?? null;
        $minWins = $condition['min_wins'] ?? 10;

        if (!$userId || !$cardSlug) {
            return false;
        }

        $card = $this->db->fetchOne(
            "SELECT id FROM cards WHERE slug = ?",
            [$cardSlug]
        );

        if (!$card) {
            return false;
        }

        $mastery = $this->db->fetchOne(
            "SELECT total_wins FROM card_mastery WHERE user_id = ? AND card_id = ?",
            [$userId, $card['id']]
        );

        return $mastery && (int) $mastery['total_wins'] >= $minWins;
    }

    /**
     * بررسی درصد برد بر اساس کمیابی کارت
     * شرط: {"rule": "win_by_rarity_percentage", "rarity": "legendary", "min_percent": 35}
     */
    private function evaluateWinByRarityPercentage(array $condition, array $context): bool
    {
        $userId = $context['user_id'] ?? null;
        $rarity = $condition['rarity'] ?? null;
        $minPercent = $condition['min_percent'] ?? 30;

        if (!$userId || !$rarity) {
            return false;
        }

        // کل بردهای کاربر
        $totalWins = $this->db->fetchValue(
            "SELECT COUNT(*) FROM game_rounds gr
             JOIN game_participants gp ON gr.winner_participant_id = gp.id
             WHERE gp.user_id = ?",
            [$userId]
        );

        if ($totalWins == 0) {
            return false;
        }

        // بردها با کارت‌های این کمیابی
        $rarityWins = $this->db->fetchValue(
            "SELECT COUNT(*) FROM game_rounds gr
             JOIN game_participants gp ON gr.winner_participant_id = gp.id
             JOIN cards c ON gr.winning_card_id = c.id
             WHERE gp.user_id = ? AND c.rarity = ?",
            [$userId, $rarity]
        );

        $percentage = ($rarityWins / $totalWins) * 100;
        return $percentage >= $minPercent;
    }

    /**
     * بررسی درصد برد با کارت‌های خاص
     * شرط: {"rule": "win_by_card_type", "card_slugs": ["draw_two", "wild_draw_four"], "min_percent": 40}
     */
    private function evaluateWinByCardType(array $condition, array $context): bool
    {
        $userId = $context['user_id'] ?? null;
        $cardSlugs = $condition['card_slugs'] ?? [];
        $minPercent = $condition['min_percent'] ?? 30;

        if (!$userId || empty($cardSlugs)) {
            return false;
        }

        $totalWins = $this->db->fetchValue(
            "SELECT COUNT(*) FROM game_rounds gr
             JOIN game_participants gp ON gr.winner_participant_id = gp.id
             WHERE gp.user_id = ?",
            [$userId]
        );

        if ($totalWins == 0) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($cardSlugs), '?'));
        $specificWins = $this->db->fetchValue(
            "SELECT COUNT(*) FROM game_rounds gr
             JOIN game_participants gp ON gr.winner_participant_id = gp.id
             JOIN cards c ON gr.winning_card_id = c.id
             WHERE gp.user_id = ? AND c.slug IN ($placeholders)",
            array_merge([$userId], $cardSlugs)
        );

        $percentage = ($specificWins / $totalWins) * 100;
        return $percentage >= $minPercent;
    }

    /**
     * بررسی برد متوالی (بدون توجه به کارت)
     * شرط: {"rule": "win_streak", "min_streak": 5}
     */
    private function evaluateWinStreak(array $condition, array $context): bool
    {
        $userId = $context['user_id'] ?? null;
        $minStreak = $condition['min_streak'] ?? 5;

        if (!$userId) {
            return false;
        }

        $streak = $this->db->fetchValue(
            "SELECT current_streak FROM leaderboard_cache WHERE user_id = ?",
            [$userId]
        );

        return $streak && (int) $streak >= $minStreak;
    }

    /**
     * بررسی تعداد کل بازی‌ها
     * شرط: {"rule": "total_games_played", "min_games": 50}
     */
    private function evaluateTotalGamesPlayed(array $condition, array $context): bool
    {
        $userId = $context['user_id'] ?? null;
        $minGames = $condition['min_games'] ?? 50;

        if (!$userId) {
            return false;
        }

        $totalGames = $this->db->fetchValue(
            "SELECT total_games FROM leaderboard_cache WHERE user_id = ?",
            [$userId]
        );

        return $totalGames && (int) $totalGames >= $minGames;
    }

    /**
     * بررسی برد ترکیبی (چند کارت مختلف در چند دور متوالی)
     * شرط: {"rule": "combo_wins", "card_slugs": ["king", "shuffle", "gift"], "in_rounds": 3}
     */
    private function evaluateComboWins(array $condition, array $context): bool
    {
        $userId = $context['user_id'] ?? null;
        $cardSlugs = $condition['card_slugs'] ?? [];
        $inRounds = $condition['in_rounds'] ?? 3;

        if (!$userId || empty($cardSlugs)) {
            return false;
        }

        // گرفتن آخرین N دور کاربر
        $recentRounds = $this->db->fetchAll(
            "SELECT c.slug FROM game_rounds gr
             JOIN game_participants gp ON gr.winner_participant_id = gp.id
             JOIN cards c ON gr.winning_card_id = c.id
             WHERE gp.user_id = ?
             ORDER BY gr.round_number DESC
             LIMIT ?",
            [$userId, $inRounds]
        );

        if (count($recentRounds) < $inRounds) {
            return false;
        }

        // بررسی اینکه آیا تمام کارت‌های مورد نیاز در این دورها وجود دارند
        $foundSlugs = array_column($recentRounds, 'slug');
        foreach ($cardSlugs as $slug) {
            if (!in_array($slug, $foundSlugs)) {
                return false;
            }
        }

        return true;
    }
}