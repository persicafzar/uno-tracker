<?php
$currentStreak = $streakInfo['current_streak'];
$bestStreak = $streakInfo['best_streak'];
$isActive = $streakInfo['is_active'];
$isBroken = $streakInfo['is_broken'];
$lastWinAt = $streakInfo['last_win_at'];
$hoursSinceLastWin = $streakInfo['hours_since_last_win'];
$resetHours = $streakInfo['reset_hours'] ?? 24; // 🆕
?>

<div class="relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-24 h-24 bg-red-500/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

    <div class="relative z-10">
        <h2 class="text-lg sm:text-xl font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
            <span class="text-2xl sm:text-3xl">🔥</span>
            زنجیره پیروزی
        </h2>

        <div class="!grid !grid-cols-1 sm:!grid-cols-3 gap-3 sm:gap-4">
            <!-- Current Streak -->
            <div class="relative overflow-hidden bg-gradient-to-br <?= $isActive ? 'from-orange-100 to-red-100 border-orange-300' : 'from-gray-100 to-gray-200 border-gray-300' ?> rounded-2xl p-4 border-2 text-center shadow-md hover:shadow-lg transition-all duration-300">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <div class="text-4xl mb-2 drop-shadow"><?= $isActive ? '🔥' : '❄️' ?></div>
                    <div class="text-3xl font-black <?= $isActive ? 'text-orange-600' : 'text-gray-400' ?>">
                        <?= $currentStreak ?>
                    </div>
                    <div class="text-sm text-gray-600 font-bold mt-1">زنجیره فعلی</div>
                    <?php if ($isActive && $currentStreak > 0): ?>
                        <div class="text-xs text-green-600 font-black mt-2 bg-green-100 px-2 py-0.5 rounded-full inline-block border border-green-200">✅ فعال</div>
                    <?php elseif ($currentStreak === 0): ?>
                        <div class="text-xs text-gray-500 font-medium mt-2">هنوز شروع نشده</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Best Streak -->
            <div class="relative overflow-hidden bg-gradient-to-br from-yellow-100 to-amber-100 border-2 border-yellow-300 rounded-2xl p-4 text-center shadow-md hover:shadow-lg transition-all duration-300">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <div class="text-4xl mb-2 drop-shadow">🏆</div>
                    <div class="text-3xl font-black text-amber-600"><?= $bestStreak ?></div>
                    <div class="text-sm text-gray-600 font-bold mt-1">بهترین رکورد</div>
                    <?php if ($bestStreak >= 10): ?>
                        <div class="text-xs text-purple-600 font-black mt-2 bg-purple-100 px-2 py-0.5 rounded-full inline-block border border-purple-200">⭐ افسانه‌ای!</div>
                    <?php elseif ($bestStreak >= 5): ?>
                        <div class="text-xs text-orange-600 font-black mt-2 bg-orange-100 px-2 py-0.5 rounded-full inline-block border border-orange-200">🔥 عالی!</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Last Win Info -->
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-100 to-indigo-100 border-2 border-blue-300 rounded-2xl p-4 text-center shadow-md hover:shadow-lg transition-all duration-300">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <div class="text-4xl mb-2 drop-shadow">⏰</div>
                    <?php if ($lastWinAt): ?>
                        <div class="text-xl font-black text-blue-600">
                            <?= $hoursSinceLastWin !== null ? round($hoursSinceLastWin, 1) : 0 ?> ساعت
                        </div>
                        <div class="text-sm text-gray-600 font-bold mt-1">از آخرین برد</div>
                        <div class="text-xs font-black mt-2 <?= $hoursSinceLastWin < ($resetHours * 0.5) ? 'text-green-600' : ($hoursSinceLastWin < $resetHours ? 'text-orange-600' : 'text-red-600') ?>">
                            <?php if ($hoursSinceLastWin < $resetHours * 0.5): ?>
                                ✅ هنوز فرصت دارید
                            <?php elseif ($hoursSinceLastWin < $resetHours): ?>
                                ⚠️ زمان رو به اتمام است
                            <?php else: ?>
                                ❌ زنجیره شکسته شد
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-xl font-black text-gray-400">---</div>
                        <div class="text-sm text-gray-600 font-bold mt-1">هنوز بردی ندارید</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Streak Milestones -->
        <div class="mt-5 pt-4 border-t-2 border-gray-200/60">
            <h4 class="text-sm font-black text-gray-700 mb-3 flex items-center gap-2">
                <span>🎯</span> نقاط عطف زنجیره پیروزی
            </h4>
            <div class="!grid !grid-cols-2 sm:!grid-cols-4 gap-2">
                <?php
                $milestones = [
                    ['count' => 3, 'icon' => '🔥', 'title' => 'آتشین', 'color' => 'from-orange-100 to-red-100'],
                    ['count' => 5, 'icon' => '⚡', 'title' => 'طوفانی', 'color' => 'from-yellow-100 to-orange-100'],
                    ['count' => 10, 'icon' => '💥', 'title' => 'شکست‌ناپذیر', 'color' => 'from-red-100 to-pink-100'],
                    ['count' => 20, 'icon' => '🌟', 'title' => 'افسانه‌ای', 'color' => 'from-purple-100 to-indigo-100'],
                ];

                foreach ($milestones as $milestone):
                    $reached = $bestStreak >= $milestone['count'];
                ?>
                    <div class="bg-gradient-to-br <?= $milestone['color'] ?> rounded-xl p-2.5 text-center border-2 <?= $reached ? 'border-green-400' : 'border-gray-200 opacity-50' ?> hover:shadow-md transition-all duration-300 hover:scale-[1.02]">
                        <div class="text-2xl drop-shadow"><?= $milestone['icon'] ?></div>
                        <div class="text-sm font-black text-gray-800"><?= $milestone['count'] ?> برد</div>
                        <div class="text-xs text-gray-600 font-medium"><?= $milestone['title'] ?></div>
                        <?php if ($reached): ?>
                            <div class="text-xs text-green-600 font-black mt-1 bg-green-100 px-1.5 py-0.5 rounded-full inline-block border border-green-200">✅ کسب شده</div>
                        <?php else: ?>
                            <div class="text-xs text-gray-500 font-medium mt-1">🔒 قفل</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>