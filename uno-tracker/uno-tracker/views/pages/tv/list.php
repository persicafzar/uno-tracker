<?php

use Core\JalaliDate;

$statusLabels = [
    'pending' => ['label' => 'در انتظار', 'color' => 'pending'],
    'active' => ['label' => 'در حال بازی', 'color' => 'active'],
    'paused' => ['label' => 'متوقف', 'color' => 'paused'],
    'finished' => ['label' => 'پایان یافته', 'color' => 'finished'],
    'cancelled' => ['label' => 'لغو شده', 'color' => 'cancelled'],
];

$currentStatus = $filters['status'] ?? '';
?>

<div class="space-y-5">

    <!-- ======= هدر ======= -->
    <div class="relative overflow-hidden rounded-2xl p-6 bg-gradient-to-r from-indigo-900/60 via-purple-900/60 to-pink-900/60 border border-white/10 shadow-xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-2xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight drop-shadow flex items-center gap-3">
                    <span class="text-4xl md:text-5xl">📺</span>
                    لیست بازی‌ها
                </h1>
                <p class="text-gray-300 text-lg mt-1 drop-shadow">همه بازی‌های موجود در سیستم</p>
            </div>
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md rounded-xl px-5 py-2 border border-white/20">
                <span class="text-2xl text-white/70">📊</span>
                <span class="text-xl text-white font-bold"><?= number_format($total) ?></span>
                <span class="text-lg text-gray-300">بازی</span>
            </div>
        </div>
    </div>

    <!-- ======= فیلترها ======= -->
    <div class="rounded-2xl p-4 bg-white/5 backdrop-blur-md border border-white/10 shadow-lg">
        <form method="GET" action="/tv" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-300 text-sm font-bold mb-2 flex items-center gap-2">
                    <span class="text-xl">🔍</span> جستجو
                </label>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                    placeholder="نام یا ID بازی"
                    class="w-full px-4 py-2.5 bg-white/10 border-2 border-white/20 rounded-xl text-white text-lg placeholder-gray-400 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/30 transition-all">
            </div>
            <div>
                <label class="block text-gray-300 text-sm font-bold mb-2 flex items-center gap-2">
                    <span class="text-xl">📊</span> وضعیت
                </label>
                <select name="status" class="w-full px-4 py-2.5 bg-white/10 border-2 border-white/20 rounded-xl text-white text-lg appearance-none focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/30 transition-all cursor-pointer">
                    <option value="" class="bg-gray-900">همه</option>
                    <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?> class="bg-gray-900">⏳ در انتظار</option>
                    <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?> class="bg-gray-900">🔴 در حال بازی</option>
                    <option value="paused" <?= $currentStatus === 'paused' ? 'selected' : '' ?> class="bg-gray-900">⏸️ متوقف</option>
                    <option value="finished" <?= $currentStatus === 'finished' ? 'selected' : '' ?> class="bg-gray-900">✅ پایان یافته</option>
                    <option value="cancelled" <?= $currentStatus === 'cancelled' ? 'selected' : '' ?> class="bg-gray-900">❌ لغو شده</option>
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="flex-1 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-lg font-bold rounded-xl transition-all shadow-lg hover:shadow-2xl hover:scale-[1.02] flex items-center justify-center gap-2">
                    <span class="text-xl">🔍</span> اعمال فیلتر
                </button>
                <a href="/tv" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-lg font-bold rounded-xl transition-all border border-white/10 flex items-center justify-center gap-2">
                    <span class="text-xl">✕</span> پاک کردن
                </a>
            </div>
        </form>
    </div>

    <!-- ======= تعداد نتایج ======= -->
    <div class="flex items-center gap-3 text-gray-300 text-lg bg-white/5 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/10 w-fit">
        <span class="text-2xl font-black text-white"><?= number_format($total) ?></span>
        <span>بازی یافت شد</span>
    </div>

    <!-- ======= لیست بازی‌ها ======= -->
    <div class="rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 shadow-lg overflow-hidden">
        <div class="overflow-x-auto p-3">
            <table class="w-full tv-table">
                <thead>
                    <tr>
                        <th class="text-right text-sm font-bold text-gray-400">شناسه</th>
                        <th class="text-right text-sm font-bold text-gray-400">نام بازی</th>
                        <th class="text-center text-sm font-bold text-gray-400">حالت</th>
                        <th class="text-center text-sm font-bold text-gray-400">داور</th>
                        <th class="text-center text-sm font-bold text-gray-400">بازیکنان</th>
                        <th class="text-center text-sm font-bold text-gray-400">دورها</th>
                        <th class="text-center text-sm font-bold text-gray-400">وضعیت</th>
                        <th class="text-center text-sm font-bold text-gray-400">تاریخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($games)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400 text-lg">
                                <div class="text-6xl mb-4 opacity-50">🔍</div>
                                <p class="font-bold">بازی یافت نشد</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($games as $game): ?>
                            <?php $status = $statusLabels[$game['status']] ?? $statusLabels['pending']; ?>
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-all group">
                                <td class="text-lg font-black text-white">#<?= $game['id'] ?></td>
                                <td>
                                    <a href="/tv/<?= $game['id'] ?>" class="text-white hover:text-indigo-400 text-xl font-bold transition-all group-hover:scale-[1.02] inline-block">
                                        <?= htmlspecialchars($game['name'] ?: 'بدون نام') ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="inline-block px-3 py-1 rounded-xl text-sm font-bold <?= $game['game_mode'] === 'solo' ? 'bg-blue-600/40 text-blue-300 border border-blue-500/30' : 'bg-purple-600/40 text-purple-300 border border-purple-500/30' ?>">
                                        <?= $game['game_mode'] === 'solo' ? '👤 انفرادی' : '👥 تیمی' ?>
                                    </span>
                                </td>
                                <td class="text-center text-gray-300 text-lg font-medium"><?= htmlspecialchars($game['referee_name'] ?? '-') ?></td>
                                <td class="text-center text-gray-300 text-lg font-medium"><?= (int)($game['total_players'] ?? 0) ?> نفر</td>
                                <td class="text-center text-gray-300 text-lg font-medium"><?= (int)($game['total_rounds_played'] ?? 0) ?></td>
                                <td class="text-center">
                                    <span class="status-badge status-<?= $status['color'] ?> text-sm px-3 py-1"><?= $status['label'] ?></span>
                                </td>
                                <td class="text-center text-gray-400 text-base font-medium"><?= !empty($game['created_at']) ? JalaliDate::format('Y/m/d H:i', strtotime($game['created_at'])) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ======= صفحه‌بندی ======= -->
    <?php if ($totalPages > 1): ?>
        <div class="rounded-2xl p-4 bg-white/5 backdrop-blur-md border border-white/10 shadow-lg">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-gray-300 text-lg">صفحه <span class="text-white font-bold text-xl"><?= $page ?></span> از <span class="text-white font-bold text-xl"><?= $totalPages ?></span></div>
                <div class="flex gap-2 flex-wrap">
                    <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-lg font-bold rounded-xl transition-all border border-white/10 hover:scale-[1.05]">← قبلی</a>
                    <?php endif; ?>
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"
                            class="px-4 py-2 text-lg font-bold rounded-xl transition-all <?= $i === $page ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg hover:scale-[1.05]' : 'bg-white/10 hover:bg-white/20 text-white border border-white/10 hover:scale-[1.05]' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1])) ?>" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-lg font-bold rounded-xl transition-all border border-white/10 hover:scale-[1.05]">بعدی →</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>