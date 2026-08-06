<?php
$xpInfo = $gamificationData['xp_info'];
$streakInfo = $gamificationData['streak_info'];
$levelData = $xpInfo['level_data'];
?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- سطح و XP -->
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-50 to-violet-100 rounded-2xl p-4 sm:p-5 border-2 border-indigo-200 shadow-xl">
        <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-violet-500/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shadow-lg flex-shrink-0"
                style="background: linear-gradient(135deg, <?= $levelData->color ?? '#6366f1' ?>, <?= $levelData->color ?? '#6366f1' ?>cc); color: white;">
                <?= $levelData->icon ?? '⭐' ?>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-black text-gray-800 text-base sm:text-lg">سطح <?= $xpInfo['current_level'] ?></div>
                        <div class="text-xs text-gray-500 font-medium"><?= htmlspecialchars($levelData->title ?? '') ?></div>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-black text-indigo-600"><?= $xpInfo['total_xp'] ?></div>
                        <div class="text-[10px] text-gray-400 font-medium">XP</div>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden shadow-inner">
                        <div class="h-full transition-all duration-700 rounded-full shadow-lg"
                            style="width: <?= $xpInfo['progress_percentage'] ?>%; background: linear-gradient(90deg, <?= $levelData->color ?? '#6366f1' ?>, <?= $levelData->color ?? '#6366f1' ?>cc);"></div>
                    </div>
                    <div class="text-[10px] text-gray-400 mt-1.5 text-center font-medium">
                        <?= $xpInfo['xp_to_next_level'] ?> XP تا سطح بعدی
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Streak & Achievements -->
    <div class="grid grid-cols-2 gap-4">
        <div class="relative overflow-hidden bg-gradient-to-br <?= $streakInfo['current_streak'] > 0 ? 'from-orange-100 to-red-100 border-orange-300' : 'from-gray-100 to-gray-200 border-gray-300' ?> rounded-2xl p-4 border-2 shadow-xl">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <div class="text-3xl mb-1 drop-shadow-lg"><?= $streakInfo['current_streak'] > 0 ? '🔥' : '❄️' ?></div>
                <div class="text-3xl font-black <?= $streakInfo['current_streak'] > 0 ? 'text-orange-600' : 'text-gray-400' ?>">
                    <?= $streakInfo['current_streak'] ?>
                </div>
                <div class="text-xs text-gray-600 font-bold mt-0.5">زنجیره پیروزی</div>
            </div>
        </div>

        <div class="relative overflow-hidden bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl p-4 border-2 border-purple-300 shadow-xl">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <div class="text-3xl mb-1 drop-shadow-lg">🏅</div>
                <div class="text-3xl font-black text-purple-600">
                    <?= $gamificationData['achievements_stats']['completed'] ?>/<?= $gamificationData['achievements_stats']['total'] ?>
                </div>
                <div class="text-xs text-gray-600 font-bold mt-0.5">مدال‌ها</div>
            </div>
        </div>
    </div>
</div>