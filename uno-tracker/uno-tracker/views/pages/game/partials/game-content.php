<?php

use Core\JalaliDate;

$currentUser = $currentUser ?? $user ?? null;
$isCurrentReferee = $currentUser && $game->referee_id === (int)$currentUser['id'];
$isAdminUser = $isAdmin ?? false;

$firstPlayer = null;
if (!empty($game->first_player_participant_id)) {
    foreach ($game->participants as $p) {
        if ($p->id == $game->first_player_participant_id) {
            $firstPlayer = $p;
            break;
        }
    }
}

$referee = \Core\Database::getInstance()->fetchOne(
    "SELECT id, nickname, avatar_path FROM users WHERE id = ?",
    [$game->referee_id]
);
?>

<div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6 space-y-4">

    <!-- Toast Messages -->
    <?php if (!empty($success)): ?>
        <script>
            (function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        iconColor: '#059669',
                        title: '<?= addslashes($success) ?>',
                        showConfirmButton: false,
                        timer: 4500,
                        timerProgressBar: true,
                        background: '#f0fdf4',
                        color: '#065f46',
                        border: '1px solid #10b981',
                        width: '420px',
                        padding: '1rem 1.5rem',
                        showClass: {
                            popup: 'animate__animated animate__slideInRight'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__slideOutRight'
                        },
                        customClass: {
                            title: 'text-base font-semibold',
                            popup: 'rounded-xl shadow-xl',
                            timerProgressBar: 'bg-emerald-500'
                        }
                    });
                }
            })();
        </script>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <script>
            (function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        iconColor: '#dc2626',
                        title: '<?= addslashes($error) ?>',
                        showConfirmButton: false,
                        timer: 4500,
                        timerProgressBar: true,
                        background: '#fef2f2',
                        color: '#991b1b',
                        border: '1px solid #dc2626',
                        width: '420px',
                        padding: '1rem 1.5rem',
                        showClass: {
                            popup: 'animate__animated animate__slideInRight'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__slideOutRight'
                        },
                        customClass: {
                            title: 'text-base font-semibold',
                            popup: 'rounded-xl shadow-xl',
                            timerProgressBar: 'bg-red-500'
                        }
                    });
                }
            })();
        </script>
    <?php endif; ?>

    <!-- Game Header -->
    <div class="relative bg-gradient-to-br from-indigo-700 via-purple-700 to-pink-700 rounded-2xl p-4 sm:p-7 text-white shadow-2xl overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/3"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

        <div class="relative z-10 flex flex-col gap-4">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="text-2xl sm:text-4xl drop-shadow-lg">🎮</span>
                    <h1 class="text-base sm:text-3xl font-black tracking-tight drop-shadow-lg leading-tight">
                        <?= htmlspecialchars($game->name ?: 'بازی #' . $game->id) ?>
                    </h1>
                </div>
                <span class="relative px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-[10px] sm:text-sm font-bold whitespace-nowrap shadow-lg
                    <?php
                    switch ($game->status) {
                        case 'pending':
                            echo 'bg-amber-400 text-amber-900';
                            break;
                        case 'active':
                            echo 'bg-emerald-400 text-emerald-900 animate-pulse ring-2 ring-emerald-300 ring-offset-2 ring-offset-transparent';
                            break;
                        case 'paused':
                            echo 'bg-orange-400 text-orange-900';
                            break;
                        case 'finished':
                            echo 'bg-sky-400 text-sky-900';
                            break;
                        case 'cancelled':
                            echo 'bg-rose-400 text-rose-900';
                            break;
                    }
                    ?>">
                    <?php
                    $statusLabels = [
                        'pending' => '⏳ در انتظار',
                        'active' => '🔴 فعال',
                        'paused' => '⏸️ متوقف',
                        'finished' => '✅ پایان',
                        'cancelled' => '❌ لغو',
                    ];
                    echo $statusLabels[$game->status];
                    ?>
                </span>
            </div>

            <div class="!grid !grid-cols-2 gap-3 sm:gap-4">
                <?php if ($referee): ?>
                    <div class="bg-white/15 backdrop-blur-md rounded-2xl p-3 sm:p-4 border-2 border-white/40 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-20 h-20 bg-amber-400/20 rounded-full blur-2xl"></div>
                        <div class="flex items-center gap-1.5 mb-2">
                            <div class="p-1 bg-amber-400/30 rounded-full">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-amber-200/90 text-[10px] sm:text-xs font-bold tracking-wider">داور بازی</span>
                            <span class="mr-auto text-amber-300 text-xs sm:text-sm">🖐️</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3">
                            <?php if (!empty($referee['avatar_path'])): ?>
                                <img src="/storage/uploads/avatars/<?= htmlspecialchars($referee['avatar_path']) ?>"
                                    class="!w-10 !h-10 sm:!w-14 sm:!h-14 aspect-square rounded-full object-cover border-2 border-amber-300/80 shadow-lg flex-shrink-0">
                            <?php else: ?>
                                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-amber-400/30 border-2 border-amber-300/80 flex items-center justify-center text-xl sm:text-3xl">👤</div>
                            <?php endif; ?>
                            <a href="#" onclick="openProfile('/users/<?= $referee['id'] ?>/partial'); return false;"
                                class="text-white hover:text-amber-200 transition font-bold text-xs sm:text-base truncate text-center sm:text-right">
                                <?= htmlspecialchars($referee['nickname']) ?>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/10 opacity-50">
                        <div class="flex items-center gap-1.5 mb-2">
                            <div class="p-1 bg-white/10 rounded-full">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-white/50 text-[10px] sm:text-xs font-medium">داور بازی</span>
                        </div>
                        <div class="text-white/50 text-xs sm:text-sm text-center">تعیین نشده</div>
                    </div>
                <?php endif; ?>

                <?php if ($firstPlayer && ($game->isActive() || $game->isFinished() || $game->isPaused())): ?>
                    <div class="bg-gradient-to-br from-amber-500/20 to-yellow-500/20 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/30 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                        <div class="flex items-center gap-1.5 mb-2">
                            <div class="p-1 bg-amber-400/30 rounded-full">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <span class="text-amber-200/80 text-[10px] sm:text-xs font-medium tracking-wide">شروع‌کننده</span>
                            <span class="mr-auto text-amber-300 text-xs sm:text-sm">⚡</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3">
                            <?php if ($firstPlayer->avatar_path): ?>
                                <img src="/storage/uploads/avatars/<?= htmlspecialchars($firstPlayer->avatar_path) ?>"
                                    class="!w-10 !h-10 sm:!w-14 sm:!h-14 aspect-square rounded-full object-cover border-2 border-amber-300/80 shadow-md flex-shrink-0">
                            <?php else: ?>
                                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-amber-400/30 border-2 border-amber-300/80 flex items-center justify-center text-xl sm:text-3xl">👤</div>
                            <?php endif; ?>
                            <div class="min-w-0 flex-1 text-center sm:text-right">
                                <div class="font-bold text-white text-xs sm:text-base truncate">
                                    <?= htmlspecialchars($firstPlayer->getDisplayName()) ?>
                                </div>
                                <?php if ($firstPlayer->team_name): ?>
                                    <div class="text-amber-200/80 text-[10px] sm:text-xs truncate">تیم <?= htmlspecialchars($firstPlayer->team_name) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/10 opacity-50">
                        <div class="flex items-center gap-1.5 mb-2">
                            <div class="p-1 bg-white/10 rounded-full">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <span class="text-white/50 text-[10px] sm:text-xs font-medium">شروع‌کننده</span>
                        </div>
                        <div class="text-white/50 text-xs sm:text-sm text-center">تعیین نشده</div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="!grid !grid-cols-2 sm:!grid-cols-4 gap-2 sm:gap-3">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-2 sm:p-3 text-center border border-white/10 hover:bg-white/20 transition-all duration-300">
                    <div class="flex items-center justify-center gap-1 text-white/70 text-[10px] sm:text-xs"><span>🎯</span><span>هدف</span></div>
                    <div class="font-black text-lg sm:text-2xl text-white drop-shadow"><?= $game->target_wins ?></div>
                    <div class="text-[8px] sm:text-[10px] text-white/50">برد</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-2 sm:p-3 text-center border border-white/10 hover:bg-white/20 transition-all duration-300">
                    <div class="flex items-center justify-center gap-1 text-white/70 text-[10px] sm:text-xs"><span>🔄</span><span>دورها</span></div>
                    <div class="font-black text-lg sm:text-2xl text-white drop-shadow"><?= $game->total_rounds_played ?></div>
                    <div class="text-[8px] sm:text-[10px] text-white/50">دور</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-2 sm:p-3 text-center border border-white/10 hover:bg-white/20 transition-all duration-300">
                    <div class="flex items-center justify-center gap-1 text-white/70 text-[10px] sm:text-xs"><span>👥</span><span>بازیکنان</span></div>
                    <div class="font-black text-lg sm:text-2xl text-white drop-shadow"><?= count($game->participants) ?></div>
                    <div class="text-[8px] sm:text-[10px] text-white/50">نفر</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-2 sm:p-3 text-center border border-white/10 hover:bg-white/20 transition-all duration-300">
                    <div class="flex items-center justify-center gap-1 text-white/70 text-[10px] sm:text-xs"><span>📅</span><span>تاریخ</span></div>
                    <div class="font-black text-xs sm:text-base text-white drop-shadow"><?= JalaliDate::format('Y/m/d', strtotime($game->created_at)); ?></div>
                    <div class="text-[8px] sm:text-[10px] text-white/50">شروع</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Game Finished Banner -->
    <?php if ($game->isFinished()): ?>
        <div class="relative bg-gradient-to-r from-amber-400 via-orange-400 to-rose-400 rounded-2xl p-6 shadow-2xl animate-bounce-in overflow-hidden">
            <div class="absolute top-0 left-0 w-32 h-32 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-40 h-40 bg-white/5 rounded-full translate-x-1/3 translate-y-1/3"></div>
            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <div class="mt-5 relative">
                    <div class="absolute inset-0 bg-yellow-300 blur-2xl opacity-50 animate-pulse"></div>
                    <span class="text-7xl sm:text-8xl animate-bounce relative block drop-shadow-2xl">🏆</span>
                </div>
                <div class="text-center sm:text-right flex-1">
                    <div class="inline-flex items-center gap-3 mb-3">
                        <div class="w-8 h-1 bg-white/60 rounded-full"></div>
                        <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight drop-shadow-lg">بازی پایان یافت!</h3>
                        <div class="w-8 h-1 bg-white/60 rounded-full"></div>
                    </div>
                    <p class="text-white/95 text-base sm:text-lg font-medium">
                        برنده:
                        <?php
                        $winner = $game->getWinner();
                        if ($winner) {
                            if ($game->isTeamMode() && $winner->team_id) {
                                $teamName = null;
                                foreach ($game->teams as $team) {
                                    if ($team->id === $winner->team_id) {
                                        $teamName = $team->name;
                                        break;
                                    }
                                }
                                if ($teamName) {
                                    echo '<strong class="text-yellow-200 text-xl sm:text-2xl drop-shadow-md">🏅 تیم ' . htmlspecialchars($teamName) . '</strong>';
                                } else {
                                    echo '<strong class="text-yellow-200 text-xl sm:text-2xl drop-shadow-md">' . htmlspecialchars($winner->getDisplayName()) . '</strong>';
                                }
                            } else {
                                echo '<strong class="text-yellow-200 text-xl sm:text-2xl drop-shadow-md">' . htmlspecialchars($winner->getDisplayName()) . '</strong>';
                            }
                        } else {
                            echo '<strong class="text-yellow-200 text-xl sm:text-2xl drop-shadow-md">نامشخص</strong>';
                        }
                        ?>
                    </p>
                    <div class="mt-2 inline-flex items-center gap-2 px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                        <span class="text-white/80 text-xs font-medium">⏱️</span>
                        <span class="text-white/90 text-sm font-semibold">در <?= $game->total_rounds_played ?> دور</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Scoreboard -->
    <div id="scoreboard" class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
        <?php include __DIR__ . '/scoreboard.php'; ?>
    </div>

    <!-- Round Form -->
    <?php if ($isCurrentReferee && $game->isActive()): ?>
        <?php $hasWinner = $game->getWinner() !== null; ?>
        <?php if ($hasWinner): ?>
            <div class="bg-orange-50 border-2 border-orange-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <span class="text-3xl">⚠️</span>
                    <div>
                        <h3 class="text-lg font-bold text-orange-800">بازی به هدف خود رسیده است</h3>
                        <p class="text-orange-700 text-sm mt-2">نمی‌توان دور دیگری ثبت کرد. لطفاً بازی را پایان دهید.</p>
                        <p class="text-orange-600 text-xs mt-2">💡 اگر می‌خواهید ادامه دهید، هدف برد را افزایش دهید.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div id="round-form-wrapper">
                <?php include __DIR__ . '/round-form.php'; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Referee Controls -->
    <?php if ($isCurrentReferee && !$game->isCancelled() && !$game->isFinished()): ?>
        <div class="bg-gradient-to-br from-gray-50/80 to-white rounded-2xl shadow-lg border border-gray-200/70 p-5 transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b-2 border-gray-200/50">
                <span class="text-2xl sm:text-3xl drop-shadow-lg">🎮</span>
                <h3 class="text-base sm:text-lg font-black text-gray-800 tracking-tight">کنترل‌های بازی</h3>
            </div>
            <div class="!grid !grid-cols-2 gap-3">
                <?php if ($game->canStart()): ?>
                    <button type="button" onclick="startGame(<?= $game->id ?>)" class="group px-4 py-3.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl shadow-md font-bold text-sm flex items-center justify-center gap-2.5 relative overflow-hidden">
                        <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                        <span class="text-xl group-hover:rotate-12 transition-transform duration-300 relative z-10">▶️</span>
                        <span class="relative z-10">شروع بازی</span>
                    </button>
                <?php endif; ?>
                <?php if ($game->canFinish()): ?>
                    <button type="button" onclick="confirmAction('آیا مطمئن هستید که می‌خواهید بازی را پایان دهید؟', '/game/<?= $game->id ?>/finish', 'پایان بازی', 'success')" class="group px-4 py-3.5 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl shadow-md font-bold text-sm flex items-center justify-center gap-2.5 relative overflow-hidden">
                        <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                        <span class="text-xl group-hover:rotate-12 transition-transform duration-300 relative z-10">🏆</span>
                        <span class="relative z-10">پایان بازی</span>
                    </button>
                <?php endif; ?>
                <?php if ($game->canPause()): ?>
                    <button type="button" onclick="confirmAction('آیا مطمئن هستید که می‌خواهید بازی را متوقف کنید؟', '/game/<?= $game->id ?>/pause', 'توقف بازی', 'warning')" class="group px-4 py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl shadow-md font-bold text-sm flex items-center justify-center gap-2.5 relative overflow-hidden">
                        <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                        <span class="text-xl group-hover:scale-110 transition-transform duration-300 relative z-10">⏸️</span>
                        <span class="relative z-10">توقف</span>
                    </button>
                <?php endif; ?>
                <?php if ($game->canResume()): ?>
                    <button type="button" onclick="confirmAction('آیا مطمئن هستید که می‌خواهید بازی را ادامه دهید؟', '/game/<?= $game->id ?>/resume', 'ادامه بازی', 'info')" class="group px-4 py-3.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl shadow-md font-bold text-sm flex items-center justify-center gap-2.5 relative overflow-hidden">
                        <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                        <span class="text-xl group-hover:scale-110 transition-transform duration-300 relative z-10">▶️</span>
                        <span class="relative z-10">ادامه</span>
                    </button>
                <?php endif; ?>
                <button type="button" onclick="confirmAction('آیا مطمئن هستید که می‌خواهید بازی را لغو کنید؟', '/game/<?= $game->id ?>/cancel', 'لغو بازی', 'error')" class="group px-4 py-3.5 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl shadow-md font-bold text-sm flex items-center justify-center gap-2.5 relative overflow-hidden">
                    <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                    <span class="text-xl group-hover:scale-110 transition-transform duration-300 relative z-10">❌</span>
                    <span class="relative z-10">لغو بازی</span>
                </button>
                <button type="button" onclick="editTargetWins(<?= $game->id ?>, <?= $game->target_wins ?>, <?= $maxWins ?? 0 ?>)" class="group px-4 py-3.5 bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 text-white rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl shadow-md font-bold text-sm flex items-center justify-center gap-2.5 relative overflow-hidden">
                    <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                    <span class="text-xl group-hover:scale-110 transition-transform duration-300 relative z-10">🎯</span>
                    <span class="relative z-10">هدف برد</span>
                </button>
                <button type="button" onclick="transferReferee(<?= $game->id ?>)" class="group px-4 py-3.5 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl shadow-md font-bold text-sm flex items-center justify-center gap-2.5 relative overflow-hidden">
                    <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                    <span class="text-xl group-hover:rotate-12 transition-transform duration-300 relative z-10">👤</span>
                    <span class="relative z-10">انتقال داور</span>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Rounds Timeline -->
    <?php include __DIR__ . '/rounds-timeline.php'; ?>

</div>

<!-- Modals -->
<?php if ($isCurrentReferee && !$game->isFinished() && !$game->isCancelled()): ?>
    <div id="edit-target-wins-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2"><span class="text-xl">🎯</span> ویرایش هدف برد</h3>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form id="edit-target-wins-form" hx-post="/game/<?= $game->id ?>/edit-rounds" hx-target="#game-page-content" hx-swap="innerHTML">
                    <input type="hidden" name="max_wins" id="max-wins-hidden" value="<?= $maxWins ?? 0 ?>">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">هدف برد جدید</label>
                        <input type="number" name="target_wins" id="target-wins-input" min="1" value="<?= $game->target_wins ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        <p class="text-xs text-gray-500 mt-2">💡 بازیکنی/تیمی که به این تعداد برد برسد، برنده بازی خواهد بود</p>
                        <p class="text-xs text-blue-600 mt-1" id="target-wins-hint">
                            <?php if (($maxWins ?? 0) > 0): ?>
                                ⚠️ هدف جدید نمی‌تواند کمتر از بالاترین تعداد برد فعلی (<?= $maxWins ?>) باشد
                            <?php else: ?>
                                ⚠️ هدف برد باید حداقل ۱ باشد
                            <?php endif; ?>
                        </p>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex-shrink-0">
                <div class="flex gap-2">
                    <button type="submit" form="edit-target-wins-form" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">ذخیره</button>
                    <button type="button" onclick="closeEditTargetWinsModal()" class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-semibold transition">انصراف</button>
                </div>
            </div>
        </div>
    </div>

    <div id="transfer-referee-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2"><span class="text-xl">👤</span> انتقال نقش داور</h3>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form id="transfer-referee-form" hx-post="/game/<?= $game->id ?>/transfer-referee" hx-target="#game-page-content" hx-swap="innerHTML">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">انتخاب داور جدید</label>
                        <select name="new_referee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" required>
                            <option value="">-- انتخاب کنید --</option>
                            <?php
                            $currentUserId = $currentUser['id'] ?? 0;
                            $users = \Core\Database::getInstance()->fetchAll("SELECT id, nickname FROM users WHERE status = 'active' AND id != ? ORDER BY nickname", [$currentUserId]);
                            foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nickname']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-yellow-800">⚠️ بعد از انتقال، شما دیگر داور این بازی نخواهید بود.</p>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex-shrink-0">
                <div class="flex gap-2">
                    <button type="submit" form="transfer-referee-form" class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition">انتقال</button>
                    <button type="button" onclick="closeTransferRefereeModal()" class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-semibold transition">انصراف</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ========================================== -->
<!-- ======= Profile Drawer ======= -->
<!-- ========================================== -->
<div x-data="profileDrawer()"
    x-init="init()"
    x-show="showProfile"
    x-cloak
    class="fixed inset-0 z-[999] overflow-hidden">

    <div x-show="showProfile"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

    <div x-show="showProfile"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 left-0 max-w-md w-full bg-white shadow-2xl overflow-y-auto border-l-2 border-gray-200/50 z-[1000]">

        <div class="backdrop-blur-sm bg-white/90 border-b-2 border-gray-200/60 flex items-center justify-between px-4 py-3.5 sm:px-6 sm:py-4 sticky top-0 z-50">
            <h3 class="text-base sm:text-lg font-black text-gray-800 tracking-tight">پروفایل کاربر</h3>
            <button
                @click.stop="close()"
                class="text-gray-500 hover:text-gray-700 transition-all duration-200 p-3 rounded-xl hover:bg-gray-100 touch-manipulation active:bg-gray-200"
                style="min-width: 48px; min-height: 48px;"
                aria-label="بستن پروفایل">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div id="profile-drawer-content" class="p-4 sm:p-6">
            <div class="text-center text-gray-500 py-12">
                <div class="animate-spin inline-block w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full mb-4"></div>
                <p class="text-sm sm:text-base font-medium">در حال بارگذاری...</p>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- ======= Profile Drawer Script ======= -->
<!-- ========================================== -->
<script>
    function profileDrawer() {
        return {
            showProfile: false,
            abortController: null, // 🆕 برای لغو درخواست‌های قبلی

            init() {
                // 🆕 استفاده از event delegation به جای listener مستقیم
                document.addEventListener('click', (e) => {
                    const trigger = e.target.closest('[data-open-profile]');
                    if (trigger) {
                        e.preventDefault();
                        this.open(trigger.dataset.openProfile);
                    }
                });

                window.addEventListener('popstate', () => {
                    if (this.showProfile) {
                        this.close();
                    }
                });
            },

            open(url) {
                // 🆕 لغو درخواست قبلی
                if (this.abortController) {
                    this.abortController.abort();
                }
                this.abortController = new AbortController();

                if (!this.showProfile) {
                    history.pushState({
                        drawer: true
                    }, '');
                }
                this.showProfile = true;
                document.body.style.overflow = 'hidden';

                const contentDiv = document.getElementById('profile-drawer-content');
                contentDiv.innerHTML = `
                    <div class="text-center text-gray-500 py-12">
                        <div class="animate-spin inline-block w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full mb-4"></div>
                        <p class="text-sm font-medium">در حال بارگذاری...</p>
                    </div>
                `;

                fetch(url, {
                        cache: 'no-store',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: this.abortController.signal // 🆕 signal برای لغو
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        return response.text();
                    })
                    .then(html => {
                        contentDiv.innerHTML = html;
                        // 🆕 Reinit Alpine فقط برای محتوای جدید
                        if (typeof Alpine !== 'undefined') {
                            Alpine.initTree(contentDiv);
                        }
                    })
                    .catch(error => {
                        if (error.name !== 'AbortError') {
                            console.error('❌ Profile load error:', error);
                            contentDiv.innerHTML = `
                            <div class="text-center text-rose-600 py-8">
                                <div class="text-4xl mb-2">⚠️</div>
                                <p class="font-bold">خطا در بارگذاری</p>
                            </div>
                        `;
                        }
                    });
            },

            close() {
                if (!this.showProfile) return;
                this.showProfile = false;
                document.body.style.overflow = '';

                // 🆕 لغو درخواست در حال انتظار
                if (this.abortController) {
                    this.abortController.abort();
                    this.abortController = null;
                }

                if (history.state && history.state.drawer) {
                    history.replaceState(null, '', location.href);
                }
            }
        }
    }

    // 🆕 تابع ساده برای باز کردن پروفایل
    window.openProfile = function(url) {
        const drawer = document.querySelector('[x-data*="profileDrawer"]');
        if (drawer && drawer.__x) {
            drawer.__x.$data.open(url);
        } else {
            // Fallback
            window.location.href = url;
        }
    };
</script>

<!-- Settings -->
<script>
    window.BASE_URL = '<?= base_url() ?>';
    console.log('🌐 BASE_URL set to:', window.BASE_URL);
</script>

<script>
    window.GAME_CONFIG = {
        gameId: <?= (int)($game->id ?? 0) ?>,
        currentUserId: <?= (int)($currentUser['id'] ?? $user['id'] ?? 0) ?>,
        isReferee: <?= ($isReferee ?? false) ? 'true' : 'false' ?>,
        maxWins: <?= (int)($maxWins ?? 0) ?>,
        participants: <?= json_encode(
                            array_map(fn($p) => [
                                'id' => $p->id,
                                'user_id' => $p->user_id,
                                'name' => $p->getDisplayName(),
                                'team_id' => $p->team_id,
                            ], $game->participants ?? [])
                        ) ?>
    };
    console.log('🎮 GAME_CONFIG initialized:', window.GAME_CONFIG);
</script>

<script src="/assets/js/game-actions.js"></script>
<script src="/assets/js/game-sse.js"></script>

<?php if (($isReferee ?? false) && !in_array($currentUser['id'] ?? 0, array_map(fn($p) => $p->user_id, $game->participants ?? []))): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.SSE) {
                console.log('🔌 Connecting to SSE for referee #' + window.GAME_CONFIG.currentUserId);
                const sseUrl = (window.BASE_URL || '') + '/sse/referee';
                window.SSE.connect('referee_' + window.GAME_CONFIG.gameId, sseUrl);
                window.SSE.on('referee_' + window.GAME_CONFIG.gameId, 'game_referee_changed', (data) => {
                    handleSSEEvent('game_referee_changed', data);
                });
            }
        });
    </script>
<?php endif; ?>

<!-- Scroll to round form -->
<script>
    (function() {
        function scrollToRoundForm() {
            const wrapper = document.getElementById('round-form-wrapper');
            if (wrapper) {
                wrapper.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(scrollToRoundForm, 100);
        });
        document.body.addEventListener('htmx:afterSwap', function(event) {
            if (event.detail.target && event.detail.target.id === 'game-page-content') {
                setTimeout(scrollToRoundForm, 150);
            }
        });
        document.body.addEventListener('htmx:afterSettle', function(event) {
            if (event.detail.target && event.detail.target.id === 'game-page-content') {
                setTimeout(scrollToRoundForm, 100);
            }
        });
    })();
</script>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes bounce-in {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }

        50% {
            opacity: 1;
            transform: scale(1.05);
        }

        70% {
            transform: scale(0.9);
        }

        100% {
            transform: scale(1);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }

    .animate-bounce-in {
        animation: bounce-in 0.6s ease-out;
    }
</style>