<?php

/**
 * 🛡️ صفحه مدیریت بازی‌های مشکوک
 */

use Core\JalaliDate;

$totalGames = $total ?? 0;
$riskCounts = ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0];
$reviewedCount = 0;
$unreviewedCount = 0;

foreach ($suspiciousGames ?? [] as $game) {
    $riskLevel = $game['risk_level'] ?? 'low';
    if (isset($riskCounts[$riskLevel])) $riskCounts[$riskLevel]++;
    if (isset($game['is_reviewed']) && (int)$game['is_reviewed'] === 1) $reviewedCount++;
    else $unreviewedCount++;
}

$riskLabels = ['low' => 'کم', 'medium' => 'متوسط', 'high' => 'زیاد', 'critical' => 'بحرانی'];
$riskColors = [
    'low' => ['badge' => 'bg-yellow-100 text-yellow-800 border-yellow-200'],
    'medium' => ['badge' => 'bg-orange-100 text-orange-800 border-orange-200'],
    'high' => ['badge' => 'bg-red-100 text-red-800 border-red-200'],
    'critical' => ['badge' => 'bg-gray-800 text-white border-gray-700'],
];
$cheatTypeLabels = [
    'fast_game' => '⚡ بازی سریع',
    'invalid_players' => '👥 بازیکنان نامعتبر',
    'too_few_players' => '👥 بازیکنان کم',
    'too_many_guests' => '🎭 مهمانان زیاد',
    'guest_only_game' => '🚫 بازی فقط مهمان',
    'unusual_win_pattern' => '📊 الگوی برد غیرعادی',
    'low_target_wins' => '🎯 هدف برد کم',
    'many_rounds' => '🔄 دورهای زیاد',
];
$gameModeLabels = ['solo' => '👤 انفرادی', 'friendly' => '👥 تیمی'];
$statusLabels = [
    'pending' => '⏳ در انتظار',
    'active' => '🔴 فعال',
    'paused' => '⏸️ متوقف',
    'finished' => '✅ پایان یافته',
    'cancelled' => '❌ لغو شده',
];

$allGameIdsOnPage = array_filter(array_column($suspiciousGames ?? [], 'id'));
?>

<div class="space-y-6" x-data="suspiciousGamesManager()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
                <span class="text-3xl">🛡️</span>
                بازی‌های مشکوک
            </h2>
            <p class="text-gray-600 text-sm font-medium mt-0.5">بازی‌هایی که توسط سیستم ضدتقلب شناسایی شده‌اند</p>
        </div>
        <div class="flex gap-2">
            <button type="button" x-show="selectedGames.length > 0" @click="bulkDelete()"
                class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] flex items-center gap-2 animate-pulse text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                حذف انتخاب‌شده‌ها (<span x-text="selectedGames.length"></span>)
            </button>
            <button type="button" @click="refreshData()"
                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                بروزرسانی
            </button>
        </div>
    </div>

    <!-- Statistics Cards - با آیکون‌های SVG متناسب -->
    <div class="!grid !grid-cols-2 md:!grid-cols-4 lg:!grid-cols-6 gap-2 sm:gap-3">
        
        <!-- 1. کل بازی‌ها - آیکون لیست (Clipboard) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl p-2.5 sm:p-4 border-2 border-gray-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-10 sm:w-12 h-10 sm:h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex flex-col items-center">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-700 mb-0.5 sm:mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <div class="text-lg sm:text-2xl font-black text-gray-800"><?= $totalGames ?></div>
                <div class="text-[8px] sm:text-xs font-medium text-gray-600 text-center">کل بازی‌ها</div>
            </div>
        </div>

        <!-- 2. بررسی نشده - آیکون چشم (Eye) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl p-2.5 sm:p-4 border-2 border-blue-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-10 sm:w-12 h-10 sm:h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex flex-col items-center">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-700 mb-0.5 sm:mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <div class="text-lg sm:text-2xl font-black text-blue-700"><?= $unreviewedCount ?></div>
                <div class="text-[8px] sm:text-xs font-medium text-blue-600 text-center">بررسی نشده</div>
            </div>
        </div>

        <!-- 3. بررسی شده - آیکون تیک در دایره (CheckCircle) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-green-100 to-emerald-200 rounded-2xl p-2.5 sm:p-4 border-2 border-green-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-10 sm:w-12 h-10 sm:h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex flex-col items-center">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-green-700 mb-0.5 sm:mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-lg sm:text-2xl font-black text-green-700"><?= $reviewedCount ?></div>
                <div class="text-[8px] sm:text-xs font-medium text-green-600 text-center">بررسی شده</div>
            </div>
        </div>

        <!-- 4. ریسک کم - آیکون سپر با تیک (ShieldCheck) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-2xl p-2.5 sm:p-4 border-2 border-yellow-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-10 sm:w-12 h-10 sm:h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex flex-col items-center">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-700 mb-0.5 sm:mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <div class="text-lg sm:text-2xl font-black text-yellow-700"><?= $riskCounts['low'] ?></div>
                <div class="text-[8px] sm:text-xs font-medium text-yellow-600 text-center">ریسک کم</div>
            </div>
        </div>

        <!-- 5. ریسک زیاد - آیکون مثلث هشدار (ExclamationTriangle) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl p-2.5 sm:p-4 border-2 border-orange-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-10 sm:w-12 h-10 sm:h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex flex-col items-center">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-orange-700 mb-0.5 sm:mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="text-lg sm:text-2xl font-black text-orange-700"><?= $riskCounts['high'] ?></div>
                <div class="text-[8px] sm:text-xs font-medium text-orange-600 text-center">ریسک زیاد</div>
            </div>
        </div>

        <!-- 6. بحرانی - آیکون دایره با ضربدر (XCircle) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-700 to-gray-800 rounded-2xl p-2.5 sm:p-4 border-2 border-gray-700 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-10 sm:w-12 h-10 sm:h-12 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex flex-col items-center">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-400 mb-0.5 sm:mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-lg sm:text-2xl font-black text-white"><?= $riskCounts['critical'] ?></div>
                <div class="text-[8px] sm:text-xs font-medium text-gray-300 text-center">بحرانی</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-3 sm:p-5 border-2 border-gray-200/70 shadow-md">
        <form method="GET" action="/admin/suspicious-games" class="!grid !grid-cols-1 sm:!grid-cols-2 md:!grid-cols-4 gap-2 sm:gap-3">
            <div>
                <label class="text-[10px] sm:text-xs text-gray-600 mb-1 block font-bold">سطح ریسک</label>
                <select name="risk_level" class="w-full px-3 py-2 sm:py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <option value="low" <?= ($filters['risk_level'] ?? '') === 'low' ? 'selected' : '' ?>>کم</option>
                    <option value="medium" <?= ($filters['risk_level'] ?? '') === 'medium' ? 'selected' : '' ?>>متوسط</option>
                    <option value="high" <?= ($filters['risk_level'] ?? '') === 'high' ? 'selected' : '' ?>>زیاد</option>
                    <option value="critical" <?= ($filters['risk_level'] ?? '') === 'critical' ? 'selected' : '' ?>>بحرانی</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] sm:text-xs text-gray-600 mb-1 block font-bold">وضعیت بررسی</label>
                <select name="is_reviewed" class="w-full px-3 py-2 sm:py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <option value="0" <?= isset($filters['is_reviewed']) && $filters['is_reviewed'] === '0' ? 'selected' : '' ?>>بررسی نشده</option>
                    <option value="1" <?= isset($filters['is_reviewed']) && $filters['is_reviewed'] === '1' ? 'selected' : '' ?>>بررسی شده</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] sm:text-xs text-gray-600 mb-1 block font-bold">جستجو در User ID</label>
                <input type="number" name="user_id" value="<?= htmlspecialchars($filters['user_id'] ?? '') ?>" placeholder="مثال: 123"
                       class="w-full px-3 py-2 sm:py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 sm:px-5 py-2 sm:py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                    🔍 فیلتر
                </button>
                <a href="/admin/suspicious-games" class="px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm hover:shadow-md">
                    پاک کردن
                </a>
            </div>
        </form>
    </div>

    <!-- Games Table -->
    <?php if (empty($suspiciousGames)): ?>
        <div class="bg-white rounded-2xl p-8 sm:p-16 border-2 border-gray-200/70 shadow-md text-center">
            <div class="text-6xl sm:text-7xl mb-4">✅</div>
            <h3 class="text-xl sm:text-2xl font-black text-gray-800 mb-2">هیچ بازی مشکوکی یافت نشد</h3>
            <p class="text-gray-500 font-medium">تمام بازی‌ها توسط سیستم ضدتقلب بررسی شده‌اند</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px]">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-4 py-3.5 w-10">
                                <input type="checkbox" @change="toggleAll($event)" class="w-4 h-4 text-indigo-600 rounded">
                            </th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">شناسه</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">بازی</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">داور</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">حالت</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">ریسک</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">نوع تقلب</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">وضعیت</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">تاریخ</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($suspiciousGames as $game): ?>
                            <?php
                            $riskLevel = $game['risk_level'] ?? 'low';
                            $colors = $riskColors[$riskLevel] ?? $riskColors['low'];
                            $cheatTypes = json_decode($game['cheat_types'] ?? '[]', true);
                            $gameId = $game['id'] ?? 0;
                            ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5">
                                    <input type="checkbox" :value="<?= $gameId ?>" x-model="selectedGames" class="w-4 h-4 text-indigo-600 rounded">
                                </td>
                                <td class="px-4 py-3.5 text-sm font-bold text-gray-600 whitespace-nowrap">#<?= $gameId ?></td>
                                <td class="px-4 py-3.5">
                                    <a href="/admin/games/<?= $game['game_id'] ?>" class="font-bold text-sm text-indigo-600 hover:text-indigo-700 transition truncate block whitespace-nowrap max-w-[100px]">
                                        <?= htmlspecialchars($game['game_name'] ?: "بازی #{$game['game_id']}") ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 text-sm font-bold text-gray-700 whitespace-nowrap">
                                    <?= htmlspecialchars($game['referee_name'] ?? 'نامشخص') ?>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="text-sm font-bold"><?= $gameModeLabels[$game['game_mode']] ?? $game['game_mode'] ?></span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold border shadow-sm <?= $colors['badge'] ?>">
                                        <?= $riskLabels[$riskLevel] ?? $riskLevel ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-wrap gap-1">
                                        <?php foreach ($cheatTypes as $type): ?>
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-[10px] font-bold border border-red-200"><?= $cheatTypeLabels[$type] ?? $type ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <?php if (!empty($game['is_reviewed'])): ?>
                                        <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200 shadow-sm">✅ بررسی شده</span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold border border-yellow-200 shadow-sm">⏳ در انتظار</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-xs font-medium text-gray-500 whitespace-nowrap">
                                    <?= !empty($game['checked_at']) ? JalaliDate::format('Y/m/d', strtotime($game['checked_at'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="showDetails(<?= htmlspecialchars(json_encode($game)) ?>)" class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="جزئیات">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                        <a href="/admin/games/<?= $game['game_id'] ?>" class="p-1.5 text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition" title="مشاهده بازی">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        </a>
                                        <?php if (empty($game['is_reviewed'])): ?>
                                            <button type="button" @click="markAsReviewed(<?= $gameId ?>)" class="p-1.5 text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition" title="بررسی شده">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" @click="deleteSingle(<?= $gameId ?>)" class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white rounded-2xl p-4 border-2 border-gray-200/70 shadow-md">
                <div class="text-sm text-gray-600 font-medium whitespace-nowrap">صفحه <?= $page ?> از <?= $totalPages ?> <span class="text-gray-400">(<?= $total ?> بازی)</span></div>
                <div class="flex gap-1 flex-wrap justify-center">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&<?= http_build_query($filters) ?>" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">قبلی</a>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?= $i ?>&<?= http_build_query($filters) ?>" class="px-3.5 py-1.5 <?= $i === $page ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?> rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&<?= http_build_query($filters) ?>" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">بعدی</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Details Modal -->
    <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col border-2 border-gray-200/70" @click.away="closeModal()">
            <div class="px-6 py-4 border-b-2 border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-800 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">🔍</span> جزئیات بازی مشکوک</h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition p-1"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <template x-if="selectedGame">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div><div class="text-xs font-bold text-gray-500 mb-1">شناسه بازی</div><div class="text-sm font-black text-gray-800" x-text="'#' + (selectedGame.id || selectedGame.ID)"></div></div>
                            <div><div class="text-xs font-bold text-gray-500 mb-1">نام بازی</div><div class="text-sm font-black text-gray-800" x-text="selectedGame.game_name || 'بدون نام'"></div></div>
                            <div><div class="text-xs font-bold text-gray-500 mb-1">داور</div><div class="text-sm font-black text-gray-800" x-text="selectedGame.referee_name || 'نامشخص'"></div></div>
                            <div><div class="text-xs font-bold text-gray-500 mb-1">حالت بازی</div><div class="text-sm font-black text-gray-800" x-text="selectedGame.game_mode === 'solo' ? 'انفرادی' : 'تیمی'"></div></div>
                            <div><div class="text-xs font-bold text-gray-500 mb-1">سطح ریسک</div><div><span class="px-2.5 py-1 rounded-full text-xs font-bold border shadow-sm" :class="getRiskBadgeClass(selectedGame.risk_level)" x-text="getRiskLabel(selectedGame.risk_level)"></span></div></div>
                            <div><div class="text-xs font-bold text-gray-500 mb-1">تاریخ بررسی</div><div class="text-sm font-black text-gray-800" x-text="formatDate(selectedGame.checked_at)"></div></div>
                        </div>
                        <div><div class="text-xs font-bold text-gray-500 mb-2">انواع تقلب شناسایی شده</div><div class="flex flex-wrap gap-2"><template x-for="type in parseJson(selectedGame.cheat_types)" :key="type"><span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-xl text-sm font-bold border border-red-200" x-text="getCheatTypeLabel(type)"></span></template></div></div>
                        <div><div class="text-xs font-bold text-gray-500 mb-2">جزئیات فنی</div><div class="bg-gray-50/80 rounded-2xl p-4 border-2 border-gray-200"><pre class="text-xs text-gray-700 overflow-x-auto font-mono" x-text="JSON.stringify(parseJson(selectedGame.details), null, 2)"></pre></div></div>
                        <div><div class="text-xs font-bold text-gray-500 mb-2">وضعیت بررسی</div><div><template x-if="selectedGame.is_reviewed"><span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-xl text-sm font-bold border border-green-200">✅ بررسی شده</span></template><template x-if="!selectedGame.is_reviewed"><span class="px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-xl text-sm font-bold border border-yellow-200">⏳ در انتظار بررسی</span></template></div></div>
                    </div>
                </template>
            </div>
            <div class="px-6 py-4 border-t-2 border-gray-200 flex-shrink-0">
                <div class="flex gap-2">
                    <a :href="'/admin/games/' + (selectedGame?.game_id || '')" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] text-center">مشاهده بازی</a>
                    <button type="button" @click="closeModal()" class="flex-1 px-4 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-bold transition-all duration-200 shadow-sm hover:shadow-md">بستن</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function suspiciousGamesManager() {
    return {
        showModal: false,
        selectedGame: null,
        selectedGames: [],
        toggleAll(event) {
            this.selectedGames = event.target.checked ? <?= json_encode($allGameIdsOnPage) ?> : [];
        },
        showDetails(game) { this.selectedGame = game; this.showModal = true; },
        closeModal() { this.showModal = false; this.selectedGame = null; },
        parseJson(str) { try { return JSON.parse(str || '[]'); } catch(e) { return []; } },
        getRiskLabel(level) { const labels = {'low':'کم','medium':'متوسط','high':'زیاد','critical':'بحرانی'}; return labels[level] || level; },
        getRiskBadgeClass(level) {
            const classes = {'low':'bg-yellow-100 text-yellow-800 border-yellow-200','medium':'bg-orange-100 text-orange-800 border-orange-200','high':'bg-red-100 text-red-800 border-red-200','critical':'bg-gray-800 text-white border-gray-700'};
            return classes[level] || 'bg-gray-100 text-gray-800 border-gray-200';
        },
        getCheatTypeLabel(type) {
            const labels = {'fast_game':'⚡ بازی سریع','invalid_players':'👥 بازیکنان نامعتبر','too_few_players':'👥 بازیکنان کم','too_many_guests':'🎭 مهمانان زیاد','guest_only_game':'🚫 بازی فقط مهمان','unusual_win_pattern':'📊 الگوی برد غیرعادی','low_target_wins':'🎯 هدف برد کم','many_rounds':'🔄 دورهای زیاد'};
            return labels[type] || type;
        },
        formatDate(dateStr) { if (!dateStr) return '-'; const d = new Date(dateStr); return d.toLocaleDateString('fa-IR') + ' ' + d.toLocaleTimeString('fa-IR'); },
        markAsReviewed(id) {
            Swal.fire({title:'علامت‌گذاری به عنوان بررسی شده',text:'آیا مطمئن هستید که این بازی را بررسی کرده‌اید؟',icon:'question',showCancelButton:true,confirmButtonColor:'#16a34a',cancelButtonColor:'#6b7280',confirmButtonText:'بله، بررسی شده',cancelButtonText:'انصراف',reverseButtons:true}).then((result)=>{
                if(result.isConfirmed){
                    fetch('/admin/suspicious-games/mark-reviewed',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},body:'id='+id})
                    .then(r=>r.json()).then(data=>{if(data.success){Swal.fire({icon:'success',title:'انجام شد',text:'بازی به عنوان بررسی شده علامت‌گذاری شد',timer:2000,showConfirmButton:false}).then(()=>window.location.reload());}else{Swal.fire({icon:'error',title:'خطا',text:data.message||'خطا در انجام عملیات'});}}).catch(()=>Swal.fire({icon:'error',title:'خطا',text:'خطا در ارتباط با سرور'}));
                }
            });
        },
        deleteSingle(id) { this.deleteGames([id]); },
        bulkDelete() { if (this.selectedGames.length === 0) return; this.deleteGames(this.selectedGames); },
        deleteGames(ids) {
            Swal.fire({title:'حذف بازی(ها)',html:`آیا مطمئن هستید که می‌خواهید <strong class="text-red-600">${ids.length}</strong> بازی را حذف کنید؟<br><span class="text-xs text-gray-500">این عملیات غیرقابل بازگشت است و آمار کاربران مرتبط به طور خودکار باز محاسبه خواهد شد.</span>`,icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#6b7280',confirmButtonText:'بله، حذف کن',cancelButtonText:'انصراف',reverseButtons:true}).then((result)=>{
                if(result.isConfirmed){
                    const form = document.createElement('form'); form.method='POST'; form.action='/admin/suspicious-games/bulk-delete';
                    ids.forEach(id=>{const input=document.createElement('input'); input.type='hidden'; input.name='sg_ids[]'; input.value=id; form.appendChild(input);});
                    document.body.appendChild(form); form.submit();
                }
            });
        },
        refreshData() { window.location.reload(); }
    };
}
</script>