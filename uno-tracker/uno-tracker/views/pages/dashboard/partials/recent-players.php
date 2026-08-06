    <!-- 🆕 بخش ۱۰ بازیکن اخیر -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span class="text-xl">👥</span>
                بازیکنان اخیر
            </h3>
            <a href="/users" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                مشاهده همه ←
            </a>
        </div>

        <?php if (empty($recentPlayers)): ?>
            <div class="text-center py-8 text-gray-500">
                <p>هنوز بازیکنی عضو نشده است</p>
            </div>
        <?php else: ?>
            <!-- 🆕 اسکرول افقی برای موبایل و دسکتاپ -->
            <div class="overflow-x-auto pb-2 -mx-2 px-2">
                <div class="flex gap-4 min-w-max">
                    <?php foreach ($recentPlayers as $player): ?>
                        <a href="/users/<?= $player['id'] ?>"
                            class="group flex-shrink-0 w-48 bg-gradient-to-br from-white to-gray-50 rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-lg transition-all duration-300 overflow-hidden">

                            <!-- هدر کارت با رنگ سطح -->
                            <div class="h-16 relative" style="background: linear-gradient(135deg, <?= htmlspecialchars($player['level_color'] ?? '#6366f1') ?>, <?= htmlspecialchars($player['level_color'] ?? '#6366f1') ?>dd)">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

                                <!-- آواتار -->
                                <div class="absolute -bottom-6 left-1/2 -translate-x-1/2">
                                    <?php if (!empty($player['avatar_path'])): ?>
                                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($player['avatar_path']) ?>"
                                            alt="<?= htmlspecialchars($player['nickname']) ?>"
                                            class="w-14 h-14 rounded-full border-4 border-white shadow-md object-cover">
                                    <?php else: ?>
                                        <div class="w-14 h-14 rounded-full border-4 border-white shadow-md bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xl font-bold">
                                            <?= mb_substr($player['nickname'] ?? '?', 0, 1) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- محتوای کارت -->
                            <div class="pt-8 pb-4 px-3 text-center">
                                <!-- نام مستعار -->
                                <div class="font-bold text-gray-800 text-sm truncate group-hover:text-indigo-600 transition">
                                    <?= htmlspecialchars($player['nickname'] ?? 'ناشناس') ?>
                                </div>

                                <!-- نام واقعی -->
                                <?php if (!empty($player['real_name'])): ?>
                                    <div class="text-xs text-gray-500 truncate mt-0.5">
                                        <?= htmlspecialchars($player['real_name']) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- لقب -->
                                <?php if (!empty($player['current_title'])): ?>
                                    <div class="flex items-center justify-center gap-1 mt-2">
                                        <span class="text-sm"><?= htmlspecialchars($player['title_icon'] ?? '🏆') ?></span>
                                        <span class="text-xs text-amber-600 font-medium truncate">
                                            <?= htmlspecialchars($player['current_title']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- سطح و XP -->
                                <div class="mt-3 space-y-1">
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-xs font-bold"
                                            style="background-color: <?= htmlspecialchars($player['level_color'] ?? '#6366f1') ?>">
                                            <?= $player['current_level'] ?>
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            <?= htmlspecialchars($player['level_title'] ?? 'تازه‌کار') ?>
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ⭐ <?= number_format($player['total_xp'] ?? 0) ?> XP
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>