<?php if (empty($recentGames)): ?>
    <div class="text-center py-10">
        <div class="text-6xl mb-3 opacity-50">🎮</div>
        <p class="text-gray-500 text-sm font-medium">هنوز بازی‌ای انجام نداده‌اید</p>
        <a href="/game/create" class="inline-block mt-3 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-xl text-sm font-bold hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
            اولین بازی را شروع کنید
        </a>
    </div>
<?php else: ?>
    <div class="space-y-2.5 sm:space-y-3">
        <?php foreach ($recentGames as $game): ?>
            <?php
            $isWinner = false;
            if (!empty($game['winner_participant_id'])) {
                $winnerParticipant = \Core\Database::getInstance()->fetchOne(
                    "SELECT user_id FROM game_participants WHERE id = ?",
                    [$game['winner_participant_id']]
                );
                $isWinner = $winnerParticipant && (int)$winnerParticipant['user_id'] === $currentUserId;
            }

            $isTeamMode = ($game['game_mode'] ?? '') === 'friendly';
            $status = $game['status'] ?? 'pending';
            $isActive = ($status === 'active');
            $isPaused = ($status === 'paused');
            $isFinished = ($status === 'finished');
            $isPending = ($status === 'pending');
            $isCancelled = ($status === 'cancelled');

            $userRole = $game['user_role'] ?? 'none';
            $roleIcon = '';
            $roleText = '';
            switch ($userRole) {
                case 'referee_only':
                    $roleIcon = '👨‍⚖️';
                    $roleText = 'داور';
                    break;
                case 'player_only':
                    $roleIcon = '🎮';
                    $roleText = 'بازیکن';
                    break;
                case 'both':
                    $roleIcon = '👨‍⚖️🎮';
                    $roleText = 'داور و بازیکن';
                    break;
                default:
                    $roleIcon = '👁️';
                    $roleText = 'تماشاچی';
            }

            $statusIcon = '';
            $statusText = '';
            $statusColor = '';
            switch ($status) {
                case 'active':
                    $statusIcon = '🔴';
                    $statusText = 'در حال اجرا';
                    $statusColor = 'bg-red-50 border-red-200 text-red-700';
                    break;
                case 'paused':
                    $statusIcon = '⏸️';
                    $statusText = 'متوقف';
                    $statusColor = 'bg-orange-50 border-orange-200 text-orange-700';
                    break;
                case 'finished':
                    $statusIcon = '✅';
                    $statusText = 'پایان یافته';
                    $statusColor = 'bg-green-50 border-green-200 text-green-700';
                    break;
                case 'pending':
                    $statusIcon = '⏳';
                    $statusText = 'در انتظار';
                    $statusColor = 'bg-yellow-50 border-yellow-200 text-yellow-700';
                    break;
                case 'cancelled':
                    $statusIcon = '❌';
                    $statusText = 'لغو شده';
                    $statusColor = 'bg-gray-50 border-gray-200 text-gray-600';
                    break;
            }

            $cardBg = '';
            if ($isActive) {
                $cardBg = 'bg-gradient-to-r from-red-50 to-red-50/50 border-red-300 ring-1 ring-red-300';
            } elseif ($isPaused) {
                $cardBg = 'bg-gradient-to-r from-orange-50 to-orange-50/50 border-orange-300';
            } elseif ($isWinner && $isFinished) {
                $cardBg = 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-300 ring-1 ring-green-300';
            } else {
                $cardBg = 'bg-gradient-to-r from-gray-50 to-gray-50/50 border-gray-200';
            }
            ?>
            <a href="/game/<?= $game['id'] ?>"
                class="<?= $isActive ? 'animate-pulse ' : '' ?>block rounded-xl p-3.5 sm:p-4 transition-all duration-300 border hover:shadow-xl <?= $cardBg ?> hover:scale-[1.01]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="text-2xl flex-shrink-0">
                            <?php if ($isWinner && $isFinished): ?>
                                <span class="drop-shadow-lg">🏆</span>
                            <?php elseif ($isActive): ?>
                                <span class="drop-shadow-lg">🔴</span>
                            <?php elseif ($isPaused): ?>
                                <span class="drop-shadow-lg">⏸️</span>
                            <?php else: ?>
                                <span class="opacity-50">💔</span>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-gray-800 text-sm sm:text-base truncate">
                                <?= htmlspecialchars($game['name'] ?: 'بازی #' . $game['id']) ?>
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-2 flex-wrap">
                                <span class="font-medium"><?= $isTeamMode ? '👥 تیمی' : '👤 انفرادی' ?></span>
                                <span class="hidden sm:inline text-gray-300">•</span>
                                <span><?= (int)($game['total_players'] ?? 0) ?> نفر</span>
                                <?php if (!empty($game['total_teams']) && $game['total_teams'] > 0): ?>
                                    <span class="hidden sm:inline text-gray-300">•</span>
                                    <span><?= $game['total_teams'] ?> تیم</span>
                                <?php endif; ?>
                                <span class="hidden sm:inline text-gray-300">•</span>
                                <span class="text-[10px] font-medium bg-white/50 px-1.5 py-0.5 rounded-full"><?= $roleIcon ?> <?= $roleText ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <div class="flex items-center gap-2 sm:gap-3 justify-start">
                            <span class="text-[10px] sm:text-xs px-2.5 py-1 rounded-full border font-bold <?= $statusColor ?> shadow-sm">
                                <?= $statusIcon ?> <?= $statusText ?>
                            </span>
                            <?php if ($isActive || $isPaused): ?>
                                <span class="text-[10px] text-gray-500 font-medium bg-white/50 px-2 py-0.5 rounded-full">
                                    دور <?= (int)($game['total_rounds_played'] ?? 0) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3 justify-start mt-1">
                            <span class="text-xs sm:text-sm font-black <?= $isWinner && $isFinished ? 'text-green-600' : 'text-gray-500' ?>">
                                <?= $game['wins_count'] ?? 0 ?> برد
                            </span>
                            <span class="text-xs sm:text-sm font-black text-indigo-600">
                                <?= $game['total_score'] ?? 0 ?> امتیاز
                            </span>
                        </div>
                        <div class="text-[10px] text-gray-400 mt-0.5 font-medium">
                            <?= \Core\JalaliDate::format('Y/m/d', strtotime($game['created_at'])) ?>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>