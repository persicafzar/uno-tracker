<?php

namespace Application\Services;

use Core\Database;
use Domain\GameParticipant;

class TeamBuilderService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }



    /**
     * گروه‌بندی بازیکنان بر اساس الگوریتم انتخاب شده
     * @return array آرایه‌ای از تیم‌ها با اطلاعات کامل بازیکنان
     */
    public function buildTeams(array $playerIds, string $algorithm, int $teamSize = 2): array
    {
        switch ($algorithm) {
            case 'random':
                $groups = $this->buildRandom($playerIds, $teamSize);
                break;

            case 'balanced':
                $groups = $this->buildBalanced($playerIds, $teamSize);
                break;

            case 'anti_synergy':
                $groups = $this->buildAntiSynergy($playerIds, $teamSize);
                break;

            case 'anti_repetition':
                $groups = $this->buildAntiRepetition($playerIds, $teamSize);
                break;

            default:
                throw new \InvalidArgumentException("الگوریتم نامعتبر: {$algorithm}");
        }

        // 🆕 تبدیل player_ids به اطلاعات کامل بازیکنان
        $teamsWithDetails = [];
        foreach ($groups as $index => $group) {
            $players = [];
            foreach ($group as $playerId) {
                $player = $this->db->fetchOne(
                    "SELECT id, nickname, real_name, avatar_path FROM users WHERE id = ?",
                    [$playerId]
                );
                if ($player) {
                    $players[] = $player;
                }
            }

            $teamsWithDetails[] = [
                'team_number' => $index + 1,
                'players' => $players,
                'player_ids' => $group,
            ];
        }

        return $teamsWithDetails;
    }

    /**
     * الگوریتم ۱: کاملاً تصادفی
     */
    private function buildRandom(array $playerIds, int $teamSize): array
    {
        shuffle($playerIds);
        return array_chunk($playerIds, $teamSize);
    }

    /**
     * الگوریتم ۲: بالانس (ضعیف با قوی) - Snake Draft
     */
    private function buildBalanced(array $playerIds, int $teamSize): array
    {
        // گرفتن امتیازات بازیکنان
        $playersWithScores = [];
        foreach ($playerIds as $userId) {
            $stats = $this->db->fetchOne(
                "SELECT total_points FROM leaderboard_cache WHERE user_id = ?",
                [$userId]
            );
            $playersWithScores[] = [
                'user_id' => $userId,
                'score' => $stats['total_points'] ?? 0  // 🆕 اگر null باشد، 0 استفاده شود
            ];
        }

        // مرتب‌سازی بر اساس امتیاز (نزولی)
        usort($playersWithScores, fn($a, $b) => $b['score'] <=> $a['score']);

        // 🆕 اصلاح: محاسبه دقیق numTeams
        $numTeams = (int) ceil(count($playerIds) / $teamSize);
        $teams = array_fill(0, $numTeams, []);

        $forward = true;
        $teamIndex = 0;

        foreach ($playersWithScores as $player) {
            // 🆕 اطمینان از اینکه teamIndex در محدوده است
            if ($teamIndex >= $numTeams) {
                $teamIndex = $numTeams - 1;
            }
            if ($teamIndex < 0) {
                $teamIndex = 0;
            }

            // 🆕 اطمینان از اینکه تیم پر نشده
            if (count($teams[$teamIndex]) >= $teamSize) {
                // پیدا کردن تیم بعدی که جا دارد
                $found = false;
                for ($i = 0; $i < $numTeams; $i++) {
                    if (count($teams[$i]) < $teamSize) {
                        $teamIndex = $i;
                        $found = true;
                        break;
                    }
                }
                if (!$found) break; // همه تیم‌ها پر شده‌اند
            }

            $teams[$teamIndex][] = $player['user_id'];

            // 🆕 اصلاح الگوریتم Snake Draft
            if ($forward) {
                if ($teamIndex === $numTeams - 1) {
                    $forward = false;
                } else {
                    $teamIndex++;
                }
            } else {
                if ($teamIndex === 0) {
                    $forward = true;
                    $teamIndex++;
                } else {
                    $teamIndex--;
                }
            }
        }

        return $teams;
    }

    /**
     * الگوریتم ۳: ضد سینرژی (جدا کردن یاران همیشگی)
     */
    private function buildAntiSynergy(array $playerIds, int $teamSize): array
    {
        // ساخت ماتریس هم‌تیمی شدن
        $synergyMatrix = [];

        foreach ($playerIds as $id1) {
            foreach ($playerIds as $id2) {
                if ($id1 < $id2) {
                    $count = $this->db->fetchValue(
                        "SELECT games_together FROM teammate_history 
                         WHERE (user_id_1 = ? AND user_id_2 = ?) 
                         OR (user_id_1 = ? AND user_id_2 = ?)",
                        [$id1, $id2, $id1, $id2]
                    );
                    $synergyMatrix[$id1][$id2] = $count ?? 0;
                    $synergyMatrix[$id2][$id1] = $count ?? 0;
                }
            }
        }

        // مرتب‌سازی بازیکنان بر اساس بیشترین هم‌تیمی شدن
        usort($playerIds, function ($a, $b) use ($synergyMatrix) {
            $sumA = array_sum($synergyMatrix[$a] ?? []);
            $sumB = array_sum($synergyMatrix[$b] ?? []);
            return $sumB <=> $sumA;
        });

        // گروه‌بندی با در نظر گرفتن سینرژی
        $numTeams = ceil(count($playerIds) / $teamSize);
        $teams = array_fill(0, $numTeams, []);

        foreach ($playerIds as $playerId) {
            // پیدا کردن تیمی که کمترین سینرژی را با بازیکن دارد
            $bestTeam = 0;
            $minSynergy = PHP_INT_MAX;

            foreach ($teams as $teamIndex => $team) {
                if (count($team) >= $teamSize) continue;

                $synergy = 0;
                foreach ($team as $teammateId) {
                    $synergy += $synergyMatrix[$playerId][$teammateId] ?? 0;
                }

                if ($synergy < $minSynergy) {
                    $minSynergy = $synergy;
                    $bestTeam = $teamIndex;
                }
            }

            $teams[$bestTeam][] = $playerId;
        }

        return $teams;
    }

    /**
     * الگوریتم ۴: ضد تکرار (جلوگیری از تکرار ترکیب‌های قبلی)
     */
    private function buildAntiRepetition(array $playerIds, int $teamSize): array
    {
        // گرفتن ترکیب‌های ۵ بازی آخر
        $recentCombinations = $this->getRecentTeamCombinations($playerIds, 5);

        // شروع با گروه‌بندی تصادفی
        $teams = $this->buildRandom($playerIds, $teamSize);

        // تلاش برای شکستن ترکیب‌های تکراری
        $maxAttempts = 10;
        $attempt = 0;

        while ($attempt < $maxAttempts && $this->isCombinationRepeated($teams, $recentCombinations)) {
            $teams = $this->buildRandom($playerIds, $teamSize);
            $attempt++;
        }

        return $teams;
    }

    /**
     * گرفتن ترکیب‌های تیمی اخیر (🆕 اصلاح شده با Alias)
     */
    private function getRecentTeamCombinations(array $playerIds, int $limit): array
    {
        if (empty($playerIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($playerIds), '?'));

        // 🆕 اصلاح کوئری: استفاده از AS user1 و AS user2 برای جلوگیری از تداخل نام ستون‌ها در PDO
        $results = $this->db->fetchAll(
            "SELECT DISTINCT gp1.user_id AS user1, gp2.user_id AS user2
             FROM game_participants gp1
             JOIN game_participants gp2 ON gp1.game_id = gp2.game_id AND gp1.team_id = gp2.team_id
             WHERE gp1.user_id IN ($placeholders) 
             AND gp2.user_id IN ($placeholders)
             AND gp1.user_id < gp2.user_id
             ORDER BY gp1.game_id DESC
             LIMIT ?",
            array_merge($playerIds, $playerIds, [$limit * 10])
        );

        return $results ?: [];
    }

    /**
     * بررسی تکراری بودن ترکیب‌ها (🆕 اصلاح شده و بهینه‌سازی شده با Hash Map)
     */
    private function isCombinationRepeated(array $teams, array $recentCombinations): bool
    {
        if (empty($recentCombinations)) {
            return false;
        }

        // 🆕 تبدیل ترکیب‌های اخیر به یک آرایه Hash برای جستجوی سریع (O(1))
        $recentPairs = [];
        foreach ($recentCombinations as $combo) {
            // استفاده از کلیدهای صحیح user1 و user2
            if (isset($combo['user1']) && isset($combo['user2'])) {
                $u1 = min($combo['user1'], $combo['user2']);
                $u2 = max($combo['user1'], $combo['user2']);
                // ساخت یک کلید یکتا برای هر جفت بازیکن (مثلاً "3-7")
                $recentPairs["{$u1}-{$u2}"] = true;
            }
        }

        if (empty($recentPairs)) {
            return false;
        }

        // بررسی تیم‌های فعلی
        foreach ($teams as $team) {
            $teamSize = count($team);
            for ($i = 0; $i < $teamSize; $i++) {
                for ($j = $i + 1; $j < $teamSize; $j++) {
                    $u1 = min($team[$i], $team[$j]);
                    $u2 = max($team[$i], $team[$j]);
                    $pairKey = "{$u1}-{$u2}";

                    // اگر این جفت بازیکن قبلاً هم‌تیمی بوده‌اند
                    if (isset($recentPairs[$pairKey])) {
                        return true; // ترکیب تکراری پیدا شد
                    }
                }
            }
        }

        return false; // هیچ ترکیب تکراری یافت نشد
    }
}
