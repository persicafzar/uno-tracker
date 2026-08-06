<?php

/**
 * 📋 صفحه لیست بازیکنان
 */

use Core\JalaliDate;

$currentPath = '/users';
?>
<div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">

    <div class="space-y-6" x-data="usersListManager()">
        
        <!-- ======= Header ======= -->
        <div class="relative bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-5 sm:p-7 text-white shadow-md overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black drop-shadow-lg tracking-tight flex items-center gap-2.5">
                        <span class="text-3xl sm:text-4xl">👥</span>
                        لیست بازیکنان
                    </h2>
                    <p class="text-white/80 text-sm font-medium mt-1 drop-shadow">
                        مجموع: <strong class="text-white"><?= number_format($total) ?></strong> بازیکن
                    </p>
                </div>
            </div>
        </div>

        <!-- ======= Filters ======= -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-md p-4 sm:p-5">
            <form method="GET" action="/users" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- جستجو -->
                <div class="md:col-span-2">
                    <label class="text-xs text-gray-600 mb-1 block font-bold">جستجو</label>
                    <input type="text"
                        name="search"
                        value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                        placeholder="جستجو بر اساس نام، نام مستعار یا شماره..."
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                </div>

                <!-- فیلتر سطح -->
                <div>
                    <label class="text-xs text-gray-600 mb-1 block font-bold">سطح</label>
                    <select name="level" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                        <option value="">همه سطوح</option>
                        <?php foreach ($allLevels as $level): ?>
                            <option value="<?= $level['level'] ?>"
                                <?= ($filters['level'] ?? '') == $level['level'] ? 'selected' : '' ?>>
                                سطح <?= $level['level'] ?> - <?= htmlspecialchars($level['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- مرتب‌سازی -->
                <div>
                    <label class="text-xs text-gray-600 mb-1 block font-bold">مرتب‌سازی</label>
                    <select name="sort" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                        <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>جدیدترین</option>
                        <option value="oldest" <?= $sortBy === 'oldest' ? 'selected' : '' ?>>قدیمی‌ترین</option>
                        <option value="xp_desc" <?= $sortBy === 'xp_desc' ? 'selected' : '' ?>>بیشترین XP</option>
                        <option value="xp_asc" <?= $sortBy === 'xp_asc' ? 'selected' : '' ?>>کمترین XP</option>
                        <option value="points_desc" <?= $sortBy === 'points_desc' ? 'selected' : '' ?>>🔥 بیشترین امتیاز</option>
                        <option value="points_asc" <?= $sortBy === 'points_asc' ? 'selected' : '' ?>>کمترین امتیاز</option>
                        <option value="level_desc" <?= $sortBy === 'level_desc' ? 'selected' : '' ?>>بالاترین سطح</option>
                        <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : '' ?>>نام (الف-ی)</option>
                        <option value="name_desc" <?= $sortBy === 'name_desc' ? 'selected' : '' ?>>نام (ی-الف)</option>
                    </select>
                </div>

                <!-- دکمه‌ها -->
                <div class="md:col-span-4 flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 md:flex-none px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                        🔍 اعمال فیلتر
                    </button>
                    <a href="/users"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm hover:shadow-md">
                        پاک کردن
                    </a>
                </div>
            </form>
        </div>

        <!-- ======= Users List ======= -->
        <?php if (empty($users)): ?>
            <div class="bg-white rounded-2xl p-12 border border-gray-200 shadow-md text-center">
                <div class="text-7xl mb-4 opacity-50">👥</div>
                <h3 class="text-2xl font-black text-gray-800 mb-2">هیچ بازیکنی یافت نشد</h3>
                <p class="text-gray-500 font-medium">با فیلترهای انتخاب شده بازیکنی پیدا نشد</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px]">
                        <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">بازیکن</th>
                                <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">لقب</th>
                                <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">سطح</th>
                                <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">XP</th>
                                <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">امتیاز</th>
                                <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">آمار بازی</th>
                                <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">تاریخ عضویت</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-indigo-50/50 transition-all duration-200 group cursor-pointer" onclick="window.location='/users/<?= $user['id'] ?>'">
                                    
                                    <!-- بازیکن -->
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <?php if (!empty($user['avatar_path'])): ?>
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 border-white shadow-sm overflow-hidden flex-shrink-0 group-hover:border-indigo-400 transition-all duration-300">
                                                    <img src="/storage/uploads/avatars/<?= htmlspecialchars($user['avatar_path']) ?>"
                                                        alt="<?= htmlspecialchars($user['nickname']) ?>"
                                                        class="w-full h-full aspect-square rounded-full object-cover">
                                                </div>
                                            <?php else: ?>
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 border-2 border-white shadow-sm flex items-center justify-center text-white font-black flex-shrink-0 group-hover:border-indigo-400 transition-all duration-300">
                                                    <?= mb_substr($user['nickname'] ?? '?', 0, 1) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="min-w-0">
                                                <div class="font-bold text-gray-800 text-sm sm:text-base truncate whitespace-nowrap">
                                                    <?= htmlspecialchars($user['nickname'] ?? 'ناشناس') ?>
                                                    <?php if ($user['id'] === $currentUserId): ?>
                                                        <span class="text-xs text-indigo-600 font-black bg-indigo-100 px-2 py-0.5 rounded-full border border-indigo-200 whitespace-nowrap inline-block">(شما)</span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($user['real_name'])): ?>
                                                    <div class="text-xs text-gray-500 font-medium truncate whitespace-nowrap">
                                                        <?= htmlspecialchars($user['real_name']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- لقب -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <?php if (!empty($user['current_title'])): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold border border-amber-200 shadow-sm whitespace-nowrap">
                                                <span class="text-sm flex-shrink-0"><?= htmlspecialchars($user['title_icon'] ?? '🏆') ?></span>
                                                <span class="truncate max-w-[60px] sm:max-w-[100px]"><?= htmlspecialchars($user['current_title']) ?></span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 font-medium">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- سطح -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-black shadow-sm flex-shrink-0"
                                                style="background-color: <?= htmlspecialchars($user['level_color'] ?? '#6366f1') ?>">
                                                <?= $user['current_level'] ?>
                                            </span>
                                            <span class="text-xs text-gray-600 font-medium truncate max-w-[60px] sm:max-w-[80px]">
                                                <?= htmlspecialchars($user['level_title'] ?? 'تازه‌کار') ?>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- XP -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="text-sm font-black text-indigo-600 whitespace-nowrap">
                                            ⭐ <?= number_format($user['total_xp'] ?? 0) ?>
                                        </span>
                                    </td>

                                    <!-- امتیاز -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-amber-100 to-yellow-100 border-2 border-amber-300 rounded-xl shadow-sm whitespace-nowrap">
                                            <span class="text-base flex-shrink-0">💎</span>
                                            <span class="text-sm font-black text-amber-700">
                                                <?= number_format($user['total_points'] ?? 0) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- آمار بازی -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="text-xs text-gray-600 space-y-0.5 font-medium">
                                            <div class="whitespace-nowrap">🎮 <?= $user['total_games'] ?? 0 ?> بازی</div>
                                            <div class="whitespace-nowrap">🏆 <?= $user['total_wins'] ?? 0 ?> برد</div>
                                            <div class="whitespace-nowrap">📊 <?= number_format($user['win_rate'] ?? 0, 1) ?>%</div>
                                        </div>
                                    </td>

                                    <!-- تاریخ عضویت -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="text-xs text-gray-500 font-medium whitespace-nowrap">
                                            <?= JalaliDate::format('Y/m/d', strtotime($user['created_at'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ======= Pagination ======= -->
            <?php if ($totalPages > 1): ?>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white rounded-2xl p-4 border border-gray-200 shadow-md">
                    <div class="text-sm text-gray-600 font-medium">
                        صفحه <?= $page ?> از <?= $totalPages ?>
                        <span class="text-gray-400 font-bold mr-1">(<?= number_format($total) ?> بازیکن)</span>
                    </div>
                    <div class="flex gap-1 flex-wrap justify-center">
                        <?php
                        $queryParams = array_filter([
                            'search' => $filters['search'] ?? '',
                            'level' => $filters['level'] ?? '',
                            'sort' => $sortBy,
                        ]);
                        ?>

                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&<?= http_build_query($queryParams) ?>"
                                class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">
                                قبلی
                            </a>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?page=<?= $i ?>&<?= http_build_query($queryParams) ?>"
                                class="px-3.5 py-1.5 <?= $i === $page ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?> rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>&<?= http_build_query($queryParams) ?>"
                                class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">
                                بعدی
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div> <!-- بسته شدن x-data div -->

</div>

<script>
    function usersListManager() {
        return {
            // می‌توانید قابلیت‌های اضافی Alpine.js را اینجا اضافه کنید
        };
    }
</script>