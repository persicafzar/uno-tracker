<?php

/**
 * تاریخچه دورها - Wrapper اصلی
 */

// گرفتن تنظیمات امتیازدهی
$settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
$baseScore = (float) $settingsRepo->get('scoring_base_score', 1.0);
$teamMultiplier = $game->isTeamMode() ? (float) $settingsRepo->get('scoring_team_multiplier', 1.5) : 1.0;
?>

<div class="bg-gradient-to-br from-white to-gray-50/80 rounded-xl shadow-md border border-gray-200/70 p-4 sm:p-5 transition-all duration-300 hover:shadow-lg">

    <!-- هدر -->
    <div class="flex items-center gap-2.5 mb-4 pb-2.5 border-b-2 border-gray-200/50">
        <span class="text-2xl sm:text-3xl drop-shadow-lg">📜</span>
        <h2 class="text-lg sm:text-xl font-black text-gray-800 tracking-tight">تاریخچه دورها</h2>
        <span class="mr-auto text-xs bg-indigo-100 text-indigo-700 px-2.5 py-0.5 rounded-full font-bold">
            <?= count($game->rounds) ?> دور
        </span>
    </div>

    <?php if (empty($game->rounds)): ?>
        <div class="text-center py-8 sm:py-10">
            <div class="text-5xl sm:text-6xl mb-3 drop-shadow-lg">🎯</div>
            <p class="text-gray-500 text-sm sm:text-base font-medium">هنوز دوری ثبت نشده است</p>
        </div>
    <?php else: ?>

        <?php include __DIR__ . '/rounds/formula-info.php'; ?>

        <div class="space-y-3 sm:space-y-4">
            <?php foreach (array_reverse($game->rounds) as $round): ?>
                <?php
                $cardInfo = null;
                if (!empty($round->winning_card_id)) {
                    $card = \Core\Database::getInstance()->fetchOne(
                        "SELECT name, emoji, score_multiplier FROM cards WHERE id = ?",
                        [$round->winning_card_id]
                    );
                    if ($card) {
                        $cardInfo = [
                            'name' => $card['name'],
                            'emoji' => $card['emoji'],
                            'multiplier' => (float) $card['score_multiplier']
                        ];
                    }
                }

                $winTypeInfo = null;
                if (!empty($round->win_type_id)) {
                    $winType = \Core\Database::getInstance()->fetchOne(
                        "SELECT name, icon, score_multiplier FROM win_types WHERE id = ?",
                        [$round->win_type_id]
                    );
                    if ($winType) {
                        $winTypeInfo = [
                            'name' => $winType['name'],
                            'icon' => $winType['icon'],
                            'multiplier' => (float) $winType['score_multiplier']
                        ];
                    }
                }

                $winnerParticipant = null;
                foreach ($game->participants as $p) {
                    if ($p->id == $round->winner_participant_id) {
                        $winnerParticipant = $p;
                        break;
                    }
                }
                ?>

                <?php if ($game->isTeamMode()): ?>
                    <?php include __DIR__ . '/rounds/round-item-team.php'; ?>
                <?php else: ?>
                    <?php include __DIR__ . '/rounds/round-item-solo.php'; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>