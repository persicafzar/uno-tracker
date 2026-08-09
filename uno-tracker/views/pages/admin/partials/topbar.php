<header class="bg-white/90 backdrop-blur-sm shadow-md border-b-2 border-gray-200/50">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">

        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 hover:text-indigo-600 transition-all duration-200 p-2 rounded-xl hover:bg-indigo-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Page Title -->
        <h1 class="text-lg sm:text-xl font-black text-gray-800 tracking-tight flex items-center gap-2.5">
            <?= htmlspecialchars($title ?? 'پنل مدیریت') ?>
        </h1>

        <!-- Right Section -->
        <div class="flex items-center gap-3">

            <!-- Site Link -->
            <a href="/" target="_blank"
                class="hidden sm:flex items-center gap-2 px-3.5 py-2 text-sm font-bold text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span>مشاهده سایت</span>
            </a>

            <!-- Admin Info -->
            <div class="flex items-center gap-3">
                <?php if (!empty($admin['avatar_path'])): ?>
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full border-2 border-indigo-500 shadow-sm overflow-hidden flex-shrink-0">
                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($admin['avatar_path']) ?>"
                            alt="آواتار"
                            class="w-full h-full aspect-square rounded-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white font-black text-lg shadow-sm flex-shrink-0">
                        <?= mb_substr($admin['nickname'] ?? 'A', 0, 1) ?>
                    </div>
                <?php endif; ?>

                <div class="hidden sm:block">
                    <div class="text-sm font-black text-gray-800">
                        <?= htmlspecialchars($admin['nickname'] ?? '') ?>
                    </div>
                    <div class="text-xs font-bold text-gray-500">
                        <?= ($admin['role'] ?? '') === 'super_admin' ? 'مدیر ارشد' : 'مدیر' ?>
                    </div>
                </div>
            </div>

        </div>

    </div>
</header>