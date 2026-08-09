<!-- Sidebar Overlay (Mobile) -->
<div x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
    x-cloak></div>

<!-- Sidebar -->
<aside
    :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 right-0 z-50 w-64 bg-gradient-to-b from-indigo-900 via-indigo-800 to-violet-900 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-screen max-h-screen shadow-2xl">

    <!-- Header - Logo -->
    <div class="flex items-center justify-between h-16 px-5 border-b border-white/10 flex-shrink-0">
        <a href="/admin" class="flex items-center gap-3">
            <img src="/assets/images/logo.svg" alt="UNO Tracker" class="bg-white h-10 rounded-xl w-10 p-1 shadow-lg">
            <div>
                <div class="font-black text-lg tracking-tight drop-shadow">UNO Tracker</div>
                <div class="text-xs text-white/60 font-medium">پنل مدیریت</div>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-white/60 hover:text-white transition duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-1 scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent hover:scrollbar-thumb-white/30" style="-webkit-overflow-scrolling: touch;">
        <?php
        $menuItems = [
            ['path' => '/admin', 'label' => 'داشبورد', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['path' => '/admin/users', 'label' => 'کاربران', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['path' => '/admin/games', 'label' => 'بازی‌ها', 'icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['path' => '/admin/cards', 'label' => 'کارت‌ها', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
            ['path' => '/admin/win-types', 'label' => 'انواع برد', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ['path' => '/admin/achievements', 'label' => 'نشان‌ها', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ['path' => '/admin/titles', 'label' => 'عناوین', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
            ['path' => '/admin/levels', 'label' => 'سطوح', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['path' => '/admin/suspicious-games', 'label' => 'گزارش‌های تقلب', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
            ['path' => '/admin/logs', 'label' => 'لاگ‌ها', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['path' => '/admin/notifications', 'label' => 'اعلان‌ها', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
            ['path' => '/admin/cleanup', 'label' => 'پاک‌سازی', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
            ['path' => '/admin/settings', 'label' => 'تنظیمات', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
        ];
        $current = $currentPath ?? '/admin';
        ?>
        <?php foreach ($menuItems as $item): ?>
            <?php $isActive = ($current === $item['path']) || ($item['path'] !== '/admin' && str_starts_with($current, $item['path'])); ?>
            <a href="<?= $item['path'] ?>"
                @click="sidebarOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 <?= $isActive ? 'bg-white/20 text-white font-black shadow-lg' : 'text-white/70 hover:bg-white/10 hover:text-white hover:scale-[1.02]' ?>">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $item['icon'] ?>" />
                </svg>
                <span class="truncate font-bold"><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Footer - بازگشت به سایت -->
    <div class="p-4 border-t border-white/10 flex-shrink-0 bg-gradient-to-b from-transparent to-black/10">
        <a href="/dashboard"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/70 hover:bg-white/10 hover:text-white hover:scale-[1.02] transition-all duration-300 font-bold">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            <span class="truncate">بازگشت به سایت</span>
        </a>
    </div>
</aside>

<style>
    .scrollbar-thin::-webkit-scrollbar { width: 5px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.2); border-radius: 3px; transition: background-color 0.2s; }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover { background-color: rgba(255,255,255,0.4); }
    .scrollbar-thin { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.2) transparent; }
    @supports (-webkit-overflow-scrolling: touch) { .scrollbar-thin { -webkit-overflow-scrolling: touch; } }
    @media (max-width: 1024px) { aside { height: 100vh !important; max-height: 100vh !important; } body.sidebar-open { overflow: hidden; } }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.effect(() => {
            const sidebarOpen = Alpine.store('sidebarOpen') || false;
            if (sidebarOpen && window.innerWidth < 1024) {
                document.body.classList.add('sidebar-open');
            } else {
                document.body.classList.remove('sidebar-open');
            }
        });
    });
</script>