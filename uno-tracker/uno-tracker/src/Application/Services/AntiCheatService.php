<?php

namespace Application\Services;

use Core\Database;
use Infrastructure\Repositories\SettingsRepository;

class AntiCheatService
{
    private Database $db;
    private array $settings = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        $settingsRepo = SettingsRepository::getInstance();
        $this->settings = [
            'enabled' => (bool) $settingsRepo->get('anticheat_enabled', true),
            'min_round_duration' => (int) $settingsRepo->get('anticheat_min_round_duration', 10),
            'min_players' => (int) $settingsRepo->get('anticheat_min_players', 3),
            'max_guests' => (int) $settingsRepo->get('anticheat_max_guests', 2),
            'max_guest_ratio' => (float) $settingsRepo->get('anticheat_max_guest_ratio', 1.0),
            'min_members' => (int) $settingsRepo->get('anticheat_min_members', 2),
            'max_win_percentage' => (int) $settingsRepo->get('anticheat_max_win_percentage', 80),
            'max_games_per_hour' => (int) $settingsRepo->get('anticheat_max_games_per_hour', 2),
            'min_target_wins_threshold' => (int) $settingsRepo->get('anticheat_min_target_wins_threshold', 5),
            'max_low_target_games' => (int) $settingsRepo->get('anticheat_max_low_target_games', 3),
            'new_account_hours' => (int) $settingsRepo->get('anticheat_new_account_hours', 24),
            'max_accounts_per_ip' => (int) $settingsRepo->get('anticheat_max_accounts_per_ip', 2),
            'max_games_created_per_day' => (int) $settingsRepo->get('anticheat_max_games_created_per_day', 3),
            'max_solo_games_per_day' => (int) $settingsRepo->get('anticheat_max_solo_games_per_day', 2),
            'max_friendly_games_per_day' => (int) $settingsRepo->get('anticheat_max_friendly_games_per_day', 2),
            // 🆕 تنظیمات جدید برای تشخیص تبانی
            'collusion_min_games' => (int) $settingsRepo->get('anticheat_collusion_min_games', 3),
            'collusion_max_opponents' => (int) $settingsRepo->get('anticheat_collusion_max_opponents', 1),
        ];
    }

    // ============================================
    // ✅ بررسی محدودیت‌های ایجاد بازی
    // ============================================
    public function checkGameCreation(int $userId, string $gameMode): array
    {
        if (!$this->settings['enabled']) {
            return ['allowed' => true, 'reasons' => []];
        }

        // 🆕 معافیت ادمین از محدودیت‌ها
        if ($this->isAdmin($userId)) {
            return ['allowed' => true, 'reasons' => []];
        }

        $reasons = [];

        $user = $this->db->fetchOne(
            "SELECT created_at FROM users WHERE id = ?",
            [$userId]
        );

        if ($user) {
            $accountAge = time() - strtotime($user['created_at']);
            $minAge = $this->settings['new_account_hours'] * 3600;
            if ($accountAge < $minAge) {
                $reasons[] = 'حساب شما تازه‌تاسیس است. لطفاً بعد از ' . $this->settings['new_account_hours'] . ' ساعت دوباره تلاش کنید.';
            }
        }

        $today = date('Y-m-d');
        $gamesCreatedToday = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM games WHERE referee_id = ? AND DATE(created_at) = ?",
            [$userId, $today]
        );

        if ($gamesCreatedToday && $gamesCreatedToday['count'] >= $this->settings['max_games_created_per_day']) {
            $reasons[] = 'شما امروز حداکثر ' . $this->settings['max_games_created_per_day'] . ' بازی ایجاد کرده‌اید.';
        }

        $gamesPlayedToday = $this->db->fetchAll(
            "SELECT g.game_mode, COUNT(*) as count
             FROM games g
             JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? AND DATE(g.created_at) = ?
             GROUP BY g.game_mode",
            [$userId, $today]
        );

        foreach ($gamesPlayedToday as $stat) {
            if ($stat['game_mode'] === 'solo' && $stat['count'] >= $this->settings['max_solo_games_per_day']) {
                $reasons[] = 'شما امروز حداکثر ' . $this->settings['max_solo_games_per_day'] . ' بازی انفرادی انجام داده‌اید.';
            } elseif ($stat['game_mode'] === 'friendly' && $stat['count'] >= $this->settings['max_friendly_games_per_day']) {
                $reasons[] = 'شما امروز حداکثر ' . $this->settings['max_friendly_games_per_day'] . ' بازی دوستانه انجام داده‌اید.';
            }
        }

        return [
            'allowed' => empty($reasons),
            'reasons' => $reasons,
        ];
    }

    // ============================================
    // ✅ بررسی کامل یک بازی
    // ============================================
    public function checkGame(int $gameId): array
    {
        if (!$this->settings['enabled']) {
            return ['is_suspicious' => false, 'cheat_types' => [], 'risk_level' => 'safe'];
        }

        $cheatTypes = [];
        $details = [];

        // ۱. بررسی سرعت بازی
        $speedCheck = $this->checkGameSpeed($gameId);
        if ($speedCheck['is_suspicious']) {
            $cheatTypes[] = 'fast_game';
            $details['speed'] = $speedCheck;
        }

        // ۲. بررسی بازیکنان
        $playersCheck = $this->checkGameParticipants($gameId);
        if ($playersCheck['is_suspicious']) {
            $cheatTypes[] = 'invalid_players';
            $details['players'] = $playersCheck;
        }

        // ۳. بررسی بازیکنان مهمان
        $guestsCheck = $this->checkGuestPlayers($gameId);
        if ($guestsCheck['is_suspicious']) {
            $cheatTypes[] = 'too_many_guests';
            $details['guests'] = $guestsCheck;
        }

        // ۴. بررسی بازی فقط مهمان
        $guestOnlyCheck = $this->checkGuestOnlyGame($gameId);
        if ($guestOnlyCheck['is_suspicious']) {
            $cheatTypes[] = 'guest_only_game';
            $details['guest_only'] = $guestOnlyCheck;
        }

        // ۵. بررسی الگوی برد (اصلاح شده - فقط در بازی کوتاه با مهمان ۱۰۰٪ چک می‌شود)
        $winPatternCheck = $this->checkWinPattern($gameId);
        if ($winPatternCheck['is_suspicious']) {
            $cheatTypes[] = 'unusual_win_pattern';
            $details['win_pattern'] = $winPatternCheck;
        }

        // ۶. بررسی هدف برد کم
        $lowTargetCheck = $this->checkLowTargetGame($gameId);
        if ($lowTargetCheck['is_suspicious']) {
            $cheatTypes[] = 'low_target_wins';
            $details['low_target'] = $lowTargetCheck;
        }

        // محاسبه سطح ریسک
        $riskLevel = $this->calculateRiskLevel($cheatTypes);

        return [
            'is_suspicious' => !empty($cheatTypes),
            'cheat_types' => $cheatTypes,
            'risk_level' => $riskLevel,
            'details' => $details,
        ];
    }

    // ============================================
    // 🆕 تشخیص حلقه تبانی (Collusion Loop Detection)
    // ============================================
    public function checkCollusionLoop(int $userId): array
    {
        if (!$this->settings['enabled']) {
            return ['is_suspicious' => false];
        }

        // 🆕 معافیت ادمین
        if ($this->isAdmin($userId)) {
            return ['is_suspicious' => false];
        }

        $minGames = $this->settings['collusion_min_games'];
        $maxOpponents = $this->settings['collusion_max_opponents'];

        // گرفتن بازی‌های ۱ ساعت اخیر کاربر
        $recentGames = $this->db->fetchAll(
            "SELECT g.id as game_id,
                    GROUP_CONCAT(DISTINCT CASE 
                        WHEN gp2.user_id IS NOT NULL AND gp2.user_id != gp.user_id 
                        THEN gp2.user_id 
                        WHEN gp2.guest_name IS NOT NULL 
                        THEN CONCAT('guest_', gp2.guest_name)
                    END) as opponents
             FROM games g
             JOIN game_participants gp ON g.id = gp.game_id
             LEFT JOIN game_participants gp2 ON g.id = gp2.game_id 
                 AND (gp2.user_id != gp.user_id OR gp2.user_id IS NULL)
             WHERE gp.user_id = ?
             AND g.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
             AND g.status IN ('finished', 'active')
             GROUP BY g.id",
            [$userId]
        );

        $totalGames = count($recentGames);

        // اگر تعداد بازی‌ها کمتر از حداقل باشد، بررسی نمی‌کنیم
        if ($totalGames < $minGames) {
            return [
                'is_suspicious' => false,
                'total_games' => $totalGames,
                'min_required' => $minGames,
            ];
        }

        // جمع‌آوری تمام حریف‌های یکتا
        $allOpponents = [];
        foreach ($recentGames as $game) {
            if (!empty($game['opponents'])) {
                $opponents = explode(',', $game['opponents']);
                foreach ($opponents as $opponent) {
                    $opponent = trim($opponent);
                    if (!empty($opponent)) {
                        $allOpponents[] = $opponent;
                    }
                }
            }
        }

        $uniqueOpponents = array_unique($allOpponents);
        $uniqueCount = count($uniqueOpponents);

        // اگر تعداد حریف‌های یکتا کمتر یا مساوی maxOpponents باشد، مشکوک است
        $isSuspicious = $uniqueCount <= $maxOpponents;

        return [
            'is_suspicious' => $isSuspicious,
            'total_games' => $totalGames,
            'unique_opponents' => $uniqueCount,
            'max_allowed_opponents' => $maxOpponents,
            'opponents_list' => $uniqueOpponents,
        ];
    }

    // ============================================
    // ✅ اصلاح شده: بررسی الگوی برد
    // ============================================
    public function checkWinPattern(int $gameId): array
    {
        $rounds = $this->db->fetchAll(
            "SELECT winner_participant_id, COUNT(*) as wins
             FROM game_rounds
             WHERE game_id = ?
             GROUP BY winner_participant_id",
            [$gameId]
        );

        $totalRounds = array_sum(array_column($rounds, 'wins'));

        if ($totalRounds === 0) {
            return ['is_suspicious' => false];
        }

        // بررسی آیا بازی مهمان دارد
        $hasGuest = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM game_participants WHERE game_id = ? AND user_id IS NULL",
            [$gameId]
        );
        $hasGuest = ($hasGuest['count'] ?? 0) > 0;

        $maxWins = max(array_column($rounds, 'wins'));
        $winPercentage = ($maxWins / $totalRounds) * 100;

        // 🆕 قانون جدید: فقط در بازی کوتاه با مهمان، ۱۰۰٪ چک می‌شود
        // بازی کوتاه = کمتر از ۵ دور
        $isShortGame = $totalRounds < 5;

        if ($isShortGame && $hasGuest) {
            // در بازی کوتاه با مهمان، فقط ۱۰۰٪ برد مشکوک است
            $isSuspicious = $winPercentage >= 100;
        } else {
            // در سایر بازی‌ها، قانون قبلی (۸۰٪) اعمال می‌شود
            $isSuspicious = $winPercentage > $this->settings['max_win_percentage'];
        }

        return [
            'is_suspicious' => $isSuspicious,
            'win_percentage' => round($winPercentage, 2),
            'max_wins' => $maxWins,
            'total_rounds' => $totalRounds,
            'threshold' => ($isShortGame && $hasGuest) ? 100 : $this->settings['max_win_percentage'],
            'is_short_game_with_guest' => $isShortGame && $hasGuest,
        ];
    }

    // ============================================
    // سایر متدها (بدون تغییر)
    // ============================================
    public function checkGameSpeed(int $gameId): array
    {
        $rounds = $this->db->fetchAll(
            "SELECT r1.created_at as start_time, r2.created_at as end_time,
                    TIMESTAMPDIFF(SECOND, r1.created_at, r2.created_at) as duration
             FROM game_rounds r1
             LEFT JOIN game_rounds r2 ON r2.game_id = r1.game_id AND r2.round_number = r1.round_number + 1
             WHERE r1.game_id = ?
             ORDER BY r1.round_number",
            [$gameId]
        );

        $fastRounds = 0;
        $totalRounds = count($rounds);

        foreach ($rounds as $round) {
            if ($round['duration'] && $round['duration'] < $this->settings['min_round_duration']) {
                $fastRounds++;
            }
        }

        $isSuspicious = $totalRounds > 0 && ($fastRounds / $totalRounds) > 0.5;

        return [
            'is_suspicious' => $isSuspicious,
            'fast_rounds' => $fastRounds,
            'total_rounds' => $totalRounds,
            'min_duration' => $this->settings['min_round_duration'],
        ];
    }

    public function checkGameParticipants(int $gameId): array
    {
        $participants = $this->db->fetchOne(
            "SELECT
                COUNT(*) as total,
                SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as members,
                SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guests
             FROM game_participants
             WHERE game_id = ?",
            [$gameId]
        );

        $isSuspicious = $participants['total'] < $this->settings['min_players']
            || $participants['members'] < $this->settings['min_members'];

        return [
            'is_suspicious' => $isSuspicious,
            'total' => $participants['total'],
            'members' => $participants['members'],
            'guests' => $participants['guests'],
            'min_players' => $this->settings['min_players'],
            'min_members' => $this->settings['min_members'],
        ];
    }

    public function checkGuestPlayers(int $gameId): array
    {
        $participants = $this->db->fetchOne(
            "SELECT
                SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as members,
                SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guests
             FROM game_participants
             WHERE game_id = ?",
            [$gameId]
        );

        $members = $participants['members'] ?? 0;
        $guests = $participants['guests'] ?? 0;

        $isSuspicious = $guests > $this->settings['max_guests']
            || ($members > 0 && ($guests / $members) > $this->settings['max_guest_ratio']);

        return [
            'is_suspicious' => $isSuspicious,
            'members' => $members,
            'guests' => $guests,
            'max_guests' => $this->settings['max_guests'],
            'max_ratio' => $this->settings['max_guest_ratio'],
        ];
    }

    public function checkGuestOnlyGame(int $gameId): array
    {
        $participants = $this->db->fetchOne(
            "SELECT
                SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as members
             FROM game_participants
             WHERE game_id = ?",
            [$gameId]
        );

        $isSuspicious = ($participants['members'] ?? 0) < 2;

        return [
            'is_suspicious' => $isSuspicious,
            'members' => $participants['members'] ?? 0,
        ];
    }

    public function checkLowTargetGame(int $gameId): array
    {
        $game = $this->db->fetchOne(
            "SELECT target_wins FROM games WHERE id = ?",
            [$gameId]
        );

        $isSuspicious = $game && $game['target_wins'] < $this->settings['min_target_wins_threshold'];

        return [
            'is_suspicious' => $isSuspicious,
            'target_wins' => $game['target_wins'] ?? 0,
            'threshold' => $this->settings['min_target_wins_threshold'],
        ];
    }

    private function calculateRiskLevel(array $cheatTypes): string
    {
        $count = count($cheatTypes);
        if ($count === 0) return 'safe';
        if ($count === 1) return 'low';
        if ($count === 2) return 'medium';
        if ($count === 3) return 'high';
        return 'critical';
    }

    // ============================================
    // 🆕 متد کمکی: بررسی ادمین بودن
    // ============================================
    private function isAdmin(int $userId): bool
    {
        $user = $this->db->fetchOne(
            "SELECT role FROM users WHERE id = ?",
            [$userId]
        );
        return $user && in_array($user['role'], ['admin', 'super_admin']);
    }

    // ============================================
    // سایر متدهای عمومی (بدون تغییر)
    // ============================================
    public function recordSuspiciousGame(int $gameId, ?int $userId, array $cheatTypes, string $riskLevel, array $details = []): void
    {
        // 🆕 بررسی اینکه آیا این بازی قبلاً به عنوان مشکوک ثبت شده است
        $existing = $this->db->fetchOne(
            "SELECT id FROM suspicious_games WHERE game_id = ?",
            [$gameId]
        );

        if ($existing) {
            // اگر قبلاً ثبت شده، آن را به‌روزرسانی کن (به جای ایجاد رکورد جدید)
            $this->db->update('suspicious_games', [
                'cheat_types' => json_encode($cheatTypes, JSON_UNESCAPED_UNICODE),
                'risk_level' => $riskLevel,
                'details' => json_encode($details, JSON_UNESCAPED_UNICODE),
            ], 'id = ?', [$existing['id']]);
            return;
        }

        // اگر قبلاً ثبت نشده، رکورد جدید ایجاد کن
        $this->db->insert('suspicious_games', [
            'game_id' => $gameId,
            'user_id' => $userId,
            'cheat_types' => json_encode($cheatTypes, JSON_UNESCAPED_UNICODE),
            'risk_level' => $riskLevel,
            'details' => json_encode($details, JSON_UNESCAPED_UNICODE),
            'is_reviewed' => 0,
        ]);
    }

    public function getSuspiciousGames(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = ['1=1'];
        $params = [];

        if (isset($filters['risk_level']) && $filters['risk_level'] !== '') {
            $where[] = 'sg.risk_level = ?';
            $params[] = $filters['risk_level'];
        }

        // ✅ اصلاح حیاتی: بررسی صریح وجود کلید و خالی نبودن مقدار
        if (array_key_exists('is_reviewed', $filters) && $filters['is_reviewed'] !== '') {
            $where[] = 'sg.is_reviewed = ?';
            $params[] = (int) $filters['is_reviewed'];
        }

        if (isset($filters['user_id']) && $filters['user_id'] !== '') {
            $where[] = 'sg.user_id = ?';
            $params[] = (int) $filters['user_id'];
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        // 🆕 تغییر از LEFT JOIN به INNER JOIN - فقط بازی‌های موجود نمایش داده شوند
        $games = $this->db->fetchAll(
            "SELECT sg.*, 
                g.name as game_name, 
                g.game_mode, 
                g.target_wins, 
                sg.created_at as game_created_at,
                COALESCE(u.nickname, 'نامشخص') as referee_name
         FROM suspicious_games sg
         INNER JOIN games g ON sg.game_id = g.id
         LEFT JOIN users u ON g.referee_id = u.id
         WHERE {$whereClause}
         ORDER BY sg.created_at DESC
         LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as count 
         FROM suspicious_games sg
         INNER JOIN games g ON sg.game_id = g.id
         WHERE {$whereClause}",
            $params
        );

        return [
            'games' => $games,
            'total' => (int) ($total['count'] ?? 0),
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil(($total['count'] ?? 0) / $perPage),
        ];
    }

    public function recalculateUserStats(int $userId): array
    {
        $stats = $this->db->fetchOne(
            "SELECT
                COUNT(DISTINCT g.id) as total_games,
                SUM(CASE WHEN g.winner_participant_id = gp.id THEN 1 ELSE 0 END) as total_wins,
                SUM(gp.total_score) as total_points
             FROM games g
             JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ? AND g.status = 'finished'",
            [$userId]
        );

        $totalGames = (int) ($stats['total_games'] ?? 0);
        $totalWins = (int) ($stats['total_wins'] ?? 0);
        $totalPoints = (float) ($stats['total_points'] ?? 0);

        $newXP = $this->calculateXPFromStats($totalGames, $totalWins, $totalPoints);

        $this->db->update('user_xp', [
            'total_xp' => $newXP,
            'current_level' => $this->calculateLevel($newXP),
        ], 'user_id = ?', [$userId]);

        $this->db->update('leaderboard_cache', [
            'total_games' => $totalGames,
            'total_wins' => $totalWins,
            'total_points' => $totalPoints,
            'win_rate' => $totalGames > 0 ? ($totalWins / $totalGames) * 100 : 0,
        ], 'user_id = ?', [$userId]);

        return [
            'total_games' => $totalGames,
            'total_wins' => $totalWins,
            'total_points' => $totalPoints,
            'new_xp' => $newXP,
            'new_level' => $this->calculateLevel($newXP),
        ];
    }

    private function calculateXPFromStats(int $totalGames, int $totalWins, float $totalPoints): int
    {
        $xpMultiplier = (float) $this->db->fetchOne(
            "SELECT setting_value FROM system_settings WHERE setting_key = 'scoring_xp_multiplier'"
        )['setting_value'] ?? 2.0;

        $gameBonus = (int) $this->db->fetchOne(
            "SELECT setting_value FROM system_settings WHERE setting_key = 'scoring_game_bonus'"
        )['setting_value'] ?? 5;

        $winBonus = (int) $this->db->fetchOne(
            "SELECT setting_value FROM system_settings WHERE setting_key = 'scoring_win_bonus'"
        )['setting_value'] ?? 15;

        $scoreXP = (int) ($totalPoints * $xpMultiplier);
        $gamesXP = $totalGames * $gameBonus;
        $winsXP = $totalWins * $winBonus;

        return $scoreXP + $gamesXP + $winsXP;
    }

    private function calculateLevel(int $xp): int
    {
        $level = $this->db->fetchOne(
            "SELECT level FROM player_levels WHERE ? BETWEEN min_xp AND max_xp ORDER BY level DESC LIMIT 1",
            [$xp]
        );

        return $level ? (int) $level['level'] : 1;
    }

    public function checkIPForRegistration(string $ipAddress): array
    {
        // 🆕 معافیت ادمین
        $user = $this->db->fetchOne(
            "SELECT role FROM users WHERE registration_ip = ? LIMIT 1",
            [$ipAddress]
        );
        if ($user && in_array($user['role'], ['admin', 'super_admin'])) {
            return ['allowed' => true, 'current_count' => 0, 'max_allowed' => 999];
        }

        $accounts = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM users WHERE registration_ip = ?",
            [$ipAddress]
        );

        $count = $accounts['count'] ?? 0;
        $maxAccounts = $this->settings['max_accounts_per_ip'];

        return [
            'allowed' => $count < $maxAccounts,
            'current_count' => $count,
            'max_allowed' => $maxAccounts,
        ];
    }

    public function recordUserIP(int $userId, string $ipAddress, ?string $userAgent = null): void
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM user_ip_tracking WHERE user_id = ? AND ip_address = ?",
            [$userId, $ipAddress]
        );

        if ($existing) {
            $this->db->query(
                "UPDATE user_ip_tracking SET last_seen_at = NOW(), login_count = login_count + 1 WHERE id = ?",
                [$existing['id']]
            );
        } else {
            $this->db->insert('user_ip_tracking', [
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        }

        $this->db->update('users', [
            'last_ip_address' => $ipAddress,
        ], 'id = ?', [$userId]);
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function updateSetting(string $key, $value): void
    {
        $settingsRepo = SettingsRepository::getInstance();
        $settingsRepo->set($key, $value);
        $this->loadSettings();
    }
}
