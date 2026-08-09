<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'پنل مدیریت') ?> - UNO Tracker</title>

    <!-- 🆕 لوگو -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/logo.svg">

    <!-- 🆕 Tailwind CSS (Local) -->
    <script src="/assets/js/tailwind.js"></script>

    <!-- 🆕 Alpine.js (Local) -->
    <script defer src="/assets/js/alpine.min.js"></script>

    <!-- 🆕 HTMX (Local) -->
    <script src="/assets/js/htmx.min.js"></script>

    <!-- 🆕 Chart.js (Local) -->
    <script src="/assets/js/chart.min.js"></script>

    <!-- 🆕 SweetAlert2 (Local) -->
    <link rel="stylesheet" href="/assets/css/sweetalert2.min.css">
    <script src="/assets/js/sweetalert2.min.js"></script>

    <!-- 🆕 Vazir Font (Local) -->
    <style>
        @font-face {
            font-family: 'Vazir';
            src: url('/assets/fonts/Vazir.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        * {
            font-family: 'Vazir', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #6366f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>
</head>

<body class="bg-gray-100" x-data="{ sidebarOpen: false }">

    <?php
    // 🆕 دریافت و نمایش پیام‌های Flash به صورت مرکزی
    $flashSuccess = $_SESSION['_flash']['success'] ?? null;
    $flashError = $_SESSION['_flash']['error'] ?? null;
    $flashErrors = $_SESSION['_flash']['errors'] ?? null; // برای آرایه‌ای از خطاها

    // پاک کردن flash messages بعد از خواندن
    unset($_SESSION['_flash']['success']);
    unset($_SESSION['_flash']['error']);
    unset($_SESSION['_flash']['errors']);
    ?>

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <?php include __DIR__ . '/../pages/admin/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Topbar -->
            <?php include __DIR__ . '/../pages/admin/partials/topbar.php'; ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-4 sm:px-6 py-6">

                    <!-- 🆕 نمایش پیام موفقیت -->
                    <?php if ($flashSuccess): ?>
                        <div class="mb-4 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3 animate-fade-in"
                            x-data="{ show: true }"
                            x-show="show"
                            x-init="setTimeout(() => show = false, 5000)"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-green-700"><?= htmlspecialchars($flashSuccess) ?></span>
                            <button @click="show = false" class="mr-auto text-green-600 hover:text-green-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- 🆕 نمایش پیام خطای تکی -->
                    <?php if ($flashError): ?>
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 animate-fade-in"
                            x-data="{ show: true }"
                            x-show="show"
                            x-init="setTimeout(() => show = false, 8000)"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-red-700"><?= htmlspecialchars($flashError) ?></span>
                            <button @click="show = false" class="mr-auto text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- 🆕 نمایش آرایه‌ای از خطاها -->
                    <?php if (!empty($flashErrors) && is_array($flashErrors)): ?>
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 animate-fade-in"
                            x-data="{ show: true }"
                            x-show="show"
                            x-init="setTimeout(() => show = false, 10000)"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="text-red-700 font-semibold">خطاها:</span>
                                <button @click="show = false" class="mr-auto text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                                <?php foreach ($flashErrors as $error): ?>
                                    <li><?= htmlspecialchars(is_array($error) ? implode(' - ', $error) : $error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?= $content ?? '' ?>
                </div>
            </main>

        </div>
    </div>

    <!-- Global Scripts -->
    <script>
        // تایید حذف با SweetAlert2
        function confirmDelete(message, formId) {
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: message || 'این عملیات قابل بازگشت نیست!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'بله، حذف کن',
                cancelButtonText: 'انصراف',
                rtl: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

</body>

</html>