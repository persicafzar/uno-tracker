<?php
$grouped = [];
foreach ($achievements as $achievement) {
    $category = $achievement->category;
    if (!isset($grouped[$category])) {
        $grouped[$category] = [
            'name' => $achievement->getCategoryName(),
            'achievements' => [],
            'completed_count' => 0,
            'total_count' => 0,
        ];
    }
    $grouped[$category]['achievements'][] = $achievement;
    $grouped[$category]['total_count']++;
    if ($achievement->user_completed) {
        $grouped[$category]['completed_count']++;
    }
}

$categoryIcons = [
    'general' => '🎮',
    'winning' => '🏆',
    'streak' => '🔥',
    'team' => '👥',
    'special' => '⭐',
];
?>

<div x-data="{ filter: 'all' }">
    <!-- Filter Tabs -->
    <div class="flex gap-2 mb-4 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-gray-300">
        <button @click="filter = 'all'"
            :class="filter === 'all' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 whitespace-nowrap flex-shrink-0 hover:shadow-md">
            همه (<?= $achievementsStats['total'] ?>)
        </button>
        <button @click="filter = 'completed'"
            :class="filter === 'completed' ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 whitespace-nowrap flex-shrink-0 hover:shadow-md">
            ✅ تکمیل شده (<?= $achievementsStats['completed'] ?>)
        </button>
        <button @click="filter = 'locked'"
            :class="filter === 'locked' ? 'bg-gradient-to-r from-gray-600 to-gray-700 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 whitespace-nowrap flex-shrink-0 hover:shadow-md">
            🔒 قفل (<?= $achievementsStats['locked'] ?>)
        </button>
    </div>

    <!-- Categories -->
    <?php foreach ($grouped as $categoryKey => $category): ?>
        <div class="mb-6"
            x-show="filter === 'all' || 
                     (filter === 'completed' && <?= $category['completed_count'] ?> > 0) || 
                     (filter === 'locked' && <?= ($category['total_count'] - $category['completed_count']) ?> > 0)">

            <!-- Category Header -->
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base sm:text-lg font-black text-gray-800 flex items-center gap-2">
                    <span class="text-2xl drop-shadow"><?= $categoryIcons[$categoryKey] ?? '🏅' ?></span>
                    <?= htmlspecialchars($category['name']) ?>
                </h3>
                <span class="text-xs text-gray-500 font-bold bg-gray-100 px-2.5 py-0.5 rounded-full border border-gray-200">
                    <?= $category['completed_count'] ?>/<?= $category['total_count'] ?>
                </span>
            </div>

            <!-- Achievements Grid -->
            <div class="!grid !grid-cols-1 sm:!grid-cols-2 lg:!grid-cols-3 gap-3">
                <?php foreach ($category['achievements'] as $achievement): ?>
                    <?php
                    $isCompleted = $achievement->user_completed;
                    $progress = $achievement->getProgressPercentage();
                    $userProgress = $achievement->user_progress ?? 0;
                    $rarityColor = $achievement->getRarityColor();
                    $rarityName = $achievement->getRarityName();
                    ?>
                    <div x-show="filter === 'all' || 
                                (filter === 'completed' && <?= $isCompleted ? 'true' : 'false' ?>) || 
                                (filter === 'locked' && <?= !$isCompleted ? 'true' : 'false' ?>)"
                        class="relative overflow-hidden bg-gradient-to-br <?= $isCompleted ? 'from-green-50 to-emerald-100 border-green-300' : 'from-gray-50 to-gray-100 border-gray-200' ?> rounded-2xl p-3.5 sm:p-4 border-2 transition-all hover:shadow-xl hover:scale-[1.02]">

                        <!-- Rarity Badge -->
                        <div class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full text-[10px] font-black text-white shadow-sm"
                            style="background-color: <?= $rarityColor ?>">
                            <?= $rarityName ?>
                        </div>

                        <!-- Completion Badge -->
                        <div class="absolute top-3 right-3 text-xl drop-shadow">
                            <?= $isCompleted ? '✅' : '🔒' ?>
                        </div>

                        <!-- Icon -->
                        <div class="text-center mt-6 mb-2">
                            <div class="inline-block text-4xl sm:text-5xl <?= !$isCompleted ? 'grayscale opacity-50' : '' ?> drop-shadow">
                                <?= $achievement->icon ?>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="text-center">
                            <h4 class="font-black text-gray-800 text-sm sm:text-base">
                                <?= htmlspecialchars($achievement->name) ?>
                            </h4>
                            <p class="text-xs text-gray-600 mt-1 line-clamp-2 font-medium">
                                <?= htmlspecialchars($achievement->description ?? '') ?>
                            </p>
                        </div>

                        <!-- Progress -->
                        <?php if (!$isCompleted): ?>
                            <div class="mt-3 text-center">
                                <span class="text-xs text-gray-600 font-bold">
                                    🎁 +<?= $achievement->xp_reward ?> امتیاز تجربه
                                </span>
                            </div>
                            <div class="mt-2.5">
                                <div class="flex justify-between text-[10px] text-gray-600 font-bold mb-0.5">
                                    <span><?= $userProgress ?>/<?= $achievement->condition_value ?></span>
                                    <span><?= round($progress) ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                        style="width: <?= max(2, $progress) ?>%; background: linear-gradient(90deg, #6366f1, #8b5cf6);">
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 text-center">
                                <span class="text-xs text-green-600 font-black bg-green-100 px-3 py-1 rounded-full border border-green-200 inline-block">
                                    🎁 +<?= $achievement->xp_reward ?> امتیاز تجربه
                                </span>
                                <?php if ($achievement->user_unlocked_at): ?>
                                    <div class="text-[10px] text-gray-500 font-medium mt-1.5">
                                        کسب شده در <?= \Core\JalaliDate::format('Y/m/d', strtotime($achievement->user_unlocked_at)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Empty States -->
    <div x-show="filter === 'completed' && <?= $achievementsStats['completed'] === 0 ? 'true' : 'false' ?>"
        class="text-center py-10">
        <div class="text-6xl mb-4 opacity-50">🏅</div>
        <p class="text-gray-500 font-medium">هنوز مدالی کسب نکرده‌اید</p>
        <p class="text-xs text-gray-400 font-medium mt-1">با بازی کردن، مدال‌های افتخار کسب کنید!</p>
    </div>

    <div x-show="filter === 'locked' && <?= $achievementsStats['locked'] === 0 ? 'true' : 'false' ?>"
        class="text-center py-10">
        <div class="text-6xl mb-4">🎉</div>
        <p class="text-gray-500 font-medium">تبریک! همه مدال‌ها را کسب کرده‌اید</p>
    </div>
</div>