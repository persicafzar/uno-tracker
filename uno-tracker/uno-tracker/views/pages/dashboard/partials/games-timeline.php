<?php
use Core\JalaliDate;

// گرفتن بازی‌ها از متغیر $timelineGames که Controller پاس می‌دهد
$games = $timelineGames ?? [];

if (empty($games)): ?>
    <div class="text-center py-8">
        <div class="text-5xl mb-3">📅</div>
        <p class="text-gray-500 text-sm sm:text-base">در این بازه زمانی بازی‌ای نداشته‌اید</p>
        <a href="/game/create" class="inline-block mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
            اولین بازی را شروع کنید
        </a>
    </div>
<?php else:
    // گروه‌بندی بازی‌ها بر اساس تاریخ شمسی
    $groupedGames = [];
    foreach ($games as $game) {
        $jDate = JalaliDate::fromGregorian(date('Y-m-d', strtotime($game['created_at'])));
        $dateKey = sprintf('%04d/%02d/%02d', $jDate['year'], $jDate['month'], $jDate['day']);
        $monthName = JalaliDate::getMonthName($jDate['month']);
        $fullDate = $jDate['day'] . ' ' . $monthName . ' ' . $jDate['year'];
        
        if (!isset($groupedGames[$dateKey])) {
            $groupedGames[$dateKey] = [
                'label' => $fullDate,
                'games' => [],
                'wins' => 0,
                'losses' => 0,
            ];
        }
        $groupedGames[$dateKey]['games'][] = $game;
        
        if (!empty($game['is_winner'])) {
            $groupedGames[$dateKey]['wins']++;
        } else {
            $groupedGames[$dateKey]['losses']++;
        }
    }
?>
    <div class="relative">
        <!-- خط عمودی تایم‌لاین -->
        <div class="absolute right-4 sm:right-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
        
        <div class="space-y-4 sm:space-y-6">
            <?php foreach ($groupedGames as $dateKey => $group): ?>
                <div class="relative pr-10 sm:pr-12">
                    <!-- نقطه تایم‌لاین -->
                    <div class="absolute right-2.5 sm:right-3.5 top-1 w-3 h-3 rounded-full border-2 <?= $group['wins'] > 0 ? 'bg-green-500 border-green-300' : 'bg-gray-400 border-gray-300' ?>"></div>
                    
                    <!-- عنوان تاریخ -->
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="font-bold text-gray-800 text-sm sm:text-base"><?= htmlspecialchars($group['label']) ?></h3>
                        <div class="flex items-center gap-1.5 text-xs">
                            <?php if ($group['wins'] > 0): ?>
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded-full font-semibold">
                                    🏆 <?= $group['wins'] ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($group['losses'] > 0): ?>
                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded-full font-semibold">
                                    💔 <?= $group['losses'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- بازی‌های این روز -->
                    <div class="space-y-2">
                        <?php foreach ($group['games'] as $game): ?>
                            <?php
                            $isWinner = !empty($game['is_winner']);
                            $isTeamMode = ($game['game_mode'] ?? '') === 'friendly';
                            $time = date('H:i', strtotime($game['created_at']));
                            ?>
                            <a href="/game/<?= $game['id'] ?>" 
                               class="block rounded-lg p-2.5 sm:p-3 border transition <?= $isWinner ? 'bg-green-50 border-green-200 hover:bg-green-100' : 'bg-gray-50 border-gray-200 hover:bg-gray-100' ?>">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <span class="text-base flex-shrink-0"><?= $isWinner ? '🏆' : '💔' ?></span>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-800 text-xs sm:text-sm truncate">
                                                <?= htmlspecialchars($game['name'] ?: 'بازی #' . $game['id']) ?>
                                            </div>
                                            <div class="text-[10px] sm:text-xs text-gray-500 mt-0.5">
                                                <?= $isTeamMode ? ' تیمی' : '👤 انفرادی' ?>
                                                • <?= $game['total_players'] ?? 0 ?> نفر
                                                <?php if (!empty($game['total_teams']) && $game['total_teams'] > 0): ?>
                                                    • <?= $game['total_teams'] ?> تیم
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <div class="flex items-center gap-1.5 sm:gap-2">
                                            <span class="text-xs sm:text-sm font-bold <?= $isWinner ? 'text-green-600' : 'text-gray-500' ?>">
                                                <?= $game['wins_count'] ?? 0 ?> برد
                                            </span>
                                            <span class="text-xs text-gray-400">|</span>
                                            <span class="text-xs sm:text-sm font-bold text-indigo-600">
                                                <?= $game['total_score'] ?? 0 ?> امتیاز
                                            </span>
                                        </div>
                                        <div class="text-[10px] sm:text-xs text-gray-400 mt-0.5">
                                            ⏰ <?= $time ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>