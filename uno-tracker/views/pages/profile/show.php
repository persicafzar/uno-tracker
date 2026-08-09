<?php

use Core\JalaliDate;

// آماده‌سازی داده‌ها برای نمودارها
$statusStatsJson = json_encode($profile['stats_by_status'] ?? [], JSON_UNESCAPED_UNICODE);
$modeStatsJson = json_encode($profile['stats_by_mode'] ?? [], JSON_UNESCAPED_UNICODE);
$dailyStatsJson = json_encode($profile['daily_stats'] ?? [], JSON_UNESCAPED_UNICODE);
$dayOfWeekStatsJson = json_encode($profile['day_of_week_stats'] ?? [], JSON_UNESCAPED_UNICODE);
$cardStatsJson = json_encode($profile['card_stats'] ?? [], JSON_UNESCAPED_UNICODE);

// اطلاعات عنوان
$titleInfo = $profile['title_info'] ?? null;
$userTitles = $profile['user_titles'] ?? [];

// آرایه ترجمه شرط‌ها
$conditionLabels = [
    'total_games' => 'تعداد بازی',
    'total_wins' => 'تعداد برد',
    'win_streak' => 'برد متوالی',
    'best_streak' => 'بهترین زنجیره',
    'team_wins' => 'برد تیمی',
    'total_points' => 'امتیاز کل',
    'max_consecutive_wins_with_card' => 'برد متوالی با کارت',
];
?>

<div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6" x-data="{ activeTab: 'stats' }">

    <!-- ========================================== -->
    <!-- ======= Header پروفایل ======= -->
    <!-- ========================================== -->
    <div class="relative bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-5 sm:p-7 text-white shadow-2xl overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-5 sm:gap-6">
            <!-- آواتار با نمایش کامل -->
            <div class="relative flex-shrink-0">
                <?php if (!empty($profile['avatar_path'])): ?>
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-white/90 shadow-2xl overflow-hidden hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($profile['avatar_path'] ?? '') ?>"
                            alt="آواتار"
                            class="w-full h-full aspect-square rounded-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-white/90 shadow-2xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-3xl sm:text-4xl font-black hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <?= mb_substr($profile['nickname'] ?? '?', 0, 1) ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($profile['is_online'])): ?>
                    <span class="absolute bottom-1 right-1 sm:bottom-2 sm:right-2 w-4 h-4 sm:w-5 sm:h-5 bg-green-500 border-2 border-white rounded-full animate-pulse"></span>
                <?php endif; ?>
            </div>

            <!-- اطلاعات کاربر -->
            <div class="flex-1 text-center sm:text-right min-w-0 w-full">
                <h1 class="text-xl sm:text-3xl font-black drop-shadow-2xl truncate"><?= htmlspecialchars($profile['nickname'] ?? '') ?></h1>
                <p class="text-white/80 text-sm sm:text-base font-medium truncate"><?= htmlspecialchars($profile['real_name'] ?? '') ?></p>

                <?php if (!empty($profile['tagline'])): ?>
                    <p class="text-white/70 italic mt-2 text-xs sm:text-sm break-words drop-shadow">"<?= htmlspecialchars($profile['tagline'] ?? '') ?>"</p>
                <?php endif; ?>

                <!-- استایل بازی و عنوان -->
                <div class="flex items-center justify-center sm:justify-start mt-3 flex-wrap gap-2">
                    <?php if (!empty($profile['playstyle_name'])): ?>
                        <span class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-xs sm:text-sm font-bold border border-white/30 shadow-sm">
                            <?= htmlspecialchars($profile['playstyle_icon'] ?? '🎮') ?>
                            <?= htmlspecialchars($profile['playstyle_name'] ?? '') ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($titleInfo)): ?>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/20 shadow-lg">
                            <div class="flex items-center gap-2.5 mb-1.5">
                                <span class="text-2xl sm:text-3xl drop-shadow"><?= htmlspecialchars($titleInfo['icon'] ?? '🏆') ?></span>
                                <div>
                                    <div class="font-black text-white text-sm sm:text-base drop-shadow"><?= htmlspecialchars($titleInfo['name'] ?? '') ?></div> <?php if (!empty($titleInfo['description'])): ?>
                                        <div class="text-xs text-white/70"><?= htmlspecialchars($titleInfo['description'] ?? '') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($titleInfo['bonus_points']) && $titleInfo['bonus_points'] > 0): ?>
                                <div class="inline-flex items-center gap-1.5 bg-green-500/30 text-green-200 px-2.5 py-1 rounded-full text-xs font-bold border border-green-500/30">
                                    ⭐ +<?= $titleInfo['bonus_points'] ?> امتیاز در هر برد
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- دکمه ویرایش -->
            <?php if ($isOwn): ?>
                <a href="/profile/edit"
                    class="group relative px-5 sm:px-7 py-3 sm:py-3.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-2xl font-bold text-sm sm:text-base transition-all duration-300 hover:shadow-2xl hover:scale-[1.05] border border-white/20 flex items-center gap-2.5 overflow-hidden flex-shrink-0 w-full sm:w-auto text-center justify-center">
                    <span class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>
                    <span class="relative z-10 text-xl group-hover:rotate-12 transition-transform duration-300">✏️</span>
                    <span class="relative z-10">ویرایش پروفایل</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <!-- ========================================== -->
    <!-- ======= وضعیت مجوزهای کاربر ======= -->
    <!-- ========================================== -->
    <?php
    $canCreateGame = !empty($profile['can_create_game']);
    $canJoinGame = !empty($profile['can_join_game']);
    $hasAnyRestriction = !$canCreateGame || !$canJoinGame;
    ?>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/70 p-4 sm:p-5 mt-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-500 to-slate-600 flex items-center justify-center text-white text-xl">
                🔐
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-black text-gray-800 tracking-tight">وضعیت دسترسی‌ها</h3>
                <p class="text-xs text-gray-500 font-medium">مجوزهای شما در سیستم</p>
            </div>
            <?php if ($hasAnyRestriction): ?>
                <span class="mr-auto px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold border border-amber-200">
                    ⚠️ محدودیت فعال
                </span>
            <?php else: ?>
                <span class="mr-auto px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold border border-emerald-200">
                    ✅ دسترسی کامل
                </span>
            <?php endif; ?>
        </div>

        <div class="!grid !grid-cols-1 sm:!grid-cols-2 gap-3">
            <!-- مجوز ساخت بازی -->
            <div class="relative overflow-hidden rounded-xl p-4 border-2 transition-all duration-300 <?= $canCreateGame ? 'bg-gradient-to-br from-emerald-50 to-green-50 border-emerald-200' : 'bg-gradient-to-br from-red-50 to-rose-50 border-red-200' ?>">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 <?= $canCreateGame ? 'bg-emerald-100' : 'bg-red-100' ?>">
                        <?= $canCreateGame ? '🎮' : '🚫' ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-black text-sm sm:text-base <?= $canCreateGame ? 'text-emerald-800' : 'text-red-800' ?>">
                                ساخت بازی
                            </h4>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $canCreateGame ? 'bg-emerald-200 text-emerald-800' : 'bg-red-200 text-red-800' ?>">
                                <?= $canCreateGame ? 'مجاز' : 'غیرمجاز' ?>
                            </span>
                        </div>
                        <p class="text-xs <?= $canCreateGame ? 'text-emerald-700' : 'text-red-700' ?> font-medium leading-relaxed">
                            <?php if ($canCreateGame): ?>
                                شما می‌توانید بازی جدید ایجاد کنید و داور بازی باشید.
                            <?php else: ?>
                                شما فعلاً اجازه ساخت بازی را ندارید. لطفاً با مدیر سیستم تماس بگیرید.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <?php if (!$canCreateGame): ?>
                    <div class="absolute top-2 right-2">
                        <span class="text-lg opacity-30">⛔</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- مجوز شرکت در بازی -->
            <div class="relative overflow-hidden rounded-xl p-4 border-2 transition-all duration-300 <?= $canJoinGame ? 'bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-200' : 'bg-gradient-to-br from-red-50 to-rose-50 border-red-200' ?>">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 <?= $canJoinGame ? 'bg-blue-100' : 'bg-red-100' ?>">
                        <?= $canJoinGame ? '👥' : '🚫' ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-black text-sm sm:text-base <?= $canJoinGame ? 'text-blue-800' : 'text-red-800' ?>">
                                شرکت در بازی
                            </h4>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $canJoinGame ? 'bg-blue-200 text-blue-800' : 'bg-red-200 text-red-800' ?>">
                                <?= $canJoinGame ? 'مجاز' : 'غیرمجاز' ?>
                            </span>
                        </div>
                        <p class="text-xs <?= $canJoinGame ? 'text-blue-700' : 'text-red-700' ?> font-medium leading-relaxed">
                            <?php if ($canJoinGame): ?>
                                شما می‌توانید در بازی‌های دیگران شرکت کنید.
                            <?php else: ?>
                                شما فعلاً اجازه شرکت در بازی را ندارید. لطفاً با مدیر سیستم تماس بگیرید.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <?php if (!$canJoinGame): ?>
                    <div class="absolute top-2 right-2">
                        <span class="text-lg opacity-30">⛔</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($hasAnyRestriction && $isOwn): ?>
            <div class="mt-4 p-3 bg-amber-50 border-2 border-amber-200 rounded-xl">
                <div class="flex items-start gap-2">
                    <span class="text-lg flex-shrink-0">💡</span>
                    <div class="text-xs text-amber-800 font-medium leading-relaxed">
                        <strong>نکته:</strong> محدودیت‌های دسترسی توسط یک مدیر سیستم اعمال شده‌اند. در صورتی که فکر می‌کنید اشتباهی رخ داده، لطفاً با یکی از مدیران تماس بگیرید.
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <!-- ========================================== -->
    <!-- ======= کارت‌های آمار ======= -->
    <!-- ========================================== -->
    <div class="!grid !grid-cols-2 md:!grid-cols-4 gap-3 sm:gap-4 p-3 sm:p-5 bg-white rounded-2xl shadow-xl border border-gray-200/70 mt-4">
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-3 sm:p-4 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <div class="text-2xl sm:text-3xl font-black text-white drop-shadow"><?= $stats['total_games'] ?? 0 ?></div>
                <div class="text-white/80 text-xs sm:text-sm mt-1">کل بازی‌های پایان‌یافته</div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-3 sm:p-4 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <div class="text-2xl sm:text-3xl font-black text-white drop-shadow"><?= $stats['total_wins'] ?? 0 ?></div>
                <div class="text-white/80 text-xs sm:text-sm font-medium mt-0.5">تعداد بردها</div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl p-3 sm:p-4 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <div class="text-2xl sm:text-3xl font-black text-white drop-shadow"><?= number_format($profile['win_rate'] ?? 0, 1) ?>%</div>
                <div class="text-white/80 text-xs sm:text-sm font-medium mt-0.5">نرخ برد</div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl p-3 sm:p-4 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <div class="text-2xl sm:text-3xl font-black text-white drop-shadow"><?= $profile['total_points'] ?? 0 ?></div>
                <div class="text-white/80 text-xs sm:text-sm font-medium mt-0.5">مجموع امتیازات</div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ======= تب‌ها ======= -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/70 p-3 sm:p-5 mt-4">
        <!-- ======= Tabs ======= -->
        <div class="flex flex-nowrap gap-1 sm:gap-2 border-b-2 border-gray-200 mb-4 overflow-x-auto pb-1 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent">
            <button @click="activeTab = 'stats'"
                :class="activeTab === 'stats' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                class="px-3 sm:px-5 py-2.5 border-b-2 font-bold transition-all duration-200 text-xs sm:text-sm rounded-t-xl whitespace-nowrap flex-shrink-0">
                📊 آمار
            </button>
            <button @click="activeTab = 'charts'"
                :class="activeTab === 'charts' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                class="px-3 sm:px-5 py-2.5 border-b-2 font-bold transition-all duration-200 text-xs sm:text-sm rounded-t-xl whitespace-nowrap flex-shrink-0">
                📈 نمودارها
            </button>
            <button @click="activeTab = 'history'"
                :class="activeTab === 'history' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                class="px-3 sm:px-5 py-2.5 border-b-2 font-bold transition-all duration-200 text-xs sm:text-sm rounded-t-xl whitespace-nowrap flex-shrink-0">
                📜 تاریخچه
            </button>
            <button @click="activeTab = 'achievements'"
                :class="activeTab === 'achievements' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                class="px-3 sm:px-5 py-2.5 border-b-2 font-bold transition-all duration-200 text-xs sm:text-sm rounded-t-xl whitespace-nowrap flex-shrink-0">
                🏆 دستاوردها
            </button>
            <?php if (count($userTitles) > 0): ?>
                <button @click="activeTab = 'titles'"
                    :class="activeTab === 'titles' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50'"
                    class="px-3 sm:px-5 py-2.5 border-b-2 font-bold transition-all duration-200 text-xs sm:text-sm rounded-t-xl whitespace-nowrap flex-shrink-0">
                    🎖️ عناوین
                </button>
            <?php endif; ?>
        </div>

        <!-- ======= Stats Tab ======= -->
        <div x-show="activeTab === 'stats'" x-cloak class="space-y-5">
            <!-- زنجیره پیروزی و امتیاز به ازای هر بازی -->
            <div class="!grid !grid-cols-1 sm:!grid-cols-2 gap-4">
                <div class="relative overflow-hidden bg-gradient-to-br from-orange-100 to-amber-100 rounded-2xl p-4 sm:p-5 border-2 border-orange-300 shadow-md">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative z-10">
                        <div class="text-3xl sm:text-5xl font-black text-orange-700 drop-shadow"><?= $profile['current_streak'] ?? 0 ?></div>
                        <div class="text-orange-800 font-bold text-sm sm:text-base">برد متوالی</div>
                        <div class="text-orange-700/70 text-xs sm:text-sm font-medium mt-1">بهترین رکورد: <?= $profile['best_streak'] ?? 0 ?> برد</div>
                        <div class="mt-2 text-3xl">🔥</div>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-indigo-100 to-violet-100 rounded-2xl p-4 sm:p-5 border-2 border-indigo-300 shadow-md">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative z-10">
                        <div class="text-3xl sm:text-5xl font-black text-indigo-700 drop-shadow"><?= number_format($profile['points_per_game'] ?? 0, 2) ?></div>
                        <div class="text-indigo-800 font-bold text-sm sm:text-base">میانگین امتیاز به ازای هر بازی</div>
                        <div class="mt-2 text-3xl">⚡</div>
                    </div>
                </div>
            </div>

            <!-- آمار تفصیلی -->
            <div class="!grid !grid-cols-2 sm:!grid-cols-4 gap-3">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-3 text-center border-2 border-blue-200 shadow-sm">
                    <div class="text-xl font-black text-blue-700"><?= $profile['total_losses'] ?? 0 ?></div>
                    <div class="text-xs text-gray-600 font-medium mt-0.5">تعداد باخت‌ها</div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-3 text-center border-2 border-purple-200 shadow-sm">
                    <div class="text-xl font-black text-purple-700"><?= $profile['active_games'] ?? 0 ?></div>
                    <div class="text-xs text-gray-600 font-medium mt-0.5">بازی‌های در جریان</div>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-3 text-center border-2 border-emerald-200 shadow-sm">
                    <div class="text-xl font-black text-emerald-700"><?= $profile['best_streak'] ?? 0 ?></div>
                    <div class="text-xs text-gray-600 font-medium mt-0.5">بهترین رکورد</div>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-3 text-center border-2 border-amber-200 shadow-sm">
                    <div class="text-xl font-black text-amber-700"><?= count($achievements ?? []) ?></div>
                    <div class="text-xs text-gray-600 font-medium mt-0.5">مدال‌های کسب شده</div>
                </div>
            </div>

            <!-- آمار بر اساس وضعیت بازی -->
            <div class="bg-white rounded-2xl p-4 border-2 border-gray-200 shadow-md">
                <h3 class="text-base sm:text-lg font-black text-gray-800 mb-4 tracking-tight">📊 آمار بر اساس وضعیت بازی</h3>
                <div class="!grid !grid-cols-2 md:!grid-cols-5 gap-3">
                    <?php
                    $statusColors = [
                        'finished' => ['bg' => 'from-green-50 to-green-100', 'text' => 'text-green-700', 'border' => 'border-green-300'],
                        'active' => ['bg' => 'from-blue-50 to-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-300'],
                        'pending' => ['bg' => 'from-yellow-50 to-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-300'],
                        'paused' => ['bg' => 'from-orange-50 to-orange-100', 'text' => 'text-orange-700', 'border' => 'border-orange-300'],
                        'cancelled' => ['bg' => 'from-red-50 to-red-100', 'text' => 'text-red-700', 'border' => 'border-red-300'],
                    ];
                    $statusIcons = [
                        'finished' => '✅',
                        'active' => '🎮',
                        'pending' => '⏳',
                        'paused' => '⏸️',
                        'cancelled' => '❌',
                    ];
                    $statusLabels = [
                        'finished' => 'پایان یافته',
                        'active' => 'در حال بازی',
                        'pending' => 'در انتظار',
                        'paused' => 'متوقف',
                        'cancelled' => 'لغو شده',
                    ];
                    $statusMap = [];
                    foreach ($profile['stats_by_status'] ?? [] as $stat) {
                        $statusMap[$stat['status']] = $stat['count'];
                    }
                    ?>
                    <?php foreach (['finished', 'active', 'pending', 'paused', 'cancelled'] as $status): ?>
                        <?php
                        $colors = $statusColors[$status] ?? $statusColors['pending'];
                        $icon = $statusIcons[$status] ?? '📊';
                        $count = $statusMap[$status] ?? 0;
                        ?>
                        <div class="bg-gradient-to-br <?= $colors['bg'] ?> rounded-xl p-3 text-center border-2 <?= $colors['border'] ?> shadow-sm">
                            <div class="text-2xl mb-1 drop-shadow"><?= $icon ?></div>
                            <div class="text-xl font-black <?= $colors['text'] ?>"><?= $count ?></div>
                            <div class="text-xs text-gray-600 font-medium mt-0.5"><?= $statusLabels[$status] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- آمار بر اساس حالت بازی -->
            <div class="bg-white rounded-2xl p-4 border-2 border-gray-200 shadow-md">
                <h3 class="text-base sm:text-lg font-black text-gray-800 mb-4 tracking-tight">🎯 آمار بر اساس حالت بازی</h3>
                <div class="!grid !grid-cols-1 md:!grid-cols-2 gap-4">
                    <?php
                    $modeIcons = [
                        'solo' => '👤',
                        'friendly' => '👥',
                    ];
                    ?>
                    <?php foreach ($profile['stats_by_mode'] ?? [] as $modeStat): ?>
                        <?php $icon = $modeIcons[$modeStat['mode']] ?? '🎮'; ?>
                        <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-4 border-2 border-gray-200 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.01]">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white/30 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                            <div class="relative z-10">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="text-3xl drop-shadow"><?= $icon ?></span>
                                    <h4 class="text-base sm:text-lg font-black text-gray-800"><?= htmlspecialchars($modeStat['label'] ?? '') ?></h4>
                                </div>
                                <div class="!grid !grid-cols-2 gap-3 text-center">
                                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-2">
                                        <div class="text-xl font-black text-indigo-600"><?= $modeStat['total_games'] ?></div>
                                        <div class="text-xs text-gray-600 font-medium">بازی</div>
                                    </div>
                                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-2">
                                        <div class="text-xl font-black text-emerald-600"><?= $modeStat['total_wins'] ?></div>
                                        <div class="text-xs text-gray-600 font-medium">برد</div>
                                    </div>
                                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-2">
                                        <div class="text-xl font-black text-rose-600"><?= $modeStat['total_points'] ?></div>
                                        <div class="text-xs text-gray-600 font-medium">امتیاز</div>
                                    </div>
                                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-2">
                                        <div class="text-xl font-black text-violet-600"><?= $modeStat['win_rate'] ?>%</div>
                                        <div class="text-xs text-gray-600 font-medium">نرخ برد</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ======= Charts Tab ======= -->
        <div x-show="activeTab === 'charts'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- نمودار پیشرفت امتیاز -->
                <div class="bg-white rounded-2xl p-4 border-2 border-gray-200 shadow-md">
                    <h3 class="text-base sm:text-lg font-black text-gray-800 mb-3 tracking-tight">📈 پیشرفت امتیاز (۳۰ روز اخیر)</h3>
                    <div class="h-64">
                        <canvas id="progressChart"></canvas>
                    </div>
                </div>

                <!-- نمودار توزیع وضعیت -->
                <div class="bg-white rounded-2xl p-4 border-2 border-gray-200 shadow-md">
                    <h3 class="text-base sm:text-lg font-black text-gray-800 mb-3 tracking-tight">🥧 توزیع وضعیت بازی‌ها</h3>
                    <div class="h-64">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- نمودار بردها/باخت‌ها -->
                <div class="bg-white rounded-2xl p-4 border-2 border-gray-200 shadow-md">
                    <h3 class="text-base sm:text-lg font-black text-gray-800 mb-3 tracking-tight">⚔️ بردها و باخت‌ها</h3>
                    <div class="h-64">
                        <canvas id="winLossChart"></canvas>
                    </div>
                </div>

                <!-- نمودار روز هفته -->
                <div class="bg-white rounded-2xl p-4 border-2 border-gray-200 shadow-md">
                    <h3 class="text-base sm:text-lg font-black text-gray-800 mb-3 tracking-tight">📅 فعالیت بر اساس روز هفته</h3>
                    <div class="h-64">
                        <canvas id="dayOfWeekChart"></canvas>
                    </div>
                </div>

                <!-- نمودار حالت بازی -->
                <div class="bg-white rounded-2xl p-4 border-2 border-gray-200 shadow-md lg:col-span-2">
                    <h3 class="text-base sm:text-lg font-black text-gray-800 mb-3 tracking-tight">🎯 مقایسه انفرادی و تیمی</h3>
                    <div class="h-64">
                        <canvas id="modeChart"></canvas>
                    </div>
                </div>

                <!-- نمودار کارت‌های پرکاربرد -->
                <?php if (!empty($profile['card_stats'])): ?>
                    <div class="bg-white rounded-2xl p-4 border-2 border-gray-200 shadow-md lg:col-span-2">
                        <?php
                        // مرتب‌سازی کارت‌ها بر اساس تعداد برد (نزولی) برای هماهنگی با نمودار
                        $sortedCards = $profile['card_stats'];
                        usort($sortedCards, function ($a, $b) {
                            return ($b['usage_count'] ?? 0) - ($a['usage_count'] ?? 0);
                        });

                        // پالت رنگ‌های ثابت (همانند نمودار)
                        $colorPalette = [
                            '#ef4444',
                            '#f97316',
                            '#f59e0b',
                            '#eab308',
                            '#84cc16',
                            '#22c55e',
                            '#10b981',
                            '#14b8a6',
                            '#06b6d4',
                            '#0ea5e9',
                            '#3b82f6',
                            '#6366f1',
                            '#8b5cf6',
                            '#a855f7',
                            '#d946ef',
                            '#ec4899',
                            '#f43f5e',
                        ];
                        $totalCards = count($sortedCards);
                        ?>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base sm:text-lg font-black text-gray-800 flex items-center gap-2">
                                <span class="text-2xl">🃏</span>
                                کارت‌های برنده پرکاربرد
                            </h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                                <?= $totalCards ?> کارت برتر
                            </span>
                        </div>
                        <div class="h-72">
                            <canvas id="cardUsageChart"></canvas>
                        </div>

                        <div class="!grid-cols-3 gap-3 grid lg:!grid-cols-6 mt-5 sm:!grid-cols-4">
                            <?php foreach ($sortedCards as $index => $card):
                                $color = $colorPalette[$index % count($colorPalette)];
                                $emoji = $card['emoji'] ?? '🃏';
                                $usage = $card['usage_count'] ?? 0;
                                // رنگ‌های روشن‌تر برای پس‌زمینه
                                $bgColor = $color . '20'; // 20% opacity
                                $borderColor = $color . '60'; // 60% opacity
                            ?>
                                <div class="relative group">
                                    <div class="rounded-xl p-3 border-2 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.03] text-center"
                                        style="background-color: <?= $bgColor ?>; border-color: <?= $borderColor ?>;">
                                        <div class="text-3xl sm:text-4xl mb-1 drop-shadow"><?= htmlspecialchars($emoji) ?></div>
                                        <div class="font-bold text-gray-800 text-xs sm:text-sm truncate" title="<?= htmlspecialchars($card['name']) ?>">
                                            <?= htmlspecialchars($card['name']) ?>
                                        </div>
                                        <div class="flex items-center justify-center gap-1 mt-1">
                                            <span class="text-lg font-black" style="color: <?= $color ?>;">
                                                🏆 <?= $usage ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($card['description'])): ?>
                                            <div class="absolute invisible group-hover:visible z-20 bottom-full left-1/2 -translate-x-1/2 -mb-1 px-4 py-2.5 bg-gray-900/95 backdrop-blur-sm text-white text-xs rounded-xl shadow-2xl max-w-36 w-max break-words whitespace-normal text-center leading-relaxed border border-white/10">
                                                <?= htmlspecialchars($card['description']) ?>
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900/95"></div>
                                            </div>
                                        <?php endif; ?>
                                        <!-- نشان کمیابی (اختیاری) -->
                                        <?php //if (!empty($card['rarity'])): 
                                        ?>
                                        <?php
                                        // $rarityLabel = match ($card['rarity']) {
                                        //     'common' => 'معمولی',
                                        //     'rare' => 'کمیاب',
                                        //     'legendary' => 'افسانه‌ای',
                                        //     default => $card['rarity']
                                        // };
                                        ?>
                                        <!-- <div class="absolute top-1 right-1 text-[8px] font-bold px-1.5 py-0.5 rounded-full bg-white/80 shadow-sm"
                                                    style="color: <?= $color ?>;">
                                                    <?php //echo mb_substr($rarityLabel, 0, 2) 
                                                    ?>
                                                </div> -->
                                        <?php //endif; 
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-2xl p-8 border-2 border-gray-200 lg:col-span-2 text-center shadow-md">
                        <div class="text-5xl mb-3 opacity-50">🃏</div>
                        <p class="text-gray-600 font-medium">هنوز کارتی برای نمایش وجود ندارد</p>
                        <p class="text-gray-500 text-sm mt-1">با بازی کردن، آمار کارت‌های شما نمایش داده می‌شود</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ======= History Tab ======= -->
        <div x-show="activeTab === 'history'" x-cloak>
            <?php if (empty($games)): ?>
                <div class="text-center py-10 sm:py-14">
                    <div class="text-6xl sm:text-7xl mb-4 opacity-50">🎮</div>
                    <p class="text-gray-600 text-sm sm:text-base font-medium">هنوز بازی‌ای انجام نداده‌اید</p>
                    <a href="/game/create" class="inline-block mt-4 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-2xl text-sm font-bold hover:shadow-2xl transition-all duration-300 hover:scale-[1.02]">
                        ایجاد بازی جدید
                    </a>
                </div>
            <?php else: ?>
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <p class="text-sm text-gray-600 font-medium">
                        نمایش <?= count($games) ?> بازی از <?= $profile['total_games'] ?? 0 ?> بازی
                    </p>
                    <a href="/games?player_id=<?= $profile['user_id'] ?>"
                        class="text-sm text-indigo-600 hover:text-indigo-700 font-bold flex items-center gap-1 hover:underline transition">
                        <span>مشاهده همه بازی‌ها</span>
                        <span>←</span>
                    </a>
                </div>

                <div class="space-y-2.5 sm:space-y-3">
                    <?php foreach ($games as $game): ?>
                        <?php
                        $isWinner = !empty($game['is_winner']);
                        $isTeamMode = ($game['game_mode'] ?? '') === 'friendly';
                        $statusLabels = [
                            'finished' => ['label' => 'پایان یافته', 'color' => 'green'],
                            'active' => ['label' => 'در حال بازی', 'color' => 'blue'],
                            'pending' => ['label' => 'در انتظار', 'color' => 'yellow'],
                            'paused' => ['label' => 'متوقف', 'color' => 'orange'],
                            'cancelled' => ['label' => 'لغو شده', 'color' => 'red'],
                        ];
                        $status = $statusLabels[$game['status']] ?? $statusLabels['pending'];
                        ?>
                        <a href="/game/<?= $game['id'] ?>"
                            class="block rounded-2xl p-3.5 sm:p-4 transition-all duration-300 border-2 hover:shadow-xl hover:scale-[1.01] <?= $isWinner ? 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-300' : 'bg-gradient-to-r from-gray-50 to-gray-50/50 border-gray-200' ?>">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <?php if ($isWinner): ?>
                                        <span class="text-2xl flex-shrink-0 drop-shadow">🏆</span>
                                    <?php elseif ($game['status'] === 'finished'): ?>
                                        <span class="text-2xl flex-shrink-0 opacity-50">💔</span>
                                    <?php else: ?>
                                        <span class="text-2xl flex-shrink-0 opacity-50">⏳</span>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-800 text-sm truncate">
                                            <?= htmlspecialchars($game['name'] ?: 'بازی #' . $game['id']) ?>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5 flex flex-wrap gap-1 items-center">
                                            <span><?= $isTeamMode ? '👥 تیمی' : '👤 انفرادی' ?></span>
                                            <span class="text-gray-300">•</span>
                                            <span><?= $game['total_players'] ?? 0 ?> نفر</span>
                                            <?php if (!empty($game['total_teams']) && $game['total_teams'] > 0): ?>
                                                <span class="text-gray-300">•</span>
                                                <span><?= $game['total_teams'] ?> تیم</span>
                                            <?php endif; ?>
                                            <span class="text-gray-300">•</span>
                                            <span class="px-2 py-0.5 bg-<?= $status['color'] ?>-100 text-<?= $status['color'] ?>-700 rounded-full text-[10px] font-bold border border-<?= $status['color'] ?>-200">
                                                <?= $status['label'] ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-2">
                                    <div class="flex items-center gap-2 sm:gap-3 justify-end">
                                        <span class="text-sm font-black <?= $isWinner ? 'text-emerald-600' : 'text-gray-500' ?>">
                                            <?= $game['wins_count'] ?? 0 ?> برد
                                        </span>
                                        <span class="text-sm font-black text-indigo-600">
                                            <?= $game['total_score'] ?? 0 ?> امتیاز
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-gray-400 font-medium mt-0.5">
                                        <?= JalaliDate::format('Y/m/d H:i', strtotime($game['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if ($hasMore): ?>
                    <div class="mt-5 text-center">
                        <a href="?page=<?= $page + 1 ?>#history"
                            class="inline-block px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl text-sm font-bold transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                            مشاهده بازی‌های بیشتر ←
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- ======= Achievements Tab ======= -->
        <div x-show="activeTab === 'achievements'" x-cloak>
            <?php if (empty($achievements)): ?>
                <div class="text-center py-10 sm:py-14">
                    <div class="text-6xl sm:text-7xl mb-4 opacity-50">🏆</div>
                    <p class="text-gray-600 text-sm sm:text-base font-medium mb-1">هنوز مدالی کسب نکرده‌اید</p>
                    <p class="text-gray-500 text-xs sm:text-sm">با بازی کردن، مدال‌های افتخار کسب کنید!</p>
                    <a href="/achievements"
                        class="inline-block mt-4 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-2xl text-sm font-bold hover:shadow-2xl transition-all duration-300 hover:scale-[1.02]">
                        مشاهده همه مدال‌ها
                    </a>
                </div>
            <?php else: ?>
                <div class="relative overflow-hidden bg-gradient-to-r from-yellow-100 to-amber-100 rounded-2xl p-4 border-2 border-yellow-300 shadow-md mb-5">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative z-10 flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-gray-800">🏆 دستاوردهای شما</h3>
                            <p class="text-sm text-gray-700 font-medium mt-0.5">
                                شما <?= count($achievements) ?> مدال کسب کرده‌اید
                            </p>
                        </div>
                        <a href="/achievements"
                            class="text-sm text-indigo-700 hover:text-indigo-900 font-bold hover:underline transition">
                            مشاهده همه ←
                        </a>
                    </div>
                </div>

                <div class="!grid !grid-cols-2 md:!grid-cols-3 lg:!grid-cols-4 gap-3">
                    <?php foreach ($achievements as $achievement): ?>
                        <?php
                        $rarityColors = [
                            'common' => ['bg' => 'from-gray-50 to-gray-100', 'border' => 'border-gray-300', 'text' => 'text-gray-700'],
                            'rare' => ['bg' => 'from-blue-50 to-blue-100', 'border' => 'border-blue-300', 'text' => 'text-blue-700'],
                            'epic' => ['bg' => 'from-purple-50 to-purple-100', 'border' => 'border-purple-300', 'text' => 'text-purple-700'],
                            'legendary' => ['bg' => 'from-yellow-50 to-orange-100', 'border' => 'border-yellow-400', 'text' => 'text-yellow-700'],
                        ];
                        $rarity = $rarityColors[$achievement->rarity] ?? $rarityColors['common'];
                        ?>
                        <div class="bg-gradient-to-br <?= $rarity['bg'] ?> rounded-2xl p-4 border-2 <?= $rarity['border'] ?> text-center hover:shadow-2xl transition-all duration-300 hover:scale-[1.03]">
                            <div class="text-4xl mb-2 drop-shadow"><?= $achievement->icon ?></div>
                            <div class="font-bold text-gray-800 text-sm mb-0.5"><?= htmlspecialchars($achievement->name ?? '') ?></div>
                            <div class="text-xs text-gray-600 line-clamp-2 mb-1.5"><?= htmlspecialchars($achievement->description ?? '') ?></div>
                            <div class="text-xs <?= $rarity['text'] ?> font-black">
                                +<?= $achievement->xp_reward ?> XP
                            </div>
                            <?php if ($achievement->user_unlocked_at): ?>
                                <div class="text-[10px] text-gray-400 font-medium mt-1">
                                    <?= JalaliDate::format('Y/m/d', strtotime($achievement->user_unlocked_at)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ======= Titles Tab ======= -->
        <?php
        $userId = $profile['user_id'] ?? $profile['id'];
        $allTitles = \Core\Database::getInstance()->fetchAll(
            "SELECT 
                    t.id, t.name, t.icon, t.description, t.bonus_points, t.condition_type, t.condition_value,
                    ut.unlocked_at, ut.is_active
                FROM titles t
                LEFT JOIN user_titles ut ON t.id = ut.title_id AND ut.user_id = ?
                ORDER BY t.priority DESC, t.id ASC",
            [$userId]
        );
        $currentTitleInfo = null;
        foreach ($allTitles as $title) {
            if (!empty($title['is_active'])) {
                $currentTitleInfo = $title;
                break;
            }
        }
        ?>

        <div x-show="activeTab === 'titles'" x-cloak>
            <div class="!grid !grid-cols-1 md:!grid-cols-2 lg:!grid-cols-3 gap-3">
                <?php foreach ($allTitles as $title): ?>
                    <?php
                    $isUnlocked = !empty($title['unlocked_at']);
                    $isCurrent = !empty($title['is_active']);
                    $conditionLabel = $conditionLabels[$title['condition_type']] ?? $title['condition_type'];

                    if ($isCurrent) {
                        $cardClass = 'border-yellow-400 bg-yellow-50 ring-2 ring-yellow-300';
                    } elseif ($isUnlocked) {
                        $cardClass = 'border-emerald-300 bg-emerald-50';
                    } else {
                        continue;
                        $cardClass = 'border-gray-200 bg-gray-50 opacity-70';
                    }
                    ?>
                    <div class="relative rounded-2xl p-4 border-2 <?= $cardClass ?> hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                        <?php if ($isCurrent): ?>
                            <span class="absolute top-3 left-3 px-2.5 py-0.5 bg-yellow-200 text-yellow-800 rounded-full text-[10px] font-black border border-yellow-300">فعال</span>
                        <?php elseif ($isUnlocked): ?>
                            <span class="absolute top-3 left-3 px-2.5 py-0.5 bg-emerald-200 text-emerald-800 rounded-full text-[10px] font-black border border-emerald-300">کسب شده</span>
                        <?php endif; ?>

                        <div class="flex items-start gap-3 mt-2">
                            <div class="text-4xl <?= !$isUnlocked ? 'grayscale opacity-50' : '' ?> drop-shadow">
                                <?= htmlspecialchars($title['icon'] ?? '🏆') ?>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-gray-800 text-sm sm:text-base"><?= htmlspecialchars($title['name'] ?? '') ?></h4>
                                <?php if (!empty($title['description'])): ?>
                                    <p class="text-xs text-gray-600 mt-0.5"><?= htmlspecialchars($title['description']) ?></p>
                                <?php endif; ?>

                                <div class="mt-2.5 pt-2 border-t border-gray-200/50">
                                    <div class="text-[10px] text-gray-500 font-medium mb-1">شرط کسب:</div>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold border border-blue-200">
                                            📋 <?= htmlspecialchars($conditionLabel ?? '') ?>: <?= $title['condition_value'] ?>
                                        </span>
                                        <?php if (!empty($title['bonus_points']) && $title['bonus_points'] > 0): ?>
                                            <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-200">
                                                ⭐ +<?= $title['bonus_points'] ?> امتیاز
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($isUnlocked && !empty($title['unlocked_at'])): ?>
                                    <div class="text-[10px] text-gray-500 mt-2 flex items-center gap-1 font-medium">
                                        <span>✅</span>
                                        <span>کسب شده در: <?= \Core\JalaliDate::format('Y/m/d', strtotime($title['unlocked_at'])) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- ======= Chart.js Scripts ======= -->
<!-- ========================================== -->
<script src="/assets/js/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusStats = <?= $statusStatsJson ?>;
        const modeStats = <?= $modeStatsJson ?>;
        const dailyStats = <?= $dailyStatsJson ?>;
        const dayOfWeekStats = <?= $dayOfWeekStatsJson ?>;
        const cardStats = <?= $cardStatsJson ?>;

        // نمودار پیشرفت امتیاز
        if (dailyStats.length > 0) {
            const progressCtx = document.getElementById('progressChart');
            if (progressCtx) {
                new Chart(progressCtx, {
                    type: 'line',
                    data: {
                        labels: dailyStats.map(d => d.date.substring(5)),
                        datasets: [{
                            label: 'امتیاز',
                            data: dailyStats.map(d => d.points),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#6366f1'
                        }, {
                            label: 'بردها',
                            data: dailyStats.map(d => d.wins),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        family: 'Vazir',
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    usePointStyle: true,
                                    padding: 15
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }

        // نمودار توزیع وضعیت
        if (statusStats.length > 0) {
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                const statusColors = {
                    'finished': '#10b981',
                    'active': '#3b82f6',
                    'pending': '#f59e0b',
                    'paused': '#f97316',
                    'cancelled': '#ef4444',
                };
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusStats.map(s => s.label),
                        datasets: [{
                            data: statusStats.map(s => s.count),
                            backgroundColor: statusStats.map(s => statusColors[s.status] ||
                                '#6b7280'),
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        family: 'Vazir',
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    usePointStyle: true,
                                    padding: 12
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        }

        // نمودار بردها/باخت‌ها
        const winLossCtx = document.getElementById('winLossChart');
        if (winLossCtx) {
            const totalWins = <?= $stats['total_wins'] ?? 0 ?>;
            const totalLosses = <?= $profile['total_losses'] ?? 0 ?>;
            new Chart(winLossCtx, {
                type: 'doughnut',
                data: {
                    labels: ['بردها', 'باخت‌ها'],
                    datasets: [{
                        data: [totalWins, totalLosses],
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    family: 'Vazir',
                                    size: 12,
                                    weight: 'bold'
                                },
                                usePointStyle: true,
                                padding: 12
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        // نمودار روز هفته
        if (dayOfWeekStats.length > 0) {
            const dayOfWeekCtx = document.getElementById('dayOfWeekChart');
            if (dayOfWeekCtx) {
                new Chart(dayOfWeekCtx, {
                    type: 'bar',
                    data: {
                        labels: dayOfWeekStats.map(d => d.day),
                        datasets: [{
                            label: 'بازی‌ها',
                            data: dayOfWeekStats.map(d => d.games),
                            backgroundColor: '#6366f1',
                            borderRadius: 8
                        }, {
                            label: 'بردها',
                            data: dayOfWeekStats.map(d => d.wins),
                            backgroundColor: '#10b981',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        family: 'Vazir',
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    usePointStyle: true,
                                    padding: 15
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }

        // نمودار حالت بازی
        if (modeStats.length > 0) {
            const modeCtx = document.getElementById('modeChart');
            if (modeCtx) {
                new Chart(modeCtx, {
                    type: 'bar',
                    data: {
                        labels: modeStats.map(m => m.label),
                        datasets: [{
                            label: 'کل بازی‌ها',
                            data: modeStats.map(m => m.total_games),
                            backgroundColor: '#6366f1',
                            borderRadius: 8
                        }, {
                            label: 'بردها',
                            data: modeStats.map(m => m.total_wins),
                            backgroundColor: '#10b981',
                            borderRadius: 8
                        }, {
                            label: 'امتیاز',
                            data: modeStats.map(m => m.total_points),
                            backgroundColor: '#f59e0b',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        family: 'Vazir',
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    usePointStyle: true,
                                    padding: 15
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }

        // نمودار کارت‌های پرکاربرد
        // نمودار کارت‌های پرکاربرد (با برچسب‌های پایین و نمایش تعداد)
        if (cardStats && cardStats.length > 0) {
            const cardCtx = document.getElementById('cardUsageChart');
            if (cardCtx) {
                const colorPalette = [
                    '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16',
                    '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9',
                    '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#d946ef',
                    '#ec4899', '#f43f5e',
                ];
                const sortedStats = [...cardStats].sort((a, b) => b.usage_count - a.usage_count);
                const labels = sortedStats.map((card) => {
                    const emoji = card.emoji || card.name.charAt(0);
                    return `${emoji} ${card.name}`;
                });
                const colors = sortedStats.map((_, i) => colorPalette[i % colorPalette.length]);

                new Chart(cardCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'تعداد برد',
                            data: sortedStats.map(c => c.usage_count),
                            backgroundColor: colors.map(c => c + 'cc'),
                            borderColor: colors,
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: colors,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        // 🔄 تغییر به حالت عمودی (برچسب‌ها در پایین)
                        indexAxis: 'x',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    title: function(context) {
                                        const card = sortedStats[context[0].dataIndex];
                                        const emoji = card.emoji || card.name.charAt(0);
                                        return `${emoji} ${card.name} : ${card.usage_count} بار برنده شده`;
                                    },
                                    label: function(context) {
                                        return `🏆 ${context.parsed.y} برد`;
                                    },
                                    // afterLabel: function(context) {
                                    //     const card = sortedStats[context.dataIndex];
                                    //     const rarityLabels = {
                                    //         'common': 'معمولی',
                                    //         'rare': 'کمیاب',
                                    //         'legendary': 'افسانه‌ای'
                                    //     };
                                    //     return `✨ ${rarityLabels[card.rarity] || card.rarity}`;
                                    // }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    color: '#374151'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.06)'
                                },
                                title: {
                                    display: true,
                                    text: 'تعداد برد',
                                    font: {
                                        size: 13,
                                        weight: 'bold'
                                    },
                                    color: '#6b7280'
                                }
                            },
                            x: {
                                // 🆕 نمایش همه برچسب‌ها در موبایل با چرخش
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 0,
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    },
                                    color: '#374151',
                                    padding: 6,
                                    // 🆕 نمایش تعداد برد در کنار نام
                                    callback: function(value, index) {
                                        const card = sortedStats[index];
                                        if (card) {
                                            const emoji = card.emoji || '';
                                            return `${emoji} ${card.name} (${card.usage_count})`;
                                        }
                                        return this.getLabelForValue(value);
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        animation: {
                            duration: 800,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }
        }
    });
</script>