<?php

use Core\JalaliDate;

$actionLabels = [
    'user_ban' => ['label' => 'مسدود کاربر', 'color' => 'red', 'icon' => '🚫'],
    'user_unban' => ['label' => 'فعال‌سازی کاربر', 'color' => 'green', 'icon' => '✅'],
    'user_delete' => ['label' => 'حذف کاربر', 'color' => 'red', 'icon' => '🗑️'],
    'user_role_change' => ['label' => 'تغییر نقش', 'color' => 'indigo', 'icon' => '🔄'],
    'game_delete' => ['label' => 'حذف بازی', 'color' => 'red', 'icon' => '🗑️'],
    'game_edit' => ['label' => 'ویرایش بازی', 'color' => 'blue', 'icon' => '✏️'],
    'achievement_create' => ['label' => 'ایجاد نشان', 'color' => 'green', 'icon' => '➕'],
    'achievement_edit' => ['label' => 'ویرایش نشان', 'color' => 'blue', 'icon' => '✏️'],
    'achievement_delete' => ['label' => 'حذف نشان', 'color' => 'red', 'icon' => '🗑️'],
    'setting_change' => ['label' => 'تغییر تنظیمات', 'color' => 'purple', 'icon' => '⚙️'],
    'login' => ['label' => 'ورود', 'color' => 'green', 'icon' => '🔓'],
    'logout' => ['label' => 'خروج', 'color' => 'gray', 'icon' => '🔒'],
];
?>

<div class="space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
            <span class="text-3xl">📋</span> لاگ‌های سیستم
        </h2>
        <p class="text-gray-600 text-sm font-medium mt-0.5">مجموع: <strong class="text-indigo-600"><?= number_format($total) ?></strong> رکورد</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-gray-200/70 shadow-md">
        <form method="GET" action="/admin/logs" class="!grid !grid-cols-1 sm:!grid-cols-2 lg:!grid-cols-4 gap-3">
            <div><label class="text-xs text-gray-600 mb-1 block font-bold">نوع عملیات</label>
                <select name="action_type" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <?php foreach ($actionLabels as $key => $info): ?>
                        <option value="<?= $key ?>" <?= ($filters['action_type'] ?? '') === $key ? 'selected' : '' ?>><?= $info['icon'] ?> <?= $info['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label class="text-xs text-gray-600 mb-1 block font-bold">نوع هدف</label>
                <select name="target_type" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <option value="user" <?= ($filters['target_type'] ?? '') === 'user' ? 'selected' : '' ?>>کاربر</option>
                    <option value="game" <?= ($filters['target_type'] ?? '') === 'game' ? 'selected' : '' ?>>بازی</option>
                    <option value="achievement" <?= ($filters['target_type'] ?? '') === 'achievement' ? 'selected' : '' ?>>نشان</option>
                    <option value="setting" <?= ($filters['target_type'] ?? '') === 'setting' ? 'selected' : '' ?>>تنظیمات</option>
                </select>
            </div>
            <div><label class="text-xs text-gray-600 mb-1 block font-bold">از تاریخ</label>
                <input type="date" name="from_date" value="<?= htmlspecialchars($filters['from_date'] ?? '') ?>" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🔍 فیلتر</button>
                <a href="/admin/logs" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm hover:shadow-md">پاک کردن</a>
                <a href="/admin/logs/export?<?= http_build_query($filters) ?>" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg> Export
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">زمان</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">ادمین</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">عملیات</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">توضیحات</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="5" class="px-4 py-16 text-center text-gray-500"><div class="text-6xl mb-4 opacity-50">📋</div><p class="font-bold text-base">لاگی یافت نشد</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <?php $action = $actionLabels[$log['action_type']] ?? ['label' => $log['action_type'], 'color' => 'gray', 'icon' => '📝']; ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5 text-xs font-medium text-gray-500 whitespace-nowrap"><?= JalaliDate::format('Y/m/d H:i:s', strtotime($log['created_at'])) ?></td>
                                <td class="px-4 py-3.5 text-sm font-bold text-gray-700 whitespace-nowrap"><?= htmlspecialchars($log['admin_name'] ?? 'نامشخص') ?></td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-<?= $action['color'] ?>-100 text-<?= $action['color'] ?>-700 rounded-full text-xs font-bold border border-<?= $action['color'] ?>-200 shadow-sm"><span><?= $action['icon'] ?></span><span><?= $action['label'] ?></span></span>
                                </td>
                                <td class="px-4 py-3.5 text-sm font-medium text-gray-600"><?= htmlspecialchars($log['description'] ?? '-') ?></td>
                                <td class="px-4 py-3.5 text-xs font-mono font-bold text-gray-500 whitespace-nowrap"><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
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
            <div class="text-sm text-gray-600 font-medium whitespace-nowrap">صفحه <?= $page ?> از <?= $totalPages ?></div>
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
</div>