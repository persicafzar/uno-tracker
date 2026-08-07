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
    <link rel="icon" type="image/svg+xml" href="/assets/images/logo.svg">

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/css/mobile.css">
    <link rel="stylesheet" href="/assets/css/notifications.css">

    <!-- JS -->
    <script src="/assets/js/tailwind.js"></script>
    <script src="/assets/js/htmx.min.js"></script>
    <script defer src="/assets/js/alpine.min.js"></script>
    <script src="/assets/js/sweetalert2.min.js"></script>
    <script src="/assets/js/chart.min.js"></script>
    <script src="/assets/js/sound-manager.js"></script>

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

        /* 🆕 جلوگیری از مشکل overflow در drawer و modal */
        body.no-scroll {
            overflow: hidden !important;
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
    // 🎵 بارگذاری تنظیمات صدای SSE
    $sseSoundConfig = [];
    try {
        $settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
        $rawConfig = $settingsRepo->get('sse_sound_settings', []);

        $sseSoundConfig = $rawConfig;
        $maxAttempts = 5;
        $attempt = 0;
        while (is_string($sseSoundConfig) && $attempt < $maxAttempts) {
            $decoded = json_decode($sseSoundConfig, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $sseSoundConfig = $decoded;
            } else {
                break;
            }
            $attempt++;
        }

        if (!is_array($sseSoundConfig) || empty($sseSoundConfig)) {
            $sseSoundConfig = [];
        }

        $defaultConfig = [
            'game_started' => ['enabled' => true, 'sound' => 'game-start'],
            'round_recorded' => ['enabled' => true, 'sound' => 'round-recorded'],
            'round_winner' => ['enabled' => true, 'sound' => 'round-win'],
            'round_loser' => ['enabled' => true, 'sound' => 'round-lose'],
            'round_undone' => ['enabled' => true, 'sound' => 'default'],
            'game_finished' => ['enabled' => true, 'sound' => 'game-win'],
            'game_winner' => ['enabled' => true, 'sound' => 'game-win'],
            'game_loser' => ['enabled' => true, 'sound' => 'round-lose'],
            'game_status_changed' => [
                'paused' => ['enabled' => true, 'sound' => 'game-pause'],
                'resumed' => ['enabled' => true, 'sound' => 'game-resume'],
            ],
            'score_updated' => ['enabled' => false, 'sound' => 'default'],
            'notification' => ['enabled' => true, 'sound' => 'default'],
            'system_message' => ['enabled' => true, 'sound' => 'default'],
        ];

        foreach ($defaultConfig as $key => $defaultValue) {
            if (!isset($sseSoundConfig[$key])) {
                $sseSoundConfig[$key] = $defaultValue;
            }
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
        error_log("SSE Sound Config Error: " . $e->getMessage());
        $sseSoundConfig = [];
        $soundFiles = ['default' => '/assets/sounds/default.mp3'];
    }
    ?>

    <script>
        window.SSE_SOUND_CONFIG = <?= json_encode($sseSoundConfig, JSON_UNESCAPED_UNICODE) ?>;
        window.SOUND_FILES = <?= json_encode($soundFiles, JSON_UNESCAPED_UNICODE) ?>;
    </script>
</head>

<body class="bg-gray-50/80" x-data="{ mobileMenuOpen: false, profileDrawerOpen: false }" x-cloak>

    <!-- ========================================== -->
    <!-- ======= Navigation ======= -->
    <!-- ========================================== -->
    <nav class="bg-white/90 backdrop-blur-sm shadow-md border-b-2 border-gray-200/50 fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-4">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <!-- Logo -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <img src="/assets/images/logo.svg" alt="UNO Tracker"
                        class="w-10 h-10 sm:w-12 sm:h-12 flex-shrink-0 drop-shadow">
                    <span class="text-xl sm:text-2xl font-black bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 bg-clip-text text-transparent tracking-tight">
                        UNO Tracker
                    </span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-1 sm:gap-2">
                    <a href="/dashboard" class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm <?= ($currentPath ?? '') === '/dashboard' ? 'bg-indigo-50 text-indigo-600' : '' ?>">
                        <span>📊</span><span>داشبورد</span>
                    </a>
                    <a href="/games" class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm <?= ($currentPath ?? '') === '/games' ? 'bg-indigo-50 text-indigo-600' : '' ?>">
                        <span>📋</span><span>بازی‌ها</span>
                    </a>
                    <a href="/users" class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm <?= ($currentPath ?? '') === '/users' ? 'bg-indigo-50 text-indigo-600' : '' ?>">
                        <span>👥</span><span>بازیکنان</span>
                    </a>
                    <a href="/achievements" class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm">
                        <span class="text-lg">🏆</span><span>دستاوردها</span>
                    </a>
                    <?php if ($canCreate ?? false): ?>
                        <a href="/game/create" class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm">
                            <span>🎮</span><span>بازی جدید</span>
                        </a>
                    <?php endif; ?>
                    <a href="/profile" class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 transition-all duration-200 font-bold text-sm">
                        <span>👤</span><span>پروفایل</span>
                    </a>
                    <button onclick="toggleSoundButton(this)" id="sound-toggle-btn-desktop"
                        class="p-2.5 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-200 flex items-center gap-1 touch-manipulation"
                        title="قطع/وصل صدا">
                        <span id="sound-icon-desktop" class="text-xl">🔊</span>
                    </button>
                    <div class="w-px h-7 bg-gray-300/60"></div>
                    <?php if ($isAdmin ?? false): ?>
                        <a href="/admin" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 hover:from-purple-200 hover:to-pink-200 transition-all duration-200 font-bold text-sm shadow-sm hover:shadow-md border-2 border-purple-200/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>پنل مدیریت</span>
                        </a>
                    <?php endif; ?>
                    <form method="POST" action="/logout" class="inline">
                        <button type="submit" class="px-3.5 py-2.5 text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-all duration-200 font-bold text-sm touch-manipulation">
                            خروج
                        </button>
                    </form>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen"
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
    <div x-show="mobileMenuOpen" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileMenuOpen = false"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40"></div>

    <div x-show="mobileMenuOpen" x-cloak
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
            <button @click="mobileMenuOpen = false"
                class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition-all duration-200 touch-manipulation"
                style="min-width: 44px; min-height: 44px;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-3 space-y-1">
            <a href="/dashboard" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                <span class="text-xl">📊</span><span>داشبورد</span>
            </a>
            <a href="/games" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold <?= ($currentPath ?? '') === '/games' ? 'bg-indigo-50 text-indigo-600' : '' ?> touch-manipulation">
                <span class="text-xl">📋</span><span>بازی‌ها</span>
            </a>
            <a href="/users" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold <?= ($currentPath ?? '') === '/users' ? 'bg-indigo-50 text-indigo-600' : '' ?> touch-manipulation">
                <span class="text-xl">👥</span><span>بازیکنان</span>
            </a>
            <a href="/achievements" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                <span class="text-xl">🏆</span><span>دستاوردها</span>
            </a>
            <?php if ($canCreate ?? false): ?>
                <a href="/game/create" @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                    <span class="text-xl">🎮</span><span>بازی جدید</span>
                </a>
            <?php endif; ?>
            <a href="/profile" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                <span class="text-xl">👤</span><span>پروفایل</span>
            </a>
            <button onclick="toggleSoundButton(this)" id="sound-toggle-btn-mobile" @click="mobileMenuOpen = false"
                class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                <span id="sound-icon-mobile" class="text-xl">🔊</span>
                <span>صدا</span>
                <span id="sound-status-mobile" class="mr-auto text-xs text-green-600 font-bold">فعال</span>
            </button>
            <div class="my-2 border-t-2 border-gray-200/60"></div>
            <?php if ($isAdmin ?? false): ?>
                <a href="/admin" @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 hover:from-purple-200 hover:to-pink-200 transition-all duration-200 font-bold border-2 border-purple-200/50 touch-manipulation">
                    <span class="text-xl">⚙️</span><span>پنل مدیریت</span>
                </a>
            <?php endif; ?>
            <form method="POST" action="/logout" @submit="mobileMenuOpen = false">
                <button type="submit"
                    class="w-full text-right flex items-center gap-3 px-4 py-3 text-rose-600 hover:bg-rose-50 rounded-xl transition-all duration-200 font-bold touch-manipulation">
                    <span class="text-xl">🚪</span><span>خروج</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ======= Main Content ======= -->
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
                <button id="install-app-btn" style="display: none;"
                    class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2">
                    📱 نصب برنامه روی گوشی
                </button>
            </div>
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- 🌍 GLOBAL PROFILE DRAWER (همیشه در دسترس) -->
    <!-- ========================================== -->
    <div id="global-profile-drawer"
        style="display: none; position: fixed; inset: 0; z-index: 9999;"
        role="dialog"
        aria-modal="true">

        <!-- Backdrop (قابل کلیک برای بستن) -->
        <div id="global-drawer-backdrop"
            onclick="closeGlobalProfileDrawer()"
            style="position: fixed; inset: 0; background: rgba(17, 24, 39, 0.75); backdrop-filter: blur(4px); cursor: pointer;">
        </div>

        <!-- Drawer Panel -->
        <div id="global-drawer-panel"
            style="position: fixed; top: 0; right: 0; bottom: 0; width: 100%; max-width: 28rem; background: white; overflow-y: auto; box-shadow: -4px 0 20px rgba(0,0,0,0.15); transform: translateX(100%); transition: transform 0.3s ease-out; border-left: 2px solid rgba(229, 231, 235, 0.5);">

            <!-- Header -->
            <div style="position: sticky; top: 0; z-index: 10; background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); border-bottom: 2px solid rgba(229, 231, 235, 0.6); padding: 0.875rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 1.125rem; font-weight: 900; color: #1f2937; margin: 0;">پروفایل کاربر</h3>
                <button onclick="closeGlobalProfileDrawer()"
                    style="min-width: 44px; min-height: 44px; padding: 0.5rem; background: transparent; border: none; border-radius: 0.75rem; cursor: pointer; color: #6b7280; display: flex; align-items: center; justify-content: center; touch-action: manipulation;"
                    aria-label="بستن">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div id="global-drawer-content" style="padding: 1rem;">
                <div style="text-align: center; color: #6b7280; padding: 3rem 0;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #6366f1; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 1rem;"></div>
                    <p style="font-weight: 500;">در حال بارگذاری...</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* 🎯 جلوگیری از اسکرول body وقتی drawer باز است */
        body.drawer-open {
            overflow: hidden !important;
            position: fixed !important;
            width: 100% !important;
            top: 0 !important;
            left: 0 !important;
        }

        /* 🎯 اطمینان از z-index دکمه همبرگری */
        nav.z-50 {
            z-index: 50 !important;
        }

        /* 🎯 Mobile menu باید زیر drawer باشد */
        .md\:hidden+div[x-show="mobileMenuOpen"] {
            z-index: 40 !important;
        }

        /* 🎯 وقتی drawer باز است، همه عناصر زیر آن باشند */
        body.drawer-open nav,
        body.drawer-open main,
        body.drawer-open footer,
        body.drawer-open .fixed:not(#global-profile-drawer) {
            pointer-events: none !important;
        }

        /* 🎯 خود drawer باید قابل تعامل باشد */
        body.drawer-open #global-profile-drawer,
        body.drawer-open #global-profile-drawer * {
            pointer-events: auto !important;
        }
    </style>

    <!-- ========================================== -->
    <!-- 🎯 GLOBAL DRAWER SCRIPT (همیشه در دسترس) -->
    <!-- ========================================== -->
    <script>
        (function() {
            'use strict';

            // متغیرهای سراسری
            let currentAbortController = null;
            let scrollPosition = 0;
            let isOpen = false;

            /**
             * 🌟 باز کردن Profile Drawer - تابع سراسری
             * از همه جا (scoreboard, rounds, ...) قابل فراخوانی است
             */
            window.openProfile = function(url) {
                if (isOpen) {
                    closeGlobalProfileDrawer(false); // بدون تغییر URL
                }

                console.log('🎯 Opening profile drawer:', url);

                // لغو درخواست قبلی
                if (currentAbortController) {
                    currentAbortController.abort();
                }
                currentAbortController = new AbortController();

                // ذخیره موقعیت اسکرول
                scrollPosition = window.pageYOffset || document.documentElement.scrollTop;

                // باز کردن drawer
                const drawer = document.getElementById('global-profile-drawer');
                const panel = document.getElementById('global-drawer-panel');
                const content = document.getElementById('global-drawer-content');

                if (!drawer || !panel || !content) {
                    console.error('❌ Drawer elements not found');
                    window.location.href = url;
                    return;
                }

                // نمایش drawer
                drawer.style.display = 'block';
                document.body.classList.add('drawer-open');
                isOpen = true;

                // انیمیشن باز شدن
                requestAnimationFrame(() => {
                    panel.style.transform = 'translateX(0)';
                });

                // Loading state
                content.innerHTML = `
            <div style="text-align: center; color: #6b7280; padding: 3rem 0;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #6366f1; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 1rem;"></div>
                <p style="font-weight: 500;">در حال بارگذاری...</p>
            </div>
        `;

                // اضافه کردن به History برای دکمه Back موبایل
                try {
                    history.pushState({
                        drawerOpen: true,
                        url: url
                    }, '', url);
                } catch (e) {
                    console.warn('History pushState failed:', e);
                }

                // Fetch محتوا
                fetch(url, {
                        cache: 'no-store',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html, */*'
                        },
                        signal: currentAbortController.signal
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.text();
                    })
                    .then(html => {
                        // بررسی اینکه هنوز drawer باز است
                        if (!isOpen) return;

                        // بررسی اینکه HTML خالی نیست
                        if (!html || html.trim() === '') {
                            throw new Error('Empty response');
                        }

                        content.innerHTML = html;
                        console.log('✅ Profile loaded successfully');

                        // Reinit Alpine برای محتوای جدید
                        if (typeof Alpine !== 'undefined' && Alpine.initTree) {
                            setTimeout(() => Alpine.initTree(content), 50);
                        }
                    })
                    .catch(error => {
                        if (error.name === 'AbortError') return;

                        console.error('❌ Profile load error:', error);
                        content.innerHTML = `
                <div style="text-align: center; color: #dc2626; padding: 2rem 0;">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">⚠️</div>
                    <p style="font-weight: bold; margin-bottom: 0.5rem;">خطا در بارگذاری پروفایل</p>
                    <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">${error.message}</p>
                    <button onclick="window.openProfile('${url}')"
                            style="padding: 0.5rem 1rem; background: #6366f1; color: white; border: none; border-radius: 0.5rem; font-weight: bold; cursor: pointer;">
                        تلاش مجدد
                    </button>
                </div>
            `;
                    });
            };

            /**
             * 🌟 بستن Profile Drawer - تابع سراسری
             */
            window.closeGlobalProfileDrawer = function(restoreHistory = true) {
                if (!isOpen) return;

                console.log('🎯 Closing profile drawer');

                const drawer = document.getElementById('global-profile-drawer');
                const panel = document.getElementById('global-drawer-panel');

                if (!drawer || !panel) return;

                // انیمیشن بسته شدن
                panel.style.transform = 'translateX(100%)';

                // بعد از پایان انیمیشن، مخفی کن
                setTimeout(() => {
                    drawer.style.display = 'none';
                    document.body.classList.remove('drawer-open');
                    isOpen = false;

                    // بازیابی موقعیت اسکرول
                    window.scrollTo(0, scrollPosition);
                }, 300);

                // لغو درخواست در حال انتظار
                if (currentAbortController) {
                    currentAbortController.abort();
                    currentAbortController = null;
                }

                // بازگشت به URL قبلی
                if (restoreHistory && history.state && history.state.drawerOpen) {
                    history.back();
                }
            };

            /**
             * 📱 دکمه Back موبایل - هندلر popstate
             */
            window.addEventListener('popstate', function(event) {
                // اگر drawer باز است و کاربر Back زد، drawer را ببند
                if (isOpen && (!event.state || !event.state.drawerOpen)) {
                    closeGlobalProfileDrawer(false); // false = بدون تغییر URL (چون الان در popstate هستیم)
                }
            });

            /**
             * ⌨️ دکمه Escape
             */
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && isOpen) {
                    closeGlobalProfileDrawer();
                }
            });

            /**
             * 🧹 Cleanup هنگام HTMX navigation
             */
            document.body.addEventListener('htmx:beforeRequest', function() {
                if (isOpen) {
                    closeGlobalProfileDrawer(false);
                }
            });

            /**
             * 🧹 Cleanup هنگام خروج از صفحه
             */
            window.addEventListener('beforeunload', function() {
                if (currentAbortController) {
                    currentAbortController.abort();
                }
            });

            console.log('✅ Global Profile Drawer initialized');
        })();
    </script>

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

    <!-- 🆕 SSE Client -->
    <?php if (isset($currentUser) || (isset($user) && !empty($user['id']))): ?>
        <script src="/assets/js/sse-client.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.SSE) {
                    window.SSE.connect('notifications', '/sse/notifications');

                    window.SSE.on('notifications', 'notification', (data) => {
                        if (window.SoundManager) {
                            window.SoundManager.playForEvent('notification', data);
                        }
                        showUserNotification(data);
                    });

                    window.SSE.on('notifications', 'system_message', (data) => {
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

    <!-- مدیریت خطاهای احراز هویت HTMX -->
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
    <!-- ======= Alpine Reinit after HTMX ======= -->
    <!-- ========================================== -->
    <script>
        (function() {
            function reinitAlpine(target) {
                if (typeof Alpine !== 'undefined' && Alpine.initTree) {
                    if (target) {
                        Alpine.initTree(target);
                    } else {
                        document.querySelectorAll('[x-data]').forEach(el => {
                            if (!el._x_dataStack) {
                                Alpine.initTree(el);
                            }
                        });
                    }
                }
            }

            document.body.addEventListener('htmx:afterSwap', function(evt) {
                if (evt.detail.target) {
                    reinitAlpine(evt.detail.target);
                }
            });

            document.body.addEventListener('htmx:afterSettle', function(evt) {
                if (evt.detail.target) {
                    reinitAlpine(evt.detail.target);
                }
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
                        console.log('✅ Service Worker registered');
                    })
                    .catch(function(error) {
                        console.log('❌ Service Worker failed:', error);
                    });
            });
        }

        let deferredPrompt = null;
        const installBtn = document.getElementById('install-app-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (installBtn) installBtn.style.display = 'inline-flex';
        });

        window.addEventListener('appinstalled', (evt) => {
            deferredPrompt = null;
            if (installBtn) installBtn.style.display = 'none';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '🎉 نصب شد!',
                    text: 'UNO Tracker با موفقیت نصب شد.',
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
                    deferredPrompt = null;
                    installBtn.style.display = 'none';
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: '📱 نصب برنامه',
                            text: 'از منوی مرورگر "Add to Home Screen" را انتخاب کنید.',
                            confirmButtonColor: '#4f46e5'
                        });
                    }
                }
            });
        }

        if (window.matchMedia('(display-mode: standalone)').matches) {
            if (installBtn) installBtn.style.display = 'none';
        }
    </script>

</body>

</html>