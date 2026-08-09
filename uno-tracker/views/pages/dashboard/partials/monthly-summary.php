<?php
$months = $monthlySummary ?? [];
?>

<?php if (empty($months)): ?>
    <div class="text-center py-12">
        <div class="text-6xl mb-4 opacity-50">📅</div>
        <p class="text-gray-500 text-sm font-medium">هنوز بازی‌ای انجام نداده‌اید</p>
        <a href="/game/create" class="inline-block mt-3 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-xl text-sm font-bold hover:shadow-2xl transition-all duration-300 hover:scale-[1.02]">
            اولین بازی را شروع کنید
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($months as $month): ?>
            <?php
            $winRate = $month['win_rate'] ?? 0;
            $isGoodMonth = $winRate >= 50;
            $isExcellentMonth = $winRate >= 70;
            $bgClass = $isExcellentMonth ? 'from-emerald-50 to-green-100 border-emerald-300 ring-1 ring-emerald-300' : ($isGoodMonth ? 'from-blue-50 to-indigo-100 border-blue-300 ring-1 ring-blue-300' : 'from-gray-50 to-slate-100 border-gray-300');
            ?>
            <div class="relative overflow-hidden bg-gradient-to-br <?= $bgClass ?> rounded-2xl p-4 sm:p-5 border shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-[1.02]">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-black text-gray-800 text-sm sm:text-base">
                            <?= htmlspecialchars($month['label']) ?>
                        </h3>
                        <span class="px-3 py-1 rounded-full text-xs font-black shadow-md <?= $isExcellentMonth ? 'bg-green-500 text-white' : ($isGoodMonth ? 'bg-blue-500 text-white' : 'bg-gray-500 text-white') ?>">
                            <?= $winRate ?>% برد
                        </span>
                    </div>

                    <div class="grid !grid-cols-2 gap-2.5 mb-3">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-2.5 text-center shadow-sm border border-white">
                            <div class="text-xl sm:text-2xl font-black text-indigo-600"><?= $month['games'] ?></div>
                            <div class="text-[10px] text-gray-500 font-semibold">بازی</div>
                        </div>
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-2.5 text-center shadow-sm border border-white">
                            <div class="text-xl sm:text-2xl font-black text-emerald-600"><?= $month['wins'] ?></div>
                            <div class="text-[10px] text-gray-500 font-semibold">برد 🏆</div>
                        </div>
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-2.5 text-center shadow-sm border border-white">
                            <div class="text-xl sm:text-2xl font-black text-rose-500"><?= $month['losses'] ?></div>
                            <div class="text-[10px] text-gray-500 font-semibold">باخت 💔</div>
                        </div>
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-2.5 text-center shadow-sm border border-white">
                            <div class="text-xl sm:text-2xl font-black text-violet-600"><?= $month['points'] ?></div>
                            <div class="text-[10px] text-gray-500 font-semibold">امتیاز</div>
                        </div>
                    </div>

                    <?php if ($month['team_games'] > 0 || $month['solo_games'] > 0): ?>
                        <div class="flex items-center gap-2 text-xs text-gray-600 mb-2 flex-wrap">
                            <?php if ($month['team_games'] > 0): ?>
                                <span class="px-2.5 py-0.5 bg-indigo-200/80 text-indigo-800 rounded-full font-bold border border-indigo-300/50">
                                    👥 تیمی: <?= $month['team_wins'] ?>/<?= $month['team_games'] ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($month['solo_games'] > 0): ?>
                                <span class="px-2.5 py-0.5 bg-purple-200/80 text-purple-800 rounded-full font-bold border border-purple-300/50">
                                    👤 انفرادی: <?= $month['solo_wins'] ?>/<?= $month['solo_games'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($month['games_list'])): ?>
                        <div class="border-t border-gray-200/60 pt-2.5 mt-2.5">
                            <div class="text-[10px] text-gray-500 font-semibold mb-1.5">آخرین بازی‌ها:</div>
                            <div class="space-y-1">
                                <?php foreach ($month['games_list'] as $game): ?>
                                    <div class="flex items-center gap-1.5 text-xs bg-white/50 backdrop-blur-sm px-2.5 py-1 rounded-lg">
                                        <span><?= $game['is_winner'] ? '🏆' : '💔' ?></span>
                                        <span class="truncate text-gray-700 font-medium"><?= htmlspecialchars($game['name']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>