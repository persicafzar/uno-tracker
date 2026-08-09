<?php
$categoryLabels = [
    'general' => ['label' => 'عمومی', 'icon' => '⚙️', 'color' => 'indigo'],
    'session' => ['label' => 'نشست (Session)', 'icon' => '🔐', 'color' => 'purple'],
    'security' => ['label' => 'امنیت', 'icon' => '🔒', 'color' => 'slate'],
    'game' => ['label' => 'بازی', 'icon' => '🎮', 'color' => 'green'],
    'scoring' => ['label' => 'امتیازدهی', 'icon' => '⭐', 'color' => 'yellow'],
    'gamification' => ['label' => 'گیمیفیکیشن', 'icon' => '🏆', 'color' => 'purple'],
    'anticheat' => ['label' => 'ضد تقلب', 'icon' => '🛡️', 'color' => 'red'],
    'upload' => ['label' => 'آپلود', 'icon' => '📤', 'color' => 'blue'],
    'display' => ['label' => 'نمایش', 'icon' => '🎨', 'color' => 'pink'],
    'notification' => ['label' => 'اعلان‌ها', 'icon' => '🔔', 'color' => 'orange'],
];

$anticheatOrder = [
    'anticheat_enabled',
    'anticheat_new_account_hours',
    'anticheat_max_accounts_per_ip',
    'anticheat_max_games_created_per_day',
    'anticheat_max_games_per_hour',
    'anticheat_max_solo_games_per_day',
    'anticheat_max_friendly_games_per_day',
    'anticheat_min_players',
    'anticheat_min_members',
    'anticheat_max_guests',
    'anticheat_max_guest_ratio',
    'anticheat_min_round_duration',
    'anticheat_max_win_percentage',
    'anticheat_min_target_wins_threshold',
    'anticheat_max_low_target_games',
    'anticheat_collusion_min_games',
    'anticheat_collusion_max_opponents',
];

$activeCategory = $_GET['tab'] ?? 'general';
?>

<div class="space-y-6" x-data="settingsManager()">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
                <span class="text-3xl">⚙️</span> تنظیمات سیستم
            </h2>
            <p class="text-gray-600 text-sm font-medium mt-0.5">مدیریت تنظیمات کلی سایت</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-2xl border-2 border-gray-200/70 shadow-md overflow-hidden">
        <div class="border-b-2 border-gray-200 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent" style="scrollbar-width: thin; scrollbar-color: #d1d5db transparent;">
            <nav class="flex flex-nowrap gap-1" aria-label="Tabs">
                <?php foreach ($categoryLabels as $catKey => $catInfo): ?>
                    <a href="?tab=<?= $catKey ?>"
                        class="flex items-center gap-2 px-4 py-3 text-sm font-bold whitespace-nowrap transition-all duration-200 flex-shrink-0
                           <?= $activeCategory === $catKey ? 'border-b-2 border-' . $catInfo['color'] . '-500 text-' . $catInfo['color'] . '-600 bg-' . $catInfo['color'] . '-50 rounded-t-xl' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' ?>">
                        <span class="text-lg"><?= $catInfo['icon'] ?></span>
                        <span><?= $catInfo['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div class="p-5">
            <?php
            $currentCategory = $settings[$activeCategory] ?? [];
            $catInfo = $categoryLabels[$activeCategory] ?? ['label' => $activeCategory, 'icon' => '📁', 'color' => 'gray'];

            if ($activeCategory === 'anticheat' && !empty($currentCategory)) {
                $orderedSettings = [];
                foreach ($anticheatOrder as $key) {
                    foreach ($currentCategory as $setting) {
                        if ($setting['key'] === $key) {
                            $orderedSettings[] = $setting;
                            break;
                        }
                    }
                }
                foreach ($currentCategory as $setting) {
                    if (!in_array($setting['key'], $anticheatOrder)) $orderedSettings[] = $setting;
                }
                $currentCategory = $orderedSettings;
            }
            ?>

            <?php if (empty($currentCategory)): ?>
                <div class="text-center py-12">
                    <div class="text-5xl mb-3 opacity-50"><?= $catInfo['icon'] ?></div>
                    <p class="text-gray-500 font-medium">هیچ تنظیمی در این دسته وجود ندارد</p>
                </div>
            <?php else: ?>
                <form method="POST" action="/admin/settings" id="settings-form">
                    <input type="hidden" name="category" value="<?= $activeCategory ?>">
                    <div class="space-y-4">
                        <?php if ($activeCategory === 'game'):
                            $firstPlayerSelection = 'random';
                            foreach ($currentCategory as $setting) {
                                if ($setting['key'] === 'first_player_selection') {
                                    $firstPlayerSelection = $setting['value'] ?? 'random';
                                    break;
                                }
                            }
                        ?>
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50  border-blue-200 rounded-2xl p-4 shadow-md">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                    <div><label class="block text-sm font-black text-gray-800 mb-1">🎯 انتخاب هوشمند بازیکن اول</label>
                                        <p class="text-xs text-gray-600 font-medium">نحوه انتخاب بازیکن شروع‌کننده بازی را مشخص کنید.</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <select name="settings[first_player_selection]" class="w-full max-w-md px-3 py-2.5  border-blue-300 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 bg-white appearance-none cursor-pointer hover:border-blue-400">
                                            <option value="random" <?= $firstPlayerSelection === 'random' ? 'selected' : '' ?>>🎲 تصادفی (Random)</option>
                                            <option value="by_score" <?= $firstPlayerSelection === 'by_score' ? 'selected' : '' ?>>🏆 بر اساس امتیاز (Score)</option>
                                            <option value="by_xp" <?= $firstPlayerSelection === 'by_xp' ? 'selected' : '' ?>>⭐ بر اساس XP</option>
                                        </select>
                                        <div class="mt-2 text-xs text-gray-500 font-medium space-y-1">
                                            <p><strong>🎲 تصادفی:</strong> هر بازیکن شانس برابر دارد</p>
                                            <p><strong>🏆 بر اساس امتیاز:</strong> بازیکن با بیشترین امتیاز کل شروع می‌کند</p>
                                            <p><strong>⭐ بر اساس XP:</strong> باتجربه‌ترین بازیکن شروع می‌کند</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($activeCategory === 'security'):
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
                            <!-- ═══════════════════════════════════════════════════════════ -->
                            <!-- بخش ۱: روش احراز هویت (همیشه نمایش داده می‌شود) -->
                            <!-- ═══════════════════════════════════════════════════════════ -->
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

                            <!-- ═══════════════════════════════════════════════════════════ -->
                            <!-- بخش ۲: تنظیمات سامانه پیامکی (فقط در حالت SMS نمایش داده می‌شود) -->
                            <!-- ═══════════════════════════════════════════════════════════ -->
                            <?php if ($authMethod === 'sms'): ?>
                                <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 border-2 border-emerald-300 rounded-2xl p-5 shadow-lg mb-4 relative overflow-hidden">
                                    <!-- عناصر تزئینی پس‌زمینه -->
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-200/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-teal-200/20 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                                    <div class="relative z-10">
                                        <!-- هدر -->
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

                                        <!-- فعال/غیرفعال کردن کلی سیستم -->
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

                                        <!-- کارت‌های تنظیمات -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                                            <!-- طول کد -->
                                            <?php if (isset($smsSettings['sms_otp_length'])): ?>
                                                <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-emerald-400 transition-all duration-200 hover:shadow-md">
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                                            <span class="text-xl">🔢</span>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <label class="block text-sm font-bold text-gray-800 mb-1">
                                                                طول کد تایید
                                                            </label>
                                                            <p class="text-xs text-gray-500 mb-2">
                                                                <?= $smsSettings['sms_otp_length']['description'] ?? 'تعداد ارقام کد' ?>
                                                            </p>
                                                            <div class="flex items-center gap-2">
                                                                <input type="number"
                                                                    name="settings[sms_otp_length]"
                                                                    value="<?= htmlspecialchars($smsSettings['sms_otp_length']['value']) ?>"
                                                                    min="4" max="8"
                                                                    class="w-20 px-3 py-2 border-2 border-gray-200 rounded-lg text-center text-lg font-bold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                                                                <span class="text-xs text-gray-500">رقم</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- زمان انقضا -->
                                            <?php if (isset($smsSettings['sms_otp_expiry'])): ?>
                                                <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-emerald-400 transition-all duration-200 hover:shadow-md">
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                                            <span class="text-xl">⏱️</span>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <label class="block text-sm font-bold text-gray-800 mb-1">
                                                                زمان انقضای کد
                                                            </label>
                                                            <p class="text-xs text-gray-500 mb-2">
                                                                <?= $smsSettings['sms_otp_expiry']['description'] ?? 'مدت اعتبار کد' ?>
                                                            </p>
                                                            <div class="flex items-center gap-2">
                                                                <input type="number"
                                                                    name="settings[sms_otp_expiry]"
                                                                    value="<?= htmlspecialchars($smsSettings['sms_otp_expiry']['value']) ?>"
                                                                    min="1" max="60"
                                                                    class="w-20 px-3 py-2 border-2 border-gray-200 rounded-lg text-center text-lg font-bold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                                                                <span class="text-xs text-gray-500">دقیقه</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- محدودیت روزانه -->
                                            <?php if (isset($smsSettings['sms_daily_limit'])): ?>
                                                <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-emerald-400 transition-all duration-200 hover:shadow-md">
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-500 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                                            <span class="text-xl">📊</span>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <label class="block text-sm font-bold text-gray-800 mb-1">
                                                                محدودیت روزانه
                                                            </label>
                                                            <p class="text-xs text-gray-500 mb-2">
                                                                <?= $smsSettings['sms_daily_limit']['description'] ?? 'حداکثر پیامک در روز' ?>
                                                            </p>
                                                            <div class="flex items-center gap-2">
                                                                <input type="number"
                                                                    name="settings[sms_daily_limit]"
                                                                    value="<?= htmlspecialchars($smsSettings['sms_daily_limit']['value']) ?>"
                                                                    min="1" max="100"
                                                                    class="w-20 px-3 py-2 border-2 border-gray-200 rounded-lg text-center text-lg font-bold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                                                                <span class="text-xs text-gray-500">پیامک</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- حداکثر تلاش -->
                                            <?php if (isset($smsSettings['sms_otp_attempt_limit'])): ?>
                                                <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-emerald-400 transition-all duration-200 hover:shadow-md">
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                                            <span class="text-xl">🛡️</span>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <label class="block text-sm font-bold text-gray-800 mb-1">
                                                                حداکثر تلاش ورود کد
                                                            </label>
                                                            <p class="text-xs text-gray-500 mb-2">
                                                                <?= $smsSettings['sms_otp_attempt_limit']['description'] ?? 'تعداد دفعات وارد کردن کد اشتباه' ?>
                                                            </p>
                                                            <div class="flex items-center gap-2">
                                                                <input type="number"
                                                                    name="settings[sms_otp_attempt_limit]"
                                                                    value="<?= htmlspecialchars($smsSettings['sms_otp_attempt_limit']['value']) ?>"
                                                                    min="1" max="20"
                                                                    class="w-20 px-3 py-2 border-2 border-gray-200 rounded-lg text-center text-lg font-bold focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                                                                <span class="text-xs text-gray-500">دفعه</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                        </div>

                                        <!-- نوار راهنما در پایین -->
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
                        <?php endif; ?>

                        <!-- ═══════════════════════════════════════════════════════════ -->
                        <!-- 🎵 بخش ویژه: تنظیمات صدای رویدادهای SSE                    -->
                        <!-- ═══════════════════════════════════════════════════════════ -->
                        <?php if ($activeCategory === 'notification'):
                            // اسکن داینامیک صداها با پسوند
                            $soundsDir = PUBLIC_PATH . '/assets/sounds';
                            $availableSounds = [];
                            $extensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];

                            if (is_dir($soundsDir)) {
                                $files = scandir($soundsDir);
                                foreach ($files as $file) {
                                    if ($file === '.' || $file === '..') continue;
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    if (in_array($ext, $extensions)) {
                                        $name = pathinfo($file, PATHINFO_FILENAME);
                                        // 🆕 ذخیره نام با پسوند برای نمایش
                                        $availableSounds[$name] = [
                                            'path' => '/assets/sounds/' . $file,
                                            'filename' => $file,  // 🆕 نام کامل با پسوند
                                            'extension' => $ext,
                                        ];
                                    }
                                }
                                ksort($availableSounds);
                            }

                            // 🆕 تعریف رویدادها با رویدادهای شخصی‌سازی‌شده
                            $sseEvents = [
                                'game_started' => ['label' => 'شروع بازی', 'icon' => '🎮', 'description' => 'وقتی بازی شروع می‌شود', 'color' => 'green'],
                                'round_recorded' => ['label' => 'ثبت دور (تماشاچیان)', 'icon' => '📊', 'description' => 'وقتی یک دور ثبت می‌شود (فقط تماشاچیان)', 'color' => 'blue'],
                                'round_winner' => ['label' => '🏆 برنده دور', 'icon' => '🎉', 'description' => 'وقتی شما یا تیم شما برنده یک دور می‌شوید', 'color' => 'yellow'],
                                'round_loser' => ['label' => '💔 بازنده دور', 'icon' => '😔', 'description' => 'وقتی شما یا تیم شما بازنده یک دور می‌شوید', 'color' => 'red'],
                                'round_undone' => ['label' => 'لغو دور', 'icon' => '↩️', 'description' => 'وقتی آخرین دور لغو می‌شود', 'color' => 'orange'],
                                'game_finished' => ['label' => 'پایان بازی (عمومی)', 'icon' => '🏁', 'description' => 'وقتی بازی تمام می‌شود (پیام عمومی)', 'color' => 'purple'],
                                'game_winner' => ['label' => '🏆 برنده بازی', 'icon' => '🎊', 'description' => 'وقتی شما یا تیم شما برنده بازی می‌شوید', 'color' => 'green'],
                                'game_loser' => ['label' => '💔 بازنده بازی', 'icon' => '😢', 'description' => 'وقتی شما یا تیم شما بازنده بازی می‌شوید', 'color' => 'red'],
                                'game_status_changed' => ['label' => 'تغییر وضعیت بازی', 'icon' => '🔄', 'description' => 'توقف یا ادامه بازی (زیرمجموعه دارد)', 'color' => 'orange', 'has_children' => true],
                                'score_updated' => ['label' => 'به‌روزرسانی امتیاز', 'icon' => '⭐', 'description' => 'وقتی امتیاز تغییر می‌کند', 'color' => 'yellow'],
                                'notification' => ['label' => 'نوتیفیکیشن کاربر', 'icon' => '🔔', 'description' => 'دستاوردها، سطح و...', 'color' => 'pink'],
                                'system_message' => ['label' => 'پیام سیستمی', 'icon' => '📢', 'description' => 'پیام‌های عمومی', 'color' => 'indigo'],
                            ];

                            // گرفتن تنظیمات فعلی
                            $sseSoundSettings = [];
                            foreach ($currentCategory as $s) {
                                if ($s['key'] === 'sse_sound_settings') {
                                    $sseSoundSettings = $s['value'] ?? [];
                                    // اگر string است، decode کن
                                    if (is_string($sseSoundSettings)) {
                                        $sseSoundSettings = json_decode($sseSoundSettings, true) ?: [];
                                    }
                                    break;
                                }
                            }
                            // 🆕 مقادیر پیش‌فرض (شامل رویدادهای شخصی‌سازی‌شده)
                            $defaultSettings = [
                                'game_started' => ['enabled' => true, 'sound' => 'game-start.mp3'],
                                'round_recorded' => ['enabled' => true, 'sound' => 'round-recorded.mp3'],
                                'round_winner' => ['enabled' => true, 'sound' => 'round-win.mp3'],
                                'round_loser' => ['enabled' => true, 'sound' => 'round-lose.mp3'],
                                'round_undone' => ['enabled' => true, 'sound' => 'default.mp3'],
                                'game_finished' => ['enabled' => true, 'sound' => 'default.mp3'],
                                'game_winner' => ['enabled' => true, 'sound' => 'game-win.mp3'],
                                'game_loser' => ['enabled' => true, 'sound' => 'round-lose.mp3'],
                                'game_status_changed' => [
                                    'paused' => ['enabled' => true, 'sound' => 'game-pause.mp3'],
                                    'resumed' => ['enabled' => true, 'sound' => 'game-resume.mp3'],
                                ],
                                'score_updated' => ['enabled' => false, 'sound' => 'default.mp3'],
                                'notification' => ['enabled' => true, 'sound' => 'default.mp3'],
                                'system_message' => ['enabled' => true, 'sound' => 'default.mp3'],
                            ];

                            // 🆕 Merge با مقادیر پیش‌فرض
                            if (empty($sseSoundSettings)) {
                                $sseSoundSettings = $defaultSettings;
                            } else {
                                foreach ($defaultSettings as $key => $defaultValue) {
                                    if (!isset($sseSoundSettings[$key])) {
                                        $sseSoundSettings[$key] = $defaultValue;
                                    }
                                }
                            }
                        ?>
                            <div class="bg-gradient-to-r from-orange-50 via-amber-50 to-yellow-50 border-2 border-orange-300 rounded-2xl p-5 shadow-lg mb-5 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-200/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-amber-200/20 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                                <div class="relative z-10">
                                    <div class="flex items-start justify-between mb-5 pb-4 border-b-2 border-orange-200/50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                                                <span class="text-3xl">🎵</span>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-black text-gray-800 flex items-center gap-2">
                                                    تنظیمات صدای رویدادهای زنده (SSE)
                                                </h3>
                                                <p class="text-xs text-gray-600 font-medium mt-1">
                                                    برای هر رویداد، صدا را انتخاب و فعال/غیرفعال کنید.
                                                    <span class="text-orange-700 font-bold"><?= count($availableSounds) ?></span> فایل صوتی در سیستم یافت شد.
                                                </p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="testAllSSESounds()"
                                            class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-xl text-xs font-bold shadow hover:shadow-md transition">
                                            <span>🎶</span>
                                            <span>تست همه</span>
                                        </button>
                                    </div>

                                    <!-- Hidden input برای ذخیره JSON -->
                                    <input type="hidden" name="settings[sse_sound_settings]" id="sse_sound_settings_input"
                                        value='<?= htmlspecialchars(json_encode($sseSoundSettings, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <?php foreach ($sseEvents as $eventKey => $eventMeta):
                                            if (isset($eventMeta['has_children']) && $eventMeta['has_children']) continue;

                                            $currentEnabled = $sseSoundSettings[$eventKey]['enabled'] ?? true;
                                            $currentSound = $sseSoundSettings[$eventKey]['sound'] ?? 'default';
                                            $color = $eventMeta['color'];
                                        ?>
                                            <div class="group bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-<?= $color ?>-400 transition-all duration-200 hover:shadow-md"
                                                data-sse-event="<?= $eventKey ?>">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-11 h-11 bg-gradient-to-br from-<?= $color ?>-400 to-<?= $color ?>-600 rounded-lg flex items-center justify-center shadow flex-shrink-0 group-hover:scale-110 transition-transform">
                                                        <span class="text-2xl"><?= $eventMeta['icon'] ?></span>
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <div class="mb-2">
                                                            <div class="font-bold text-gray-800 text-sm"><?= $eventMeta['label'] ?></div>
                                                            <div class="text-xs text-gray-500"><?= $eventMeta['description'] ?></div>
                                                        </div>

                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                                                                <input type="checkbox"
                                                                    class="sse-event-enabled w-4 h-4 text-<?= $color ?>-600 rounded focus:ring-<?= $color ?>-500"
                                                                    <?= $currentEnabled ? 'checked' : '' ?>
                                                                    data-event="<?= $eventKey ?>"
                                                                    onchange="updateSSESettings()">
                                                                <span class="text-xs font-bold text-gray-700">فعال</span>
                                                            </label>
                                                            <select class="sse-event-sound flex-1 min-w-[120px] max-w-[220px] px-2 py-1.5 border-2 border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-<?= $color ?>-500 bg-white"
                                                                data-event="<?= $eventKey ?>"
                                                                onchange="updateSSESettings()">
                                                                <?php foreach ($availableSounds as $soundName => $soundData):
                                                                    $soundValue = $soundData['filename'];
                                                                    $isSelected = ($currentSound === $soundValue) ||
                                                                        ($currentSound === $soundName) ||
                                                                        (pathinfo($currentSound, PATHINFO_FILENAME) === $soundName);
                                                                ?>
                                                                    <option value="<?= htmlspecialchars($soundValue) ?>"
                                                                        <?= $isSelected ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($soundData['filename']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>

                                                            <button type="button"
                                                                onclick="playSSETestSound('<?= htmlspecialchars($eventKey) ?>')"
                                                                class="px-2.5 py-1.5 bg-gradient-to-r from-<?= $color ?>-500 to-<?= $color ?>-600 text-white rounded-lg text-xs font-bold shadow hover:shadow-md hover:scale-105 transition flex items-center gap-1"
                                                                title="پخش تست">
                                                                <span>▶</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                        <!-- کارت ویژه: game_status_changed -->
                                        <div class="group bg-white rounded-xl p-4 border-2 border-orange-300 hover:border-orange-500 transition-all duration-200 hover:shadow-md md:col-span-2"
                                            data-sse-event="game_status_changed">
                                            <div class="flex items-start gap-3 mb-3">
                                                <div class="w-11 h-11 bg-gradient-to-br from-orange-400 to-red-600 rounded-lg flex items-center justify-center shadow flex-shrink-0">
                                                    <span class="text-2xl">🔄</span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="font-bold text-gray-800 text-sm">تغییر وضعیت بازی</div>
                                                    <div class="text-xs text-gray-500">برای هر وضعیت، صدای جداگانه تنظیم کنید</div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mr-14">
                                                <?php
                                                $pausedEnabled = $sseSoundSettings['game_status_changed']['paused']['enabled'] ?? true;
                                                $pausedSound = $sseSoundSettings['game_status_changed']['paused']['sound'] ?? 'game-pause';
                                                ?>
                                                <div class="bg-orange-50 rounded-lg p-3 border border-orange-200">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="text-lg">⏸️</span>
                                                        <span class="font-bold text-sm text-gray-800">توقف بازی (Paused)</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                            <input type="checkbox"
                                                                class="sse-event-enabled w-4 h-4 text-orange-600 rounded"
                                                                <?= $pausedEnabled ? 'checked' : '' ?>
                                                                data-event="game_status_changed.paused"
                                                                onchange="updateSSESettings()">
                                                            <span class="text-xs font-bold">فعال</span>
                                                        </label>
                                                        <select class="sse-event-sound flex-1 min-w-[100px] px-2 py-1.5 border-2 border-orange-200 rounded-lg text-xs bg-white"
                                                            data-event="game_status_changed.paused"
                                                            onchange="updateSSESettings()">
                                                            <?php foreach ($availableSounds as $soundName => $soundData):
                                                                $soundValue = $soundData['filename'];
                                                                $isSelected = ($pausedSound === $soundValue) ||
                                                                    ($pausedSound === $soundName) ||
                                                                    (pathinfo($pausedSound, PATHINFO_FILENAME) === $soundName);
                                                            ?>
                                                                <option value="<?= htmlspecialchars($soundValue) ?>"
                                                                    <?= $isSelected ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($soundData['filename']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="button"
                                                            onclick="playSSETestSound('game_status_changed.paused')"
                                                            class="px-2 py-1.5 bg-orange-500 text-white rounded-lg text-xs font-bold hover:bg-orange-600">
                                                            ▶
                                                        </button>
                                                    </div>
                                                </div>

                                                <?php
                                                $resumedEnabled = $sseSoundSettings['game_status_changed']['resumed']['enabled'] ?? true;
                                                $resumedSound = $sseSoundSettings['game_status_changed']['resumed']['sound'] ?? 'game-resume';
                                                ?>
                                                <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="text-lg">▶️</span>
                                                        <span class="font-bold text-sm text-gray-800">ادامه بازی (Resumed)</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                            <input type="checkbox"
                                                                class="sse-event-enabled w-4 h-4 text-green-600 rounded"
                                                                <?= $resumedEnabled ? 'checked' : '' ?>
                                                                data-event="game_status_changed.resumed"
                                                                onchange="updateSSESettings()">
                                                            <span class="text-xs font-bold">فعال</span>
                                                        </label>
                                                        <select class="sse-event-sound flex-1 min-w-[100px] px-2 py-1.5 border-2 border-green-200 rounded-lg text-xs bg-white"
                                                            data-event="game_status_changed.resumed"
                                                            onchange="updateSSESettings()">
                                                            <?php foreach ($availableSounds as $soundName => $soundData):
                                                                $soundValue = $soundData['filename'];
                                                                $isSelected = ($resumedSound === $soundValue) ||
                                                                    ($resumedSound === $soundName) ||
                                                                    (pathinfo($resumedSound, PATHINFO_FILENAME) === $soundName);
                                                            ?>
                                                                <option value="<?= htmlspecialchars($soundValue) ?>"
                                                                    <?= $isSelected ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($soundData['filename']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="button"
                                                            onclick="playSSETestSound('game_status_changed.resumed')"
                                                            class="px-2 py-1.5 bg-green-500 text-white rounded-lg text-xs font-bold hover:bg-green-600">
                                                            ▶
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                        <div class="flex items-start gap-2">
                                            <span class="text-lg flex-shrink-0">💡</span>
                                            <div class="text-xs text-blue-800 space-y-1">
                                                <p class="font-bold">راهنما:</p>
                                                <ul class="list-disc list-inside space-y-0.5 text-blue-700">
                                                    <li>صداها به صورت داینامیک از پوشه <code class="bg-blue-100 px-1 rounded">public/assets/sounds/</code> خوانده می‌شوند</li>
                                                    <li>فرمت‌های پشتیبانی‌شده: MP3, WAV, OGG, M4A, AAC, WebM</li>
                                                    <li>🏆 <strong>رویدادهای شخصی‌سازی‌شده:</strong> برنده/بازنده دور و بازی اعلان‌های جداگانه دارند</li>
                                                    <li>تنظیمات برای همه کاربران سایت اعمال می‌شود</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <audio id="sse-test-audio" preload="auto"></audio>

                            <script>
                                const availableSounds = <?= json_encode($availableSounds, JSON_UNESCAPED_UNICODE) ?>;

                                function updateSSESettings() {
                                    const settings = {};

                                    // رویدادهای ساده
                                    document.querySelectorAll('[data-sse-event]').forEach(card => {
                                        const eventKey = card.dataset.sseEvent;
                                        if (eventKey === 'game_status_changed') return;

                                        const enabled = card.querySelector('.sse-event-enabled')?.checked ?? true;
                                        const sound = card.querySelector('.sse-event-sound')?.value ?? 'default.mp3';

                                        settings[eventKey] = {
                                            enabled,
                                            sound
                                        };
                                    });

                                    // game_status_changed با زیرمجموعه
                                    settings.game_status_changed = {
                                        paused: {
                                            enabled: document.querySelector('[data-event="game_status_changed.paused"]')?.checked ?? true,
                                            sound: document.querySelector('select[data-event="game_status_changed.paused"]')?.value ?? 'game-pause.mp3'
                                        },
                                        resumed: {
                                            enabled: document.querySelector('[data-event="game_status_changed.resumed"]')?.checked ?? true,
                                            sound: document.querySelector('select[data-event="game_status_changed.resumed"]')?.value ?? 'game-resume.mp3'
                                        }
                                    };

                                    document.getElementById('sse_sound_settings_input').value = JSON.stringify(settings);

                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'info',
                                            title: 'تغییرات در حافظه - برای ذخیره دکمه پایین را بزنید',
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    }
                                }

                                function playSSETestSound(eventKey) {
                                    let soundName = 'default';

                                    if (eventKey.includes('.')) {
                                        const select = document.querySelector(`select[data-event="${eventKey}"]`);
                                        soundName = select?.value ?? 'default.mp3';
                                    } else {
                                        const select = document.querySelector(`[data-sse-event="${eventKey}"] .sse-event-sound`);
                                        soundName = select?.value ?? 'default.mp3';
                                    }

                                    // 🆕 استفاده از availableSounds برای پیدا کردن path
                                    const soundData = availableSounds[soundName];
                                    let audioUrl;

                                    if (soundData && soundData.path) {
                                        audioUrl = soundData.path;
                                    } else {
                                        // اگر soundName فرمت دارد
                                        if (soundName.includes('.')) {
                                            audioUrl = '/assets/sounds/' + soundName;
                                        } else {
                                            audioUrl = '/assets/sounds/' + soundName + '.mp3';
                                        }
                                    }

                                    const audio = document.getElementById('sse-test-audio');
                                    audio.src = audioUrl;
                                    audio.volume = 0.7;
                                    audio.currentTime = 0;

                                    audio.play().catch(err => {
                                        console.error('Play error:', err);
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'error',
                                                title: 'خطا در پخش صدا',
                                                text: 'URL: ' + audioUrl,
                                                showConfirmButton: false,
                                                timer: 3000
                                            });
                                        }
                                    });

                                    const btn = event.target.closest('button');
                                    if (btn) {
                                        btn.classList.add('scale-125');
                                        setTimeout(() => btn.classList.remove('scale-125'), 300);
                                    }
                                }

                                function testAllSSESounds() {
                                    const sounds = new Set();
                                    document.querySelectorAll('.sse-event-sound').forEach(select => {
                                        sounds.add(select.value);
                                    });

                                    const soundsArray = Array.from(sounds);
                                    let index = 0;

                                    function playNext() {
                                        if (index >= soundsArray.length) {
                                            if (typeof Swal !== 'undefined') {
                                                Swal.fire({
                                                    toast: true,
                                                    position: 'top-end',
                                                    icon: 'success',
                                                    title: 'تست همه صداها تمام شد',
                                                    showConfirmButton: false,
                                                    timer: 2000
                                                });
                                            }
                                            return;
                                        }

                                        const soundName = soundsArray[index];
                                        const soundData = availableSounds[soundName];
                                        if (soundData) {
                                            const audio = document.getElementById('sse-test-audio');
                                            audio.src = soundData.path;
                                            audio.volume = 0.7;
                                            audio.play().catch(() => {});
                                            setTimeout(() => {
                                                index++;
                                                playNext();
                                            }, 2500);
                                        } else {
                                            index++;
                                            playNext();
                                        }
                                    }

                                    playNext();
                                }
                            </script>
                        <?php endif; ?>

                        <?php
                        // تعریف کلیدهای SMS که باید در باکس ویژه نمایش داده شوند
                        $smsKeys = ['sms_enabled', 'sms_otp_length', 'sms_otp_expiry', 'sms_daily_limit', 'sms_otp_attempt_limit'];
                        // 🆕 کلید تنظیمات صدای SSE که باید در باکس ویژه نمایش داده شود
                        $sseSoundKeys = ['sse_sound_settings'];

                        foreach ($currentCategory as $setting): ?>
                            <?php
                            if ($activeCategory === 'game' && $setting['key'] === 'first_player_selection') continue;
                            if ($activeCategory === 'security' && $setting['key'] === 'auth_method') continue;
                            // 🆕 رد کردن کلیدهای SMS از لیست عمومی (چون در باکس ویژه نمایش داده می‌شوند)
                            if ($activeCategory === 'security' && in_array($setting['key'], $smsKeys)) continue;
                            // 🆕 رد کردن کلید تنظیمات صدای SSE از لیست عمومی
                            if ($activeCategory === 'notification' && in_array($setting['key'], $sseSoundKeys)) continue;
                            ?>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start p-4 bg-gray-50/80 rounded-2xl  border-gray-200 hover:border-indigo-300 transition-all duration-200 hover:shadow-md">
                                <div><label class="block text-sm font-black text-gray-800 mb-1"><?= htmlspecialchars(str_replace('_', ' ', $setting['key'])) ?></label><?php if (!empty($setting['description'])): ?><p class="text-xs text-gray-500 font-medium"><?= htmlspecialchars($setting['description']) ?></p><?php endif; ?></div>
                                <div class="md:col-span-2">
                                    <?php if ($setting['type'] === 'boolean'): ?>
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="hidden" name="settings[<?= $setting['key'] ?>]" value="0">
                                            <input type="checkbox" name="settings[<?= $setting['key'] ?>]" value="1" <?= $setting['value'] ? 'checked' : '' ?> class="w-5 h-5 text-<?= $catInfo['color'] ?>-600 rounded focus:ring-<?= $catInfo['color'] ?>-500">
                                            <span class="text-sm font-bold text-gray-700">فعال</span>
                                        </label>
                                    <?php elseif ($setting['type'] === 'integer'): ?>
                                        <input type="number" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" class="w-full max-w-xs px-3 py-2.5  border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                                    <?php elseif ($setting['type'] === 'float'): ?>
                                        <input type="number" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" step="0.01" class="w-full max-w-xs px-3 py-2.5  border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                                    <?php else: ?>
                                        <input type="text" name="settings[<?= $setting['key'] ?>]" value="<?= htmlspecialchars($setting['value']) ?>" class="w-full max-w-xs px-3 py-2.5  border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-<?= $catInfo['color'] ?>-500 focus:ring-2 focus:ring-<?= $catInfo['color'] ?>-200 transition-all duration-200">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-6 flex items-center justify-between bg-gray-50/80 rounded-2xl p-4  border-gray-200 shadow-sm">
                        <div class="text-sm font-bold text-gray-600">💾 تغییرات ذخیره نشده دارید</div>
                        <button type="submit" class="px-6 py-2.5 bg-<?= $catInfo['color'] ?>-600 hover:bg-<?= $catInfo['color'] ?>-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">💾 ذخیره تنظیمات</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function settingsManager() {
        return {
            init() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('saved') === '1') {
                    this.showToast('تنظیمات با موفقیت ذخیره شد', 'success');
                    urlParams.delete('saved');
                    const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                    window.history.replaceState({}, '', newUrl);
                }
            },
            showToast(message, type = 'info') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: type,
                        title: message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            }
        };
    }
</script>