<?php

use Core\JalaliDate;

$currentJalali = JalaliDate::fromTimestamp();
?>
<div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
    <!-- ================================================== -->
    <!-- ======= هدر داشبورد - نسخه‌ی بازطراحی‌شده ======= -->
    <!-- ================================================== -->
    <div class="relative bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-5 sm:p-7 text-white shadow-2xl mb-4 sm:mb-6 overflow-hidden">

        <!-- حلقه‌های تزئینی پس‌زمینه -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

        <!-- خطوط تزئینی -->
        <div class="absolute top-10 right-20 w-32 h-0.5 bg-white/10 rotate-12"></div>
        <div class="absolute bottom-10 left-16 w-24 h-0.5 bg-white/10 -rotate-12"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-5">
            <!-- بخش آواتار و اطلاعات کاربر -->
            <div class="flex items-center gap-4 sm:gap-5">
                <!-- آواتار با نمایش کامل -->
                <?php if (!empty($user['avatar_path'])): ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 border-white/90 shadow-2xl overflow-hidden flex-shrink-0 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($user['avatar_path']) ?>"
                            alt="<?= htmlspecialchars($user['nickname']) ?>"
                            class="w-full h-full aspect-square rounded-full object-cover ">
                    </div>
                <?php else: ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 border-white/90 shadow-2xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-3xl sm:text-4xl font-black flex-shrink-0 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <?= mb_substr($user['nickname'] ?? '?', 0, 1) ?>
                    </div>
                <?php endif; ?>

                <!-- اطلاعات کاربر -->
                <div class="text-center sm:text-right">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black drop-shadow-2xl tracking-tight">
                        سلام، <?= htmlspecialchars($user['nickname']) ?>
                        <span class="inline-block animate-wave">👋</span>
                        <?php if (!empty($user['tagline'])): ?>
                            <p class="break-words italic mt-2 sm:text-sm text-xs"><?= htmlspecialchars($user['tagline'] ?? '') ?></p>
                        <?php endif; ?>
                    </h1>
                    <p class="text-white/80 text-sm sm:text-base font-medium mt-0.5 drop-shadow">
                        <?= JalaliDate::format('l، j F Y') ?>
                    </p>
                </div>
            </div>
            <?php if ($canCreate): ?>
                <!-- دکمه بازی جدید -->
                <a href="/game/create"
                    class="group relative px-5 sm:px-7 py-3 sm:py-3.5 bg-white/95 backdrop-blur-sm text-indigo-700 rounded-2xl font-bold text-sm sm:text-base hover:bg-white transition-all duration-300 hover:shadow-2xl hover:scale-[1.05] shadow-lg flex items-center gap-2.5 overflow-hidden">

                    <!-- افکت لایه‌ی درخشان -->
                    <span class="absolute inset-0 bg-gradient-to-r from-indigo-100 to-violet-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>

                    <!-- محتوای دکمه -->
                    <span class="relative z-10 text-xl group-hover:rotate-12 transition-transform duration-300">🎮</span>
                    <span class="relative z-10 font-black">بازی جدید</span>

                    <!-- فلش کوچک -->
                    <span class="relative z-10 text-sm opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300">←</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- استایل انیمیشن موج برای دست تکان دادن -->
    <style>
        @keyframes wave {

            0%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(15deg);
            }

            75% {
                transform: rotate(-10deg);
            }
        }

        .animate-wave {
            display: inline-block;
            animation: wave 1.5s ease-in-out infinite;
            transform-origin: 70% 70%;
        }
    </style>

    <!-- Stats Cards -->
    <div class="grid !grid-cols-2 lg:!grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
        <?php include __DIR__ . '/partials/stats-cards.php'; ?>
    </div>
    <?php include __DIR__ . '/partials/top-players.php'; ?>


    <!-- 🆕 Recent Games & Comparison (منتقل شده به بالا) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6 items-stretch">
        <!-- Recent Games -->
        <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                    <span class="text-2xl ml-2">🎮</span>
                    بازیهای اخیر شما

                </h2>
                <a href="/games" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">
                    مشاهده همه ←
                </a>
            </div>
            <div class="flex-1">
                <?php include __DIR__ . '/partials/recent-games.php'; ?>
            </div>
        </div>

        <!-- Comparison with Friends -->
        <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm flex flex-col">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                    <span class="text-2xl ml-2">👥</span>
                    مقایسه با رقبا
                </h2>
                <div class="flex flex-wrap gap-2">
                    <!-- انتخاب حالت -->
                    <div class="flex gap-1 text-xs border-l border-gray-300 pl-2 ml-2">
                        <button onclick="loadFriendsComparison(currentPeriod, 'rivals')"
                            class="px-2 py-1 rounded bg-indigo-100 text-indigo-600 font-semibold transition"
                            id="btn-mode-rivals"
                            title="فقط کسانی که با شما بازی کرده‌اند">
                            🎯 رقبا
                        </button>
                        <button onclick="loadFriendsComparison(currentPeriod, 'all')"
                            class="px-2 py-1 rounded hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition"
                            id="btn-mode-all"
                            title="همه کاربران سایت">
                            🌐 همه
                        </button>
                    </div>
                    <!-- انتخاب بازه زمانی -->
                    <div class="flex gap-1 text-xs">
                        <button onclick="loadFriendsComparison('all', currentMode)"
                            class="px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-600 font-semibold transition"
                            id="btn-period-all">
                            همه
                        </button>
                        <button onclick="loadFriendsComparison('month', currentMode)"
                            class="px-2.5 py-1 rounded-lg hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition"
                            id="btn-period-month">
                            ماه
                        </button>
                        <button onclick="loadFriendsComparison('3months', currentMode)"
                            class="px-2.5 py-1 rounded-lg hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition"
                            id="btn-period-3months">
                            ۳ ماه
                        </button>
                        <button onclick="loadFriendsComparison('6months', currentMode)"
                            class="px-2.5 py-1 rounded-lg hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition"
                            id="btn-period-6months">
                            ۶ ماه
                        </button>
                    </div>
                </div>
            </div>
            <div id="friends-comparison-container" class="flex-1">
                <?php include __DIR__ . '/partials/friends-comparison.php'; ?>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
        <!-- Progress Chart -->
        <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="text-2xl ml-2">📈</span>
                پیشرفت ۳۰ روز اخیر
            </h2>
            <?php include __DIR__ . '/partials/progress-chart.php'; ?>
        </div>

        <!-- Win Distribution -->
        <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="text-2xl ml-2">🥧</span>
                توزیع بردها
            </h2>
            <?php include __DIR__ . '/partials/win-distribution.php'; ?>
        </div>
    </div>

    <!-- Weekly Stats -->
    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm mb-4 sm:mb-6">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="text-2xl ml-2">📊</span>
            آمار ۷ روز اخیر
        </h2>
        <?php include __DIR__ . '/partials/weekly-stats.php'; ?>
    </div>

    <!-- 🆕 Gamification Summary -->
    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm mb-4 sm:mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                <span class="text-2xl ml-2">🏆</span>
                پیشرفت و دستاوردها
            </h2>
            <a href="/achievements" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">
                مشاهده همه ←
            </a>
        </div>
        <?php include __DIR__ . '/partials/gamification-summary.php'; ?>
    </div>

    <!-- Monthly Summary Cards -->
    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                <span class="text-2xl ml-2">📅</span>
                خلاصه ماهانه
            </h2>
            <div class="flex gap-1 text-xs">
                <button onclick="loadMonthlySummary(3)"
                    class="px-2.5 py-1 rounded-lg hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition"
                    id="btn-months-3">
                    ۳ ماه
                </button>
                <button onclick="loadMonthlySummary(6)"
                    class="px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-600 font-semibold transition"
                    id="btn-months-6">
                    ۶ ماه
                </button>
                <button onclick="loadMonthlySummary(12)"
                    class="px-2.5 py-1 rounded-lg hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition"
                    id="btn-months-12">
                    ۱۲ ماه
                </button>
            </div>
        </div>
        <div id="monthly-summary-container">
            <?php include __DIR__ . '/partials/monthly-summary.php'; ?>
        </div>
    </div>
    <!-- 🃏 کارت‌ها و نوع‌های برد -->
    <div class="mt-4 sm:mt-6">
        <?php include __DIR__ . '/partials/cards-and-win-types.php'; ?>
    </div>
</div>

<script>
    // متغیرهای سراسری برای نگهداری حالت فعلی
    let currentPeriod = 'all';
    let currentMode = 'rivals';

    // لود خلاصه ماهانه
    function loadMonthlySummary(months) {
        ['3', '6', '12'].forEach(m => {
            const btn = document.getElementById('btn-months-' + m);
            if (btn) {
                if (parseInt(m) === months) {
                    btn.className = 'px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-600 font-semibold transition';
                } else {
                    btn.className = 'px-2.5 py-1 rounded-lg hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition';
                }
            }
        });

        fetch('/dashboard/monthly-summary?months=' + months)
            .then(response => response.text())
            .then(html => {
                document.getElementById('monthly-summary-container').innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
    }

    // لود مقایسه با دوستان
    function loadFriendsComparison(period, mode) {
        currentPeriod = period;
        currentMode = mode;

        // به‌روزرسانی دکمه‌های بازه زمانی
        ['all', 'month', '3months', '6months'].forEach(p => {
            const btn = document.getElementById('btn-period-' + p);
            if (btn) {
                if (p === period) {
                    btn.className = 'px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-600 font-semibold transition';
                } else {
                    btn.className = 'px-2.5 py-1 rounded-lg hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition';
                }
            }
        });

        // به‌روزرسانی دکمه‌های حالت
        ['rivals', 'all'].forEach(m => {
            const btn = document.getElementById('btn-mode-' + m);
            if (btn) {
                if (m === mode) {
                    btn.className = 'px-2 py-1 rounded bg-indigo-100 text-indigo-600 font-semibold transition';
                } else {
                    btn.className = 'px-2 py-1 rounded hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition';
                }
            }
        });

        fetch('/dashboard/friends-comparison?period=' + period + '&mode=' + mode)
            .then(response => response.text())
            .then(html => {
                document.getElementById('friends-comparison-container').innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
    }
</script>