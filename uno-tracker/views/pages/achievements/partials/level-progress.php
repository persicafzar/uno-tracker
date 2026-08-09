<?php
// 🆕 Null safety برای levelData
$levelData = $xpInfo['level_data'] ?? null;
$progressPercentage = $xpInfo['progress_percentage'] ?? 0;
$totalXp = $xpInfo['total_xp'] ?? 0;
$xpToNext = $xpInfo['xp_to_next_level'] ?? 0;
$currentLevel = $xpInfo['current_level'] ?? 1;

// 🆕 Fallback اگر levelData null است
if (!$levelData) {
    $levelData = (object)[
        'icon' => '⭐',
        'title' => 'سطح ' . $currentLevel,
        'color' => '#6366f1',
        'min_xp' => 0,
        'max_xp' => 1000,
    ];
}

// 🆕 اطمینان از وجود properties
$levelIcon = $levelData->icon ?? '⭐';
$levelTitle = $levelData->title ?? 'سطح ' . $currentLevel;
$levelColor = $levelData->color ?? '#6366f1';
$levelMinXp = $levelData->min_xp ?? 0;
$levelMaxXp = $levelData->max_xp ?? 1000;
?>

<div class="relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-24 h-24 bg-violet-500/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

    <div class="relative z-10">
        <h2 class="text-lg sm:text-xl font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
            <span class="text-2xl sm:text-3xl"><?= $levelIcon ?></span>
            سطح و پیشرفت
        </h2>

        <!-- Level Badge -->
        <div class="flex flex-col sm:flex-row items-center gap-5 mb-6">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full flex items-center justify-center shadow-2xl flex-shrink-0"
                style="background: linear-gradient(135deg, <?= $levelColor ?>, <?= $levelColor ?>dd);">
                <div class="text-center text-white">
                    <div class="text-3xl sm:text-4xl font-black drop-shadow"><?= $currentLevel ?></div>
                    <div class="text-[10px] font-bold opacity-80">سطح</div>
                </div>
            </div>

            <div class="flex-1 text-center sm:text-right">
                <h3 class="text-xl sm:text-2xl font-black" style="color: <?= $levelColor ?>">
                    <?= htmlspecialchars($levelTitle) ?>
                </h3>
                <p class="text-gray-500 text-sm font-medium mt-1">
                    <?= number_format($totalXp) ?> امتیاز تجربه از <?= number_format($levelMaxXp + 1) ?>
                </p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex justify-between text-xs font-bold text-gray-600 mb-1">
                <span>پیشرفت به سطح بعدی</span>
                <span><?= round($progressPercentage) ?>%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 sm:h-4 overflow-hidden shadow-inner">
                <div class="h-full rounded-full transition-all duration-700 flex items-center justify-center text-[10px] text-white font-black"
                    style="width: <?= max(5, $progressPercentage) ?>%; background: linear-gradient(90deg, <?= $levelColor ?>, <?= $levelColor ?>cc);">
                    <?php if ($progressPercentage > 15): ?>
                        <?= round($progressPercentage) ?>%
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-xs text-gray-500 font-medium mt-1.5 text-center">
                <strong class="text-indigo-600"><?= number_format($xpToNext) ?></strong> امتیاز دیگر برای رسیدن به سطح <?= $currentLevel + 1 ?>
            </div>
        </div>

        <!-- نقشه سطوح -->
        <div class="border-t-2 border-gray-200/60 pt-4 mt-4">
            <h4 class="text-sm font-black text-gray-700 mb-3 flex items-center gap-2">
                <span>🗺️</span> نقشه سطوح
            </h4>

            <div class="relative">
                <button onclick="scrollLevels(-1)"
                    class="hidden sm:flex absolute left-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white shadow-xl rounded-full items-center justify-center text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition border-2 border-gray-200 hover:border-indigo-300">
                    ←
                </button>
                <button onclick="scrollLevels(1)"
                    class="hidden sm:flex absolute right-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white shadow-xl rounded-full items-center justify-center text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition border-2 border-gray-200 hover:border-indigo-300">
                    →
                </button>

                <div id="levels-container"
                    class="flex gap-3 overflow-x-auto scroll-smooth py-4 px-1"
                    style="scrollbar-width: thin; scrollbar-color: #6366f1 #e5e7eb;">

                    <?php foreach ($allLevels as $level): ?>
                        <?php
                        $isCurrentLevel = $level->level === $currentLevel;
                        $isUnlocked = $level->level < $currentLevel;
                        ?>
                        <div class="flex-shrink-0 w-36 sm:w-40 group">
                            <?php if ($isCurrentLevel): ?>
                                <!-- سطح فعلی -->
                                <div class="relative rounded-2xl p-3 border-3 transition-all shadow-2xl scale-105"
                                    style="background: linear-gradient(135deg, <?= $level->color ?>20, <?= $level->color ?>05);
                                            border-color: <?= $level->color ?>; border-width: 3px;">
                                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 text-[10px] font-black text-white rounded-full whitespace-nowrap shadow-lg"
                                        style="background-color: <?= $level->color ?>">
                                        ⭐ الان اینجایید
                                    </div>
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                    <div class="flex justify-center mb-2 mt-2">
                                        <div class="w-14 h-14 rounded-full flex items-center justify-center text-3xl shadow-lg"
                                            style="background: linear-gradient(135deg, <?= $level->color ?>, <?= $level->color ?>cc); color: white;">
                                            <?= $level->icon ?>
                                        </div>
                                    </div>
                                    <div class="text-center mb-1">
                                        <div class="text-[10px] text-gray-500 font-bold">سطح</div>
                                        <div class="text-xl font-black text-gray-800"><?= $level->level ?></div>
                                    </div>
                                    <div class="text-center mb-2">
                                        <div class="text-xs font-black truncate" style="color: <?= $level->color ?>">
                                            <?= htmlspecialchars($level->title) ?>
                                        </div>
                                    </div>
                                    <div class="text-center pt-2 border-t-2" style="border-color: <?= $level->color ?>40">
                                        <div class="text-[10px] text-gray-600 font-bold">
                                            <?= number_format($level->min_xp) ?> - <?= number_format($level->max_xp) ?>
                                        </div>
                                        <div class="text-[9px] text-gray-500 font-medium">امتیاز تجربه</div>
                                    </div>
                                </div>

                            <?php elseif ($isUnlocked): ?>
                                <!-- سطح کسب شده -->
                                <div class="relative rounded-2xl p-3 border-2 transition-all hover:shadow-lg hover:scale-[1.02]"
                                    style="background: linear-gradient(135deg, #dcfce7, #f0fdf4);
                                            border-color: #86efac;">
                                    <div class="absolute top-2 right-2 text-green-500 text-lg drop-shadow">✅</div>
                                    <div class="flex justify-center mb-2 mt-1">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl shadow-md"
                                            style="background: linear-gradient(135deg, #22c55e, #16a34a); color: white;">
                                            <?= $level->icon ?>
                                        </div>
                                    </div>
                                    <div class="text-center mb-1">
                                        <div class="text-[10px] text-gray-500 font-bold">سطح</div>
                                        <div class="text-lg font-black text-green-700"><?= $level->level ?></div>
                                    </div>
                                    <div class="text-center mb-2">
                                        <div class="text-xs font-bold text-green-700 truncate">
                                            <?= htmlspecialchars($level->title) ?>
                                        </div>
                                    </div>
                                    <div class="text-center pt-2 border-t-2 border-green-200">
                                        <div class="text-[10px] text-green-700 font-bold">
                                            <?= number_format($level->min_xp) ?> - <?= number_format($level->max_xp) ?>
                                        </div>
                                        <div class="text-[9px] text-green-600 font-medium">امتیاز تجربه</div>
                                    </div>
                                </div>

                            <?php else: ?>
                                <!-- سطح قفل -->
                                <div class="relative rounded-2xl p-3 border-2 border-gray-200 transition-all opacity-60 hover:opacity-80 hover:shadow-md"
                                    style="background: #f9fafb;">
                                    <div class="absolute top-2 right-2 text-gray-400 text-lg">🔒</div>
                                    <div class="flex justify-center mb-2 mt-1">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl grayscale opacity-50">
                                            <?= $level->icon ?>
                                        </div>
                                    </div>
                                    <div class="text-center mb-1">
                                        <div class="text-[10px] text-gray-400 font-bold">سطح</div>
                                        <div class="text-lg font-black text-gray-400"><?= $level->level ?></div>
                                    </div>
                                    <div class="text-center mb-2">
                                        <div class="text-xs font-bold text-gray-400 truncate">
                                            <?= htmlspecialchars($level->title) ?>
                                        </div>
                                    </div>
                                    <div class="text-center pt-2 border-t-2 border-gray-200">
                                        <div class="text-[10px] text-gray-400 font-bold">
                                            <?= number_format($level->min_xp) ?> - <?= number_format($level->max_xp) ?>
                                        </div>
                                        <div class="text-[9px] text-gray-400 font-medium">امتیاز تجربه</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="text-center text-[10px] text-gray-400 font-medium mt-2 sm:hidden">
                ← برای مشاهده بیشتر، به چپ یا راست بکشید →
            </div>
        </div>
    </div>
</div>

<style>
    #levels-container::-webkit-scrollbar {
        height: 6px;
    }

    #levels-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #levels-container::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        border-radius: 10px;
    }

    #levels-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
    }
</style>

<script>
    function scrollLevels(direction) {
        const container = document.getElementById('levels-container');
        if (container) {
            container.scrollBy({
                left: direction * 200,
                behavior: 'smooth'
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('levels-container');
        if (container) {
            const currentLevelCard = container.querySelector('.scale-105');
            if (currentLevelCard) {
                setTimeout(() => {
                    currentLevelCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }, 600);
            }
        }
    });
</script>