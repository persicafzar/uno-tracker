<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title><?= htmlspecialchars($title ?? 'UNO Tracker') ?></title>
    
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
    
    <!-- Mobile CSS -->
    <link rel="stylesheet" href="/assets/css/mobile.css">
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Load Vazir Font */
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
        
        .dir-ltr {
            direction: ltr;
            text-align: left;
        }
        
        /* انیمیشن‌های ملایم */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
        }
        
        /* Safe area for notched devices */
        @supports (padding: max(0px)) {
            body {
                padding-left: env(safe-area-inset-left);
                padding-right: env(safe-area-inset-right);
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-indigo-50 to-violet-50 min-h-screen flex items-center justify-center">
    
    <div class="w-full max-w-md px-3 sm:px-4 fade-in">
        <!-- Logo -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-block mb-3 sm:mb-4">
                <img src="/assets/images/logo.svg" alt="UNO Tracker" class="w-16 h-16 sm:w-20 sm:h-20 mx-auto">
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent mb-2">
                UNO Tracker
            </h1>
            <p class="text-gray-600 text-xs sm:text-sm">پلتفرم حرفه‌ای ثبت و آنالیز بازی‌های کارتی</p>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-8">
            <?= $content ?>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 sm:mt-6">
            <p class="text-xs sm:text-sm text-gray-500">
                طراحی و توسعه توسط <span class="font-semibold text-indigo-600">حسن حمیدی قمر</span>
            </p>
        </div>
    </div>

</body>
</html>