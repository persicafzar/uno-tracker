<?php

use Core\JalaliDate;

$todayJalali = JalaliDate::format('Y/m/d');

$oldDataJson = htmlspecialchars(json_encode([
    'game_name' => $old['game_name'] ?? '',
    'game_mode' => $old['game_mode'] ?? 'solo',
    'target_wins' => (int)($old['target_wins'] ?? 10),
    'player_ids' => array_map('intval', $old['player_ids'] ?? []),
    'guest_players' => $old['guest_players'] ?? [],
    'team_names' => $old['team_names'] ?? [],
    'team_algorithm' => $old['team_algorithm'] ?? 'manual',
    'player_teams' => $old['player_teams'] ?? [],
], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');

$playersDataJson = json_encode(array_map(function ($p) {
    return ['id' => $p['id'], 'nickname' => $p['nickname'], 'real_name' => $p['real_name'], 'avatar_path' => $p['avatar_path'] ?? ''];
}, $players), JSON_UNESCAPED_UNICODE);
?>

<div id="game-create-container"
    x-data="window.gameCreator()"
    x-init="$nextTick(() => loadOldData($el))"
    data-old='<?= $oldDataJson ?>'
    data-players='<?= $playersDataJson ?>'
    data-today='<?= $todayJalali ?>'
    class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">

    <!-- ======= Header ======= -->
    <div class="relative bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 rounded-2xl p-5 sm:p-7 text-white shadow-2xl mb-4 sm:mb-6 overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-5">
            <div class="flex items-center gap-4 sm:gap-5">
                <?php if (!empty($currentUser['avatar_path'])): ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 border-white/90 shadow-2xl overflow-hidden flex-shrink-0 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <img src="/storage/uploads/avatars/<?= htmlspecialchars($currentUser['avatar_path']) ?>"
                            alt="<?= htmlspecialchars($currentUser['nickname']) ?>"
                            class="w-full h-full aspect-square rounded-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-4 border-white/90 shadow-2xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-3xl sm:text-4xl font-black flex-shrink-0 hover:border-amber-300 transition-all duration-300 hover:scale-105">
                        <?= mb_substr($currentUser['nickname'] ?? '?', 0, 1) ?>
                    </div>
                <?php endif; ?>
                <div class="text-center sm:text-right">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black drop-shadow-2xl tracking-tight">🎮 ایجاد بازی جدید</h1>
                    <p class="text-white/80 text-sm sm:text-base font-medium mt-0.5 drop-shadow">
                        <?= htmlspecialchars($currentUser['nickname'] ?? '') ?> •
                        <?= JalaliDate::format('l، j F Y') ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openCreateGameGuide()"
                    class="group px-3 py-2.5 bg-white/20 hover:bg-white/30 rounded-xl transition-all duration-300 text-sm font-bold flex items-center gap-1.5 hover:scale-[1.05]">
                    <span>❓</span>
                    راهنما
                </button>
                <a href="/dashboard"
                    class="group px-3 py-2.5 bg-white/20 hover:bg-white/30 rounded-xl transition-all duration-300 text-sm font-bold flex items-center gap-1.5 hover:scale-[1.05]">
                    <span>↩️</span>
                    <span class="hidden sm:inline">بازگشت</span>
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border-2 border-red-200 text-red-700 px-4 py-3.5 rounded-2xl mb-6 shadow-md border border-red-200">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/game"
        hx-post="/game"
        hx-target="#game-create-container"
        hx-swap="outerHTML"
        class="space-y-5">

        <input type="hidden" name="player_teams" :value="JSON.stringify(playerTeams)">
        <input type="hidden" name="guest_players" :value="JSON.stringify(guestPlayers)">
        <input type="hidden" name="team_names" :value="JSON.stringify(teamNames)">
        <input type="hidden" name="team_assignments" :value="getTeamAssignmentsJson()">

        <?php include __DIR__ . '/partials/game-name.php'; ?>
        <?php include __DIR__ . '/partials/game-mode.php'; ?>
        <?php include __DIR__ . '/partials/target-wins.php'; ?>
        <?php include __DIR__ . '/partials/select-players.php'; ?>
        <?php include __DIR__ . '/partials/player-counter.php'; ?>
        <?php include __DIR__ . '/partials/team-setup.php'; ?>

        <button type="submit"
            :disabled="!canCreateGame()"
            :class="!canCreateGame() ? 'opacity-50 cursor-not-allowed' : 'hover:scale-[1.02]'"
            class="w-full bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 hover:from-indigo-700 hover:via-violet-700 hover:to-purple-700 text-white font-black py-3.5 sm:py-4.5 rounded-2xl transition-all duration-300 text-base sm:text-lg shadow-xl hover:shadow-2xl flex items-center justify-center gap-2 group">
            <span class="text-2xl group-hover:rotate-12 transition-transform duration-300">🚀</span>
            ایجاد بازی
        </button>
    </form>
</div>

<!-- مودال راهنما -->
<?php include __DIR__ . '/partials/game-guide-modal.php'; ?>

<script src="/assets/js/sortable.min.js"></script>
<script src="/assets/js/game-creator.js"></script>