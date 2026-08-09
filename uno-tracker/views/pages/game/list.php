<?php

use Core\JalaliDate;

$statusLabels = [
    'pending' => ['label' => 'در انتظار', 'color' => 'yellow'],
    'active' => ['label' => 'در حال بازی', 'color' => 'blue'],
    'paused' => ['label' => 'متوقف', 'color' => 'orange'],
    'finished' => ['label' => 'پایان یافته', 'color' => 'green'],
    'cancelled' => ['label' => 'لغو شده', 'color' => 'red'],
];

$roleLabels = [
    'referee_only' => ['label' => 'فقط داور', 'icon' => '👨‍⚖️', 'color' => 'indigo'],
    'player_any' => ['label' => 'بازی کرده', 'icon' => '🎮', 'color' => 'blue'],
    'both' => ['label' => 'داور و بازیکن', 'icon' => '⚖️', 'color' => 'purple'],
    'player_only' => ['label' => 'فقط بازیکن', 'icon' => '🎯', 'color' => 'green'],
    'none' => ['label' => '-', 'icon' => '', 'color' => 'gray'],
];

$resultLabels = [
    'win' => ['label' => 'برد', 'icon' => '🏆', 'color' => 'green'],
    'loss' => ['label' => 'باخت', 'icon' => '💔', 'color' => 'red'],
    'ongoing' => ['label' => 'در جریان', 'icon' => '⏳', 'color' => 'blue'],
    'none' => ['label' => '-', 'icon' => '', 'color' => 'gray'],
];

$selectedPlayerJson = $selectedPlayer ? htmlspecialchars(json_encode($selectedPlayer, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') : '';
$selectedPlayerIdValue = (int)($selectedPlayerId ?? 0);
?>

<div x-data="gamesPage()"
    data-initial-player="<?= $selectedPlayerJson ?>"
    data-initial-player-id="<?= $selectedPlayerIdValue ?>"
    class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">

    <!-- ======= Header ======= -->
    <div class="relative bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-5 sm:p-7 text-white shadow-2xl mb-4 sm:mb-6 overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-5">
            <div class="flex items-center gap-4 sm:gap-5">
                <?php if (!empty($currentUser['avatar_path'])): ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 border-white/90 shadow-2xl overflow-hidden flex-shrink-0 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($currentUser['avatar_path']) ?>"
                            alt="<?= htmlspecialchars($currentUser['nickname']) ?>"
                            class="w-full h-full aspect-square rounded-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 border-white/90 shadow-2xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-3xl sm:text-4xl font-black flex-shrink-0 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <?= mb_substr($currentUser['nickname'] ?? '?', 0, 1) ?>
                    </div>
                <?php endif; ?>
                <div class="text-center sm:text-right">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black drop-shadow-2xl tracking-tight">📋 لیست بازی‌ها</h1>
                    <p class="text-white/80 text-sm sm:text-base font-medium mt-0.5 drop-shadow">
                        <?= htmlspecialchars($currentUser['nickname'] ?? '') ?>
                    </p>
                </div>
            </div>
            <?php if ($canCreate): ?>
                <a href="/game/create"
                    class="group relative px-5 sm:px-7 py-3 sm:py-3.5 bg-white/95 backdrop-blur-sm text-indigo-700 rounded-2xl font-bold text-sm sm:text-base hover:bg-white transition-all duration-300 hover:shadow-2xl hover:scale-[1.05] shadow-lg flex items-center gap-2.5 overflow-hidden">
                    <span class="absolute inset-0 bg-gradient-to-r from-indigo-100 to-violet-100 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>
                    <span class="relative z-10 text-xl group-hover:rotate-12 transition-transform duration-300">➕</span>
                    <span class="relative z-10 font-black">بازی جدید</span>
                    <span class="relative z-10 text-sm opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300">←</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ======= Filters ======= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/70 shadow-xl mb-4">
        <form method="GET" action="/games" id="games-filter-form">
            <div class="grid !grid-cols-1 sm:!grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="text-xs text-gray-600 mb-1 block font-bold">🔍 جستجو</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                        placeholder="نام یا ID بازی"
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                </div>
                <div>
                    <label class="text-xs text-gray-600 mb-1 block font-bold">👤 بازیکن</label>
                    <button type="button"
                        @click="openPlayerModal()"
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm text-right bg-white hover:border-indigo-500 transition-all duration-200 flex items-center justify-between">
                        <span x-text="selectedPlayer ? selectedPlayer.nickname : 'همه بازیکنان'"
                            :class="selectedPlayer ? 'text-gray-800 font-semibold' : 'text-gray-500'"></span>
                        <span class="text-gray-400">▼</span>
                    </button>
                    <input type="hidden" name="player_id" x-model="selectedPlayerId">
                </div>
            </div>

            <div class="grid !grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="text-xs text-gray-600 mb-1 block font-bold">🎯 حالت</label>
                    <select name="mode" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-300">
                        <option value="">همه</option>
                        <option value="solo" <?= ($filters['mode'] ?? '') === 'solo' ? 'selected' : '' ?>>👤 انفرادی</option>
                        <option value="friendly" <?= ($filters['mode'] ?? '') === 'friendly' ? 'selected' : '' ?>>👥 تیمی</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600 mb-1 block font-bold">📊 وضعیت</label>
                    <select name="status" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-300">
                        <option value="">همه</option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>در انتظار</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>در حال بازی</option>
                        <option value="paused" <?= ($filters['status'] ?? '') === 'paused' ? 'selected' : '' ?>>متوقف</option>
                        <option value="finished" <?= ($filters['status'] ?? '') === 'finished' ? 'selected' : '' ?>>پایان یافته</option>
                        <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>لغو شده</option>
                    </select>
                </div>
            </div>

            <div class="grid !grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="text-xs text-gray-600 mb-1 block font-bold">🎭 نقش</label>
                    <select name="role"
                        x-bind:disabled="!selectedPlayer"
                        :class="!selectedPlayer ? 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200' : 'bg-white text-gray-800 border-gray-200 hover:border-indigo-300'"
                        class="w-full px-3 py-2.5 border-2 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer">
                        <option value="">انتخاب کنید</option>
                        <option value="referee_only" <?= ($filters['role'] ?? '') === 'referee_only' ? 'selected' : '' ?>>👨‍⚖️ فقط داور بوده</option>
                        <option value="player_any" <?= ($filters['role'] ?? '') === 'player_any' ? 'selected' : '' ?>>🎮 بازی کرده</option>
                        <option value="both" <?= ($filters['role'] ?? '') === 'both' ? 'selected' : '' ?>>⚖️ هم داور هم بازی کرده</option>
                        <option value="player_only" <?= ($filters['role'] ?? '') === 'player_only' ? 'selected' : '' ?>>🎯 فقط بازی کرده (بدون داور)</option>
                    </select>
                    <p x-show="!selectedPlayer" class="text-[10px] text-gray-400 mt-1 font-medium">ابتدا بازیکن را انتخاب کنید</p>
                </div>
                <div>
                    <label class="text-xs text-gray-600 mb-1 block font-bold">🏆 نتیجه</label>
                    <select name="result"
                        x-bind:disabled="!selectedPlayer"
                        :class="!selectedPlayer ? 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200' : 'bg-white text-gray-800 border-gray-200 hover:border-indigo-300'"
                        class="w-full px-3 py-2.5 border-2 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer">
                        <option value="">انتخاب کنید</option>
                        <option value="win" <?= ($filters['result'] ?? '') === 'win' ? 'selected' : '' ?>>🏆 بردها</option>
                        <option value="loss" <?= ($filters['result'] ?? '') === 'loss' ? 'selected' : '' ?>>💔 باخت‌ها</option>
                        <option value="ongoing" <?= ($filters['result'] ?? '') === 'ongoing' ? 'selected' : '' ?>>⏳ در جریان</option>
                    </select>
                    <p x-show="!selectedPlayer" class="text-[10px] text-gray-400 mt-1 font-medium">ابتدا بازیکن را انتخاب کنید</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-sm font-bold transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
                    🔍 فیلتر
                </button>
                <button type="button" @click="resetFilters()" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md">
                    پاک کردن
                </button>
            </div>
        </form>
    </div>

    <!-- ======= Result Count ======= -->
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/70 rounded-2xl px-4 py-3 mb-4 shadow-md">
        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-400/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative z-10 flex items-center justify-between flex-wrap gap-2">
            <div class="text-sm text-blue-800 font-medium">
                <span class="font-black text-lg"><?= number_format($total) ?></span> بازی یافت شد
                <?php if ($selectedPlayer): ?>
                    <span class="text-blue-600">• برای: <strong class="text-blue-900"><?= htmlspecialchars($selectedPlayer['nickname']) ?></strong></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ======= Legend ======= -->
    <div class="bg-gray-50/80 backdrop-blur-sm border border-gray-200/70 rounded-2xl p-3 mb-4 shadow-sm">
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="font-black text-gray-700 ml-2">راهنما:</span>
            <?php foreach ($roleLabels as $roleKey => $roleInfo): ?>
                <?php if ($roleKey !== 'none'): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-<?= $roleInfo['color'] ?>-100 text-<?= $roleInfo['color'] ?>-700 rounded-full font-bold border border-<?= $roleInfo['color'] ?>-200">
                        <span><?= $roleInfo['icon'] ?></span>
                        <span><?= $roleInfo['label'] ?></span>
                    </span>
                <?php endif; ?>
            <?php endforeach; ?>
            <span class="text-gray-300">|</span>
            <?php foreach ($resultLabels as $resultKey => $resultInfo): ?>
                <?php if ($resultKey !== 'none'): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-<?= $resultInfo['color'] ?>-100 text-<?= $resultInfo['color'] ?>-700 rounded-full font-bold border border-<?= $resultInfo['color'] ?>-200">
                        <span><?= $resultInfo['icon'] ?></span>
                        <span><?= $resultInfo['label'] ?></span>
                    </span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ======= Games Table ======= -->
    <div class="bg-white rounded-2xl border border-gray-200/70 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-600">ID</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-600">نام بازی</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-600">حالت</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-600">داور</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-600">برنده</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-600">بازیکنان</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-600">دور</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-600">وضعیت</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-600">تاریخ</th>
                        <?php if ($selectedPlayer): ?>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-600">نقش</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-600">نتیجه</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    <?php if (empty($games)): ?>
                        <tr>
                            <td colspan="<?= $selectedPlayer ? 11 : 9 ?>" class="px-4 py-16 text-center text-gray-500">
                                <div class="text-6xl mb-4 opacity-50">🔍</div>
                                <p class="font-medium text-base">بازی یافت نشد</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($games as $game): ?>
                            <?php
                            $status = $statusLabels[$game['status']] ?? $statusLabels['pending'];
                            $role = $roleLabels[$game['user_role']] ?? $roleLabels['none'];
                            $result = $resultLabels[$game['user_result']] ?? $resultLabels['none'];
                            ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5 text-sm text-gray-600 font-bold">#<?= $game['id'] ?></td>
                                <td class="px-4 py-3.5">
                                    <a href="/game/<?= $game['id'] ?>" class="font-bold text-gray-800 text-sm hover:text-indigo-600 transition group-hover:text-indigo-600">
                                        <?= htmlspecialchars($game['name'] ?: 'بدون نام') ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-block px-2.5 py-1 bg-<?= $game['game_mode'] === 'solo' ? 'blue' : 'purple' ?>-100 text-<?= $game['game_mode'] === 'solo' ? 'blue' : 'purple' ?>-700 rounded-full text-xs font-bold border border-<?= $game['game_mode'] === 'solo' ? 'blue' : 'purple' ?>-200">
                                        <?= $game['game_mode'] === 'solo' ? '👤 انفرادی' : '👥 تیمی' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm text-gray-700 font-medium">
                                    <?= htmlspecialchars($game['referee_name'] ?? '-') ?>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm">
                                    <?php if (!empty($game['winner_name'])): ?>
                                        <span class="text-amber-600 font-black">
                                            🏆 <?= htmlspecialchars($game['winner_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm text-gray-700 font-medium">
                                    <?= (int)($game['total_players'] ?? 0) ?> نفر
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm text-gray-700 font-medium">
                                    <?= (int)($game['total_rounds_played'] ?? 0) ?>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-block px-2.5 py-1 bg-<?= $status['color'] ?>-100 text-<?= $status['color'] ?>-700 rounded-full text-xs font-bold border border-<?= $status['color'] ?>-200">
                                        <?= $status['label'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center text-xs text-gray-500 font-medium">
                                    <?= !empty($game['created_at']) ? JalaliDate::format('Y/m/d H:i', strtotime($game['created_at'])) : '-' ?>
                                </td>
                                <?php if ($selectedPlayer): ?>
                                    <td class="px-4 py-3.5 text-center">
                                        <?php if ($game['user_role'] !== 'none'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-<?= $role['color'] ?>-100 text-<?= $role['color'] ?>-700 rounded-full text-xs font-bold border border-<?= $role['color'] ?>-200">
                                                <span><?= $role['icon'] ?></span>
                                                <span class="hidden sm:inline"><?= $role['label'] ?></span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <?php if ($game['user_result'] !== 'none'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-<?= $result['color'] ?>-100 text-<?= $result['color'] ?>-700 rounded-full text-xs font-bold border border-<?= $result['color'] ?>-200">
                                                <span><?= $result['icon'] ?></span>
                                                <span class="hidden sm:inline"><?= $result['label'] ?></span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- ======= Pagination ======= -->
    <?php if ($totalPages > 1): ?>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white rounded-2xl p-4 border border-gray-200/70 shadow-xl mt-4">
            <div class="text-sm text-gray-600 font-medium">
                صفحه <?= $page ?> از <?= $totalPages ?>
            </div>
            <div class="flex gap-1 flex-wrap justify-center">
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1, 'player_id' => $selectedPlayerId])) ?>"
                        class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md">
                        قبلی
                    </a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <a href="?<?= http_build_query(array_merge($filters, ['page' => $i, 'player_id' => $selectedPlayerId])) ?>"
                        class="px-3.5 py-1.5 <?= $i === $page ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?> rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1, 'player_id' => $selectedPlayerId])) ?>"
                        class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md">
                        بعدی
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ======= Modal انتخاب بازیکن ======= -->
    <div x-show="showPlayerModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="closePlayerModal()"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        style="display: none;">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[80vh] flex flex-col border border-gray-200/70" @click.stop>

            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h3 class="text-lg font-black text-gray-800">👤 انتخاب بازیکن</h3>
                <button @click="closePlayerModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition">✕</button>
            </div>

            <div class="p-4 border-b border-gray-200">
                <input type="text"
                    x-model="playerSearch"
                    @input.debounce.300ms="fetchPlayers()"
                    placeholder="🔍 جستجوی بازیکن..."
                    class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                    autofocus>
            </div>

            <div class="flex-1 overflow-y-auto p-2">
                <template x-if="playersLoading">
                    <div class="text-center py-8 text-gray-500">
                        <div class="animate-spin inline-block w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full"></div>
                        <p class="text-sm font-medium mt-2">در حال بارگذاری...</p>
                    </div>
                </template>

                <template x-if="!playersLoading && players.length === 0">
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-5xl mb-2 opacity-50">🔍</div>
                        <p class="text-sm font-medium">بازیکنی یافت نشد</p>
                    </div>
                </template>

                <template x-if="!playersLoading && players.length > 0">
                    <div class="space-y-1">
                        <template x-for="player in players" :key="player.id">
                            <button type="button"
                                @click="selectPlayer(player)"
                                class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-indigo-50 transition-all duration-200 text-right group border border-transparent hover:border-indigo-200">
                                <template x-if="player.avatar_path">
                                    <img :src="'/storage/uploads/avatars/' + player.avatar_path"
                                        class="!w-11 !h-11 aspect-square rounded-full object-cover border-2 border-gray-200 group-hover:border-indigo-300 transition-all duration-200 flex-shrink-0">
                                </template>
                                <template x-if="!player.avatar_path">
                                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-400 to-violet-400 flex items-center justify-center text-white font-black text-lg flex-shrink-0">
                                        <span x-text="(player.nickname || '?').charAt(0)"></span>
                                    </div>
                                </template>

                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-gray-800 text-sm truncate" x-text="player.nickname"></div>
                                    <div class="text-xs text-gray-500 truncate" x-text="player.real_name || player.phone"></div>
                                </div>

                                <span class="text-indigo-600 text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity">انتخاب ←</span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            <div class="p-4 border-t border-gray-200 flex gap-2">
                <button type="button"
                    @click="clearPlayer(); closePlayerModal()"
                    class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md">
                    🗑️ همه بازیکنان
                </button>
                <button type="button"
                    @click="closePlayerModal()"
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-lg">
                    ✓ بستن
                </button>
            </div>

        </div>
    </div>

</div>

<script>
    function gamesPage() {
        const container = document.querySelector('[x-data="gamesPage()"]');
        let initialPlayer = null;
        let initialPlayerId = null;

        if (container) {
            const playerJson = container.dataset.initialPlayer;
            if (playerJson && playerJson.trim() !== '') {
                try {
                    initialPlayer = JSON.parse(playerJson);
                } catch (e) {
                    console.error('Error parsing player JSON:', e);
                }
            }

            const playerId = container.dataset.initialPlayerId;
            if (playerId && playerId !== '0') {
                initialPlayerId = parseInt(playerId);
            }
        }

        return {
            selectedPlayer: initialPlayer,
            selectedPlayerId: initialPlayerId,
            showPlayerModal: false,
            playerSearch: '',
            players: [],
            playersLoading: false,

            openPlayerModal() {
                this.showPlayerModal = true;
                this.playerSearch = '';
                this.fetchPlayers();
            },

            closePlayerModal() {
                this.showPlayerModal = false;
            },

            async fetchPlayers() {
                this.playersLoading = true;
                try {
                    const url = '/games/players?search=' + encodeURIComponent(this.playerSearch);
                    const response = await fetch(url);
                    const data = await response.json();
                    this.players = data.players || [];
                } catch (error) {
                    console.error('Error fetching players:', error);
                    this.players = [];
                } finally {
                    this.playersLoading = false;
                }
            },

            selectPlayer(player) {
                this.selectedPlayer = player;
                this.selectedPlayerId = player.id;
                this.closePlayerModal();
            },

            clearPlayer() {
                this.selectedPlayer = null;
                this.selectedPlayerId = null;

                const roleSelect = document.querySelector('select[name="role"]');
                const resultSelect = document.querySelector('select[name="result"]');
                if (roleSelect) roleSelect.value = '';
                if (resultSelect) resultSelect.value = '';
            },

            resetFilters() {
                window.location.href = '/games';
            }
        };
    }
</script>