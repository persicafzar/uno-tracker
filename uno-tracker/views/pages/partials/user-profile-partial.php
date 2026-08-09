<?php
$isGuest = !empty($profile['is_guest']) || empty($profile['user_id']);
$hasCurrentGame = !empty($profile['current_game']) && is_array($profile['current_game']) && isset($profile['current_game']['name']);
$titleInfo = $profile['title_info'] ?? null;
?>

<!-- ========================================== -->
<!-- ======= کارت پروفایل کاربر (نسخه‌ی متعادل برای دراور) ======= -->
<!-- ========================================== -->
<div class="space-y-4 sm:space-y-5">

    <!-- ======= هدر با آواتار ======= -->
    <div class="relative overflow-hidden bg-gradient-to-br from-white to-gray-50/80 rounded-2xl p-4 sm:p-5 border-2 border-gray-200/70 shadow-sm text-center">
        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 bg-violet-500/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        
        <div class="relative z-10">
            <!-- آواتار -->
            <?php if (!empty($profile['avatar_path'])): ?>
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-indigo-500 shadow-lg overflow-hidden mx-auto hover:scale-105 transition-all duration-300">
                    <img src="/storage/uploads/avatars/<?= htmlspecialchars($profile['avatar_path']) ?>"
                        alt="<?= htmlspecialchars($profile['nickname']) ?>"
                        class="w-full h-full aspect-square rounded-full object-cover">
                </div>
            <?php else: ?>
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-indigo-200 to-violet-200 mx-auto border-4 border-indigo-500 shadow-lg flex items-center justify-center text-3xl sm:text-4xl font-black text-indigo-700">
                    <?= mb_substr($profile['nickname'] ?? '?', 0, 1) ?>
                </div>
            <?php endif; ?>

            <!-- نام -->
            <h2 class="text-xl sm:text-2xl font-black text-gray-800 mt-3 flex items-center justify-center gap-2 flex-wrap">
                <span><?= htmlspecialchars($profile['nickname']) ?></span>
                <?php if ($isGuest): ?>
                    <span class="text-xs bg-gray-200 text-gray-600 px-2.5 py-0.5 rounded-full font-bold border border-gray-300 shadow-sm">مهمان</span>
                <?php endif; ?>
            </h2>
            
            <?php if (!$isGuest): ?>
                <p class="text-sm text-gray-500 font-medium mt-0.5"><?= htmlspecialchars($profile['real_name'] ?? '') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ======= عنوان و بونوس ======= -->
    <?php if ($titleInfo && !$isGuest): ?>
        <div class="relative overflow-hidden bg-gradient-to-r from-yellow-100/80 via-amber-100/80 to-orange-100/80 rounded-2xl p-4 sm:p-5 border-2 border-yellow-300 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-12 h-12 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            
            <div class="relative z-10 flex items-center gap-3">
                <span class="text-4xl sm:text-5xl drop-shadow-md flex-shrink-0"><?= htmlspecialchars($titleInfo['icon'] ?? '🏆') ?></span>
                <div class="flex-1 min-w-0">
                    <div class="text-[10px] text-orange-600 font-black tracking-wider">عنوان فعال</div>
                    <h3 class="text-base sm:text-lg font-black text-gray-800 truncate"><?= htmlspecialchars($titleInfo['name'] ?? '') ?></h3>
                    <?php if (!empty($titleInfo['bonus_points']) && $titleInfo['bonus_points'] > 0): ?>
                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                            <span class="bg-green-500/20 text-green-700 px-2.5 py-0.5 rounded-full text-xs font-black border border-green-300 shadow-sm inline-flex items-center gap-1">
                                <span>⭐</span>
                                <span>+<?= $titleInfo['bonus_points'] ?> امتیاز</span>
                            </span>
                            <span class="text-[10px] text-gray-500 font-medium">در هر برد</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ======= بازی فعلی ======= -->
    <?php if ($hasCurrentGame): ?>
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-100/80 to-violet-100/80 rounded-2xl p-4 sm:p-5 border-2 border-indigo-300 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-14 h-14 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            
            <div class="relative z-10">
                <h3 class="font-black text-gray-800 text-sm sm:text-base mb-3 flex items-center gap-2 truncate">
                    <span class="text-xl drop-shadow">🎮</span>
                    <span class="truncate">بازی فعلی: <span class="text-indigo-700"><?= htmlspecialchars($profile['current_game']['name'] ?? 'نامشخص') ?></span></span>
                </h3>
                
                <div class="!grid !grid-cols-2 gap-2.5 text-xs sm:text-sm">
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-2.5 border border-indigo-200 shadow-sm text-center">
                        <div class="text-gray-500 font-medium text-[10px]">حالت</div>
                        <div class="font-black text-gray-800">
                            <?= $profile['current_game']['mode'] === 'friendly' ? '👥 تیمی' : '👤 انفرادی' ?>
                        </div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-2.5 border border-indigo-200 shadow-sm text-center">
                        <div class="text-gray-500 font-medium text-[10px]">هدف</div>
                        <div class="font-black text-gray-800"><?= $profile['current_game']['target_wins'] ?> برد</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-2.5 border border-green-200 shadow-sm text-center">
                        <div class="text-gray-500 font-medium text-[10px]">بردها</div>
                        <div class="font-black text-green-600"><?= $profile['current_game']['wins_in_game'] ?? 0 ?></div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-2.5 border border-indigo-200 shadow-sm text-center">
                        <div class="text-gray-500 font-medium text-[10px]">امتیاز</div>
                        <div class="font-black text-indigo-600"><?= $profile['current_game']['score_in_game'] ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ======= آمار ======= -->
    <?php if (!$isGuest): ?>
        <div class="!grid !grid-cols-2 gap-2.5 sm:gap-3">
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-2xl p-3 sm:p-4 text-center border-2 border-indigo-300 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02] group">
                <div class="absolute top-0 right-0 w-10 h-10 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <div class="text-xl sm:text-2xl font-black text-indigo-700 drop-shadow"><?= $profile['total_games'] ?? 0 ?></div>
                    <div class="text-xs text-gray-600 font-medium mt-0.5">کل بازی‌ها</div>
                </div>
            </div>
            
            <div class="relative overflow-hidden bg-gradient-to-br from-green-100 to-green-200 rounded-2xl p-3 sm:p-4 text-center border-2 border-green-300 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02] group">
                <div class="absolute top-0 right-0 w-10 h-10 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <div class="text-xl sm:text-2xl font-black text-green-700 drop-shadow"><?= $profile['total_wins'] ?? 0 ?></div>
                    <div class="text-xs text-gray-600 font-medium mt-0.5">کل بردها</div>
                </div>
            </div>
            
            <div class="relative overflow-hidden bg-gradient-to-br from-violet-100 to-violet-200 rounded-2xl p-3 sm:p-4 text-center border-2 border-violet-300 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02] group">
                <div class="absolute top-0 right-0 w-10 h-10 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <div class="text-xl sm:text-2xl font-black text-violet-700 drop-shadow"><?= number_format($profile['win_rate'] ?? 0, 1) ?>%</div>
                    <div class="text-xs text-gray-600 font-medium mt-0.5">نرخ برد</div>
                </div>
            </div>
            
            <div class="relative overflow-hidden bg-gradient-to-br from-pink-100 to-pink-200 rounded-2xl p-3 sm:p-4 text-center border-2 border-pink-300 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02] group">
                <div class="absolute top-0 right-0 w-10 h-10 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10">
                    <div class="text-xl sm:text-2xl font-black text-pink-700 drop-shadow"><?= $profile['total_points'] ?? 0 ?></div>
                    <div class="text-xs text-gray-600 font-medium mt-0.5">امتیاز کل</div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ======= دکمه مشاهده پروفایل کامل ======= -->
    <?php if (!$isGuest): ?>
        <a href="/users/<?= $profile['user_id'] ?? $profile['id'] ?>"
            class="group relative block w-full text-center px-5 py-3 bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 hover:from-indigo-700 hover:via-violet-700 hover:to-purple-700 text-white rounded-2xl font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.02] text-sm sm:text-base overflow-hidden">
            <span class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
            <span class="relative z-10 flex items-center justify-center gap-2">
                <span class="text-xl group-hover:rotate-12 transition-transform duration-300">👤</span>
                <span>مشاهده پروفایل کامل</span>
            </span>
        </a>
    <?php else: ?>
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-5 text-center border-2 border-gray-200 shadow-sm">
            <div class="text-4xl mb-2 opacity-50">👤</div>
            <p class="text-gray-600 text-sm font-medium">این بازیکن مهمان است و حساب کاربری ندارد.</p>
        </div>
    <?php endif; ?>

</div>