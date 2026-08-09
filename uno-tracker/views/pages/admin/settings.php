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

            // مرتب‌سازی ویژه برای anticheat
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

            // بارگذاری فایل مربوط به هر تب
            $partialPath = __DIR__ . '/partials/settings/' . $activeCategory . '.php';
            if (file_exists($partialPath)) {
                include $partialPath;
            } else {
                // اگر فایل وجود نداشت، یک پیام خطا نمایش بده
                echo '<div class="text-center py-12"><div class="text-5xl mb-3 opacity-50">⚠️</div><p class="text-gray-500 font-medium">فایل تنظیمات این بخش یافت نشد.</p></div>';
            }
            ?>
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