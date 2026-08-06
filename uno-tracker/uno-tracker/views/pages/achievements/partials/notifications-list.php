<?php

use Core\JalaliDate;

if (empty($notifications)):
?>
    <div class="text-center py-8">
        <div class="text-5xl mb-3 opacity-50">🔔</div>
        <p class="text-gray-500 font-medium">اعلانی وجود ندارد</p>
    </div>
<?php else: ?>
    <div class="space-y-2.5">
        <?php foreach ($notifications as $notification): ?>
            <?php
            $isUnread = !$notification->is_read;

            $bgColor = match ($notification->type) {
                'achievement' => 'bg-yellow-50 border-yellow-200',
                'title' => 'bg-purple-50 border-purple-200',
                'level_up' => 'bg-indigo-50 border-indigo-200',
                'streak' => 'bg-orange-50 border-orange-200',
                default => 'bg-gray-50 border-gray-200',
            };
            ?>
            <div class="relative rounded-2xl p-3.5 border-2 <?= $bgColor ?> <?= $isUnread ? 'ring-2 ring-indigo-300 shadow-md' : 'opacity-75' ?> transition-all duration-300 hover:shadow-lg hover:scale-[1.01]">
                <?php if ($isUnread): ?>
                    <div class="absolute top-3 left-3 w-3 h-3 bg-indigo-500 rounded-full animate-pulse shadow-lg shadow-indigo-500/50"
                        title="اعلان خوانده نشده"></div>
                <?php endif; ?>

                <div class="flex items-start gap-3 <?= $isUnread ? 'pr-4' : '' ?>">
                    <!-- Icon -->
                    <div class="text-3xl flex-shrink-0 drop-shadow">
                        <?= $notification->icon ?>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-800 text-sm sm:text-base">
                            <?= htmlspecialchars($notification->title) ?>
                        </h4>
                        <?php if ($notification->message): ?>
                            <p class="text-xs text-gray-600 font-medium mt-0.5">
                                <?= htmlspecialchars($notification->message) ?>
                            </p>
                        <?php endif; ?>
                        <div class="text-[10px] text-gray-400 font-medium mt-1">
                            <?= JalaliDate::format('Y/m/d H:i', strtotime($notification->created_at)) ?>
                        </div>
                    </div>

                    <?php if ($isUnread): ?>
                        <div class="flex-shrink-0">
                            <button onclick="markNotificationAsRead(<?= $notification->id ?>)"
                                class="inline-block px-5 py-2 bg-white hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-xl transition border-2 border-gray-300 shadow-sm hover:shadow-md min-w-[80px]">
                                ✓ خواندم
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>