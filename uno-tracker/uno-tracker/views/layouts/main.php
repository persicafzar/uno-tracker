<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title><?= htmlspecialchars($title ?? 'UNO Tracker') ?></title>

    <!-- PWA & Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <meta name="background-color" content="#0f172a">
    <link rel="apple-touch-icon" href="/assets/images/icons/icon-192x192.png">
    <link rel="mask-icon" href="/assets/images/icons/icon-512x512.png" color="#4f46e5">
    <meta name="msapplication-TileColor" content="#4f46e5">

    <!-- لوگو -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/logo.svg">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="/assets/css/sweetalert2.min.css">

    <!-- Tailwind CSS (Local) -->
    <script src="/assets/js/tailwind.js"></script>

    <!-- HTMX (Local) -->
    <script src="/assets/js/htmx.min.js"></script>

    <!-- Alpine.js (Local) -->
    <script defer src="/assets/js/alpine.min.js"></script>

    <!-- SweetAlert2 JS (Local) -->
    <script src="/assets/js/sweetalert2.min.js"></script>

    <!-- Chart.js (Local) -->
    <script src="/assets/js/chart.min.js"></script>

    <!-- Mobile CSS -->
    <link rel="stylesheet" href="/assets/css/mobile.css">

    <!-- Notification Styles -->
    <link rel="stylesheet" href="/assets/css/notifications.css">



    <style>
        [x-cloak] {
            display: none !important;
        }

        @font-face {
            font-family: 'Vazir';
            src: url('/assets/fonts/Vazir.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        * {
            font-family: 'Vazir', Tahoma, Arial, sans-serif;
        }

        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
        }

        @supports (padding: max(0px)) {
            body {
                padding-left: env(safe-area-inset-left);
                padding-right: env(safe-area-inset-right);
            }
        }

        @keyframes soundPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .sound-btn-active {
            animation: soundPulse 0.3s ease-out;
        }

        .htmx-indicator {
            display: none;
            opacity: 0;
            transition: opacity 200ms ease-in;
        }

        .htmx-request .htmx-indicator,
        .htmx-request.htmx-indicator {
            display: block;
            opacity: 1;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #6366f1, #8b5cf6);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #4f46e5, #7c3aed);
        }

        .touch-manipulation {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }

        html,
        body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        .container,
        .max-w-7xl,
        .max-w-6xl,
        .max-w-5xl,
        .max-w-4xl,
        .max-w-3xl,
        .max-w-2xl,
        .max-w-xl,
        .max-w-lg,
        .max-w-md {
            max-width: 100%;
        }

        @media (max-width: 640px) {
            .min-w-max {
                min-width: 0 !important;
            }

            .w-max {
                width: auto !important;
            }

            .overflow-x-auto {
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
    <?php
    // 🎵 بارگذاری تنظیمات صدای SSE و انتقال به JavaScript
    $sseSoundConfig = [];
    try {
        $settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
        $rawConfig = $settingsRepo->get('sse_sound_settings', []);

        // 🎯 مرحله ۱: Decode چندلایه با مدیریت escaped JSON
        $sseSoundConfig = $rawConfig;
        $maxAttempts = 5;
        $attempt = 0;

        while (is_string($sseSoundConfig) && $attempt < $maxAttempts) {
            // 🆕 ابتدا stripslashes برای حذف backslash
            $cleanConfig = stripslashes($sseSoundConfig);

            // اگر هنوز string با quote بود، دوباره stripslashes
            if (strlen($cleanConfig) > 0 && $cleanConfig[0] === '"') {
                $cleanConfig = stripslashes($cleanConfig);
            }

            $decoded = json_decode($cleanConfig, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $sseSoundConfig = $decoded;
                error_log("✅ SSE config decoded at attempt {$attempt}");
            } else {
                // اگر با stripslashes کار نکرد، مقدار اصلی را امتحان کن
                $decoded = json_decode($sseSoundConfig, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $sseSoundConfig = $decoded;
                    error_log("✅ SSE config decoded (original) at attempt {$attempt}");
                } else {
                    error_log("⚠️ Decode failed at attempt {$attempt}: " . json_last_error_msg());
                    break;
                }
            }
            $attempt++;
        }

        // 🎯 مرحله ۲: اگر بعد از decode باز هم آرایه نشد، از default استفاده کن
        if (!is_array($sseSoundConfig) || empty($sseSoundConfig)) {
            error_log("⚠️ SSE_SOUND_CONFIG not valid array. Type: " . gettype($sseSoundConfig));
            $sseSoundConfig = [];
        }

        // 🎯 مرحله ۳: مقادیر پیش‌فرض کامل (شامل همه رویدادهای شخصی‌سازی‌شده)
        $defaultConfig = [
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

        // 🎯 مرحله ۴: Merge هوشمند - کلیدهای گم‌شده را از default اضافه کن
        foreach ($defaultConfig as $key => $defaultValue) {
            if (!isset($sseSoundConfig[$key])) {
                $sseSoundConfig[$key] = $defaultValue;
            }
            // برای game_status_changed (زیرمجموعه)
            if ($key === 'game_status_changed' && is_array($defaultValue)) {
                if (!isset($sseSoundConfig[$key]) || !is_array($sseSoundConfig[$key])) {
                    $sseSoundConfig[$key] = $defaultValue;
                } else {
                    foreach ($defaultValue as $subKey => $subDefaultValue) {
                        if (!isset($sseSoundConfig[$key][$subKey])) {
                            $sseSoundConfig[$key][$subKey] = $subDefaultValue;
                        }
                    }
                }
            }
        }

        error_log("✅ Final SSE config keys: " . implode(', ', array_keys($sseSoundConfig)));

        // اسکن صداها برای SoundManager
        $soundsDir = PUBLIC_PATH . '/assets/sounds';
        $soundFiles = ['default' => '/assets/sounds/default.mp3'];
        if (is_dir($soundsDir)) {
            $files = scandir($soundsDir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'])) {
                    $name = pathinfo($file, PATHINFO_FILENAME);
                    $soundFiles[$name] = '/assets/sounds/' . $file;
                }
            }
        }
    } catch (\Throwable $e) {
        error_log("❌ SSE Sound Config Error: " . $e->getMessage());
        $sseSoundConfig = [];
        $soundFiles = ['default' => '/assets/sounds/default.mp3'];
    }
    ?>

    <!-- 🎵 تنظیمات صدای SSE - انتقال از PHP به JS -->
    <script>
        window.SSE_SOUND_CONFIG = <?= json_encode($sseSoundConfig, JSON_UNESCAPED_UNICODE) ?>;
        window.SOUND_FILES = <?= json_encode($soundFiles, JSON_UNESCAPED_UNICODE) ?>;

        // 🆕 Debug: نمایش در کنسول
        console.log('🎵 SSE_SOUND_CONFIG loaded:', window.SSE_SOUND_CONFIG);
        console.log('🎵 Type of SSE_SOUND_CONFIG:', typeof window.SSE_SOUND_CONFIG);
        console.log('🎵 Is Array:', Array.isArray(window.SSE_SOUND_CONFIG));
        console.log('🎵 Available keys:', Object.keys(window.SSE_SOUND_CONFIG || {}));
        console.log('🎵 SOUND_FILES loaded:', Object.keys(window.SOUND_FILES || {}).length, 'files');

        // 🆕 بررسی وجود کلیدهای مهم
        const requiredKeys = ['round_undone', 'round_winner', 'round_loser', 'game_winner', 'game_loser', 'round_recorded'];
        requiredKeys.forEach(key => {
            if (window.SSE_SOUND_CONFIG && window.SSE_SOUND_CONFIG[key]) {
                console.log(`✅ ${key}:`, window.SSE_SOUND_CONFIG[key]);
            } else {
                console.warn(`⚠️ ${key} MISSING!`);
            }
        });
    </script>

    <!-- Sound Manager -->
    <script src="/assets/js/sound-manager.js"></script>
</head>

<body class="bg-gray-50/80" x-data="{ mobileMenuOpen: false }" x-cloak>

    <!-- ========================================== -->
    <!-- ======= Navigation (همیشه فیکس در بالا) ======= -->
    <!-- ========================================== -->
    <nav class="bg-white/90 backdrop-blur-sm shadow-md border-b-2 border-gray-200/50 fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-4">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <img src="/assets/images/logo.svg"
                        alt="UNO Tracker"
                        class="w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0 drop-shadow">
                    <span class="text-xl sm:text-2xl font-black bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 bg-clip-text text-transparent tracking-tight">
                        UNO Tracker
                    </span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-1 sm:gap-2">
                    <a href="/dashboard"
                        class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm <?= ($currentPath ?? '') === '/dashboard' ? 'bg-indigo-50 text-indigo-600' : '' ?>">
                        <span>📊</span>
                        <span>داشبورد</span>
                    </a>
                    <a href="/games"
                        class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm <?= ($currentPath ?? '') === '/games' ? 'bg-indigo-50 text-indigo-600' : '' ?>">
                        <span>📋</span>
                        <span>بازی‌ها</span>
                    </a>
                    <a href="/users"
                        class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm <?= ($currentPath ?? '') === '/users' ? 'bg-indigo-50 text-indigo-600' : '' ?>">
                        <span>👥</span>
                        <span>بازیکنان</span>
                    </a>
                    <a href="/achievements"
                        class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm">
                        <span class="text-lg">🏆</span>
                        <span>دستاوردها</span>
                    </a>
                    <?php if ($canCreate): ?>
                        <a href="/game/create"
                            class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm">
                            <span>🎮</span>
                            <span>بازی جدید</span>
                        </a>
                    <?php endif; ?>
                    <a href="/profile"
                        class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm">
                        <span>👤</span>
                        <span>پروفایل</span>
                    </a>

                    <button
                        onclick="toggleSoundButton(this)"
                        id="sound-toggle-btn-desktop"
                        class="p-2.5 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-200 flex items-center gap-1 touch-manipulation"
                        title="قطع/وصل صدا">
                        <span id="sound-icon-desktop" class="text-xl">🔊</span>
                    </button>

                    <div class="w-px h-7 bg-gray-300/60"></div>

                    <?php if ($isAdmin): ?>
                        <a href="/admin"
                            class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 hover:from-purple-200 hover:to-pink-200 transition-all duration-200 font-bold text-sm shadow-sm hover:shadow-md border-2 border-purple-200/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>پنل مدیریت</span>
                        </a>
                    <?php endif; ?>

                    <form method="POST" action="/logout" class="inline">
                        <button type="submit"
                            class="px-3.5 py-2.5 text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-all duration-200 font-bold text-sm touch-manipulation">
                            خروج
                        </button>
                    </form>
                </div>

                <!-- Mobile Menu Button -->
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden p-3 rounded-xl text-gray-700 hover:bg-gray-100 active:bg-gray-200 transition-all duration-200 flex-shrink-0 touch-manipulation"
                    style="min-width: 48px; min-height: 48px;"
                    aria-label="باز و بسته کردن منو">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- ========================================== -->
    <!-- ======= Mobile Menu Sidebar ======= -->
    <!-- ========================================== -->

    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileMenuOpen = false"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40">
    </div>

    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 w-72 bg-white shadow-2xl overflow-y-auto border-l-2 border-gray-200/50 z-50">

        <div class="flex items-center justify-between p-4 border-b-2 border-gray-200/60">
            <div class="flex items-center gap-2.5">
                <img src="/assets/images/logo.svg" alt="UNO Tracker" class="w-9 h-9 drop-shadow">
                <span class="font-black text-lg bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">منو</span>
            </div>
            <button
                @click="mobileMenuOpen = false"
                class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-all duration-200 touch-manipulation"
                style="min-width: 44px; min-height: 44px;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-3 space-y-1">
            <a href="/dashboard"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                <span class="text-xl">📊</span>
                <span>داشبورد</span>
            </a>
            <a href="/games"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold <?= ($currentPath ?? '') === '/games' ? 'bg-indigo-50 text-indigo-600' : '' ?> touch-manipulation">
                <span class="text-xl">📋</span>
                <span>بازی‌ها</span>
            </a>
            <a href="/users"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold <?= ($currentPath ?? '') === '/users' ? 'bg-indigo-50 text-indigo-600' : '' ?> touch-manipulation">
                <span class="text-xl">👥</span>
                <span>بازیکنان</span>
            </a>
            <a href="/achievements"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                <span class="text-xl">🏆</span>
                <span>دستاوردها</span>
            </a>
            <?php if ($canCreate): ?>
                <a href="/game/create"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                    <span class="text-xl">🎮</span>
                    <span>بازی جدید</span>
                </a>
            <?php endif; ?>
            <a href="/profile"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                <span class="text-xl">👤</span>
                <span>پروفایل</span>
            </a>

            <button
                onclick="toggleSoundButton(this)"
                id="sound-toggle-btn-mobile"
                @click="mobileMenuOpen = false"
                class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                <span id="sound-icon-mobile" class="text-xl">🔊</span>
                <span>صدا</span>
                <span id="sound-status-mobile" class="mr-auto text-xs text-green-600 font-bold">فعال</span>
            </button>

            <div class="my-2 border-t-2 border-gray-200/60"></div>

            <?php if ($isAdmin): ?>
                <a href="/admin"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 hover:from-purple-200 hover:to-pink-200 transition-all duration-200 font-bold border-2 border-purple-200/50 touch-manipulation">
                    <span class="text-xl">⚙️</span>
                    <span>پنل مدیریت</span>
                </a>
            <?php endif; ?>

            <form method="POST" action="/logout" @submit="mobileMenuOpen = false">
                <button type="submit"
                    class="w-full text-right flex items-center gap-3 px-4 py-3 text-rose-600 hover:bg-rose-50 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                    <span class="text-xl">🚪</span>
                    <span>خروج</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ======= Main Content (با padding-top برای جلوگیری از زیر رفتن محتوا زیر نوار ناوبری) ======= -->
    <!-- ========================================== -->
    <main class="mt-16 py-6">
        <?= $content ?>
    </main>

    <!-- ========================================== -->
    <!-- ======= Footer ======= -->
    <!-- ========================================== -->
    <footer class="bg-white/90 backdrop-blur-sm border-t-2 border-gray-200/50 mt-auto py-5 sm:py-7">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 text-center text-gray-600 text-xs sm:text-sm">
            <p class="font-bold">UNO Tracker v1.0</p>
            <p class="mt-1 font-medium">
                طراحی و توسعه توسط
                <span class="font-black bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 bg-clip-text text-transparent">
                    حسن حمیدی قمر
                </span>
            </p>
            <p class="mt-1 text-xs text-gray-400 font-medium">Built with PHP + HTMX + Alpine.js + Tailwind CSS</p>

            <div class="mt-4">
                <button id="install-app-btn"
                    style="display: none;"
                    class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2">
                    📱 نصب برنامه روی گوشی
                </button>
            </div>
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- ======= Sound Control Script ======= -->
    <!-- ========================================== -->
    <script>
        function toggleSoundButton(btn) {
            if (!window.SoundManager) {
                console.warn('⚠️ SoundManager not loaded');
                return;
            }
            const enabled = window.SoundManager.toggle();
            const desktopIcon = document.getElementById('sound-icon-desktop');
            if (desktopIcon) desktopIcon.textContent = enabled ? '🔊' : '🔇';
            const mobileIcon = document.getElementById('sound-icon-mobile');
            if (mobileIcon) mobileIcon.textContent = enabled ? '🔊' : '🔇';
            const mobileStatus = document.getElementById('sound-status-mobile');
            if (mobileStatus) {
                mobileStatus.textContent = enabled ? 'فعال' : 'غیرفعال';
                mobileStatus.className = 'mr-auto text-xs font-bold ' + (enabled ? 'text-green-600' : 'text-rose-600');
            }
            if (btn) {
                btn.classList.add('sound-btn-active');
                setTimeout(() => btn.classList.remove('sound-btn-active'), 300);
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: enabled ? 'success' : 'info',
                    title: enabled ? '🔊 صدا فعال شد' : '🔇 صدا غیرفعال شد',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
            }
            console.log(`🔊 Sound ${enabled ? 'enabled' : 'disabled'}`);
        }

        function updateAllSoundButtons() {
            if (!window.SoundManager) return;
            const enabled = window.SoundManager.enabled;
            const desktopIcon = document.getElementById('sound-icon-desktop');
            if (desktopIcon) desktopIcon.textContent = enabled ? '🔊' : '🔇';
            const mobileIcon = document.getElementById('sound-icon-mobile');
            if (mobileIcon) mobileIcon.textContent = enabled ? '🔊' : '🔇';
            const mobileStatus = document.getElementById('sound-status-mobile');
            if (mobileStatus) {
                mobileStatus.textContent = enabled ? 'فعال' : 'غیرفعال';
                mobileStatus.className = 'mr-auto text-xs font-bold ' + (enabled ? 'text-green-600' : 'text-rose-600');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateAllSoundButtons();
        });
    </script>

    <!-- 🆕 SSE Client برای نوتیفیکیشن‌های کاربر -->
    <?php if (isset($currentUser) || (isset($user) && !empty($user['id']))): ?>
        <script src="/assets/js/sse-client.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.SSE) {
                    // اتصال به کانال نوتیفیکیشن
                    window.SSE.connect('notifications', '/sse/notifications');

                    // 🎵 هندلر نوتیفیکیشن با صدا
                    window.SSE.on('notifications', 'notification', (data) => {
                        console.log('🔔 نوتیفیکیشن جدید:', data);

                        // 🎵 پخش صدای نوتیفیکیشن
                        if (window.SoundManager) {
                            window.SoundManager.playForEvent('notification', data);
                        }

                        showUserNotification(data);
                    });

                    // 🎵 هندلر پیام سیستمی با صدا
                    window.SSE.on('notifications', 'system_message', (data) => {
                        console.log('📢 پیام سیستمی:', data);

                        if (window.SoundManager) {
                            window.SoundManager.playForEvent('system_message', data);
                        }

                        showUserNotification(data);
                    });

                    window.SSE.startHeartbeat(window.location.pathname);
                }
            });

            function showUserNotification(data) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: data.type || 'info',
                        title: data.title || data.message,
                        text: data.description || '',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                    });
                }
            }
        </script>
    <?php endif; ?>

    <!-- مدیریت هوشمند خطاهای احراز هویت در HTMX -->
    <script>
        document.body.addEventListener('htmx:responseError', function(evt) {
            if (evt.detail.xhr.status === 401 || evt.detail.xhr.status === 403) {
                try {
                    const response = JSON.parse(evt.detail.xhr.responseText);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '/login';
                    }
                } catch (e) {
                    window.location.href = '/login';
                }
            }
        });
    </script>

    <!-- ========================================== -->
    <!-- ======= پردازش مجدد Alpine بعد از HTMX ======= -->
    <!-- ========================================== -->
    <script>
        (function() {
            function reinitAlpine() {
                if (typeof Alpine !== 'undefined' && Alpine.initTree) {
                    Alpine.initTree(document.body);
                    document.querySelectorAll('[x-data]').forEach(el => {
                        Alpine.initTree(el);
                    });
                    console.log('🔄 Alpine reinitialized after HTMX swap');
                }
            }

            document.body.addEventListener('htmx:afterSwap', function(evt) {
                if (evt.detail.target && typeof Alpine !== 'undefined' && Alpine.initTree) {
                    Alpine.initTree(evt.detail.target);
                    console.log('🔄 Alpine processed on swapped target');
                }
                reinitAlpine();
            });

            document.body.addEventListener('htmx:afterSettle', function(evt) {
                reinitAlpine();
            });

            document.body.addEventListener('htmx:afterOnLoad', function(evt) {
                reinitAlpine();
            });

            console.log('✅ Alpine reinit script installed');
        })();
    </script>

    <!-- ========================================== -->
    <!-- ======= PWA Scripts ======= -->
    <!-- ========================================== -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('✅ Service Worker registered:', registration);
                    })
                    .catch(function(error) {
                        console.log('❌ Service Worker registration failed:', error);
                    });
            });
        }

        let deferredPrompt = null;
        const installBtn = document.getElementById('install-app-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (installBtn) {
                installBtn.style.display = 'inline-flex';
                console.log('📱 Install button displayed');
            }
        });

        window.addEventListener('appinstalled', (evt) => {
            console.log('✅ App installed successfully');
            deferredPrompt = null;
            if (installBtn) {
                installBtn.style.display = 'none';
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '🎉 نصب شد!',
                    text: 'UNO Tracker با موفقیت روی دستگاه شما نصب شد.',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        });

        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const result = await deferredPrompt.userChoice;
                    console.log(`📱 User choice: ${result.outcome}`);
                    if (result.outcome === 'accepted') {
                        console.log('✅ User accepted the install prompt');
                    } else {
                        console.log('❌ User dismissed the install prompt');
                    }
                    deferredPrompt = null;
                    installBtn.style.display = 'none';
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: '📱 نصب برنامه',
                            text: 'برای نصب، از منوی مرورگر گزینه "Add to Home Screen" یا "Install App" را انتخاب کنید.',
                            confirmButtonColor: '#4f46e5'
                        });
                    } else {
                        alert('برای نصب، از منوی مرورگر گزینه "Add to Home Screen" را انتخاب کنید.');
                    }
                }
            });
        }

        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('📱 App is running in standalone mode (installed)');
            if (installBtn) {
                installBtn.style.display = 'none';
            }
        }

        window.addEventListener('resize', () => {
            if (window.matchMedia('(display-mode: standalone)').matches) {
                if (installBtn) {
                    installBtn.style.display = 'none';
                }
            }
        });

        console.log('🚀 PWA Scripts initialized');
    </script>
</body>

</html>