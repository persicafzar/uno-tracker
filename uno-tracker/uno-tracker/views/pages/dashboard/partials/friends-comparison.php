<?php
$comparison = $friendsComparison ?? [
    'friends' => [],
    'user_rank' => null,
    'total_players' => 0,
    'mode' => 'rivals',
    'period_label' => 'ابتدا تاکنون'
];
$friends = $comparison['friends'] ?? [];
$userRank = $comparison['user_rank'];
$totalPlayers = $comparison['total_players'] ?? 0;
$mode = $comparison['mode'] ?? 'rivals';
$periodLabel = $comparison['period_label'] ?? 'ابتدا تاکنون';
?>

<?php if (empty($friends)): ?>
    <div class="text-center py-12">
        <div class="text-6xl mb-4 opacity-50 drop-shadow-lg">👥</div>
        <?php if ($mode === 'rivals'): ?>
            <p class="text-gray-500 text-sm sm:text-base font-medium">
                در این بازه زمانی بازی‌ای با دیگران نداشته‌اید
            </p>
            <p class="text-gray-400 text-xs mt-1.5">
                💡 می‌توانید حالت "همه" را انتخاب کنید تا همه کاربران را ببینید
            </p>
        <?php else: ?>
            <p class="text-gray-500 text-sm sm:text-base font-medium">
                در این بازه زمانی هیچ کاربری بازی نکرده است
            </p>
        <?php endif; ?>
        <?php if ($canCreate): ?>
            <a href="/game/create" class="inline-block mt-4 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-xl text-sm font-bold hover:shadow-2xl transition-all duration-300 hover:scale-[1.02]">
                🎮 یک بازی جدید شروع کنید
            </a>
        <?php else: ?>
            <span class="inline-block mt-4 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-xl text-sm font-bold hover:shadow-2xl transition-all duration-300 hover:scale-[1.02]">
                🎮 شما فعلاً اجازه ساخت بازی را ندارید. لطفاً با مدیر سیستم تماس بگیرید.
            </span>
        <?php endif; ?>

    </div>
<?php else: ?>
    <!-- Info Banner با افکت شیشه‌ای -->
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/70 rounded-2xl p-3 sm:p-4 mb-4 shadow-md">
        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-400/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 bg-indigo-400/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10 items-center gap-2 text-xs sm:text-sm text-blue-800 font-medium">
            <!-- <span class="text-lg">ℹ️</span> -->
            <?php if ($mode === 'rivals'): ?>
                این لیست شامل کاربرانی است که از <strong class="text-blue-900"><?= htmlspecialchars($periodLabel) ?></strong> با شما در یک بازی مشترک بوده‌اند
            <?php else: ?>
                این لیست شامل همه کاربران سایت از <strong class="text-blue-900"><?= htmlspecialchars($periodLabel) ?></strong> است
            <?php endif; ?>
        </div>
    </div>

    <!-- User Rank Banner - با افکت درخشان -->
    <?php if ($userRank): ?>
        <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-4 sm:p-5 mb-5 text-white shadow-2xl hover:shadow-3xl transition-all duration-500">
            <!-- حلقه‌های تزئینی -->
            <div class="absolute top-0 right-0 w-56 h-56 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-4xl drop-shadow-2xl animate-bounce mt-5">🏆</span>
                    <div>
                        <div class="font-black text-lg sm:text-xl drop-shadow-lg">رتبه شما</div>
                        <div class="text-xs text-white/80 font-medium">
                            از بین <strong class="text-white drop-shadow"><?= $totalPlayers ?></strong> بازیکن
                            <?php if ($mode === 'rivals'): ?>
                                <span class="text-white/70">(رقبای شما)</span>
                            <?php else: ?>
                                <span class="text-white/70">(کل کاربران)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="text-center sm:text-right">
                    <div class="text-4xl sm:text-5xl font-black drop-shadow-2xl"><?= $userRank ?></div>
                    <div class="text-xs text-white/90 font-bold drop-shadow">
                        <?php if ($userRank === 1): ?>
                            🥇 نفر اول!
                        <?php elseif ($userRank === 2): ?>
                            🥈 نفر دوم
                        <?php elseif ($userRank === 3): ?>
                            🥉 نفر سوم
                        <?php elseif ($userRank <= 10): ?>
                            🏅 جزو ۱۰ نفر برتر
                        <?php else: ?>
                            از <?= $totalPlayers ?> نفر
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- فاصله با نفر اول -->
            <?php if ($userRank > 1 && !empty($friends) && isset($friends[0])): ?>
                <?php
                $leaderPoints = $friends[0]['total_points'];
                $userInList = false;
                $userPoints = 0;
                foreach ($friends as $f) {
                    if ((int)$f['id'] === ($currentUserId ?? 0)) {
                        $userInList = true;
                        $userPoints = $f['total_points'];
                        break;
                    }
                }

                if (!$userInList) {
                    $userData = \Core\Database::getInstance()->fetchOne(
                        "SELECT SUM(gp.total_score) as total_points
                         FROM game_participants gp
                         JOIN games g ON gp.game_id = g.id
                         WHERE gp.user_id = ? AND g.status = 'finished'",
                        [$currentUserId ?? 0]
                    );
                    $userPoints = (int)($userData['total_points'] ?? 0);
                }

                $gap = $leaderPoints - $userPoints;
                ?>
                <?php if ($gap > 0): ?>
                    <div class="relative z-10 mt-3 pt-3 border-t border-white/20 flex items-center gap-2 text-sm text-white/90">
                        <span class="text-lg">📊</span>
                        <span>فاصله با نفر اول: <strong class="text-white font-black drop-shadow"><?= $gap ?></strong> امتیاز</span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Friends List -->
    <div class="space-y-2.5">
        <?php foreach ($friends as $index => $friend): ?>
            <?php
            $rank = $index + 1;
            $isCurrentUser = (int)$friend['id'] === ($currentUserId ?? 0);

            $rankIcon = '';
            $rankBg = '';
            $rankBorder = '';
            $rankShadow = '';

            if ($rank === 1) {
                $rankIcon = '🥇';
                $rankBg = 'bg-gradient-to-r from-yellow-50 to-amber-50';
                $rankBorder = 'border-yellow-400';
                $rankShadow = 'shadow-yellow-200/50';
            } elseif ($rank === 2) {
                $rankIcon = '🥈';
                $rankBg = 'bg-gradient-to-r from-gray-50 to-slate-50';
                $rankBorder = 'border-gray-400';
                $rankShadow = 'shadow-gray-200/50';
            } elseif ($rank === 3) {
                $rankIcon = '🥉';
                $rankBg = 'bg-gradient-to-r from-orange-50 to-amber-50';
                $rankBorder = 'border-orange-400';
                $rankShadow = 'shadow-orange-200/50';
            } else {
                $rankBg = 'bg-gradient-to-r from-white to-gray-50/50';
                $rankBorder = 'border-gray-200';
                $rankShadow = 'shadow-gray-200/30';
            }
            ?>
            <div class="flex items-center gap-2 sm:gap-3 p-3 sm:p-4 rounded-2xl border-2 <?= $rankBg ?> <?= $rankBorder ?> <?= $rankShadow ?> <?= $isCurrentUser ? 'ring-2 ring-indigo-500 ring-offset-2 shadow-xl' : 'shadow-sm' ?> transition-all duration-300 hover:shadow-xl hover:scale-[1.01] group">
                <!-- Rank -->
                <div class="text-2xl sm:text-3xl w-12 h-12 flex items-center justify-center flex-shrink-0 drop-shadow">
                    <?php if ($rankIcon): ?>
                        <span><?= $rankIcon ?></span>
                    <?php else: ?>
                        <span class="text-xl font-black text-gray-400">#<?= $rank ?></span>
                    <?php endif; ?>
                </div>

                <!-- Avatar -->
                <?php if (!empty($friend['avatar_path'])): ?>
                    <img src="/storage/uploads/avatars/<?= htmlspecialchars($friend['avatar_path']) ?>"
                        class="!w-11 !h-11 sm:!w-14 sm:!h-14 aspect-square rounded-full object-cover border-3 border-white shadow-lg flex-shrink-0 group-hover:border-indigo-300 transition-all duration-300">
                <?php else: ?>
                    <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-indigo-200 to-violet-200 flex items-center justify-center border-3 border-white shadow-lg flex-shrink-0 text-2xl">
                        👤
                    </div>
                <?php endif; ?>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-gray-800 text-sm sm:text-base truncate flex items-center gap-2">
                        <span><?= htmlspecialchars($friend['nickname']) ?></span>
                        <?php if ($isCurrentUser): ?>
                            <span class="text-[10px] text-indigo-600 font-black bg-indigo-100 px-2 py-0.5 rounded-full border border-indigo-200">(شما)</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5 flex-wrap">
                        <span class="font-medium">🎮 <?= $friend['games_played'] ?> بازی</span>
                        <span class="hidden sm:inline text-gray-300">•</span>
                        <span class="text-emerald-600 font-bold">🏆 <?= $friend['total_wins'] ?> برد</span>
                        <span class="hidden sm:inline text-gray-300">•</span>
                        <span class="font-medium">📊 <?= $friend['win_rate'] ?>%</span>
                    </div>
                </div>

                <!-- Points -->
                <div class="text-right flex-shrink-0">
                    <div class="font-black text-indigo-600 text-lg sm:text-xl drop-shadow"><?= $friend['total_points'] ?></div>
                    <div class="text-[10px] sm:text-xs text-gray-400 font-semibold">امتیاز</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- نمایش تعداد کل اگر بیشتر از ۱۰ نفر باشد -->
    <?php if ($totalPlayers > 10): ?>
        <div class="text-center text-xs text-gray-500 mt-4 p-3 bg-gray-50/80 backdrop-blur-sm rounded-2xl border border-gray-200/50 shadow-sm">
            📊 نمایش ۱۰ نفر از <strong class="text-gray-700"><?= $totalPlayers ?></strong> بازیکن
            <?php if ($userRank && $userRank > 10): ?>
                <br class="sm:hidden">
                <span class="text-indigo-600 font-bold">
                    رتبه شما: <?= $userRank ?> از <?= $totalPlayers ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>