<?php

/**
 * 📧 مدیریت اعلان‌های سیستم
 */

use Core\JalaliDate;

$typeLabels = [
    'achievement' => ['label' => 'نشان', 'icon' => '🏅', 'color' => 'purple'],
    'level_up' => ['label' => 'ارتقا سطح', 'icon' => '⭐', 'color' => 'yellow'],
    'title' => ['label' => 'عنوان', 'icon' => '🏆', 'color' => 'blue'],
    'challenge' => ['label' => 'ماموریت', 'icon' => '🎯', 'color' => 'green'],
    'game' => ['label' => 'بازی', 'icon' => '🎮', 'color' => 'indigo'],
    'system' => ['label' => 'سیستم', 'icon' => '📢', 'color' => 'gray'],
];
?>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
                <span class="text-3xl">📧</span> مدیریت اعلان‌ها
            </h2>
            <p class="text-gray-600 text-sm font-medium mt-0.5">مجموع: <strong class="text-indigo-600"><?= number_format($total) ?></strong> اعلان</p>
        </div>
        <div class="flex gap-2">
            <a href="/admin/notifications/export?<?= http_build_query($filters) ?>"
                class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-2xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-gray-200/70 shadow-md">
        <form method="GET" action="/admin/notifications" class="!grid !grid-cols-1 sm:!grid-cols-2 lg:!grid-cols-5 gap-3">
            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">جستجو کاربر</label>
                <input type="text" name="user_id" value="<?= htmlspecialchars($filters['user_id'] ?? '') ?>" placeholder="شناسه کاربر"
                    class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
            </div>
            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">نوع اعلان</label>
                <select name="type" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <?php foreach ($typeLabels as $key => $info): ?>
                        <option value="<?= $key ?>" <?= ($filters['type'] ?? '') === $key ? 'selected' : '' ?>><?= $info['icon'] ?> <?= $info['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">وضعیت</label>
                <select name="is_read" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="">همه</option>
                    <option value="0" <?= ($filters['is_read'] ?? '') === '0' ? 'selected' : '' ?>>خوانده نشده</option>
                    <option value="1" <?= ($filters['is_read'] ?? '') === '1' ? 'selected' : '' ?>>خوانده شده</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">از تاریخ</label>
                <input type="date" name="from_date" value="<?= htmlspecialchars($filters['from_date'] ?? '') ?>" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
            </div>
            <div>
                <label class="text-xs text-gray-600 mb-1 block font-bold">تا تاریخ</label>
                <input type="date" name="to_date" value="<?= htmlspecialchars($filters['to_date'] ?? '') ?>" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex items-end gap-2">
                <button type="submit" class="flex-1 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🔍 فیلتر</button>
                <a href="/admin/notifications" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-all duration-200 shadow-sm hover:shadow-md">پاک کردن</a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-2xl p-4 border-2 border-gray-200/70 shadow-md">
        <h3 class="text-sm font-black text-gray-700 mb-3 flex items-center gap-2.5"><span class="text-xl">⚡</span> عملیات گروهی</h3>
        <div class="!grid !grid-cols-1 sm:!grid-cols-3 gap-3">
            <form method="POST" action="/admin/notifications/delete-old" class="flex gap-2" id="form-delete-old-notifications">
                <select name="days_old" id="notifications-days-old" class="flex-1 px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                    <option value="7">🗓️ قدیمی‌تر از ۷ روز</option>
                    <option value="30" selected>🗓️ قدیمی‌تر از ۳۰ روز</option>
                    <option value="90">🗓️ قدیمی‌تر از ۹۰ روز</option>
                    <option value="180">🗓️ قدیمی‌تر از ۱۸۰ روز (۶ ماه)</option>
                    <option value="365">🗓️ قدیمی‌تر از ۳۶۵ روز (۱ سال)</option>
                </select>
                <button type="button" onclick="confirmDeleteOldNotifications()" class="px-4 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف</button>
            </form>
            <form method="POST" action="/admin/notifications/delete-all" id="form-delete-all-notifications">
                <button type="button" onclick="confirmDeleteAllNotifications(<?= number_format($total) ?>)" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف همه اعلان‌ها</button>
            </form>
            <form method="POST" action="/admin/notifications/mark-all-read" id="form-mark-all-read">
                <button type="button" onclick="confirmMarkAllAsRead()" class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">✅ علامت‌گذاری همه به عنوان خوانده شده</button>
            </form>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">ID</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">کاربر</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">نوع</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">عنوان</th>
                        <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">پیام</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">وضعیت</th>
                        <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($notifications)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-gray-500">
                                <div class="text-6xl mb-4 opacity-50">📧</div>
                                <p class="font-bold text-base">اعلانی یافت نشد</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                            <?php $typeInfo = $typeLabels[$notif['type']] ?? $typeLabels['system']; ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5 text-sm font-bold text-gray-600 whitespace-nowrap">#<?= $notif['id'] ?></td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-xs font-black flex-shrink-0 border border-gray-300"><?= mb_substr($notif['user_nickname'] ?? '?', 0, 1) ?></div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-800"><?= htmlspecialchars($notif['user_nickname'] ?? 'نامشخص') ?></div>
                                            <div class="text-xs font-medium text-gray-500">#<?= $notif['user_id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-<?= $typeInfo['color'] ?>-100 text-<?= $typeInfo['color'] ?>-700 rounded-full text-xs font-bold border border-<?= $typeInfo['color'] ?>-200 shadow-sm"><span><?= $typeInfo['icon'] ?></span><span><?= $typeInfo['label'] ?></span></span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2"><span class="text-xl flex-shrink-0"><?= $notif['icon'] ?? $typeInfo['icon'] ?></span><span class="text-sm font-bold text-gray-800 truncate max-w-[120px]"><?= htmlspecialchars($notif['title']) ?></span></div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-sm font-medium text-gray-600 line-clamp-2 max-w-xs"><?= htmlspecialchars($notif['message'] ?? '-') ?></p>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if ($notif['is_read']): ?>
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold border border-gray-200 shadow-sm">✅ خوانده شده</span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold border border-blue-200 shadow-sm animate-pulse">📨 خوانده نشده</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-500"><?= JalaliDate::format('Y/m/d', strtotime($notif['created_at'])) ?></div>
                                    <div class="text-xs font-medium text-gray-400"><?= date('H:i', strtotime($notif['created_at'])) ?></div>
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
            <div class="text-sm text-gray-600 font-medium whitespace-nowrap">صفحه <?= $page ?> از <?= $totalPages ?> (مجموع: <?= number_format($total) ?>)</div>
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
    <script>
        function confirmDeleteOldNotifications() {
            const select = document.getElementById('notifications-days-old');
            const daysText = select.options[select.selectedIndex].text.replace('🗓️ ', '');

            Swal.fire({
                title: '🗑️ حذف اعلان‌های قدیمی',
                html: `
            <div class="text-right">
                <p class="text-gray-700 mb-3">آیا از حذف اعلان‌های زیر اطمینان دارید؟</p>
                <div class="bg-orange-50 border-2 border-orange-200 rounded-xl p-3 text-sm">
                    <span class="text-orange-700 font-bold">📅 ${daysText}</span>
                </div>
                <p class="text-red-600 text-xs mt-3">⚠️ این عملیات <strong>غیرقابل بازگشت</strong> است!</p>
            </div>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '🗑️ بله، حذف کن',
                cancelButtonText: 'انصراف',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-old-notifications').submit();
                }
            });
        }

        function confirmDeleteAllNotifications(totalCount) {
            Swal.fire({
                title: '⚠️ حذف همه اعلان‌ها',
                html: `
            <div class="text-right">
                <p class="text-gray-700 mb-3">آیا از حذف <strong>تمام</strong> اعلان‌ها اطمینان دارید؟</p>
                <div class="bg-red-50 border-2 border-red-300 rounded-xl p-3 text-sm mb-3">
                    <span class="text-red-700 font-bold">📧 ${totalCount} اعلان</span>
                </div>
                <div class="bg-red-100 border border-red-300 rounded-xl p-3">
                    <p class="text-red-800 text-sm font-bold">⚠️ هشدار جدی!</p>
                    <p class="text-red-700 text-xs mt-1">
                        این عملیات <strong>کاملاً غیرقابل بازگشت</strong> است و تمام اعلان‌های همه کاربران به صورت دائمی حذف خواهند شد!
                    </p>
                </div>
            </div>
        `,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '🗑️ بله، همه را حذف کن',
                cancelButtonText: 'انصراف',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-all-notifications').submit();
                }
            });
        }
        /**
         * تأیید علامت‌گذاری همه اعلان‌ها به عنوان خوانده شده
         */
        function confirmMarkAllAsRead() {
            Swal.fire({
                title: '✅ علامت‌گذاری به عنوان خوانده شده',
                html: `
            <div class="text-right">
                <p class="text-gray-700 mb-3">
                    آیا از علامت‌گذاری <strong>تمام اعلان‌های خوانده نشده</strong> به عنوان خوانده شده اطمینان دارید؟
                </p>
                <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-3 text-sm">
                    <span class="text-blue-700 font-bold">📧 همه اعلان‌های خوانده نشده</span>
                </div>
                <p class="text-blue-600 text-xs mt-3">
                    ℹ️ این عملیات وضعیت اعلان‌های همه کاربران را تغییر می‌دهد.
                </p>
            </div>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0891b2',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '✅ بله، علامت‌گذاری کن',
                cancelButtonText: 'انصراف',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-mark-all-read').submit();
                }
            });
        }
    </script>
</div>