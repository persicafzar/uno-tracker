<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'UNO Tracker TV') ?></title>

    <!-- فونت وزیر -->
    <style>
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
    </style>

    <!-- Tailwind CSS -->
    <script src="/assets/js/tailwind.js"></script>
    <!-- HTMX -->
    <script src="/assets/js/htmx.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="/assets/js/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="/assets/css/sweetalert2.min.css">
    <!-- Chart.js -->
    <script src="/assets/js/chart.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="/assets/js/alpine.min.js"></script>
    <!-- Sound Manager -->
    <script src="/assets/js/sound-manager.js"></script>
    <!-- SSE Client -->
    <script src="/assets/js/sse-client.js"></script>

    <style>
        body {
            background: #0f172a;
            color: #e2e8f0;
            margin: 0;
            padding: 25px 35px 15px;
            padding-top: 70px;
            /* ✅ افزایش فاصله از بالا */
            min-height: 100vh;
            font-family: 'Vazir', Tahoma, Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: #4f46e5;
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6366f1;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
            }

            50% {
                box-shadow: 0 0 30px rgba(99, 102, 241, 0.5);
            }
        }

        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        /* ======= کانتینر اصلی - کاملاً وسط ======= */
        .tv-container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* ======= محتوای داخلی با padding-left برای دکمه‌ها ======= */
        .tv-content-inner {
            padding-left: 110px;
            padding-right: 20px;
            margin-top: 10px;
            /* ✅ فاصله اضافی از بالا */
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .tv-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(71, 85, 105, 0.5);
            border-radius: 14px;
            padding: 14px 20px;
            transition: all 0.3s;
        }

        .tv-card:hover {
            border-color: #6366f1;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.15);
        }

        .tv-btn {
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: bold;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            font-family: 'Vazir', Tahoma, Arial, sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .tv-btn-primary {
            background: #4f46e5;
            color: white;
        }

        .tv-btn-primary:hover {
            background: #6366f1;
            transform: scale(1.02);
        }

        .tv-btn-secondary {
            background: #334155;
            color: #e2e8f0;
        }

        .tv-btn-secondary:hover {
            background: #475569;
        }

        .tv-btn-success {
            background: #10b981;
            color: #0f172a;
        }

        .tv-btn-success:hover {
            background: #34d399;
            transform: scale(1.02);
        }

        .tv-input {
            background: #1e293b;
            border: 2px solid #334155;
            border-radius: 12px;
            padding: 10px 14px;
            color: #e2e8f0;
            font-size: 1rem;
            width: 100%;
            transition: all 0.2s;
            font-family: 'Vazir', Tahoma, Arial, sans-serif;
        }

        .tv-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }

        .tv-input::placeholder {
            color: #64748b;
        }

        .tv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1.1rem;
        }

        .tv-table th {
            text-align: right;
            padding: 8px 12px;
            background: #1e293b;
            color: #94a3b8;
            font-weight: bold;
            border-bottom: 2px solid #334155;
        }

        .tv-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #1e293b;
        }

        .tv-table tr:hover td {
            background: #1e293b;
        }

        .status-badge {
            padding: 3px 10px;
            border-radius: 16px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .status-active {
            background: #10b981;
            color: #0f172a;
        }

        .status-paused {
            background: #f59e0b;
            color: #0f172a;
        }

        .status-finished {
            background: #3b82f6;
            color: #0f172a;
        }

        .status-pending {
            background: #fbbf24;
            color: #0f172a;
        }

        .status-cancelled {
            background: #ef4444;
            color: #0f172a;
        }

        a {
            color: #a5b4fc;
            text-decoration: none;
            transition: color 0.2s;
        }

        a:hover {
            color: #818cf8;
        }

        /* ======= دکمه‌های شناور ======= */
        .floating-actions {
            position: fixed;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 14px;
            z-index: 999;
        }

        .floating-actions .btn-float {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            width: 72px;
            padding: 10px 6px 8px;
            border-radius: 16px;
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(71, 85, 105, 0.4);
            color: #e2e8f0;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
            text-decoration: none;
            cursor: pointer;
            font-family: 'Vazir', Tahoma, Arial, sans-serif;
            font-size: 1rem;
            line-height: 1.2;
        }

        .floating-actions .btn-float .icon {
            font-size: 1.7rem;
            line-height: 1.3;
        }

        .floating-actions .btn-float .label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #cbd5e1;
            text-align: center;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .floating-actions .btn-float:hover {
            transform: scale(1.06);
            background: rgba(99, 102, 241, 0.25);
            border-color: #6366f1;
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.25);
        }

        .floating-actions .btn-float:active {
            transform: scale(0.95);
        }

        .floating-actions .btn-float.refresh {
            background: rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .floating-actions .btn-float.refresh:hover {
            background: rgba(16, 185, 129, 0.35);
            border-color: #10b981;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.25);
        }

        .floating-actions .btn-float.list {
            background: rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.25);
        }

        .floating-actions .btn-float.list:hover {
            background: rgba(99, 102, 241, 0.3);
            border-color: #6366f1;
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.25);
        }

        .floating-actions .btn-float.home {
            background: rgba(168, 85, 247, 0.15);
            border-color: rgba(168, 85, 247, 0.25);
        }

        .floating-actions .btn-float.home:hover {
            background: rgba(168, 85, 247, 0.3);
            border-color: #a855f7;
            box-shadow: 0 0 30px rgba(168, 85, 247, 0.25);
        }

        /* ======= فوتر ======= */
        .tv-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 10px 0 4px;
            margin-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            flex-shrink: 0;
        }

        .tv-footer img {
            width: 40px;
            height: 40px;
        }

        .tv-footer .brand {
            font-size: 1.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #818cf8, #a78bfa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        /* ======= تنظیمات واکنش‌گرا ======= */
        @media (max-width: 1200px) {
            .tv-content-inner {
                padding-left: 90px;
                padding-right: 15px;
            }
        }

        @media (max-width: 1024px) {
            body {
                padding: 18px 20px 12px;
                padding-top: 60px;
            }

            .floating-actions {
                left: 14px;
                gap: 10px;
            }

            .floating-actions .btn-float {
                width: 56px;
                padding: 8px 4px 6px;
                border-radius: 14px;
            }

            .floating-actions .btn-float .icon {
                font-size: 1.3rem;
            }

            .floating-actions .btn-float .label {
                font-size: 0.6rem;
            }

            .tv-content-inner {
                padding-left: 75px;
                padding-right: 12px;
                margin-top: 8px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 12px 14px 8px;
                padding-top: 50px;
            }

            .floating-actions {
                left: 10px;
                gap: 8px;
            }

            .floating-actions .btn-float {
                width: 48px;
                padding: 6px 3px 4px;
                border-radius: 12px;
            }

            .floating-actions .btn-float .icon {
                font-size: 1.1rem;
            }

            .floating-actions .btn-float .label {
                font-size: 0.5rem;
            }

            .tv-content-inner {
                padding-left: 60px;
                padding-right: 10px;
                margin-top: 6px;
            }

            .tv-footer .brand {
                font-size: 1.2rem;
            }

            .tv-footer img {
                width: 32px;
                height: 32px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 8px 10px 6px;
                padding-top: 40px;
            }

            .floating-actions {
                left: 6px;
                gap: 6px;
            }

            .floating-actions .btn-float {
                width: 42px;
                padding: 4px 2px 3px;
                border-radius: 10px;
            }

            .floating-actions .btn-float .icon {
                font-size: 0.9rem;
            }

            .floating-actions .btn-float .label {
                font-size: 0.45rem;
            }

            .tv-content-inner {
                padding-left: 50px;
                padding-right: 8px;
                margin-top: 4px;
            }
        }

        @media (min-width: 1200px) {
            body {
                padding: 28px 45px 16px;
                padding-top: 20px;
            }

            .tv-card {
                padding: 18px 26px;
            }

            .tv-table {
                font-size: 1.2rem;
            }

            .tv-table th,
            .tv-table td {
                padding: 12px 18px;
            }

            .floating-actions .btn-float {
                width: 80px;
                padding: 12px 8px 10px;
                border-radius: 18px;
            }

            .floating-actions .btn-float .icon {
                font-size: 1.9rem;
            }

            .floating-actions .btn-float .label {
                font-size: 0.8rem;
            }

            .tv-content-inner {
                padding-left: 110px;
                padding-right: 25px;
                margin-top: 12px;
            }

            .tv-footer .brand {
                font-size: 2rem;
            }

            .tv-footer img {
                width: 52px;
                height: 52px;
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <?php
    // 🆕 دریافت تنظیمات Auto-Refresh از دیتابیس
    $fallbackEnabled = true;
    $fallbackSeconds = 10;
    try {
        $settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
        $fallbackEnabled = (bool) $settingsRepo->get('sse_fallback_enabled', true);
        $fallbackSeconds = (int) $settingsRepo->get('sse_fallback_refresh_seconds', 10);
    } catch (\Throwable $e) {
        error_log("Auto-Refresh Config Error (TV): " . $e->getMessage());
    }
    ?>
    <script>
        window.SSE_FALLBACK_CONFIG = {
            enabled: <?= $fallbackEnabled ? 'true' : 'false' ?>,
            refreshSeconds: <?= (int) $fallbackSeconds ?>,
        };
        console.log('🔄 SSE_FALLBACK_CONFIG (TV):', window.SSE_FALLBACK_CONFIG);
    </script>

</head>

<body>
    <div class="tv-container">
        <!-- ======= دکمه‌های شناور ======= -->
        <div class="floating-actions" role="navigation" aria-label="کنترل‌های تلویزیون">
            <button onclick="refreshTV()" class="btn-float refresh" title="تازه‌سازی">
                <span class="icon">🔄</span>
                <span class="label">تازه‌سازی</span>
            </button>
            <!-- 🆕 دکمه آخرین بازی -->
            <button onclick="handleLatestGameTV()" class="btn-float list" title="آخرین بازی">
                <span class="icon">▶️</span>
                <span class="label">آخرین بازی</span>
            </button>
            <a href="/tv" class="btn-float list" title="لیست بازی‌ها">
                <span class="icon">📺</span>
                <span class="label">لیست</span>
            </a>
            <a href="/dashboard" class="btn-float home" title="بازگشت">
                <span class="icon">🏠</span>
                <span class="label">داشبورد</span>
            </a>
        </div>

        <!-- ======= محتوای داخلی با padding-left و margin-top ======= -->
        <div class="tv-content-inner">
            <div class="fade-in" style="flex:1;">
                <?= $content ?>
            </div>

            <!-- ======= فوتر ======= -->
            <footer class="tv-footer">
                <img src="/assets/images/logo.svg" alt="UNO Tracker">
                <span class="brand">UNO Tracker TV</span>
            </footer>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- اسکریپت‌ها                                  -->
    <!-- ========================================== -->
    <script>
        window.BASE_URL = '<?= base_url() ?>';

        document.body.addEventListener('htmx:responseError', function(evt) {
            if (evt.detail.xhr.status === 401 || evt.detail.xhr.status === 403) {
                window.location.href = '/login';
            }
        });

        window.TV_MODE = true;

        /**
         * 🎯 مدیریت کلیک روی "آخرین بازی" در حالت TV
         * 🔄 هدایت به صفحه TV (نه صفحه عادی بازی)
         */
        async function handleLatestGameTV() {
            try {
                const response = await fetch('/api/game/latest-active', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                // ✅ اگر بازی پیدا شد، به صفحه TV هدایت کن
                if (data.success !== false && data.game_id) {
                    window.location.href = '/tv/' + data.game_id;
                    return;
                }

                // ❌ اگر بازی‌ای وجود نداشت، پیغام نمایش بده
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        iconColor: '#6366f1',
                        title: data.message || 'شما در حال حاضر هیچ بازی فعال، در انتظار یا متوقف شده‌ای ندارید',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        background: '#f0f9ff',
                        color: '#1e40af',
                        width: '420px',
                        padding: '1rem 1.5rem',
                        customClass: {
                            popup: 'swal2-notification-border'
                        }
                    });
                } else {
                    alert(data.message || 'شما در حال حاضر هیچ بازی فعال، در انتظار یا متوقف شده‌ای ندارید');
                }

            } catch (error) {
                console.error('❌ Error fetching latest game:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'خطا در ارتباط با سرور',
                        showConfirmButton: false,
                        timer: 3000,
                    });
                }
            }
        }

        function refreshTV() {
            const path = window.location.pathname;
            if (path === '/tv' || path === '/tv/') {
                location.reload();
                return;
            }
            const match = path.match(/^\/tv\/(\d+)$/);
            if (match) {
                const gameId = match[1];
                const target = '#tv-game-content';
                const url = (window.BASE_URL || '') + '/tv/' + gameId + '?partial=1';
                console.log('🔄 TV Refresh:', url);
                if (typeof htmx !== 'undefined') {
                    htmx.ajax('GET', url, {
                        target: target,
                        swap: 'innerHTML',
                        headers: {
                            'HX-Request': 'true',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(() => {
                        const content = document.querySelector(target);
                        if (content) htmx.process(content);
                        console.log('✅ TV Refresh completed');
                    }).catch(() => location.reload());
                } else {
                    location.reload();
                }
                return;
            }
            location.reload();
        }
    </script>

    <!-- ========================================== -->
    <!-- Screen Wake Lock                           -->
    <!-- ========================================== -->
    <script>
        (function() {
            'use strict';

            const isTVPage = window.location.pathname.startsWith('/tv');
            if (!isTVPage) return;

            console.log('🖥️ راه‌اندازی Screen Wake Lock برای TV');

            let wakeLock = null;
            let heartbeatInterval = null;
            let keepAliveInterval = null;
            let clickInterval = null;
            let videoElement = null;

            async function requestWakeLock() {
                if (!('wakeLock' in navigator)) {
                    console.log('⚠️ Wake Lock پشتیبانی نمی‌شود');
                    return false;
                }
                try {
                    if (wakeLock) {
                        console.log('✅ Wake Lock قبلاً فعال است');
                        return true;
                    }
                    wakeLock = await navigator.wakeLock.request('screen');
                    console.log('✅ Screen Wake Lock فعال شد');
                    wakeLock.addEventListener('release', () => {
                        console.log('🔓 Wake Lock آزاد شد');
                        wakeLock = null;
                        if (document.visibilityState === 'visible') {
                            setTimeout(requestWakeLock, 2000);
                        }
                    });
                    return true;
                } catch (err) {
                    console.warn('❌ Wake Lock خطا:', err.message);
                    return false;
                }
            }

            function startHeartbeat() {
                if (heartbeatInterval) clearInterval(heartbeatInterval);
                heartbeatInterval = setInterval(async () => {
                    try {
                        await fetch('/sse/heartbeat', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: `page=${encodeURIComponent(window.location.pathname)}`
                        });
                        console.log('💓 Heartbeat ارسال شد');
                    } catch (e) {
                        console.warn('💓 Heartbeat error:', e);
                    }
                }, 15000);
            }

            function startClickSimulation() {
                if (clickInterval) clearInterval(clickInterval);
                clickInterval = setInterval(() => {
                    try {
                        const event = new MouseEvent('click', {
                            view: window,
                            bubbles: true,
                            cancelable: true
                        });
                        document.body.dispatchEvent(event);
                        console.log('🖱️ کلیک ساختگی');
                    } catch (e) {}
                }, 60000);
            }

            function startDOMTickle() {
                let el = document.getElementById('keep-alive-tickle');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'keep-alive-tickle';
                    el.style.cssText = `
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        width: 1px;
                        height: 1px;
                        opacity: 0.01;
                        pointer-events: none;
                        z-index: -9999;
                        transition: transform 0.1s;
                    `;
                    document.body.appendChild(el);
                }
                if (keepAliveInterval) clearInterval(keepAliveInterval);
                keepAliveInterval = setInterval(() => {
                    if (el) {
                        const x = Math.random() * 2 - 1;
                        el.style.transform = `translateX(${x}px)`;
                        console.log('🔄 DOM Tickle');
                    }
                }, 30000);
            }

            function createHiddenVideo() {
                if (videoElement) return;
                videoElement = document.createElement('video');
                videoElement.style.cssText = `
                    position: fixed;
                    bottom: 0;
                    right: 0;
                    width: 1px;
                    height: 1px;
                    opacity: 0.01;
                    pointer-events: none;
                    z-index: -9999;
                `;
                videoElement.muted = true;
                videoElement.loop = true;
                videoElement.playsInline = true;
                const emptyVideo = 'data:video/mp4;base64,AAAAIGZ0eXBpc29tAAACAGlzb21pc28yYXZjMW1wNDEAAAAIZnJlZQAAAQxtZGF0AAAAMAAAABAAAAABAAABAAABAAAAAAABAAABAAAB';
                videoElement.src = emptyVideo;
                document.body.appendChild(videoElement);
                videoElement.play().catch(() => {});
                console.log('🎬 ویدیوی مخفی ایجاد شد');
            }

            async function handleVisibilityChange() {
                if (document.visibilityState === 'visible') {
                    console.log('👁️ صفحه قابل مشاهده شد');
                    if (!wakeLock) await requestWakeLock();
                }
            }

            async function init() {
                console.log('🔒 راه‌اندازی همه روش‌های جلوگیری از اسکرین سیور');
                await requestWakeLock();
                startHeartbeat();
                startClickSimulation();
                startDOMTickle();
                try {
                    createHiddenVideo();
                } catch (e) {}
                document.addEventListener('visibilitychange', handleVisibilityChange);
                setInterval(async () => {
                    if (!wakeLock && document.visibilityState === 'visible') {
                        console.log('🔄 بررسی دوره‌ای Wake Lock...');
                        await requestWakeLock();
                    }
                }, 120000);
                console.log('✅ همه روش‌های جلوگیری از اسکرین سیور راه‌اندازی شدند');
            }

            setTimeout(init, 1000);

            window.addEventListener('pagehide', () => {
                if (wakeLock) try {
                    wakeLock.release();
                } catch (e) {}
                if (heartbeatInterval) clearInterval(heartbeatInterval);
                if (clickInterval) clearInterval(clickInterval);
                if (keepAliveInterval) clearInterval(keepAliveInterval);
                if (videoElement) try {
                    videoElement.pause();
                } catch (e) {}
                console.log('🧹 پاکسازی انجام شد');
            });

        })();
    </script>
</body>

</html>