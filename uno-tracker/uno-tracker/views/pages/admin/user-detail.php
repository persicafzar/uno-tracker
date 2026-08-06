<?php

use Core\JalaliDate;

$statusLabels = [
    'active' => ['label' => 'فعال', 'color' => 'green'],
    'banned' => ['label' => 'مسدود', 'color' => 'red'],
    'pending' => ['label' => 'در انتظار', 'color' => 'yellow'],
];

$roleLabels = [
    'user' => ['label' => 'کاربر', 'color' => 'gray'],
    'admin' => ['label' => 'مدیر', 'color' => 'indigo'],
    'super_admin' => ['label' => 'مدیر ارشد', 'color' => 'purple'],
];

$status = $statusLabels[$user['status']] ?? $statusLabels['active'];
$role = $roleLabels[$user['role']] ?? $roleLabels['user'];
$gamesStats = $user['games_stats'] ?? [];
$gamificationStats = $user['gamification_stats'] ?? [];
$recentGames = $user['recent_games'] ?? [];
$isSelf = $user['id'] === $admin['id'];
?>

<div class="space-y-6">

    <!-- Back Button -->
    <a href="/admin/users" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-indigo-600 transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        بازگشت به لیست کاربران
    </a>

    <!-- ======= کانتینر گرید سه‌ستونه ======= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mt-6 items-stretch">

        <!-- ======= ستون اول: اطلاعات کاربر + وضعیت حساب ======= -->
        <div class="flex flex-col gap-4 h-full">
            <div class="flex-1 relative overflow-hidden bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-5 sm:p-7 text-white shadow-md">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <div class="relative z-10 flex flex-col items-center gap-4 h-full justify-center">
                    <?php if (!empty($user['avatar_path'])): ?>
                        <div class="w-24 h-24 rounded-full border-4 border-white/90 shadow-2xl overflow-hidden flex-shrink-0 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                            <img src="/storage/uploads/avatars/<?= htmlspecialchars($user['avatar_path']) ?>" alt="<?= htmlspecialchars($user['nickname']) ?>" class="w-full h-full aspect-square rounded-full object-cover">
                        </div>
                    <?php else: ?>
                        <div class="w-24 h-24 rounded-full border-4 border-white/90 shadow-2xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-4xl font-black hover:border-amber-300 transition-all duration-300 hover:scale-105"><?= mb_substr($user['nickname'] ?? '?', 0, 1) ?></div>
                    <?php endif; ?>
                    <div class="text-center">
                        <h2 class="text-2xl font-black drop-shadow-lg"><?= htmlspecialchars($user['nickname'] ?? '-') ?></h2>
                        <p class="text-white/80 text-sm font-medium drop-shadow"><?= htmlspecialchars($user['real_name'] ?? '-') ?></p>
                        <?php if (!empty($user['tagline'])): ?><p class="text-white/70 italic mt-2 text-sm drop-shadow">"<?= htmlspecialchars($user['tagline']) ?>"</p><?php endif; ?>
                        <div class="flex flex-wrap items-center justify-center gap-2 mt-3">
                            <span class="px-3 py-1 bg-<?= $role['color'] ?>-500 rounded-full text-xs font-bold shadow-sm"><?= $role['label'] ?></span>
                            <span class="px-3 py-1 bg-<?= $status['color'] ?>-500 rounded-full text-xs font-bold shadow-sm"><?= $status['label'] ?></span>
                            <?php if (!empty($user['is_online'])): ?>
                                <span class="px-3 py-1 bg-green-500 rounded-full text-xs font-bold flex items-center gap-1 shadow-sm"><span class="w-2 h-2 bg-white rounded-full animate-pulse"></span> آنلاین</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$isSelf && $user['role'] !== 'super_admin'): ?>
                <div class="flex-shrink-0 bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden transition hover:shadow-lg">
                    <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <h4 class="text-sm font-black text-gray-700 flex items-center gap-2.5"><span class="text-xl">🔒</span> وضعیت حساب</h4>
                    </div>
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3"><span class="text-2xl"><?= $user['status'] === 'banned' ? '⛔' : '✅' ?></span><span class="text-sm font-bold text-gray-800"><?= $user['status'] === 'banned' ? 'مسدود شده' : 'فعال' ?></span></div>
                        <form method="POST" action="/admin/users/<?= $user['id'] ?>/<?= $user['status'] === 'banned' ? 'unban' : 'ban' ?>" id="form-ban-toggle">
                            <button type="button" onclick="confirmBanToggle(<?= $user['status'] === 'banned' ? 'false' : 'true' ?>, '<?= htmlspecialchars($user['nickname']) ?>')" class="px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 transform hover:scale-105 flex items-center gap-2 <?= $user['status'] === 'banned' ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white shadow-md hover:shadow-lg' : 'bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white shadow-md hover:shadow-lg' ?>"><?= $user['status'] === 'banned' ? '✅ فعال‌سازی' : '🚫 مسدود کردن' ?></button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ======= ستون دوم: مجوزهای بازی ======= -->
        <?php if (!$isSelf && $user['role'] !== 'super_admin'): ?>
            <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden transition hover:shadow-lg h-full">
                <div class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-purple-50 border-b-2 border-indigo-100">
                    <h4 class="text-sm font-black text-gray-700 flex items-center gap-2.5"><span class="text-xl">🎮</span> مجوزهای بازی</h4>
                </div>
                <div class="px-4 py-3 space-y-3">
                    <div class="flex items-center justify-between bg-gray-50/80 rounded-2xl p-3 border-2 border-gray-100 hover:shadow-sm transition">
                        <div class="flex items-center gap-2"><span class="text-xl">🛠️</span>
                            <div><span class="text-sm font-bold text-gray-800">ساخت بازی</span><span class="text-xs text-gray-500 font-medium block">ایجاد بازی جدید</span></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold <?= !empty($user['can_create_game']) ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' ?>"><?= !empty($user['can_create_game']) ? '✅ مجاز' : '❌ ممنوع' ?></span>
                            <form method="POST" action="/admin/users/<?= $user['id'] ?>/<?= !empty($user['can_create_game']) ? 'ban' : 'allow' ?>-create-game">
                                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 hover:scale-105 shadow-sm <?= !empty($user['can_create_game']) ? 'bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white' : 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white' ?>"><?= !empty($user['can_create_game']) ? '🚫 سلب' : '✅ اعطا' ?></button>
                            </form>
                        </div>
                    </div>
                    <div class="flex items-center justify-between bg-gray-50/80 rounded-2xl p-3 border-2 border-gray-100 hover:shadow-sm transition">
                        <div class="flex items-center gap-2"><span class="text-xl">🤝</span>
                            <div><span class="text-sm font-bold text-gray-800">شرکت در بازی</span><span class="text-xs text-gray-500 font-medium block">حضور در بازی دیگران</span></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold <?= !empty($user['can_join_game']) ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' ?>"><?= !empty($user['can_join_game']) ? '✅ مجاز' : '❌ ممنوع' ?></span>
                            <form method="POST" action="/admin/users/<?= $user['id'] ?>/<?= !empty($user['can_join_game']) ? 'ban' : 'allow' ?>-join-game">
                                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 hover:scale-105 shadow-sm <?= !empty($user['can_join_game']) ? 'bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white' : 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white' ?>"><?= !empty($user['can_join_game']) ? '🚫 سلب' : '✅ اعطا' ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======= ستون سوم: عملیات مدیریتی ======= -->
            <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden transition hover:shadow-lg h-full">
                <div class="px-4 py-3 bg-gradient-to-r from-amber-50 to-yellow-50 border-b-2 border-amber-100">
                    <h4 class="text-sm font-black text-gray-700 flex items-center gap-2.5"><span class="text-xl">⚙️</span> عملیات مدیریتی</h4>
                </div>
                <div class="px-4 py-3 space-y-3">
                    <form method="POST" action="/admin/users/<?= $user['id'] ?>/reset-password" id="form-reset-password">
                        <button type="button" onclick="confirmResetPassword('<?= htmlspecialchars($user['nickname']) ?>')" class="w-full px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] flex items-center justify-center gap-2"><span class="text-xl">🔑</span> ریست پسورد به 123456</button>
                    </form>
                    <form method="POST" action="/admin/users/<?= $user['id'] ?>/recalculate-stats" id="form-recalculate-stats">
                        <button type="button" onclick="confirmRecalculateStats('<?= htmlspecialchars($user['nickname']) ?>')" class="w-full px-4 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            باز محاسبه آمار (ضد تقلب)
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Personal Info -->
        <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
            <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">👤</span> اطلاعات شخصی</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b-2 border-gray-100"><span class="text-gray-600 text-sm font-medium">شناسه</span><span class="font-bold text-gray-800">#<?= $user['id'] ?></span></div>
                <div class="flex justify-between py-2 border-b-2 border-gray-100"><span class="text-gray-600 text-sm font-medium">نام مستعار</span><span class="font-bold text-gray-800"><?= htmlspecialchars($user['nickname'] ?? '-') ?></span></div>
                <div class="flex justify-between py-2 border-b-2 border-gray-100"><span class="text-gray-600 text-sm font-medium">نام واقعی</span><span class="font-bold text-gray-800"><?= htmlspecialchars($user['real_name'] ?? '-') ?></span></div>
                <div class="flex justify-between py-2 border-b-2 border-gray-100"><span class="text-gray-600 text-sm font-medium">شماره تماس</span><span class="font-bold text-gray-800 font-mono" dir="ltr"><?= htmlspecialchars($user['phone'] ?? '-') ?></span></div>
                <div class="flex justify-between py-2 border-b-2 border-gray-100"><span class="text-gray-600 text-sm font-medium">تاریخ عضویت</span><span class="font-bold text-gray-800"><?= !empty($user['created_at']) ? JalaliDate::format('Y/m/d', strtotime($user['created_at'])) : '-' ?></span></div>
                <div class="flex justify-between py-2"><span class="text-gray-600 text-sm font-medium">آخرین بازدید</span><span class="font-bold text-gray-800"><?= !empty($user['last_seen_at']) ? JalaliDate::format('Y/m/d H:i', strtotime($user['last_seen_at'])) : 'هرگز' ?></span></div>
            </div>
        </div>

        <!-- Game Stats -->
        <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
            <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">🎮</span> آمار بازی‌ها</h3>
            <div class="!grid !grid-cols-2 gap-3">
                <?php
                $totalGames = (int)($gamesStats['total_games'] ?? 0);
                $totalWins = (int)($gamesStats['total_wins'] ?? 0);
                $totalPoints = (int)($gamesStats['total_points'] ?? 0);
                $winRate = (float)($gamesStats['win_rate'] ?? 0);
                ?>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-3 text-center border-2 border-blue-200 shadow-sm">
                    <div class="text-2xl font-black text-blue-700"><?= $totalGames ?></div>
                    <div class="text-[10px] font-medium text-gray-600">کل بازی‌ها</div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-3 text-center border-2 border-green-200 shadow-sm">
                    <div class="text-2xl font-black text-green-700"><?= $totalWins ?></div>
                    <div class="text-[10px] font-medium text-gray-600">کل بردها</div>
                </div>
                <div class="bg-gradient-to-br from-yellow-50 to-amber-100 rounded-2xl p-3 text-center border-2 border-yellow-200 shadow-sm">
                    <div class="text-2xl font-black text-yellow-700"><?= $totalPoints ?></div>
                    <div class="text-[10px] font-medium text-gray-600">امتیاز کل</div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-2xl p-3 text-center border-2 border-purple-200 shadow-sm">
                    <div class="text-2xl font-black text-purple-700"><?= number_format($winRate, 1) ?>%</div>
                    <div class="text-[10px] font-medium text-gray-600">نرخ برد</div>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t-2 border-gray-200">
                <div class="flex justify-between text-sm font-bold mb-1"><span class="text-gray-600">نرخ برد</span><span class="text-indigo-600"><?= number_format($winRate, 1) ?>%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: <?= $winRate ?>%"></div>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t-2 border-gray-100 grid grid-cols-2 gap-2 text-xs font-medium">
                <div class="flex justify-between"><span class="text-gray-500">انفرادی:</span><span class="font-bold"><?= (int)($gamesStats['solo_games'] ?? 0) ?></span></div>
                <div class="flex justify-between"><span class="text-gray-500">برد انفرادی:</span><span class="font-bold"><?= (int)($gamesStats['solo_wins'] ?? 0) ?></span></div>
                <div class="flex justify-between"><span class="text-gray-500">تیمی:</span><span class="font-bold"><?= (int)($gamesStats['team_games'] ?? 0) ?></span></div>
                <div class="flex justify-between"><span class="text-gray-500">برد تیمی:</span><span class="font-bold"><?= (int)($gamesStats['team_wins'] ?? 0) ?></span></div>
            </div>
        </div>
    </div>

    <!-- Gamification Stats -->
    <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
        <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">🏆</span> آمار گیمیفیکیشن</h3>
        <div class="!grid !grid-cols-2 md:!grid-cols-4 gap-3">
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-3 text-center border-2 border-indigo-200 shadow-sm">
                <div class="text-2xl mb-1">⭐</div>
                <div class="text-2xl font-black text-indigo-700"><?= (int)($gamificationStats['current_level'] ?? 1) ?></div>
                <div class="text-[10px] font-medium text-gray-600">سطح فعلی</div>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-amber-100 rounded-2xl p-3 text-center border-2 border-yellow-200 shadow-sm">
                <div class="text-2xl mb-1">✨</div>
                <div class="text-2xl font-black text-yellow-700"><?= (int)($gamificationStats['total_xp'] ?? 0) ?></div>
                <div class="text-[10px] font-medium text-gray-600">امتیاز تجربه</div>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-2xl p-3 text-center border-2 border-purple-200 shadow-sm">
                <div class="text-2xl mb-1">🏅</div>
                <div class="text-2xl font-black text-purple-700"><?= (int)($gamificationStats['achievements_count'] ?? 0) ?></div>
                <div class="text-[10px] font-medium text-gray-600">نشان‌ها</div>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-red-100 rounded-2xl p-3 text-center border-2 border-orange-200 shadow-sm">
                <div class="text-2xl mb-1">🔥</div>
                <div class="text-2xl font-black text-orange-700"><?= (int)($gamificationStats['best_streak'] ?? 0) ?></div>
                <div class="text-[10px] font-medium text-gray-600">بهترین زنجیره</div>
            </div>
        </div>
    </div>

    <!-- Recent Games -->
    <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
        <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">📊</span> آخرین بازی‌ها</h3>
        <?php if (empty($recentGames)): ?>
            <div class="text-center py-8 text-gray-500">
                <div class="text-5xl mb-3 opacity-50">🎮</div>
                <p class="font-medium">هنوز بازی‌ای انجام نداده است</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">بازی</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">حالت</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">وضعیت</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">نتیجه</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">امتیاز</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">تاریخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($recentGames as $game): ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5"><a href="/admin/games/<?= $game['id'] ?>" class="text-sm font-bold text-gray-800 hover:text-indigo-600 transition truncate block whitespace-nowrap max-w-[120px]"><?= htmlspecialchars($game['name'] ?: "بازی #{$game['id']}") ?></a></td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap"><span class="text-xs px-2.5 py-1 bg-<?= $game['game_mode'] === 'solo' ? 'blue' : 'purple' ?>-100 text-<?= $game['game_mode'] === 'solo' ? 'blue' : 'purple' ?>-700 rounded-full font-bold border border-<?= $game['game_mode'] === 'solo' ? 'blue' : 'purple' ?>-200 shadow-sm"><?= $game['game_mode'] === 'solo' ? '👤 انفرادی' : '👥 تیمی' ?></span></td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap"><span class="text-xs px-2.5 py-1 bg-<?= $game['status'] === 'finished' ? 'green' : 'yellow' ?>-100 text-<?= $game['status'] === 'finished' ? 'green' : 'yellow' ?>-700 rounded-full font-bold border border-<?= $game['status'] === 'finished' ? 'green' : 'yellow' ?>-200 shadow-sm"><?= $game['status'] === 'finished' ? 'پایان یافته' : 'در حال بازی' ?></span></td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap"><?php if (!empty($game['is_winner']) && $game['is_winner']): ?><span class="text-green-600 font-black">🏆 برنده</span><?php elseif ($game['status'] === 'finished'): ?><span class="text-gray-500 font-medium">باخت</span><?php else: ?><span class="text-gray-400 font-medium">-</span><?php endif; ?></td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="text-sm font-black text-indigo-600"><?= (int)($game['total_score'] ?? 0) ?></div>
                                    <div class="text-xs font-medium text-gray-500"><?= (int)($game['wins_count'] ?? 0) ?> برد</div>
                                </td>
                                <td class="px-4 py-3.5 text-center text-xs font-medium text-gray-500 whitespace-nowrap"><?= !empty($game['created_at']) ? JalaliDate::format('Y/m/d', strtotime($game['created_at'])) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <script>
        function confirmBanToggle(isCurrentlyBanned, nickname) {
            if (isCurrentlyBanned) {
                // کاربر فعال است → می‌خواهیم مسدود کنیم
                Swal.fire({
                    title: '🚫 مسدود کردن کاربر',
                    html: `
                <div class="text-right">
                    <p class="text-gray-700 mb-2">آیا از مسدود کردن کاربر زیر اطمینان دارید؟</p>
                    <div class="bg-orange-50 border-2 border-orange-200 rounded-xl p-3 text-sm">
                        <span class="text-orange-700 font-bold">👤 ${nickname}</span>
                    </div>
                    <p class="text-red-600 text-xs mt-3">⚠️ این کاربر دیگر نمی‌تواند وارد سیستم شود.</p>
                </div>
            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea580c',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '🚫 بله، مسدود کن',
                    cancelButtonText: 'انصراف',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('form-ban-toggle').submit();
                    }
                });
            } else {
                // کاربر مسدود است → می‌خواهیم فعال کنیم
                Swal.fire({
                    title: '✅ فعال‌سازی کاربر',
                    html: `
                <div class="text-right">
                    <p class="text-gray-700 mb-2">آیا از فعال‌سازی کاربر زیر اطمینان دارید؟</p>
                    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-3 text-sm">
                        <span class="text-green-700 font-bold">👤 ${nickname}</span>
                    </div>
                    <p class="text-green-600 text-xs mt-3">✅ این کاربر دوباره می‌تواند وارد سیستم شود.</p>
                </div>
            `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '✅ بله، فعال کن',
                    cancelButtonText: 'انصراف',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('form-ban-toggle').submit();
                    }
                });
            }
        }

        function confirmResetPassword(nickname) {
            Swal.fire({
                title: '🔑 ریست پسورد',
                html: `
            <div class="text-right">
                <p class="text-gray-700 mb-2">آیا از ریست پسورد کاربر زیر اطمینان دارید؟</p>
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-3 text-sm mb-3">
                    <span class="text-yellow-700 font-bold">👤 ${nickname}</span>
                </div>
                <div class="bg-amber-100 border border-amber-300 rounded-xl p-3">
                    <p class="text-amber-800 text-sm">
                        پسورد جدید: <strong class="font-mono text-base">123456</strong>
                    </p>
                </div>
                <p class="text-orange-600 text-xs mt-3">⚠️ پس از ریست، حتماً به کاربر اطلاع دهید.</p>
            </div>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '🔑 بله، ریست کن',
                cancelButtonText: 'انصراف',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-reset-password').submit();
                }
            });
        }

        function confirmRecalculateStats(nickname) {
            Swal.fire({
                title: '🔄 باز محاسبه آمار',
                html: `
            <div class="text-right">
                <p class="text-gray-700 mb-2">آیا از باز محاسبه آمار کاربر زیر اطمینان دارید؟</p>
                <div class="bg-orange-50 border-2 border-orange-200 rounded-xl p-3 text-sm mb-3">
                    <span class="text-orange-700 font-bold">👤 ${nickname}</span>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                    <p class="text-red-700 text-xs leading-relaxed">
                        ⚠️ این عملیات آمار را بر اساس <strong>بازی‌های معتبر</strong> بازنویسی می‌کند.<br>
                        📊 امتیاز، XP، سطح و نشان‌ها مجدداً محاسبه خواهند شد.
                    </p>
                </div>
            </div>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '🔄 بله، باز محاسبه کن',
                cancelButtonText: 'انصراف',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-recalculate-stats').submit();
                }
            });
        }
    </script>
</div>