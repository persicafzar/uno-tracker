<?php

use Core\JalaliDate;

$statusLabels = [
    'pending' => ['label' => 'در انتظار', 'color' => 'yellow'],
    'active' => ['label' => 'در حال بازی', 'color' => 'blue'],
    'paused' => ['label' => 'متوقف', 'color' => 'orange'],
    'finished' => ['label' => 'پایان یافته', 'color' => 'green'],
    'cancelled' => ['label' => 'لغو شده', 'color' => 'red'],
];

$status = $statusLabels[$game['status']] ?? $statusLabels['pending'];
$participants = $game['participants'] ?? [];
$teams = $game['teams'] ?? [];
$rounds = $game['rounds'] ?? [];
?>
<div class="space-y-6" x-data="gameDetailManager()">

    <!-- Back Button -->
    <a href="/admin/games" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-indigo-600 transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        بازگشت به لیست بازی‌ها
    </a>

    <!-- Game Header -->
    <div class="relative bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 rounded-2xl p-5 sm:p-7 text-white shadow-md overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black drop-shadow-lg tracking-tight"><?= htmlspecialchars($game['name'] ?: "بازی #{$game['id']}") ?></h2>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-<?= $status['color'] ?>-500 rounded-full text-xs font-bold"><?= $status['label'] ?></span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-bold"><?= $game['game_mode'] === 'solo' ? '👤 انفرادی' : '👥 تیمی' ?></span>
                    <span class="text-sm text-white/80 font-medium">🎯 هدف: <?= (int)($game['target_wins'] ?? 10) ?> برد</span>
                    <span class="text-sm text-white/80 font-medium">📊 دورها: <?= (int)($game['total_rounds_played'] ?? 0) ?></span>
                </div>
            </div>
            <div>
                <div class="text-sm text-white/80 font-medium">داور:</div>
                <div class="font-bold drop-shadow">🚩 <?= htmlspecialchars($game['referee_name'] ?? '-') ?></div>
                <?php if (!empty($game['winner_name'])): ?>
                    <div class="text-sm text-white/80 font-medium mt-2">برنده:</div>
                    <div class="font-bold flex items-center gap-2 drop-shadow">
                        <?php if (empty($game['winner_user_id'])): ?>
                            <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs">👥 مهمان</span>
                        <?php endif; ?>
                        <span>🏆 <?= htmlspecialchars($game['winner_name']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($game['first_player_name'])): ?>
                    <div class="text-sm text-white/80 font-medium mt-2">شروع‌کننده:</div>
                    <div class="font-bold flex items-center gap-2 drop-shadow">
                        <?php if (empty($game['first_player_user_id'])): ?>
                            <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs">👥 مهمان</span>
                        <?php endif; ?>
                        <span>🎲 <?= htmlspecialchars($game['first_player_name']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="text-xs text-white/80 font-medium mt-2"><?= !empty($game['created_at']) ? JalaliDate::format('Y/m/d H:i', strtotime($game['created_at'])) : '-' ?></div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="!grid !grid-cols-2 md:!grid-cols-5 gap-3 sm:gap-4">
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-3 sm:p-4 border-2 border-gray-200 shadow-md text-center hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-12 h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 text-2xl mb-1">👥</div>
            <div class="relative z-10 text-2xl font-black text-indigo-600"><?= count($participants) ?></div>
            <div class="relative z-10 text-xs font-medium text-gray-600 mt-1">شرکت‌کننده</div>
        </div>
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-3 sm:p-4 border-2 border-gray-200 shadow-md text-center hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-12 h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 text-2xl mb-1">🏆</div>
            <div class="relative z-10 text-2xl font-black text-yellow-600"><?= count($teams) ?></div>
            <div class="relative z-10 text-xs font-medium text-gray-600 mt-1">تیم</div>
        </div>
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-3 sm:p-4 border-2 border-gray-200 shadow-md text-center hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-12 h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 text-2xl mb-1">📊</div>
            <div class="relative z-10 text-2xl font-black text-green-600"><?= count($rounds) ?></div>
            <div class="relative z-10 text-xs font-medium text-gray-600 mt-1">دور</div>
        </div>
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-3 sm:p-4 border-2 border-gray-200 shadow-md text-center hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
            <div class="absolute top-0 right-0 w-12 h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 text-2xl mb-1">⚙️</div>
            <div class="relative z-10 text-xs font-black text-gray-700 mt-1"><?= htmlspecialchars($game['team_builder_algorithm'] ?? 'دستی') ?></div>
            <div class="relative z-10 text-xs font-medium text-gray-600 mt-1">الگوریتم</div>
        </div>
        <?php if (!empty($game['first_player_name'])): ?>
            <div class="relative overflow-hidden bg-gradient-to-br from-orange-100 to-yellow-100 rounded-2xl p-3 sm:p-4 border-2 border-orange-300 shadow-md text-center hover:shadow-lg transition-all duration-300 hover:scale-[1.02] group">
                <div class="absolute top-0 right-0 w-12 h-12 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10 text-2xl mb-1">🎲</div>
                <div class="relative z-10 text-xs font-black text-orange-700 truncate" title="<?= htmlspecialchars($game['first_player_name']) ?>"><?= htmlspecialchars($game['first_player_name']) ?></div>
                <div class="relative z-10 text-xs font-medium text-gray-600 mt-1">شروع‌کننده</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <?php if ($game['status'] !== 'finished' && $game['status'] !== 'cancelled'): ?>
        <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
            <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
                <span class="text-2xl">⚡</span> عملیات سریع
            </h3>
            <div class="!grid !grid-cols-2 md:!grid-cols-4 gap-3">
                <button type="button" @click="openStatusModal()"
                    class="px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] text-sm">
                    🔄 تغییر وضعیت
                </button>
                <button type="button" @click="openRefereeModal()"
                    class="px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] text-sm">
                    👤 تغییر داور
                </button>
                <button type="button" @click="openRoundsModal()"
                    class="px-4 py-3 bg-gradient-to-r from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] text-sm">
                    📊 ویرایش دورها
                </button>
                <button type="button" @click="openAddPlayerModal()"
                    class="px-4 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] text-sm">
                    ➕ افزودن بازیکن
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Participants -->
    <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
        <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
            <span class="text-2xl">👥</span> شرکت‌کنندگان (<?= count($participants) ?> نفر)
        </h3>
        <?php if (empty($participants)): ?>
            <div class="text-center py-8 text-gray-500">
                <div class="text-5xl mb-3 opacity-50">👥</div>
                <p class="font-medium">شرکت‌کننده‌ای یافت نشد</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">بازیکن</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">تیم</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">بردها</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">امتیاز</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">نتیجه</th>
                            <?php if ($game['status'] !== 'finished' && $game['status'] !== 'cancelled'): ?>
                                <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">عملیات</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($participants as $participant): ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($participant['avatar_path'])): ?>
                                            <div class="w-10 h-10 rounded-full border-2 border-gray-200 shadow-sm overflow-hidden flex-shrink-0 group-hover:border-indigo-400 transition-all duration-300">
                                                <img src="/storage/uploads/avatars/<?= htmlspecialchars($participant['avatar_path']) ?>"
                                                    alt="<?= htmlspecialchars($participant['nickname'] ?? '') ?>"
                                                    class="w-full h-full aspect-square rounded-full object-cover">
                                            </div>
                                        <?php elseif (!empty($participant['nickname'])): ?>
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-violet-400 flex items-center justify-center text-white font-black shadow-sm flex-shrink-0">
                                                <?= mb_substr($participant['nickname'], 0, 1) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-lg flex-shrink-0">👥</div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="font-bold text-gray-800 text-sm flex items-center gap-1">
                                                <?= htmlspecialchars($participant['nickname'] ?? $participant['guest_name'] ?? 'نامشخص') ?>
                                                <?php if (empty($participant['nickname']) && !empty($participant['guest_name'])): ?>
                                                    <span class="text-xs px-1.5 py-0.5 bg-gray-200 text-gray-600 rounded font-bold border border-gray-300">👥 مهمان</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($participant['real_name'])): ?>
                                                <div class="text-xs text-gray-500 font-medium"><?= htmlspecialchars($participant['real_name']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm font-bold text-gray-700 whitespace-nowrap">
                                    <?= htmlspecialchars($participant['team_name'] ?? '-') ?>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm font-black text-gray-800 whitespace-nowrap">
                                    <?= (int)($participant['wins_count'] ?? 0) ?>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm font-black text-indigo-600 whitespace-nowrap">
                                    <?= (int)($participant['total_score'] ?? 0) ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if (!empty($participant['is_winner'])): ?>
                                        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold border border-yellow-200 shadow-sm">🏆 برنده</span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs font-medium">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($game['status'] !== 'finished' && $game['status'] !== 'cancelled'): ?>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <form method="POST" action="/admin/games/<?= $game['id'] ?>/remove-participant/<?= $participant['participant_id'] ?>"
                                            id="remove-participant-<?= $participant['participant_id'] ?>" class="inline">
                                            <button type="button"
                                                onclick="confirmRemoveParticipant(<?= $participant['participant_id'] ?>, '<?= htmlspecialchars($participant['nickname'] ?? $participant['guest_name'] ?? 'نامشخص') ?>')"
                                                class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition"
                                                title="حذف بازیکن">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Teams -->
    <?php if (!empty($teams)): ?>
        <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
            <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
                <span class="text-2xl">🏆</span> تیم‌ها (<?= count($teams) ?> تیم)
            </h3>
            <div class="!grid !grid-cols-1 md:!grid-cols-2 gap-3">
                <?php
                $colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
                foreach ($teams as $index => $team):
                    $color = $colors[$index % count($colors)];
                ?>
                    <div class="p-4 rounded-2xl border-2 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.01]" style="border-color: <?= $color ?>; background: <?= $color ?>10">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-black" style="background-color: <?= $color ?>"><?= $index + 1 ?></div>
                                <span class="font-black text-gray-800"><?= htmlspecialchars($team['name'] ?? "تیم " . ($index + 1)) ?></span>
                            </div>
                            <span class="text-sm font-black" style="color: <?= $color ?>"><?= (int)($team['team_score'] ?? 0) ?> امتیاز</span>
                        </div>
                        <div class="text-xs font-medium text-gray-600">👥 <?= (int)($team['members_count'] ?? 0) ?> عضو</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Rounds -->
    <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
        <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
            <span class="text-2xl">📊</span> دورهای بازی (<?= count($rounds) ?> دور)
        </h3>
        <?php if (empty($rounds)): ?>
            <div class="text-center py-8 text-gray-500">
                <div class="text-5xl mb-3 opacity-50">📊</div>
                <p class="font-medium">هنوز دوری ثبت نشده است</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">دور</th>
                            <th class="px-4 py-3.5 text-right text-xs font-black text-gray-700 whitespace-nowrap">برنده</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">کارت برنده</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">نوع برد</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">امتیاز</th>
                            <th class="px-4 py-3.5 text-center text-xs font-black text-gray-700 whitespace-nowrap">زمان</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($rounds as $round): ?>
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block w-8 h-8 bg-indigo-100 text-indigo-700 rounded-full font-black text-sm leading-8 border border-indigo-200 shadow-sm"><?= (int)$round['round_number'] ?></span>
                                </td>
                                <td class="px-4 py-3.5 text-sm font-bold text-gray-800 whitespace-nowrap">
                                    <?= htmlspecialchars($round['winner_name'] ?? '-') ?>
                                    <?php if (!empty($round['winner_team_name'])): ?>
                                        <div class="text-xs text-gray-500 font-medium">(<?= htmlspecialchars($round['winner_team_name']) ?>)</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if (!empty($round['card_name'])): ?>
                                        <span class="text-xs px-2.5 py-1 bg-purple-100 text-purple-700 rounded-full font-bold border border-purple-200 shadow-sm">🃏 <?= htmlspecialchars($round['card_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs font-medium">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if (!empty($round['win_type_name'])): ?>
                                        <span class="text-xs px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full font-bold border border-blue-200 shadow-sm"><?= htmlspecialchars($round['win_type_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs font-medium">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center text-sm font-black text-green-600 whitespace-nowrap">+<?= (int)($round['calculated_score'] ?? 0) ?></td>
                                <td class="px-4 py-3.5 text-center text-xs font-medium text-gray-500 whitespace-nowrap"><?= !empty($round['created_at']) ? JalaliDate::format('H:i:s', strtotime($round['created_at'])) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-2xl p-5 border-2 border-gray-200/70 shadow-md">
        <h3 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
            <span class="text-2xl">⚙️</span> عملیات
        </h3>
        <div class="flex flex-wrap gap-3">
            <a href="/game/<?= $game['id'] ?>" target="_blank"
                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                👁️ مشاهده در سایت
            </a>
            <form method="POST" action="/admin/games/<?= $game['id'] ?>/delete"
                id="delete-game-detail-<?= $game['id'] ?>" class="inline">
                <button type="button"
                    onclick="confirmDelete('آیا از حذف این بازی مطمئن هستید؟', 'delete-game-detail-<?= $game['id'] ?>')"
                    class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-2xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">
                    🗑️ حذف بازی
                </button>
            </form>
        </div>
    </div>

    <!-- Modals -->
    <div x-show="showStatusModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border-2 border-gray-200/70" @click.stop>
            <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">🔄</span> تغییر وضعیت بازی</h3>
            <form method="POST" action="/admin/games/<?= $game['id'] ?>/status">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">وضعیت جدید</label>
                    <select name="status" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                        <option value="pending" <?= $game['status'] === 'pending' ? 'selected' : '' ?>>⏳ در انتظار</option>
                        <option value="active" <?= $game['status'] === 'active' ? 'selected' : '' ?>>🔴 در حال بازی</option>
                        <option value="paused" <?= $game['status'] === 'paused' ? 'selected' : '' ?>>⏸️ متوقف</option>
                        <option value="finished" <?= $game['status'] === 'finished' ? 'selected' : '' ?>>✅ پایان یافته</option>
                        <option value="cancelled" <?= $game['status'] === 'cancelled' ? 'selected' : '' ?>>❌ لغو شده</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">ذخیره</button>
                    <button type="button" @click="closeStatusModal()" class="flex-1 px-4 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-bold transition-all duration-200 shadow-sm hover:shadow-md">انصراف</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showRefereeModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border-2 border-gray-200/70" @click.stop>
            <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">👤</span> تغییر داور بازی</h3>
            <form method="POST" action="/admin/games/<?= $game['id'] ?>/referee">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">داور جدید</label>
                    <select name="referee_id" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white" required>
                        <option value="">-- انتخاب کنید --</option>
                        <?php
                        $users = \Core\Database::getInstance()->fetchAll("SELECT id, nickname FROM users WHERE status = 'active' ORDER BY nickname");
                        foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nickname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">ذخیره</button>
                    <button type="button" @click="closeRefereeModal()" class="flex-1 px-4 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-bold transition-all duration-200 shadow-sm hover:shadow-md">انصراف</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showRoundsModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border-2 border-gray-200/70" @click.stop>
            <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">📊</span> ویرایش تعداد دورها</h3>
            <form method="POST" action="/admin/games/<?= $game['id'] ?>/rounds">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">تعداد دورهای بازی شده</label>
                    <input type="number" name="total_rounds" min="0" value="<?= (int)($game['total_rounds_played'] ?? 0) ?>" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" required>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">ذخیره</button>
                    <button type="button" @click="closeRoundsModal()" class="flex-1 px-4 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-bold transition-all duration-200 shadow-sm hover:shadow-md">انصراف</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showAddPlayerModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border-2 border-gray-200/70" @click.stop>
            <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight"><span class="text-2xl">➕</span> افزودن بازیکن</h3>
            <form method="POST" action="/admin/games/<?= $game['id'] ?>/add-participant">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">نوع بازیکن</label>
                    <select x-model="playerType" @change="playerTypeChanged()" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                        <option value="user">کاربر ثبت‌نام شده</option>
                        <option value="guest">بازیکن مهمان</option>
                    </select>
                </div>
                <div x-show="playerType === 'user'">
                    <label class="block text-sm font-bold text-gray-700 mb-2">انتخاب کاربر</label>
                    <select name="user_id" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 appearance-none cursor-pointer hover:border-indigo-300 bg-white">
                        <option value="">-- انتخاب کنید --</option>
                        <?php
                        $users = \Core\Database::getInstance()->fetchAll("SELECT id, nickname FROM users WHERE status = 'active' ORDER BY nickname");
                        foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nickname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div x-show="playerType === 'guest'">
                    <label class="block text-sm font-bold text-gray-700 mb-2">نام بازیکن مهمان</label>
                    <input type="text" name="guest_name" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" placeholder="مثال: بازیکن ۱">
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02]">افزودن</button>
                    <button type="button" @click="closeAddPlayerModal()" class="flex-1 px-4 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-bold transition-all duration-200 shadow-sm hover:shadow-md">انصراف</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function gameDetailManager() {
        return {
            showStatusModal: false,
            showRefereeModal: false,
            showRoundsModal: false,
            showAddPlayerModal: false,
            playerType: 'user',
            openStatusModal() {
                this.showStatusModal = true;
            },
            closeStatusModal() {
                this.showStatusModal = false;
            },
            openRefereeModal() {
                this.showRefereeModal = true;
            },
            closeRefereeModal() {
                this.showRefereeModal = false;
            },
            openRoundsModal() {
                this.showRoundsModal = true;
            },
            closeRoundsModal() {
                this.showRoundsModal = false;
            },
            openAddPlayerModal() {
                this.showAddPlayerModal = true;
                this.playerType = 'user';
            },
            closeAddPlayerModal() {
                this.showAddPlayerModal = false;
            },
            playerTypeChanged() {}
        };
    }

    function confirmDelete(message, formId) {
        Swal.fire({
            title: 'حذف بازی',
            text: message,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#1f2937',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '🗑️ بله، حذف کن',
            cancelButtonText: 'انصراف',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) document.getElementById(formId).submit();
        });
    }

    function confirmRemoveParticipant(participantId, participantName) {
        Swal.fire({
            title: 'حذف بازیکن',
            html: `آیا مطمئن هستید که می‌خواهید <strong class="text-red-600">${participantName}</strong> را از بازی حذف کنید؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '🗑️ بله، حذف کن',
            cancelButtonText: 'انصراف',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('remove-participant-' + participantId).submit();
        });
    }
</script>