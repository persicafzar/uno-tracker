<?php
// استخراج تنظیمات مورد نیاز
$authMethod = 'password';
$authMethodSetting = null;
$smsSettings = [];
$smsKeys = ['sms_enabled', 'sms_otp_length', 'sms_otp_expiry', 'sms_daily_limit', 'sms_otp_attempt_limit'];

foreach ($currentCategory as $setting) {
    if ($setting['key'] === 'auth_method') {
        $authMethod = $setting['value'] ?? 'password';
        $authMethodSetting = $setting;
    }
    if (in_array($setting['key'], $smsKeys)) {
        $smsSettings[$setting['key']] = $setting;
    }
}
?>

<form method="POST" action="/admin/settings" id="settings-form">
    <input type="hidden" name="category" value="<?= $activeCategory ?>">
    <div class="space-y-4">

        <!-- بخش ۱: روش احراز هویت -->
        <div class="bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 rounded-2xl p-5 shadow-md mb-4">
            <h3 class="text-base font-black text-gray-800 mb-3 flex items-center gap-2">
                <span class="text-2xl">🔐</span>
                روش احراز هویت کاربران
            </h3>
            <p class="text-xs text-gray-600 font-medium mb-4">
                تعیین کنید کاربران با رمز عبور یا کد پیامکی وارد شوند. در حالت پیامکی، کپچا حذف می‌شود و کد تایید پیامکی جایگزین می‌گردد.
            </p>

            <?php if ($authMethodSetting): ?>
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <label class="block text-sm font-black text-gray-800 mb-3">
                        <?= htmlspecialchars(str_replace('_', ' ', $authMethodSetting['key'])) ?>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-500 transition <?= $authMethodSetting['value'] === 'password' ? 'border-indigo-500 bg-indigo-50' : '' ?>">
                            <input type="radio" name="settings[auth_method]" value="password"
                                <?= $authMethodSetting['value'] === 'password' ? 'checked' : '' ?>
                                class="w-5 h-5 text-indigo-600">
                            <div>
                                <div class="font-bold text-gray-800">🔑 رمز عبور + کپچا</div>
                                <div class="text-xs text-gray-500">روش کلاسیک - بدون هزینه پیامک</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-green-500 transition <?= $authMethodSetting['value'] === 'sms' ? 'border-green-500 bg-green-50' : '' ?>">
                            <input type="radio" name="settings[auth_method]" value="sms"
                                <?= $authMethodSetting['value'] === 'sms' ? 'checked' : '' ?>
                                class="w-5 h-5 text-green-600">
                            <div>
                                <div class="font-bold text-gray-800">📱 کد پیامکی (OTP)</div>
                                <div class="text-xs text-gray-500">مدرن و راحت - نیاز به پنل پیامک</div>
                            </div>
                        </label>
                    </div>

                    <?php if ($authMethodSetting['value'] === 'sms'): ?>
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                            <p class="text-xs text-yellow-800 font-medium">
                                ⚠️ <strong>توجه:</strong> برای استفاده از این قابلیت باید فایل <code class="bg-yellow-100 px-2 py-0.5 rounded">config/melipayamak.php</code> را با اطلاعات پنل ملی پیامک پر کنید.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- بخش ۲: تنظیمات سامانه پیامکی (فقط در حالت SMS) -->
        <?php if ($authMethod === 'sms'): ?>
            <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 border-2 border-emerald-300 rounded-2xl p-5 shadow-lg mb-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-200/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-teal-200/20 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-5 pb-4 border-b-2 border-emerald-200/50">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-3xl">📱</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800 flex items-center gap-2">
                                    سامانه پیامکی
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold border border-emerald-200">
                                        فعال
                                    </span>
                                </h3>
                                <p class="text-xs text-gray-600 font-medium mt-1">
                                    پیکربندی ارسال کد تایید از طریق ملی پیامک (Melipayamak)
                                </p>
                            </div>
                        </div>
                        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-100 border border-emerald-300 rounded-xl">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-bold text-emerald-700">OTP System</span>
                        </div>
                    </div>

                    <?php if (isset($smsSettings['sms_enabled'])): ?>
                        <div class="mb-5 p-4 bg-white rounded-xl border-2 border-emerald-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-lg flex items-center justify-center shadow">
                                        <span class="text-xl"><?= $smsSettings['sms_enabled']['value'] ? '✅' : '❌' ?></span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">وضعیت سیستم پیامک</div>
                                        <div class="text-xs text-gray-500">
                                            <?= $smsSettings['sms_enabled']['description'] ?? 'فعال‌سازی کلی ارسال پیامک' ?>
                                        </div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="settings[sms_enabled]" value="0">
                                    <input type="checkbox" name="settings[sms_enabled]" value="1"
                                        <?= $smsSettings['sms_enabled']['value'] ? 'checked' : '' ?>
                                        class="sr-only peer">
                                    <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-200 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-600 shadow-inner"></div>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php if (isset($smsSettings['sms_otp_length'])): ?>
                            <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-emerald-400 transition-all duration-200 hover:shadow-md">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <span class="text-xl">🔢</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label class="block text-sm font-bold text-gray-800 mb-1">طول کد تایید</label>
                                        <p class="text-xs text-gray-500 mb-2"><?= $smsSettings['sms_otp_length']['description'] ?? 'تعداد ارقام کد' ?></p>
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="settings[sms_otp_length]" value="<?= htmlspecialchars($smsSettings['sms_otp_length']['value']) ?>" min="4" max="8" class="w-20 px-3 py-2 border-2 border-gray-200 rounded-lg text-center text-lg font-bold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                                            <span class="text-xs text-gray-500">رقم</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($smsSettings['sms_otp_expiry'])): ?>
                            <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-emerald-400 transition-all duration-200 hover:shadow-md">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <span class="text-xl">⏱️</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label class="block text-sm font-bold text-gray-800 mb-1">زمان انقضای کد</label>
                                        <p class="text-xs text-gray-500 mb-2"><?= $smsSettings['sms_otp_expiry']['description'] ?? 'مدت اعتبار کد' ?></p>
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="settings[sms_otp_expiry]" value="<?= htmlspecialchars($smsSettings['sms_otp_expiry']['value']) ?>" min="1" max="60" class="w-20 px-3 py-2 border-2 border-gray-200 rounded-lg text-center text-lg font-bold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                                            <span class="text-xs text-gray-500">دقیقه</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($smsSettings['sms_daily_limit'])): ?>
                            <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-emerald-400 transition-all duration-200 hover:shadow-md">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-500 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <span class="text-xl">📊</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label class="block text-sm font-bold text-gray-800 mb-1">محدودیت روزانه</label>
                                        <p class="text-xs text-gray-500 mb-2"><?= $smsSettings['sms_daily_limit']['description'] ?? 'حداکثر پیامک در روز' ?></p>
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="settings[sms_daily_limit]" value="<?= htmlspecialchars($smsSettings['sms_daily_limit']['value']) ?>" min="1" max="100" class="w-20 px-3 py-2 border-2 border-gray-200 rounded-lg text-center text-lg font-bold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                                            <span class="text-xs text-gray-500">پیامک</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($smsSettings['sms_otp_attempt_limit'])): ?>
                            <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-emerald-400 transition-all duration-200 hover:shadow-md">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <span class="text-xl">🛡️</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label class="block text-sm font-bold text-gray-800 mb-1">حداکثر تلاش ورود کد</label>
                                        <p class="text-xs text-gray-500 mb-2"><?= $smsSettings['sms_otp_attempt_limit']['description'] ?? 'تعداد دفعات وارد کردن کد اشتباه' ?></p>
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="settings[sms_otp_attempt_limit]" value="<?= htmlspecialchars($smsSettings['sms_otp_attempt_limit']['value']) ?>" min="1" max="20" class="w-20 px-3 py-2 border-2 border-gray-200 rounded-lg text-center text-lg font-bold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                                            <span class="text-xs text-gray-500">دفعه</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-5 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-start gap-2">
                            <span class="text-lg flex-shrink-0">💡</span>
                            <div class="text-xs text-blue-800 space-y-1">
                                <p class="font-bold">راهنمای سریع:</p>
                                <ul class="list-disc list-inside space-y-0.5 text-blue-700">
                                    <li>طول کد ۶ رقمی استاندارد و امن است</li>
                                    <li>زمان انقضای ۵ دقیقه تعادل خوبی بین امنیت و راحتی است</li>
                                    <li>محدودیت روزانه از سوءاستفاده و هزینه اضافی جلوگیری می‌کند</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- سایر تنظیمات امنیتی (غیر از SMS و auth_method) -->
        <?php foreach ($currentCategory as $setting): ?>
            <?php if (in_array($setting['key'], array_merge(['auth_method'], $smsKeys))) continue; ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start p-4 bg-gray-50/80 rounded-2xl border border-gray-200 hover:border-indigo-300 transition-all duration-200 hover:shadow-md">
                <div>
                    <label class="block text-sm font-black text-gray-800 mb-1"><?= htmlspecialchars(str_replace('_', ' ', $setting['key'])) ?></label>
                    <?php if (!empty($setting['description'])): ?>
                        <p class="text-xs text-gray-500 font-medium"><?= htmlspecialchars($setting['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="md:col-span-2">
                    <?php if ($setting['type'] === 'boolean'): ?>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="settings[<?= $setting['key'] ?>]" value="0">
                            <input type="checkbox" name="settings[<?= $setting['key'] ?>]" value="1" <?= $setting['value'] ? 'checked' : '' ?> class="w-5 h-5 text-<?= $catInfo['color'] ?>-600 rounded focus:ring-<?= $catInfo['color'] ?>-500">
                            <span class="text-sm font-bold text-gray-700">فعال</span>
                        </label>
                    <?php elseif ($setting['type'] === 'integer'): ?>
                        <input type="number" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                    <?php elseif ($setting['type'] === 'float'): ?>
                        <input type="number" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" step="0.01" class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                    <?php else: ?>
                        <input type="text" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" class="w-full max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-6 flex items-center justify-between bg-gray-50/80 rounded-2xl p-4 border border-gray-200 shadow-sm">
        <div class="text-sm font-bold text-gray-600">💾 تغییرات ذخیره نشده دارید</div>
        <button type="submit" class="px-6 py-2.5 bg-<?= $catInfo['color'] ?>-600 hover:bg-<?= $catInfo['color'] ?>-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">💾 ذخیره تنظیمات</button>
    </div>
</form>