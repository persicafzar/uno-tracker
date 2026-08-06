<!-- برترین بازیکنان - با تصاویر کامل -->
<div class="bg-gradient-to-br from-gray-50/80 to-white rounded-2xl border border-gray-200/70 shadow-xl p-4 sm:p-6 mb-4 sm:mb-6 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-violet-500/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

    <div class="relative z-10 flex items-center justify-between mb-4">
        <h3 class="text-base sm:text-lg font-black text-gray-800 flex items-center gap-2.5">
            <span class="text-2xl drop-shadow-lg">🏆</span>
            برترین بازیکنان
        </h3>
        <a href="/users?sort=points_desc" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-700 font-bold hover:underline transition">
            مشاهده همه ←
        </a>
    </div>

    <?php if (empty($recentPlayers)): ?>
        <div class="text-center py-10 text-gray-500">
            <div class="text-6xl mb-3 opacity-50">🏆</div>
            <p class="text-sm font-medium">هنوز بازیکنی امتیازی کسب نکرده است</p>
        </div>
    <?php else: ?>
        <div class="-mx-2 overflow-x-auto pb-2 px-2 py-2">
            <div class="flex gap-5 min-w-max">
                <?php foreach ($recentPlayers as $index => $player):
                    $medals = ['🥇', '🥈', '🥉'];
                    $medal = $medals[$index] ?? null;
                ?>
                    <a href="/users/<?= $player['id'] ?>"
                        class="group flex-shrink-0 w-52 bg-white rounded-2xl border-2 border-gray-200 hover:border-indigo-400 hover:shadow-2xl transition-all duration-500 overflow-hidden relative hover:scale-[1.03]">

                        <?php if ($medal): ?>
                            <div class="absolute top-3 right-3 z-20 text-3xl drop-shadow-2xl animate-bounce">
                                <?= $medal ?>
                            </div>
                        <?php endif; ?>

                        <!-- هدر با گرادیانت پویا -->
                        <div class="h-20 relative " style="background: linear-gradient(135deg, <?= htmlspecialchars($player['level_color'] ?? '#6366f1') ?>, <?= htmlspecialchars($player['level_color'] ?? '#6366f1') ?>dd)">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                            <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                            <div class="absolute top-2 left-2 bg-white/95 text-gray-800 text-xs font-black px-2.5 py-1 rounded-full shadow-lg backdrop-blur-sm">
                                #<?= $index + 1 ?>
                            </div>

                            <!-- ===== آواتار با نمایش کامل ===== -->
                            <div class="absolute -bottom-7 left-1/2 -translate-x-1/2">
                                <?php if (!empty($player['avatar_path'])): ?>
                                    <div class="w-16 h-16 sm:w-[72px] sm:h-[72px] rounded-full border-4 border-white/90 shadow-2xl overflow-hidden group-hover:border-indigo-300 transition-all duration-300">
                                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($player['avatar_path']) ?>"
                                            alt="<?= htmlspecialchars($player['nickname']) ?>"
                                            class="w-full h-full  aspect-square rounded-full object-cover">
                                    </div>
                                <?php else: ?>
                                    <div class="w-16 h-16 sm:w-[72px] sm:h-[72px] rounded-full border-4 border-white/90 shadow-2xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-2xl font-black">
                                        <?= mb_substr($player['nickname'] ?? '?', 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="pt-9 pb-4 px-3 text-center">
                            <div class="font-bold text-gray-800 text-sm truncate group-hover:text-indigo-600 transition">
                                <?= htmlspecialchars($player['nickname'] ?? 'ناشناس') ?>
                                <?php if (!empty($player['real_name'])): ?>
                                    <span class="text-xs text-gray-400 truncate mt-0.5">(<?= htmlspecialchars($player['real_name']) ?>)</span>
                                <?php endif; ?>
                            </div>



                            <p class="italic mt-2 text-xs sm:text-sm break-words text-gray-400 h-10"><?= htmlspecialchars($player['tagline'] ?? '') ?></p>

                            <!-- لقب و امتیاز -->
                            <div class="flex items-center justify-between mt-2.5 px-1.5 gap-1 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-xl py-2 shadow-sm">
                                <div class="flex-1 text-center min-w-0">
                                    <?php if (!empty($player['current_title'])): ?>
                                        <div class="flex flex-col items-center justify-center gap-0.5">
                                            <span class="text-sm drop-shadow"><?= htmlspecialchars($player['title_icon'] ?? '🏆') ?></span>
                                            <span class="text-[10px] text-amber-600 font-semibold truncate">
                                                <?= htmlspecialchars($player['current_title']) ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">-</span>
                                    <?php endif; ?>
                                </div>
                                <div class="w-px h-9 bg-gray-200 flex-shrink-0"></div>
                                <div class="flex-1 text-center min-w-0">
                                    <div class="flex flex-col items-center justify-center gap-0.5">
                                        <span class="text-sm">💎</span>
                                        <span class="text-[10px] text-amber-600 font-semibold truncate">امتیاز <?= number_format($player['total_points'] ?? 0) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- سطح و XP -->
                            <div class="flex items-center justify-between mt-2 px-1.5 gap-1 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl py-2 shadow-sm">
                                <div class="flex-1 text-center min-w-0">
                                    <div class="flex flex-col items-center justify-center gap-0.5">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-xs font-bold shadow-sm"
                                            style="background-color: <?= htmlspecialchars($player['level_color'] ?? '#6366f1') ?>">
                                            <?= $player['current_level'] ?>
                                        </span>
                                        <span class="text-[10px] text-gray-600 truncate">
                                            <?= htmlspecialchars($player['level_title'] ?? 'تازه‌کار') ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="w-px h-9 bg-gray-200 flex-shrink-0"></div>
                                <div class="flex-1 text-center min-w-0">
                                    <div class="flex flex-col items-center justify-center gap-0.5">
                                        <span class="text-sm">⭐</span>
                                        <span class="text-[10px] text-indigo-600 font-semibold truncate">
                                            <?= number_format($player['total_xp'] ?? 0) ?> XP
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>