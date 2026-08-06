<?php

use Core\JalaliDate;
?>

<div class="space-y-6">

    <!-- Welcome Banner -->
    <div class="relative bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-5 sm:p-7 text-white shadow-md ">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

        <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black drop-shadow-lg tracking-tight mb-1">خوش آمدید، <?= htmlspecialchars($admin['nickname']) ?> 👋</h2>
                <p class="text-white/80 text-sm font-medium drop-shadow"><?= JalaliDate::format('l، j F Y') ?></p>
            </div>
            <div class="flex flex-wrap gap-3 justify-center mt-4 sm:mt-0">
                <a href="/admin/users" class="px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-xl transition-all duration-300 text-sm font-bold hover:scale-[1.02] border border-white/20">
                    مدیریت کاربران
                </a>
                <a href="/admin/games" class="px-4 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-xl transition-all duration-300 text-sm font-bold hover:scale-[1.02] border border-white/20">
                    مدیریت بازی‌ها
                </a>
                <!-- Dropdown Export -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="px-4 py-2.5 bg-green-500/80 hover:bg-green-600 rounded-xl transition-all duration-300 text-sm font-bold flex items-center gap-1.5 border border-green-400/30">
                        <span>📥 Export</span>
                        <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border-2 border-gray-200 py-1 z-50">
                        <a href="/admin/users/export" class="flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition rounded-xl">👥 کاربران</a>
                        <a href="/admin/games/export" class="flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition rounded-xl">🎮 بازی‌ها</a>
                        <a href="/admin/logs/export" class="flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition rounded-xl">📋 لاگ‌ها</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="!grid !grid-cols-2 !gap-2 lg:!grid-cols-4 lg:!gap-4">
        <!-- Total Users -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl p-3 sm:p-5 border-2 border-blue-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-600/20 rounded-xl flex items-center justify-center text-3xl">👥</div>
                <span class="text-xs font-bold text-blue-700 bg-white/50 px-2.5 py-0.5 rounded-full border border-blue-200">کل</span>
            </div>
            <div class="relative z-10 text-3xl font-black text-gray-800"><?= number_format($stats['total_users']) ?></div>
            <div class="relative z-10 text-sm font-medium text-gray-600 mt-1">کل کاربران</div>
            <div class="relative z-10 flex items-center gap-2 mt-3 text-xs font-bold">
                <span class="text-green-600">✅ <?= number_format($stats['active_users']) ?> فعال</span>
                <span class="text-red-600">🚫 <?= number_format($stats['banned_users']) ?> مسدود</span>
            </div>
        </div>

        <!-- Total Games -->
        <div class="relative overflow-hidden bg-gradient-to-br from-green-100 to-emerald-200 rounded-2xl p-3 sm:p-5 border-2 border-green-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-600/20 rounded-xl flex items-center justify-center text-3xl">🎮</div>
                <span class="text-xs font-bold text-green-700 bg-white/50 px-2.5 py-0.5 rounded-full border border-green-200">کل</span>
            </div>
            <div class="relative z-10 text-3xl font-black text-gray-800"><?= number_format($stats['total_games']) ?></div>
            <div class="relative z-10 text-sm font-medium text-gray-600 mt-1">کل بازی‌ها</div>
            <div class="relative z-10 flex items-center gap-2 mt-3 text-xs font-bold">
                <span class="text-blue-600">🔵 <?= number_format($stats['active_games']) ?> فعال</span>
                <span class="text-gray-600">✅ <?= number_format($stats['finished_games']) ?> پایان یافته</span>
            </div>
        </div>

        <!-- Today's Games -->
        <div class="relative overflow-hidden bg-gradient-to-br from-purple-100 to-violet-200 rounded-2xl p-3 sm:p-5 border-2 border-purple-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-600/20 rounded-xl flex items-center justify-center text-3xl">📅</div>
                <span class="text-xs font-bold text-purple-700 bg-white/50 px-2.5 py-0.5 rounded-full border border-purple-200">امروز</span>
            </div>
            <div class="relative z-10 text-3xl font-black text-gray-800"><?= number_format($stats['today_games']) ?></div>
            <div class="relative z-10 text-sm font-medium text-gray-600 mt-1">بازی‌های امروز</div>
            <div class="relative z-10 text-xs font-bold text-purple-600 mt-3">🎯 میانگین روزانه</div>
        </div>

        <!-- Achievements -->
        <div class="relative overflow-hidden bg-gradient-to-br from-yellow-100 to-amber-200 rounded-2xl p-3 sm:p-5 border-2 border-yellow-300 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-yellow-600/20 rounded-xl flex items-center justify-center text-3xl">🏆</div>
                <span class="text-xs font-bold text-yellow-700 bg-white/50 px-2.5 py-0.5 rounded-full border border-yellow-200">گیمیفیکیشن</span>
            </div>
            <div class="relative z-10 text-3xl font-black text-gray-800"><?= number_format($stats['total_achievements_unlocked']) ?></div>
            <div class="relative z-10 text-sm font-medium text-gray-600 mt-1">نشان‌های کسب شده</div>
            <div class="relative z-10 text-xs font-bold text-amber-600 mt-3">⭐ <?= number_format($stats['total_titles_unlocked'] ?? 0) ?> عنوان</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="!grid !grid-cols-1 lg:!grid-cols-2 gap-6">
        <!-- User Registration Chart -->
        <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
            <h3 class="text-base sm:text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
                <span class="text-2xl">📈</span>
                ثبت‌نام کاربران (۳۰ روز اخیر)
            </h3>
            <div class="h-64">
                <canvas id="userRegistrationChart"></canvas>
            </div>
        </div>

        <!-- Games Chart -->
        <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
            <h3 class="text-base sm:text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
                <span class="text-2xl">🎮</span>
                بازی‌های ایجاد شده (۳۰ روز اخیر)
            </h3>
            <div class="h-64">
                <canvas id="gamesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Game Mode Distribution -->
    <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
        <h3 class="text-base sm:text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
            <span class="text-2xl">🥧</span>
            توزیع حالت بازی‌ها
        </h3>
        <div class="!grid !grid-cols-1 md:!grid-cols-2 gap-6">
            <div class="h-64">
                <canvas id="gameModeChart"></canvas>
            </div>
            <div class="space-y-3">
                <?php foreach ($gameModeDistribution as $mode): ?>
                    <div class="flex items-center justify-between p-3.5 bg-gray-50/80 rounded-2xl border-2 border-gray-200 hover:shadow-md transition-all duration-300 hover:scale-[1.01]">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl"><?= $mode['mode'] === 'انفرادی' ? '👤' : '👥' ?></span>
                            <span class="font-black text-gray-800"><?= htmlspecialchars($mode['mode']) ?></span>
                        </div>
                        <div class="text-left">
                            <div class="text-xl font-black text-indigo-600"><?= number_format($mode['count']) ?></div>
                            <div class="text-xs text-gray-500 font-medium">بازی</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // User Registration Chart
        const userRegCtx = document.getElementById('userRegistrationChart');
        if (userRegCtx) {
            new Chart(userRegCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($userRegistrationStats['labels']) ?>,
                    datasets: [{
                        label: 'کاربران جدید',
                        data: <?= json_encode($userRegistrationStats['data']) ?>,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#6366f1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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

        // Games Chart
        const gamesCtx = document.getElementById('gamesChart');
        if (gamesCtx) {
            new Chart(gamesCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($gamesStats['labels']) ?>,
                    datasets: [{
                        label: 'بازی‌ها',
                        data: <?= json_encode($gamesStats['data']) ?>,
                        backgroundColor: '#10b981',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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

        // Game Mode Distribution Chart
        const gameModeCtx = document.getElementById('gameModeChart');
        if (gameModeCtx) {
            new Chart(gameModeCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($gameModeDistribution, 'mode')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_column($gameModeDistribution, 'count')) ?>,
                        backgroundColor: ['#6366f1', '#10b981'],
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
    });
</script>