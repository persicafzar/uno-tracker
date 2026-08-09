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
?>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
                <span class="text-3xl">👥</span>
                مدیریت کاربران
            </h2>
            <p class="text-gray-600 text-sm font-medium mt-0.5">مجموع: <strong class="text-indigo-600"><?= number_format($total) ?></strong> کاربر</p>
        </div>

        <!-- 🆕 دکمه باز محاسبه آمار همه -->
        <?php if ($admin['role'] === 'super_admin'): ?>
            <button type="button" onclick="confirmRecalculateAll()"
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                <span>🔄</span>
                <span>باز محاسبه آمار همه کاربران</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-gray-200/70 shadow-md">
        <form method="GET" action="/admin/users" class="!grid !grid-cols-1 sm:!grid-cols-2 lg:!grid-cols-4 gap-3">
            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">جستجو</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                    placeholder="نام، نام مستعار یا شماره تماس"
                    class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
            </div>

            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">وضعیت</label>
                <select name="status" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>فعال</option>
                    <option value="banned" <?= ($filters['status'] ?? '') === 'banned' ? 'selected' : '' ?>>مسدود</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>در انتظار</option>
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">نقش</label>
                <select name="role" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>کاربر</option>
                    <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>مدیر</option>
                    <option value="super_admin" <?= ($filters['role'] ?? '') === 'super_admin' ? 'selected' : '' ?>>مدیر ارشد</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                    🔍 فیلتر
                </button>
                <a href="/admin/users" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm hover:shadow-md">
                    پاک کردن
                </a>
                <a href="/admin/users/export?<?= http_build_query($filters) ?>"
                    class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">کاربر</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">شماره تماس</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">نقش</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">وضعیت</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">آنلاین</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">بازی‌ها</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">تاریخ عضویت</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center text-gray-500">
                                <div class="text-6xl mb-4 opacity-50">🔍</div>
                                <p class="font-bold text-base">کاربری یافت نشد</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <?php
                            $status = $statusLabels[$user['status']] ?? $statusLabels['active'];
                            $role = $roleLabels[$user['role']] ?? $roleLabels['user'];
                            $isSelf = $user['id'] === $admin['id'];
                            ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
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
                                            <a href="/admin/users/<?= $user['id'] ?>" class="font-bold text-gray-800 text-sm sm:text-base hover:text-indigo-600 transition truncate block whitespace-nowrap">
                                                <?= htmlspecialchars($user['nickname'] ?? '-') ?>
                                                <?php if ($isSelf): ?>
                                                    <span class="text-xs text-indigo-600 font-black bg-indigo-100 px-2 py-0.5 rounded-full border border-indigo-200 whitespace-nowrap inline-block">(شما)</span>
                                                <?php endif; ?>
                                            </a>
                                            <div class="text-xs text-gray-500 font-medium truncate whitespace-nowrap"><?= htmlspecialchars($user['real_name'] ?? '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-sm text-gray-600 font-mono whitespace-nowrap" dir="ltr">
                                    <?= htmlspecialchars($user['phone'] ?? '-') ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 bg-<?= $role['color'] ?>-100 text-<?= $role['color'] ?>-700 rounded-full text-xs font-bold border border-<?= $role['color'] ?>-200 shadow-sm whitespace-nowrap">
                                        <?= $role['label'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 bg-<?= $status['color'] ?>-100 text-<?= $status['color'] ?>-700 rounded-full text-xs font-bold border border-<?= $status['color'] ?>-200 shadow-sm whitespace-nowrap">
                                        <?= $status['label'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if (!empty($user['is_online'])): ?>
                                        <span class="inline-flex items-center gap-1 text-xs text-green-600 font-bold whitespace-nowrap">
                                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></span>
                                            آنلاین
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 font-medium whitespace-nowrap">آفلاین</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-700"><?= (int)($user['total_games'] ?? 0) ?> بازی</div>
                                    <div class="text-xs text-green-600 font-bold"><?= (int)($user['total_wins'] ?? 0) ?> برد</div>
                                </td>
                                <td class="px-4 py-3.5 text-center text-xs text-gray-500 font-medium whitespace-nowrap">
                                    <?= !empty($user['created_at']) ? JalaliDate::format('Y/m/d', strtotime($user['created_at'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <?php if (!$isSelf && $user['role'] !== 'super_admin'): ?>
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- تغییر نقش -->
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open"
                                                    class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                                    title="تغییر نقش">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false"
                                                    class="absolute left-0 mt-1 w-36 bg-white rounded-2xl shadow-xl border-2 border-gray-200 py-1 z-10">
                                                    <form method="POST" action="/admin/users/<?= $user['id'] ?>/role" class="flex flex-col">
                                                        <button type="submit" name="role" value="user"
                                                            class="w-full text-right px-3 py-1.5 text-xs font-bold hover:bg-indigo-50 <?= ($user['role'] ?? '') === 'user' ? 'text-indigo-600' : 'text-gray-700' ?>">
                                                            کاربر
                                                        </button>
                                                        <button type="submit" name="role" value="admin"
                                                            class="w-full text-right px-3 py-1.5 text-xs font-bold hover:bg-indigo-50 <?= ($user['role'] ?? '') === 'admin' ? 'text-indigo-600' : 'text-gray-700' ?>">
                                                            مدیر
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- مجوزهای بازی -->
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open"
                                                    class="p-1.5 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition"
                                                    title="مجوزهای بازی">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false"
                                                    class="absolute left-0 mt-1 w-48 bg-white rounded-2xl shadow-xl border-2 border-gray-200 py-1 z-10">
                                                    <div class="px-3 py-1.5 text-xs font-black text-gray-500 border-b-2 border-gray-200">مجوزهای بازی</div>
                                                    <!-- ساخت بازی -->
                                                    <?php if (empty($user['can_create_game'])): ?>
                                                        <form method="POST" action="/admin/users/<?= $user['id'] ?>/allow-create-game">
                                                            <button type="submit" class="w-full text-right px-3 py-1.5 text-xs font-bold hover:bg-gray-50 text-green-600 flex items-center gap-2">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                                </svg>
                                                                اجازه ساخت بازی
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" action="/admin/users/<?= $user['id'] ?>/ban-create-game">
                                                            <button type="submit" class="w-full text-right px-3 py-1.5 text-xs font-bold hover:bg-gray-50 text-orange-600 flex items-center gap-2">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" />
                                                                </svg>
                                                                منع ساخت بازی
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <!-- شرکت در بازی -->
                                                    <?php if (empty($user['can_join_game'])): ?>
                                                        <form method="POST" action="/admin/users/<?= $user['id'] ?>/allow-join-game">
                                                            <button type="submit" class="w-full text-right px-3 py-1.5 text-xs font-bold hover:bg-gray-50 text-green-600 flex items-center gap-2">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                                                </svg>
                                                                اجازه شرکت در بازی
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" action="/admin/users/<?= $user['id'] ?>/ban-join-game">
                                                            <button type="submit" class="w-full text-right px-3 py-1.5 text-xs font-bold hover:bg-gray-50 text-red-600 flex items-center gap-2">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" />
                                                                </svg>
                                                                منع شرکت در بازی
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- مسدود/فعال -->
                                            <?php if (($user['status'] ?? '') === 'banned'): ?>
                                                <form method="POST" action="/admin/users/<?= $user['id'] ?>/unban" class="inline">
                                                    <button type="submit" class="p-1.5 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition" title="فعال‌سازی">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" action="/admin/users/<?= $user['id'] ?>/ban" class="inline">
                                                    <button type="submit" class="p-1.5 text-orange-500 hover:text-orange-700 hover:bg-orange-50 rounded-lg transition" title="مسدود کردن">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <!-- حذف -->
                                            <form method="POST" action="/admin/users/<?= $user['id'] ?>/delete"
                                                id="delete-user-<?= $user['id'] ?>" class="inline">
                                                <button type="button"
                                                    onclick="confirmDelete('آیا از حذف کاربر <?= htmlspecialchars($user['nickname'] ?? '') ?> مطمئن هستید؟', 'delete-user-<?= $user['id'] ?>')"
                                                    class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition"
                                                    title="حذف">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 font-medium">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white rounded-2xl p-4 border-2 border-gray-200/70 shadow-md">
            <div class="text-sm text-gray-600 font-medium whitespace-nowrap">
                صفحه <?= $page ?> از <?= $totalPages ?> (مجموع: <?= number_format($total) ?>)
            </div>
            <div class="flex gap-1 flex-wrap justify-center">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&<?= http_build_query($filters) ?>"
                        class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">
                        قبلی
                    </a>
                <?php endif; ?>
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <a href="?page=<?= $i ?>&<?= http_build_query($filters) ?>"
                        class="px-3.5 py-1.5 <?= $i === $page ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?> rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&<?= http_build_query($filters) ?>"
                        class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 hover:shadow-md whitespace-nowrap">
                        بعدی
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
    function confirmDelete(message, formId) {
        Swal.fire({
            title: 'حذف کاربر',
            text: message,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '🗑️ بله، حذف کن',
            cancelButtonText: 'انصراف',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>

<script>
    function confirmDelete(message, formId) {
        Swal.fire({
            title: 'حذف کاربر',
            text: message,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '🗑️ بله، حذف کن',
            cancelButtonText: 'انصراف',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    // 🆕 تایید باز محاسبه آمار همه کاربران
    function confirmRecalculateAll() {
        Swal.fire({
            title: '🔄 باز محاسبه آمار همه کاربران',
            html: `
                <div class="text-right">
                    <p class="text-gray-700 mb-3">
                        آیا از باز محاسبه آمار <strong>تمام کاربران</strong> اطمینان دارید؟
                    </p>
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-3">
                        <p class="text-orange-800 text-sm">
                            ⚠️ این عملیات ممکن است <strong>چند دقیقه</strong> طول بکشد.
                        </p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                        <p class="text-blue-800 text-xs">
                            📊 آمار بازی‌ها، XP، سطح، مدال‌ها و القاب همه کاربران از صفر محاسبه خواهد شد.
                        </p>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '🔄 بله، شروع کن',
            cancelButtonText: 'انصراف',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                executeRecalculateAll();
            }
        });
    }

    // 🆕 اجرای باز محاسبه با نمایش loading
    function executeRecalculateAll() {
        Swal.fire({
            title: '🔄 در حال پردازش...',
            html: `
                <div class="text-center">
                    <div class="mb-4">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent"></div>
                    </div>
                    <p class="text-gray-700">لطفاً صبر کنید...</p>
                    <p class="text-gray-500 text-sm mt-2">این عملیات ممکن است چند دقیقه طول بکشد</p>
                </div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
        });

        // ارسال درخواست به سرور
        fetch('/admin/users/recalculate-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: 'batch_size=50'
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '✅ عملیات با موفقیت انجام شد',
                        html: `
                        <div class="text-right">
                            <p class="text-gray-700 mb-2">${data.message}</p>
                            <div class="bg-green-50 border border-green-200 rounded-xl p-3 mt-3">
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div><strong>کل کاربران:</strong> ${data.stats.total}</div>
                                    <div><strong>موفق:</strong> ${data.stats.success}</div>
                                    <div><strong>ناموفق:</strong> ${data.stats.failed}</div>
                                    <div><strong>مدت زمان:</strong> ${data.stats.duration} ثانیه</div>
                                </div>
                            </div>
                        </div>
                    `,
                        confirmButtonColor: '#16a34a',
                        confirmButtonText: 'بستن',
                        timer: 8000,
                        timerProgressBar: true,
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '❌ خطا در انجام عملیات',
                        text: data.error || 'خطای ناشناخته',
                        confirmButtonColor: '#dc2626',
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: '❌ خطای ارتباط با سرور',
                    text: 'لطفاً دوباره تلاش کنید',
                    confirmButtonColor: '#dc2626',
                });
                console.error('Recalculate error:', error);
            });
    }
</script>