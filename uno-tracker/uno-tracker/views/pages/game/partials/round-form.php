<?php
$isTeamMode = $game->isTeamMode();

// 🆕 پیدا کردن برنده آخرین دور
$lastWinnerId = null;
if (!empty($game->rounds)) {
    $lastRound = end($game->rounds);
    $lastWinnerId = $lastRound->winner_participant_id ?? null;
}
?>
<!-- ================================================== -->
<!-- ======= فرم ثبت نتیجه دور - نسخه‌ی نهایی ======= -->
<!-- ================================================== -->
<div class="bg-gradient-to-br from-indigo-50 via-violet-50 to-purple-50 rounded-2xl p-4 sm:p-6 border-2 border-indigo-200/50 shadow-lg">

    <!-- هدر -->
    <div class="flex items-center gap-3 mb-5 pb-3 border-b-2 border-indigo-200/30">
        <span class="text-3xl sm:text-4xl drop-shadow-lg">🎯</span>
        <div>
            <h3 class="text-lg sm:text-2xl font-black text-gray-800 tracking-tight">ثبت نتیجه دور</h3>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">برنده‌ی این دور را انتخاب کنید</p>
        </div>
        <?php if ($isTeamMode): ?>
            <span class="mr-auto px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">تیمی</span>
        <?php endif; ?>
    </div>

    <form id="round-form"
        hx-post="/game/<?= $game->id ?>/round"
        hx-target="#game-page-content"
        hx-swap="innerHTML"
        hx-indicator="#round-loading"
        class="space-y-5">

        <!-- انتخاب برنده -->
        <div>
            <label class="block text-gray-700 mb-3 font-bold text-sm sm:text-base">
                برنده این دور را انتخاب کنید
                <?php if ($isTeamMode): ?>
                    <span class="text-xs text-gray-400 font-normal block mt-0.5">(روی بازیکن برنده کلیک کنید)</span>
                <?php endif; ?>
            </label>

            <?php if ($isTeamMode && !empty($game->teams)): ?>
                <!-- ======= حالت تیمی (دوستونه در موبایل) ======= -->
                <div class="!grid !grid-cols-2 gap-4">
                    <?php foreach ($game->teams as $team):
                        $teamMembers = $team->getMembers();
                        $teamColor = htmlspecialchars($team->color_hex);
                    ?>
                        <div class="bg-white rounded-2xl border-2 p-4 shadow-sm hover:shadow-md transition-all duration-300"
                            style="border-color: <?= $teamColor ?>40">

                            <!-- هدر تیم -->
                            <div class="flex items-center justify-between mb-3 pb-2 border-b-2"
                                style="border-color: <?= $teamColor ?>30">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm"
                                        style="background-color: <?= $teamColor ?>">
                                        <?= array_search($team, $game->teams) + 1 ?>
                                    </div>
                                    <span class="font-bold text-sm" style="color: <?= $teamColor ?>">
                                        <?= htmlspecialchars($team->name) ?>
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                                    <?= count($teamMembers) ?> نفر
                                </span>
                            </div>

                            <!-- بازیکنان تیم -->
                            <div class="space-y-2">
                                <?php foreach ($teamMembers as $member): ?>
                                    <?php
                                    $isLastWinner = ($lastWinnerId && $member->id === $lastWinnerId);
                                    ?>
                                    <label class="cursor-pointer block group">
                                        <input type="radio"
                                            name="winner_participant_id"
                                            value="<?= $member->id ?>"
                                            required
                                            class="peer sr-only">
                                        <div class="flex flex-col sm:flex-row items-center sm:items-center p-2.5 rounded-xl border-2 transition-all duration-200 hover:bg-gray-100 group-hover:scale-[1.01] text-center sm:text-right
                                            <?= $isLastWinner 
                                                ? 'border-amber-400/50 bg-amber-50/80 ring-1 ring-amber-400/30' 
                                                : 'bg-gray-50/80 border-transparent' ?>
                                            peer-checked:border-indigo-500 peer-checked:bg-indigo-50/80 peer-checked:shadow-md peer-checked:ring-2 peer-checked:ring-indigo-400/40">

                                            <!-- آواتار -->
                                            <?php if ($member->avatar_path): ?>
                                                <img src="/storage/uploads/avatars/<?= htmlspecialchars($member->avatar_path) ?>"
                                                    class="!w-10 !h-10 sm:!w-9 sm:!h-9 aspect-square rounded-full object-cover border-2 border-gray-200 flex-shrink-0 mb-1 sm:mb-0 sm:ml-2">
                                            <?php else: ?>
                                                <div class="w-10 h-10 sm:w-9 sm:h-9 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center border-2 border-gray-200 flex-shrink-0 text-xl mb-1 sm:mb-0 sm:ml-2">
                                                    👤
                                                </div>
                                            <?php endif; ?>

                                            <!-- اطلاعات -->
                                            <div class="flex-1 min-w-0 sm:mr-2">
                                                <div class="font-semibold text-gray-800 text-sm truncate flex items-center justify-center sm:justify-start gap-1">
                                                    <?= htmlspecialchars($member->getDisplayName()) ?>
                                                    <?php if ($isLastWinner): ?>
                                                        <span class="text-amber-500 text-base" title="برنده آخرین دور">⭐</span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($member->real_name)): ?>
                                                    <div class="text-[10px] text-gray-400 truncate"><?= htmlspecialchars($member->real_name) ?></div>
                                                <?php else: ?>
                                                    <div class="text-[10px] text-gray-400">مهمان</div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- تعداد برد -->
                                            <div class="bg-indigo-50 border border-indigo-200 flex-shrink-0 font-bold mt-1 px-2 py-0.5 rounded-full sm:mt-0 text-indigo-700 text-xs">
                                                <?= $member->wins_count ?> برد
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- ======= حالت انفرادی (دوستونه در موبایل) ======= -->
                <div class="!grid !grid-cols-2 sm:!grid-cols-3 lg:!grid-cols-4 gap-3">
                    <?php foreach ($game->participants as $participant): ?>
                        <?php
                        $isLastWinner = ($lastWinnerId && $participant->id === $lastWinnerId);
                        ?>
                        <label class="cursor-pointer group">
                            <input type="radio"
                                name="winner_participant_id"
                                value="<?= $participant->id ?>"
                                required
                                class="peer sr-only">
                            <div class="p-3 sm:p-4 bg-white rounded-2xl border-2 transition-all duration-200 text-center hover:scale-[1.03] hover:shadow-md group-hover:border-indigo-300
                                <?= $isLastWinner 
                                    ? 'border-amber-400/50 bg-amber-50/80 ring-1 ring-amber-400/30' 
                                    : 'border-gray-200' ?>
                                peer-checked:border-indigo-500 peer-checked:bg-indigo-50/80 peer-checked:shadow-lg peer-checked:ring-2 peer-checked:ring-indigo-400/40">
                                
                                <?php if ($participant->avatar_path): ?>
                                    <img src="/storage/uploads/avatars/<?= htmlspecialchars($participant->avatar_path) ?>"
                                        class="!w-12 !h-12 sm:!w-14 sm:!h-14 aspect-square rounded-full mx-auto mb-2 object-cover border-2 border-gray-200 group-hover:border-indigo-300 transition-all duration-200">
                                <?php else: ?>
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-indigo-100 to-violet-100 mx-auto mb-2 flex items-center justify-center text-2xl border-2 border-gray-200 group-hover:border-indigo-300 transition-all duration-200">
                                        👤
                                    </div>
                                <?php endif; ?>

                                <div class="font-bold text-gray-800 text-xs sm:text-sm truncate flex items-center justify-center gap-1">
                                    <?= htmlspecialchars($participant->getDisplayName()) ?>
                                    <?php if ($isLastWinner): ?>
                                        <span class="text-amber-500 text-base" title="برنده آخرین دور">⭐</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($participant->real_name)): ?>
                                    <div class="text-[10px] text-gray-400 truncate"><?= htmlspecialchars($participant->real_name) ?></div>
                                <?php endif; ?>
                                <div class="bg-indigo-50 border border-indigo-200 font-bold inline-block mt-1 px-2 py-0.5 rounded-full text-indigo-700 text-sm">
                                    <?= $participant->wins_count ?> برد
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- کارت برنده و نوع برد (دو ستونه در همه‌ی ابعاد) -->
        <div class="!grid !grid-cols-2 gap-4">
            <!-- کارت برنده -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base flex items-center gap-1.5">
                    <span>🃏</span> کارت برنده <span class="text-xs text-gray-400 font-normal">(اختیاری)</span>
                </label>
                <select name="winning_card_id"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-sm sm:text-base appearance-none cursor-pointer hover:border-indigo-300">
                    <option value="">-- انتخاب کنید --</option>
                    <?php
                    $cards = (new \Infrastructure\Repositories\CardRepository())->findAllActive();
                    foreach ($cards as $card):
                    ?>
                        <option value="<?= $card->id ?>">
                            <?= htmlspecialchars($card->emoji ?? '') ?> <?= htmlspecialchars($card->name) ?>
                            (×<?= $card->score_multiplier ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- نوع برد -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base flex items-center gap-1.5">
                    <span>⚡</span> نوع برد <span class="text-xs text-gray-400 font-normal">(اختیاری)</span>
                </label>
                <select name="win_type_id"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-sm sm:text-base appearance-none cursor-pointer hover:border-indigo-300">
                    <option value="">-- انتخاب کنید --</option>
                    <?php
                    $winTypes = \Core\Database::getInstance()->fetchAll("SELECT * FROM win_types WHERE is_active = 1");
                    foreach ($winTypes as $winType):
                    ?>
                        <option value="<?= $winType['id'] ?>">
                            <?= htmlspecialchars($winType['icon'] ?? '') ?> <?= htmlspecialchars($winType['name']) ?>
                            (×<?= $winType['score_multiplier'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- دکمه ثبت -->
        <button type="submit"
            class="w-full bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 hover:from-green-600 hover:via-emerald-600 hover:to-teal-600 text-white font-bold py-3.5 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-xl shadow-lg text-sm sm:text-base flex items-center justify-center gap-2 group">
            <span class="text-xl group-hover:rotate-12 transition-transform duration-300">✅</span>
            <span>ثبت نتیجه دور</span>
        </button>

        <!-- Loading Indicator -->
        <div id="round-loading" class="htmx-indicator text-center py-4">
            <div class="inline-flex items-center gap-3">
                <div class="animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full"></div>
                <p class="text-gray-600 font-medium text-sm">در حال ثبت نتیجه...</p>
            </div>
        </div>
    </form>
</div>

<script>
    (function() {
        const form = document.getElementById('round-form');
        if (form && typeof htmx !== 'undefined') {
            htmx.process(form);
            form.addEventListener('submit', function(e) {
                if (htmx && htmx.ajax) {
                    e.preventDefault();
                }
            });
        }
    })();
</script>