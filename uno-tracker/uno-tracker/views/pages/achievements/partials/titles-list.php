<?php

/**
 * لیست همه عناوین قابل کسب
 * 
 * 🆕 بهبودها:
 * - پشتیبانی کامل از همه condition_type ها (شامل team_wins, solo_wins, team_games, solo_games)
 * - محاسبه دقیق progress با پشتیبانی از برد تیمی
 * - بهبود خوانایی و نگهداری کد
 */

use Core\Database;

$db = Database::getInstance();
$currentUserId = $userId ?? ($profile['user_id'] ?? ($profile['id'] ?? 0));

// 🆕 آمار فعلی کاربر - با پشتیبانی کامل از برد تیمی
$userStats = $db->fetchOne(
    "SELECT 
        (SELECT COUNT(DISTINCT g.id) FROM games g JOIN game_participants gp ON g.id = gp.game_id WHERE gp.user_id = ? AND g.status = 'finished') as total_games,
        (SELECT COUNT(DISTINCT g.id) FROM games g JOIN game_participants gp ON g.id = gp.game_id 
         WHERE gp.user_id = ? AND g.status = 'finished' 
         AND (
             (g.game_mode = 'solo' AND g.winner_participant_id = gp.id)
             OR (g.game_mode = 'friendly' AND g.winner_team_id IS NOT NULL AND g.winner_team_id = gp.team_id)
         )) as total_wins,
        (SELECT COUNT(DISTINCT g.id) FROM games g JOIN game_participants gp ON g.id = gp.game_id 
         WHERE gp.user_id = ? AND g.game_mode = 'solo' AND g.status = 'finished' 
         AND g.winner_participant_id = gp.id) as solo_wins,
        (SELECT COUNT(DISTINCT g.id) FROM games g JOIN game_participants gp ON g.id = gp.game_id 
         WHERE gp.user_id = ? AND g.game_mode = 'friendly' AND g.status = 'finished' 
         AND g.winner_team_id IS NOT NULL AND g.winner_team_id = gp.team_id) as team_wins,
        (SELECT COUNT(DISTINCT g.id) FROM games g JOIN game_participants gp ON g.id = gp.game_id 
         WHERE gp.user_id = ? AND g.game_mode = 'solo' AND g.status = 'finished') as solo_games,
        (SELECT COUNT(DISTINCT g.id) FROM games g JOIN game_participants gp ON g.id = gp.game_id 
         WHERE gp.user_id = ? AND g.game_mode = 'friendly' AND g.status = 'finished') as team_games,
        (SELECT best_streak FROM user_streaks WHERE user_id = ?) as best_streak,
        (SELECT current_streak FROM user_streaks WHERE user_id = ?) as current_streak,
        (SELECT COALESCE(SUM(gp.total_score), 0) FROM games g JOIN game_participants gp ON g.id = gp.game_id WHERE gp.user_id = ? AND g.status = 'finished') as total_points
    ",
    [
        $currentUserId, // total_games
        $currentUserId, // total_wins
        $currentUserId, // solo_wins
        $currentUserId, // team_wins
        $currentUserId, // solo_games
        $currentUserId, // team_games
        $currentUserId, // best_streak
        $currentUserId, // current_streak
        $currentUserId  // total_points
    ]
);

// 🆕 تبدیل به مقادیر عددی برای جلوگیری از null
$stats = [
    'total_games' => (int)($userStats['total_games'] ?? 0),
    'total_wins' => (int)($userStats['total_wins'] ?? 0),
    'solo_wins' => (int)($userStats['solo_wins'] ?? 0),
    'team_wins' => (int)($userStats['team_wins'] ?? 0),
    'solo_games' => (int)($userStats['solo_games'] ?? 0),
    'team_games' => (int)($userStats['team_games'] ?? 0),
    'best_streak' => (int)($userStats['best_streak'] ?? 0),
    'current_streak' => (int)($userStats['current_streak'] ?? 0),
    'win_streak' => (int)($userStats['current_streak'] ?? 0), // Alias
    'total_points' => (int)($userStats['total_points'] ?? 0),
];

// گرفتن همه عناوین فعال
$allTitles = $db->fetchAll("SELECT * FROM titles WHERE is_active = 1 ORDER BY priority DESC, bonus_points DESC");

// گرفتن عنوان فعال فعلی کاربر
$currentTitleInfo = $db->fetchOne(
    "SELECT t.id, t.name, t.icon, t.bonus_points, t.description
    FROM users u
    LEFT JOIN titles t ON u.current_title_id = t.id
    WHERE u.id = ?",
    [$currentUserId]
);

// 🆕 برچسب‌های شرط با پشتیبانی کامل
$conditionLabels = [
    'total_games' => 'تعداد بازی',
    'total_wins' => 'تعداد برد',
    'solo_wins' => 'بردهای انفرادی',
    'team_wins' => 'بردهای تیمی',
    'solo_games' => 'بازی‌های انفرادی',
    'team_games' => 'بازی‌های تیمی',
    'win_streak' => 'زنجیره پیروزی فعلی',
    'current_streak' => 'زنجیره پیروزی فعلی',
    'best_streak' => 'بهترین زنجیره پیروزی',
    'total_points' => 'امتیاز کل',
    'max_consecutive_wins_with_card' => 'برد متوالی با کارت',
];
?>

<!-- عنوان فعال فعلی -->
<?php if ($currentTitleInfo): ?>
    <div class="relative overflow-hidden bg-gradient-to-r from-yellow-100 via-amber-100 to-orange-100 rounded-2xl p-5 border-2 border-yellow-300 shadow-xl mb-6">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="text-6xl drop-shadow-2xl"><?= htmlspecialchars($currentTitleInfo['icon'] ?? '🏆') ?></div>
            <div class="flex-1">
                <div class="text-xs text-orange-600 font-black tracking-wider mb-1">عنوان فعال شما</div>
                <h3 class="text-2xl font-black text-gray-800"><?= htmlspecialchars($currentTitleInfo['name'] ?? '') ?></h3>
                <?php if (!empty($currentTitleInfo['description'])): ?>
                    <p class="text-sm text-gray-600 font-medium mt-1"><?= htmlspecialchars($currentTitleInfo['description'] ?? '') ?></p>
                <?php endif; ?>
                <div class="flex items-center gap-3 mt-3">
                    <?php if (!empty($currentTitleInfo['bonus_points']) && $currentTitleInfo['bonus_points'] > 0): ?>
                        <div class="bg-green-500/20 text-green-700 px-3 py-1.5 rounded-xl text-sm font-black border border-green-300 shadow-sm">
                            ⭐ بونوس: +<?= $currentTitleInfo['bonus_points'] ?> امتیاز در هر برد
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="bg-gray-50 rounded-2xl p-6 border-2 border-dashed border-gray-300 mb-6 text-center">
        <div class="text-4xl mb-2 opacity-50">🔒</div>
        <p class="text-gray-500 font-medium">شما هنوز عنوانی را فعال نکرده‌اید. شرایط عناوین زیر را تکمیل کنید!</p>
    </div>
<?php endif; ?>

<!-- لیست همه عناوین -->
<div>
    <h3 class="text-base sm:text-lg font-black text-gray-800 mb-3 flex items-center gap-2">
        <span class="text-2xl">🎖️</span>
        همه عناوین قابل کسب (<?= count($allTitles) ?>)
    </h3>
    <div class="!grid !grid-cols-1 md:!grid-cols-2 lg:!grid-cols-3 gap-3">
        <?php foreach ($allTitles as $titleItem): ?>
            <?php
            $condType = $titleItem['condition_type'] ?? '';
            $condValue = (int) ($titleItem['condition_value'] ?? 0);
            $bonusPoints = (int) ($titleItem['bonus_points'] ?? 0);

            // 🆕 گرفتن مقدار فعلی با پشتیبانی از همه condition_type ها
            $currentValue = $stats[$condType] ?? 0;

            // برای condition_type های ناشناخته، مقدار 0 استفاده می‌شود
            if (!array_key_exists($condType, $stats)) {
                error_log("⚠️ Unknown condition_type in title {$titleItem['id']}: {$condType}");
            }

            $isUnlocked = $currentValue >= $condValue && $condValue > 0;
            $isCurrent = ($currentTitleInfo && $titleItem['id'] == $currentTitleInfo['id']);

            // 🆕 محاسبه درصد پیشرفت (با جلوگیری از تقسیم بر صفر)
            $progressPercentage = $condValue > 0
                ? min(100, round(($currentValue / $condValue) * 100, 1))
                : ($isUnlocked ? 100 : 0);
            ?>

            <div class="relative rounded-2xl p-4 border-2 transition-all hover:shadow-xl hover:scale-[1.02]
                <?= $isCurrent ? 'border-yellow-400 bg-yellow-50 ring-2 ring-yellow-300' : ($isUnlocked ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50') ?>">

                <?php if ($isCurrent): ?>
                    <div class="absolute top-3 left-3 z-10">
                        <span class="px-2.5 py-0.5 bg-yellow-200 text-yellow-800 rounded-full text-[10px] font-black border border-yellow-300 shadow-sm">⭐ فعال</span>
                    </div>
                <?php elseif ($isUnlocked): ?>
                    <div class="absolute top-3 left-3 z-10">
                        <span class="px-2.5 py-0.5 bg-green-200 text-green-800 rounded-full text-[10px] font-black border border-green-300 shadow-sm">✅ کسب شده</span>
                    </div>
                <?php else: ?>
                    <div class="absolute top-3 left-3 z-10">
                        <span class="px-2.5 py-0.5 bg-gray-200 text-gray-600 rounded-full text-[10px] font-black border border-gray-300 shadow-sm">🔒 قفل</span>
                    </div>
                <?php endif; ?>

                <div class="flex items-start gap-3 mt-4">
                    <div class="text-4xl <?= !$isUnlocked ? 'grayscale opacity-40' : '' ?> drop-shadow">
                        <?= htmlspecialchars($titleItem['icon'] ?? '🏆') ?>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-black text-gray-800 text-sm sm:text-base"><?= htmlspecialchars($titleItem['name'] ?? '') ?></h4>
                        <?php if (!empty($titleItem['description'])): ?>
                            <p class="text-xs text-gray-600 mt-0.5"><?= htmlspecialchars($titleItem['description'] ?? '') ?></p>
                        <?php endif; ?>

                        <div class="flex flex-col gap-1 mt-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-gray-600 font-bold">
                                    📋 <?= htmlspecialchars($conditionLabels[$condType] ?? $condType) ?>
                                </span>
                                <span class="text-[10px] font-black <?= $isUnlocked ? 'text-green-600' : 'text-gray-500' ?>">
                                    <?= number_format($currentValue) ?> / <?= number_format($condValue) ?>
                                </span>
                            </div>

                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                    style="width: <?= $progressPercentage ?>%; background: <?= $isUnlocked ? 'linear-gradient(90deg, #22c55e, #16a34a)' : 'linear-gradient(90deg, #6366f1, #8b5cf6)' ?>">
                                </div>
                            </div>

                            <?php if ($bonusPoints > 0): ?>
                                <div class="mt-1.5">
                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-bold border border-green-200">
                                        ⭐ +<?= $bonusPoints ?> امتیاز بونوس در هر برد
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>