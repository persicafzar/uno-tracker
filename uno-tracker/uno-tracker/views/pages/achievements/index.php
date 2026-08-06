<?php

use Core\JalaliDate;

$xpInfo = $gamificationData['xp_info'];
$streakInfo = $gamificationData['streak_info'];
$achievementsStats = $gamificationData['achievements_stats'];
$notificationsCount = $gamificationData['notifications_count'];
$levelData = $xpInfo['level_data'];
$progressPercentage = $xpInfo['progress_percentage'];

$userId = $user['id'];
$titleInfo = \Core\Database::getInstance()->fetchOne(
    "SELECT t.id, t.name, t.icon, t.bonus_points, t.description
     FROM users u
     LEFT JOIN titles t ON u.current_title_id = t.id
     WHERE u.id = ?",
    [$userId]
);
?>

<div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">

    <!-- ======= Header ======= -->
    <div class="relative bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-5 sm:p-7 text-white shadow-2xl mb-4 sm:mb-6 overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-5">
            <div class="flex items-center gap-4 sm:gap-5">
                <?php if (!empty($user['avatar_path'])): ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 border-white/90 shadow-2xl overflow-hidden flex-shrink-0 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($user['avatar_path']) ?>"
                            alt="<?= htmlspecialchars($user['nickname']) ?>"
                            class="w-full h-full aspect-square rounded-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 border-white/90 shadow-2xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-3xl sm:text-4xl font-black flex-shrink-0 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <?= mb_substr($user['nickname'] ?? '?', 0, 1) ?>
                    </div>
                <?php endif; ?>
                <div class="text-center sm:text-right">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black drop-shadow-2xl tracking-tight">🏆 دستاوردها و پیشرفت</h1>
                    <p class="text-white/80 text-sm sm:text-base font-medium mt-0.5 drop-shadow">
                        <?= htmlspecialchars($user['nickname'] ?? '') ?> •
                        <?= JalaliDate::format('l، j F Y') ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openGuideModal()"
                    class="group px-3 py-2.5 bg-white/20 hover:bg-white/30 rounded-xl transition-all duration-300 text-sm font-bold flex items-center gap-1.5 hover:scale-[1.05]">
                    <span>❓</span>
                    راهنما
                </button>
                <div class="text-center sm:text-right">
                    <div class="text-3xl sm:text-4xl font-black drop-shadow-2xl"><?= $xpInfo['total_xp'] ?></div>
                    <div class="text-xs text-white/80 font-medium">امتیاز تجربه</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======= کارت‌های نمای کلی ======= -->
    <div class="!grid !grid-cols-2 lg:!grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
        <a href="#level-section" class="relative overflow-hidden bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-2xl p-3 sm:p-4 border-2 border-indigo-300 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between mb-2">
                <div class="text-3xl sm:text-4xl drop-shadow"><?= $levelData->icon ?? '⭐' ?></div>
                <div class="text-[10px] text-indigo-600 font-black">سطح</div>
            </div>
            <div class="relative z-10 text-2xl sm:text-3xl font-black text-indigo-700"><?= $xpInfo['current_level'] ?></div>
            <div class="relative z-10 text-xs text-indigo-700/70 font-medium truncate"><?= htmlspecialchars($levelData->title ?? '') ?></div>
        </a>

        <a href="#streak-section" class="relative overflow-hidden bg-gradient-to-br from-orange-100 to-amber-100 rounded-2xl p-3 sm:p-4 border-2 border-orange-300 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between mb-2">
                <div class="text-3xl sm:text-4xl drop-shadow">🔥</div>
                <div class="text-[10px] text-orange-600 font-black">زنجیره</div>
            </div>
            <div class="relative z-10 text-2xl sm:text-3xl font-black text-orange-700"><?= $streakInfo['current_streak'] ?></div>
            <div class="relative z-10 text-xs text-orange-700/70 font-medium">بهترین: <?= $streakInfo['best_streak'] ?></div>
        </a>

        <a href="#achievements-section" class="relative overflow-hidden bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl p-3 sm:p-4 border-2 border-purple-300 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between mb-2">
                <div class="text-3xl sm:text-4xl drop-shadow">🏅</div>
                <div class="text-[10px] text-purple-600 font-black">مدال‌ها</div>
            </div>
            <div class="relative z-10 text-2xl sm:text-3xl font-black text-purple-700"><?= $achievementsStats['completed'] ?>/<?= $achievementsStats['total'] ?></div>
            <div class="relative z-10 text-xs text-purple-700/70 font-medium"><?= $achievementsStats['completion_percentage'] ?>%</div>
        </a>

        <a href="#titles-section" class="relative overflow-hidden bg-gradient-to-br from-amber-100 to-yellow-100 rounded-2xl p-3 sm:p-4 border-2 border-amber-300 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between mb-2">
                <div class="text-3xl sm:text-4xl drop-shadow"><?= htmlspecialchars($activeTitle['icon'] ?? '🏆') ?></div>
                <div class="text-[10px] text-amber-600 font-black">لقب</div>
            </div>
            <div class="relative z-10 text-lg sm:text-xl font-black text-amber-700 truncate">
                <?= htmlspecialchars($activeTitle['name'] ?? 'بدون لقب') ?>
            </div>
            <div class="relative z-10 text-xs text-amber-700/70 font-medium">
                <?php if ($activeTitle): ?>
                    +<?= $activeTitle['bonus_points'] ?? 0 ?> امتیاز جایزه
                <?php else: ?>
                    هنوز لقبی کسب نکرده‌اید
                <?php endif; ?>
            </div>
        </a>
    </div>

    <!-- ======= سطح و پیشرفت ======= -->
    <div id="level-section" class="bg-white rounded-2xl p-4 sm:p-6 border-2 border-gray-200/70 shadow-xl mb-4 sm:mb-6">
        <?php include __DIR__ . '/partials/level-progress.php'; ?>
    </div>

    <!-- ======= زنجیره پیروزی ======= -->
    <div id="streak-section" class="bg-white rounded-2xl p-4 sm:p-6 border-2 border-gray-200/70 shadow-xl mb-4 sm:mb-6">
        <?php include __DIR__ . '/partials/streak-info.php'; ?>
    </div>

    <!-- ======= عناوین و لقب‌ها ======= -->
    <div id="titles-section" class="bg-white rounded-2xl p-4 sm:p-6 border-2 border-gray-200/70 shadow-xl mb-4 sm:mb-6">
        <h2 class="text-lg sm:text-xl font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
            <span class="text-2xl sm:text-3xl">🎖️</span>
            عناوین و لقب‌ها
        </h2>
        <?php include __DIR__ . '/partials/titles-list.php'; ?>
    </div>

    <!-- ======= مدال‌های افتخار ======= -->
    <div id="achievements-section" class="bg-white rounded-2xl p-4 sm:p-6 border-2 border-gray-200/70 shadow-xl mb-4 sm:mb-6">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <h2 class="text-lg sm:text-xl font-black text-gray-800 flex items-center gap-2.5 tracking-tight">
                <span class="text-2xl sm:text-3xl">🏅</span>
                مدال‌های افتخار
                <span class="text-sm font-normal text-gray-500 mr-2">(<?= $achievementsStats['completed'] ?>/<?= $achievementsStats['total'] ?>)</span>
            </h2>
        </div>
        <?php include __DIR__ . '/partials/achievements-list.php'; ?>
    </div>

    <!-- ======= اعلان‌ها ======= -->
    <div id="notifications-section" class="bg-white rounded-2xl p-4 sm:p-6 border-2 border-gray-200/70 shadow-xl">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <h2 class="text-lg sm:text-xl font-black text-gray-800 flex items-center gap-2.5 tracking-tight">
                <span class="text-2xl sm:text-3xl">🔔</span>
                اعلان‌ها
                <?php if ($notificationsCount > 0): ?>
                    <span class="px-2.5 py-0.5 bg-red-500 text-white text-xs rounded-full font-bold"><?= $notificationsCount ?></span>
                <?php endif; ?>
            </h2>
            <?php if ($notificationsCount > 0): ?>
                <button onclick="markAllAsRead()"
                    class="text-xs text-indigo-600 hover:text-indigo-700 font-bold hover:underline transition">
                    علامت‌گذاری همه به عنوان خوانده شده
                </button>
            <?php endif; ?>
        </div>
        <div id="notifications-container">
            <div class="text-center py-4">
                <div class="animate-spin inline-block w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full"></div>
                <p class="text-gray-500 text-sm font-medium mt-2">در حال بارگذاری...</p>
            </div>
        </div>
    </div>

    <!-- ======= مودال راهنما ======= -->
    <?php include __DIR__ . '/partials/guide-modal.php'; ?>
</div>

<!-- دکمه شناور راهنما -->
<button onclick="openGuideModal()"
    id="floating-guide-btn"
    class="fixed bottom-4 left-4 sm:bottom-6 sm:left-6 z-40 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-full w-14 h-14 sm:w-16 sm:h-16 shadow-2xl transition-all duration-300 hover:scale-110 flex items-center justify-center text-2xl sm:text-3xl border-2 border-white/20">
    ❓
</button>

<script>
    function openGuideModal() {
        const modal = document.getElementById('guide-modal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            // اسکرول به تب فعال
            setTimeout(() => {
                const container = document.getElementById('guide-tabs-container');
                const activeBtn = container?.querySelector('.border-indigo-600');
                if (container && activeBtn) {
                    activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            }, 150);
        }
    }

    function closeGuideModal() {
        const modal = document.getElementById('guide-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function switchGuideTab(tabName) {
        const tabs = document.querySelectorAll('.guide-tab-content');
        tabs.forEach(tab => tab.style.display = 'none');

        const activeTab = document.getElementById('guide-tab-' + tabName);
        if (activeTab) activeTab.style.display = 'block';

        const buttons = document.querySelectorAll('.guide-tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('border-indigo-600', 'text-indigo-600', 'bg-indigo-50');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        const activeBtn = document.getElementById('guide-btn-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-gray-500');
            activeBtn.classList.add('border-indigo-600', 'text-indigo-600', 'bg-indigo-50');
            activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    // اسکرول تب‌های راهنما
    function scrollGuideTabs(direction) {
        const container = document.getElementById('guide-tabs-container');
        if (container) {
            container.scrollBy({ left: direction * 150, behavior: 'smooth' });
        }
    }

    document.addEventListener('click', function(e) {
        const modal = document.getElementById('guide-modal');
        if (modal && e.target === modal) {
            closeGuideModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeGuideModal();
    });

    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();
    });

    function loadNotifications() {
        fetch('/achievements/notifications?limit=20')
            .then(response => response.text())
            .then(html => {
                document.getElementById('notifications-container').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('notifications-container').innerHTML =
                    '<div class="text-center py-4 text-red-500 font-medium">خطا در بارگذاری اعلان‌ها</div>';
            });
    }

    function markNotificationAsRead(notificationId) {
        fetch('/achievements/notifications/read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'notification_id=' + notificationId
            })
            .then(() => loadNotifications())
            .catch(error => console.error('Error:', error));
    }

    function markAllAsRead() {
        fetch('/achievements/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(() => loadNotifications())
            .catch(error => console.error('Error:', error));
    }
</script>