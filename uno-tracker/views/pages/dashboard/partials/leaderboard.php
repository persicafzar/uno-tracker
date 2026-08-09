<?php if (empty($leaderboard['players'])): ?>
    <div class="text-center py-8">
        <div class="text-4xl mb-2">🏆</div>
        <p class="text-gray-600 text-sm">هنوز بازیکنی در لیدربورد نیست</p>
    </div>
<?php else: ?>
    <div class="space-y-2">
        <?php foreach ($leaderboard['players'] as $index => $player): ?>
            <?php
            $rank = $index + 1;
            // 🆕 استفاده از $currentUserId به جای $this->auth->id()
            $isCurrentUser = (int)$player['id'] === ($currentUserId ?? 0);
            $rankIcon = '';
            $rankBg = '';

            if ($rank === 1) {
                $rankIcon = '🥇';
                $rankBg = 'bg-yellow-50 border-yellow-200';
            } elseif ($rank === 2) {
                $rankIcon = '🥈';
                $rankBg = 'bg-gray-50 border-gray-300';
            } elseif ($rank === 3) {
                $rankIcon = '🥉';
                $rankBg = 'bg-orange-50 border-orange-200';
            } else {
                $rankBg = 'bg-white border-gray-200';
            }
            ?>
            <div class="flex items-center gap-2 sm:gap-3 p-2 sm:p-3 rounded-lg border <?= $rankBg ?> <?= $isCurrentUser ? 'ring-2 ring-indigo-300' : '' ?>">
                <div class="text-lg sm:text-xl font-bold w-8 text-center">
                    <?= $rankIcon ?: $rank ?>
                </div>
                <?php if (!empty($player['avatar_path'])): ?>
                    <img src="/storage/uploads/avatars/<?= htmlspecialchars($player['avatar_path']) ?>"
                        class="!w-8 !h-8 sm:!w-10 sm:!h-10 aspect-square rounded-full object-cover border border-gray-300">
                <?php else: ?>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-200 flex items-center justify-center border border-gray-300">👤</div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-800 text-sm truncate">
                        <?= htmlspecialchars($player['nickname']) ?>
                        <?php if ($isCurrentUser): ?>
                            <span class="text-xs text-indigo-600">(شما)</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-xs text-gray-500">
                        <?= $player['total_games'] ?> بازی • نرخ برد <?= $player['win_rate'] ?>%
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="font-bold text-indigo-600 text-sm sm:text-base"><?= $player['total_points'] ?></div>
                    <div class="text-xs text-gray-500">امتیاز</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($leaderboard['current_user_rank']) && $leaderboard['current_user_rank'] > 10): ?>
        <div class="text-center text-xs text-gray-500 mt-3">
            رتبه شما: <strong class="text-indigo-600"><?= $leaderboard['current_user_rank'] ?></strong>
        </div>
    <?php endif; ?>
<?php endif; ?>