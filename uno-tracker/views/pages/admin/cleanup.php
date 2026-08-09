<?php

/**
 * 🗑️ مدیریت پاک‌سازی دیتابیس
 */

use Core\JalaliDate;

$formatOldest = function ($date) {
    if (!$date) return 'خالی';
    $days = floor((time() - strtotime($date)) / 86400);
    return JalaliDate::format('Y/m/d', strtotime($date)) . " ({$days} روز پیش)";
};
?>
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
            <span class="text-3xl">🗑️</span> پاک‌سازی دیتابیس
        </h2>
        <p class="text-gray-600 text-sm font-medium mt-0.5">مدیریت و پاک‌سازی جداول لاگ و داده‌های قدیمی</p>
    </div>

    <!-- Warning -->
    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-300 rounded-2xl p-4 shadow-md">
        <div class="flex items-start gap-3">
            <span class="text-3xl flex-shrink-0">⚠️</span>
            <div>
                <h3 class="text-sm font-black text-yellow-800">هشدار مهم</h3>
                <p class="text-yellow-700 text-xs font-medium mt-1">حذف داده‌ها قابل بازگشت نیست. قبل از حذف، از Export گرفتن مطمئن شوید. توصیه می‌شود فقط داده‌های قدیمی‌تر از ۳۰ روز را حذف کنید.</p>
            </div>
        </div>
    </div>

    <!-- Total Stats -->
    <div class="bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl p-5 border-2 border-gray-300 shadow-md">
        <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">📊</span> خلاصه وضعیت</h3>
        <div class="!grid !grid-cols-2 md:!grid-cols-4 gap-3">
            <?php
            $totalCount = $stats['admin_logs']['count'] + $stats['notifications']['count'] +
                $stats['referee_actions_log']['count'] + $stats['sse_events']['count'];
            ?>
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-3 text-center border-2 border-gray-200 shadow-sm">
                <div class="text-2xl font-black text-gray-800"><?= number_format($totalCount) ?></div>
                <div class="text-[10px] font-medium text-gray-600 mt-0.5">کل رکوردها</div>
            </div>
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-3 text-center border-2 border-blue-200 shadow-sm">
                <div class="text-2xl font-black text-blue-600"><?= number_format($stats['admin_logs']['count']) ?></div>
                <div class="text-[10px] font-medium text-gray-600 mt-0.5">لاگ ادمین</div>
            </div>
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-3 text-center border-2 border-purple-200 shadow-sm">
                <div class="text-2xl font-black text-purple-600"><?= number_format($stats['notifications']['count']) ?></div>
                <div class="text-[10px] font-medium text-gray-600 mt-0.5">اعلان‌ها</div>
            </div>
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-3 text-center border-2 border-orange-200 shadow-sm">
                <div class="text-2xl font-black text-orange-600"><?= number_format($stats['sse_events']['count']) ?></div>
                <div class="text-[10px] font-medium text-gray-600 mt-0.5">رویدادهای SSE</div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="!grid !grid-cols-1 md:!grid-cols-2 gap-5">
        <!-- Admin Logs -->
        <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3">
                <h3 class="text-base font-black text-white flex items-center gap-2"><span class="text-2xl">📋</span> لاگ‌های ادمین</h3>
            </div>
            <div class="p-4">
                <div class="!grid !grid-cols-2 gap-3 mb-4">
                    <div class="bg-blue-50/80 rounded-2xl p-3 text-center border-2 border-blue-200 shadow-sm"><div class="text-2xl font-black text-blue-600"><?= number_format($stats['admin_logs']['count']) ?></div><div class="text-[10px] font-medium text-gray-600">تعداد رکوردها</div></div>
                    <div class="bg-gray-50/80 rounded-2xl p-3 text-center border-2 border-gray-200 shadow-sm"><div class="text-sm font-bold text-gray-600"><?= $formatOldest($stats['admin_logs']['oldest']) ?></div><div class="text-[10px] font-medium text-gray-500">قدیمی‌ترین</div></div>
                </div>
                <div class="space-y-2">
                    <form method="POST" action="/admin/cleanup/admin-logs/delete-old" class="flex gap-2" onsubmit="return confirmCleanup(this, 'حذف لاگ‌های ادمین', 'لاگ‌های ادمین', this.days_old.options[this.days_old.selectedIndex].text)">
                        <select name="days_old" class="flex-1 px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                            <option value="7">🗓️ قدیمی‌تر از ۷ روز</option>
                            <option value="30" selected>🗓️ قدیمی‌تر از ۳۰ روز</option>
                            <option value="90">🗓️ قدیمی‌تر از ۹۰ روز</option>
                            <option value="180">🗓️ قدیمی‌تر از ۱۸۰ روز (۶ ماه)</option>
                            <option value="365">🗓️ قدیمی‌تر از ۳۶۵ روز (۱ سال)</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف</button>
                    </form>
                    <form method="POST" action="/admin/cleanup/admin-logs/delete-all" onsubmit="return confirmCleanupAll(this, 'حذف همه لاگ‌های ادمین', '<?= number_format($stats['admin_logs']['count']) ?> لاگ ادمین')">
                        <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف همه</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-3">
                <h3 class="text-base font-black text-white flex items-center gap-2"><span class="text-2xl">📧</span> اعلان‌ها</h3>
            </div>
            <div class="p-4">
                <div class="!grid !grid-cols-2 gap-3 mb-4">
                    <div class="bg-purple-50/80 rounded-2xl p-3 text-center border-2 border-purple-200 shadow-sm"><div class="text-2xl font-black text-purple-600"><?= number_format($stats['notifications']['count']) ?></div><div class="text-[10px] font-medium text-gray-600">تعداد رکوردها</div></div>
                    <div class="bg-gray-50/80 rounded-2xl p-3 text-center border-2 border-gray-200 shadow-sm"><div class="text-sm font-bold text-gray-600"><?= $formatOldest($stats['notifications']['oldest']) ?></div><div class="text-[10px] font-medium text-gray-500">قدیمی‌ترین</div></div>
                </div>
                <div class="space-y-2">
                    <form method="POST" action="/admin/cleanup/notifications/delete-old" class="flex gap-2" onsubmit="return confirmCleanup(this, 'حذف اعلان‌ها', 'اعلان‌ها', this.days_old.options[this.days_old.selectedIndex].text)">
                        <select name="days_old" class="flex-1 px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                            <option value="7">🗓️ قدیمی‌تر از ۷ روز</option>
                            <option value="30" selected>🗓️ قدیمی‌تر از ۳۰ روز</option>
                            <option value="90">🗓️ قدیمی‌تر از ۹۰ روز</option>
                            <option value="180">🗓️ قدیمی‌تر از ۱۸۰ روز (۶ ماه)</option>
                            <option value="365">🗓️ قدیمی‌تر از ۳۶۵ روز (۱ سال)</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف</button>
                    </form>
                    <form method="POST" action="/admin/cleanup/notifications/delete-all" onsubmit="return confirmCleanupAll(this, 'حذف همه اعلان‌ها', '<?= number_format($stats['notifications']['count']) ?> اعلان')">
                        <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف همه</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Referee Actions Log -->
        <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 py-3">
                <h3 class="text-base font-black text-white flex items-center gap-2"><span class="text-2xl">⚖️</span> لاگ اعمال داور</h3>
            </div>
            <div class="p-4">
                <div class="!grid !grid-cols-2 gap-3 mb-4">
                    <div class="bg-green-50/80 rounded-2xl p-3 text-center border-2 border-green-200 shadow-sm"><div class="text-2xl font-black text-green-600"><?= number_format($stats['referee_actions_log']['count']) ?></div><div class="text-[10px] font-medium text-gray-600">تعداد رکوردها</div></div>
                    <div class="bg-gray-50/80 rounded-2xl p-3 text-center border-2 border-gray-200 shadow-sm"><div class="text-sm font-bold text-gray-600"><?= $formatOldest($stats['referee_actions_log']['oldest']) ?></div><div class="text-[10px] font-medium text-gray-500">قدیمی‌ترین</div></div>
                </div>
                <div class="space-y-2">
                    <form method="POST" action="/admin/cleanup/referee-actions/delete-old" class="flex gap-2" onsubmit="return confirmCleanup(this, 'حذف لاگ‌های داور', 'لاگ اعمال داور', this.days_old.options[this.days_old.selectedIndex].text)">
                        <select name="days_old" class="flex-1 px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                            <option value="7">🗓️ قدیمی‌تر از ۷ روز</option>
                            <option value="30" selected>🗓️ قدیمی‌تر از ۳۰ روز</option>
                            <option value="90">🗓️ قدیمی‌تر از ۹۰ روز</option>
                            <option value="180">🗓️ قدیمی‌تر از ۱۸۰ روز (۶ ماه)</option>
                            <option value="365">🗓️ قدیمی‌تر از ۳۶۵ روز (۱ سال)</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف</button>
                    </form>
                    <form method="POST" action="/admin/cleanup/referee-actions/delete-all" onsubmit="return confirmCleanupAll(this, 'حذف همه لاگ‌های داور', '<?= number_format($stats['referee_actions_log']['count']) ?> لاگ داور')">
                        <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف همه</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SSE Events -->
        <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-3">
                <h3 class="text-base font-black text-white flex items-center gap-2"><span class="text-2xl">📡</span> رویدادهای SSE</h3>
            </div>
            <div class="p-4">
                <div class="!grid !grid-cols-2 gap-3 mb-4">
                    <div class="bg-orange-50/80 rounded-2xl p-3 text-center border-2 border-orange-200 shadow-sm"><div class="text-2xl font-black text-orange-600"><?= number_format($stats['sse_events']['count']) ?></div><div class="text-[10px] font-medium text-gray-600">تعداد رکوردها</div></div>
                    <div class="bg-gray-50/80 rounded-2xl p-3 text-center border-2 border-gray-200 shadow-sm"><div class="text-sm font-bold text-gray-600"><?= $formatOldest($stats['sse_events']['oldest']) ?></div><div class="text-[10px] font-medium text-gray-500">قدیمی‌ترین</div></div>
                </div>
                <div class="space-y-2">
                    <form method="POST" action="/admin/cleanup/sse-events/delete-old" class="flex gap-2" onsubmit="return confirmCleanup(this, 'حذف رویدادهای SSE', 'رویدادهای SSE', this.hours_old.options[this.hours_old.selectedIndex].text)">
                        <select name="hours_old" class="flex-1 px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                            <option value="1">⏰ قدیمی‌تر از ۱ ساعت</option>
                            <option value="6">⏰ قدیمی‌تر از ۶ ساعت</option>
                            <option value="12">⏰ قدیمی‌تر از ۱۲ ساعت</option>
                            <option value="24" selected>⏰ قدیمی‌تر از ۲۴ ساعت (۱ روز)</option>
                            <option value="72">⏰ قدیمی‌تر از ۷۲ ساعت (۳ روز)</option>
                            <option value="168">⏰ قدیمی‌تر از ۱۶۸ ساعت (۱ هفته)</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف</button>
                    </form>
                    <form method="POST" action="/admin/cleanup/sse-events/delete-all" onsubmit="return confirmCleanupAll(this, 'حذف همه رویدادهای SSE', '<?= number_format($stats['sse_events']['count']) ?> رویداد SSE')">
                        <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">🗑️ حذف همه</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmCleanup(form, title, typeName, periodText) {
    Swal.fire({
        title: '🗑️ ' + title,
        html: `<div class="text-right"><p class="text-gray-700 mb-3">آیا از حذف <strong>${typeName}</strong> زیر مطمئن هستید؟</p><div class="bg-orange-50 border-2 border-orange-200 rounded-2xl p-3 text-sm"><span class="text-orange-700 font-bold">${periodText}</span></div><p class="text-red-600 text-xs mt-3 font-bold">⚠️ این عملیات <strong>غیرقابل بازگشت</strong> است!</p></div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea580c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '🗑️ بله، حذف کن',
        cancelButtonText: 'انصراف',
        reverseButtons: true
    }).then((result) => { if (result.isConfirmed) form.submit(); });
    return false;
}

function confirmCleanupAll(form, title, countText) {
    Swal.fire({
        title: '⚠️ ' + title,
        html: `<div class="text-right"><p class="text-gray-700 mb-3">آیا از حذف <strong>تمام</strong> رکوردها مطمئن هستید؟</p><div class="bg-red-50 border-2 border-red-300 rounded-2xl p-3 text-sm"><span class="text-red-700 font-bold">🗑️ ${countText}</span></div><div class="bg-red-100 border-2 border-red-300 rounded-2xl p-3 mt-3"><p class="text-red-800 text-sm font-bold">⚠️ هشدار جدی!</p><p class="text-red-700 text-xs mt-1 font-medium">این عملیات <strong>کاملاً غیرقابل بازگشت</strong> است و تمام داده‌ها به صورت دائمی حذف خواهند شد!</p></div></div>`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '🗑️ بله، همه را حذف کن',
        cancelButtonText: 'انصراف',
        reverseButtons: true
    }).then((result) => { if (result.isConfirmed) form.submit(); });
    return false;
}
</script>