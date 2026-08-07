<?php

/**
 * محتوای اصلی صفحه بازی
 * 
 * 🆕 اصلاحات:
 * - اضافه کردن #game-page-content wrapper برای HTMX swap
 * - اضافه کردن sse_fallback_refresh_seconds به GAME_CONFIG
 * - بهبود ساختار Toast Messages
 */

use Core\JalaliDate;

$currentUser = $currentUser ?? $user ?? null;
$isCurrentReferee = $currentUser && $game->referee_id === (int)($currentUser['id'] ?? 0);
$isAdminUser = $isAdmin ?? false;

// 🆕 گرفتن بازیکن اول
$firstPlayer = null;
if (!empty($game->first_player_participant_id)) {
    foreach ($game->participants as $p) {
        if ($p->id == $game->first_player_participant_id) {
            $firstPlayer = $p;
            break;
        }
    }
}

// 🆕 گرفتن اطلاعات داور
$referee = \Core\Database::getInstance()->fetchOne(
    "SELECT id, nickname, avatar_path FROM users WHERE id = ?",
    [$game->referee_id]
);

// 🆕 گرفتن تنظیمات Fallback Refresh از دیتابیس
$settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
$sseFallbackSeconds = (int)$settingsRepo->get('sse_fallback_refresh_seconds', 10);
?>

<!-- 🆕 Wrapper اصلی برای HTMX swap -->
<div id="game-page-content">
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
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                        <?= htmlspecialchars($game->name ?? 'بازی بدون نام') ?>
                    </h1>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold border border-white/30">
                            <?= $game->game_mode === 'friendly' ? '👥 تیمی' : '👤 انفرادی' ?>
                        </span>
                        <?php if ($game->status === 'active'): ?>
                            <span class="px-3 py-1 bg-green-500/30 backdrop-blur-sm rounded-full text-xs font-bold border border-green-400/50 animate-pulse">
                                🔴 در حال اجرا
                            </span>
                        <?php elseif ($game->status === 'paused'): ?>
                            <span class="px-3 py-1 bg-yellow-500/30 backdrop-blur-sm rounded-full text-xs font-bold border border-yellow-400/50">
                                ⏸️ متوقف
                            </span>
                        <?php elseif ($game->status === 'finished'): ?>
                            <span class="px-3 py-1 bg-blue-500/30 backdrop-blur-sm rounded-full text-xs font-bold border border-blue-400/50">
                                ✅ پایان یافته
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20">
                        <div class="text-xs text-white/70 font-medium">هدف</div>
                        <div class="text-2xl font-black"><?= $game->target_wins ?></div>
                        <div class="text-[10px] text-white/60">برد</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20">
                        <div class="text-xs text-white/70 font-medium">دورها</div>
                        <div class="text-2xl font-black"><?= count($game->rounds ?? []) ?></div>
                        <div class="text-[10px] text-white/60">ثبت شده</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20">
                        <div class="text-xs text-white/70 font-medium">بازیکنان</div>
                        <div class="text-2xl font-black"><?= count($game->participants ?? []) ?></div>
                        <div class="text-[10px] text-white/60">نفر</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/20">
                        <div class="text-xs text-white/70 font-medium">داور</div>
                        <div class="text-sm font-bold truncate"><?= htmlspecialchars($referee['nickname'] ?? 'نامشخص') ?></div>
                        <div class="text-[10px] text-white/60">قضاوت</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Game Finished Banner -->
        <?php if ($game->isFinished()): ?>
            <?php
            $winnerParticipant = null;
            if (!empty($game->winner_participant_id)) {
                foreach ($game->participants as $p) {
                    if ($p->id == $game->winner_participant_id) {
                        $winnerParticipant = $p;
                        break;
                    }
                }
            }
            ?>
            <div class="bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 rounded-2xl p-5 text-white shadow-xl">
                <div class="flex items-center gap-4">
                    <div class="text-5xl">🏆</div>
                    <div class="flex-1">
                        <h3 class="text-xl font-black mb-1">بازی پایان یافت!</h3>
                        <p class="text-white/90 font-bold">
                            برنده: <?= htmlspecialchars($winnerParticipant ? $winnerParticipant->getDisplayName() : 'نامشخص') ?>
                        </p>
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
            <?php include __DIR__ . '/round-form.php'; ?>
        <?php endif; ?>

        <!-- Referee Controls -->
        <?php if ($isCurrentReferee && !$game->isCancelled() && !$game->isFinished()): ?>
            <?php include __DIR__ . '/referee-controls.php'; ?>
        <?php endif; ?>

        <!-- Rounds Timeline -->
        <?php include __DIR__ . '/rounds-timeline.php'; ?>

    </div>
</div>

<!-- Modals -->
<?php if ($isCurrentReferee && !$game->isFinished() && !$game->isCancelled()): ?>
    <div id="edit-target-wins-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <h3 class="text-lg font-black text-gray-800 mb-4">🎯 ویرایش هدف برد</h3>
            <form method="POST" action="/game/<?= $game->id ?>/update-target" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">هدف جدید</label>
                    <input type="number" name="target_wins" value="<?= $game->target_wins ?>"
                        min="3" max="50"
                        class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition">
                        ذخیره
                    </button>
                    <button type="button" onclick="document.getElementById('edit-target-wins-modal').classList.add('hidden')"
                        class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">
                        انصراف
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="transfer-referee-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <h3 class="text-lg font-black text-gray-800 mb-4">👤 انتقال داوری</h3>
            <form method="POST" action="/game/<?= $game->id ?>/transfer-referee" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">داور جدید</label>
                    <select name="referee_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                        <?php foreach ($game->participants as $p): ?>
                            <?php if ($p->user_id && $p->user_id != $game->referee_id): ?>
                                <option value="<?= $p->user_id ?>"><?= htmlspecialchars($p->getDisplayName()) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition">
                        انتقال
                    </button>
                    <button type="button" onclick="document.getElementById('transfer-referee-modal').classList.add('hidden')"
                        class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">
                        انصراف
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- 🆕 انتقال تنظیمات بازی و Fallback Refresh به JavaScript -->
<script>
    window.GAME_CONFIG = window.GAME_CONFIG || {};
    window.GAME_CONFIG.gameId = <?= (int)($game->id ?? 0) ?>;
    window.GAME_CONFIG.currentUserId = <?= (int)($currentUser['id'] ?? ($user['id'] ?? 0)) ?>;
    window.GAME_CONFIG.isReferee = <?= ($isCurrentReferee ?? false) ? 'true' : 'false' ?>;
    window.GAME_CONFIG.maxWins = <?= (int)($game->target_wins ?? 0) ?>;
    window.GAME_CONFIG.gameMode = '<?= htmlspecialchars($game->game_mode ?? 'solo') ?>';
    window.GAME_CONFIG.gameStatus = '<?= htmlspecialchars($game->status ?? 'pending') ?>';

    // 🆕 تنظیمات Fallback Refresh (از دیتابیس)
    window.GAME_CONFIG.sseFallbackSeconds = <?= $sseFallbackSeconds ?>;

    // 🆕 اطلاعات بازیکنان
    window.GAME_CONFIG.participants = <?= json_encode(
                                            array_map(fn($p) => [
                                                'id' => $p->id,
                                                'user_id' => $p->user_id,
                                                'name' => $p->getDisplayName(),
                                                'team_id' => $p->team_id,
                                            ], $game->participants ?? []),
                                            JSON_UNESCAPED_UNICODE
                                        ) ?>;

    console.log('🎮 GAME_CONFIG initialized:', {
        gameId: window.GAME_CONFIG.gameId,
        isReferee: window.GAME_CONFIG.isReferee,
        fallbackSeconds: window.GAME_CONFIG.sseFallbackSeconds,
        participants: window.GAME_CONFIG.participants.length
    });
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