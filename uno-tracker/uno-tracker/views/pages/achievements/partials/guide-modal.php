<!-- مودال راهنما -->
<div id="guide-modal"
    class="fixed inset-0 z-50 items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    style="display: none;">

    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden flex flex-col border-2 border-white/20">

        <!-- Header -->
        <div class="relative bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 p-5 sm:p-6 text-white flex-shrink-0 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black flex items-center gap-2.5 drop-shadow">
                        <span>📖</span>
                        راهنمای دستاوردها
                    </h2>
                    <p class="text-white/80 text-xs sm:text-sm font-medium mt-0.5">همه چیز درباره سیستم پیشرفت</p>
                </div>
                <button onclick="closeGuideModal()"
                    class="text-white/80 hover:text-white text-2xl w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/20 transition duration-200">
                    ✕
                </button>
            </div>
        </div>

        <!-- Tabs با اسکرول افقی -->
        <div class="relative border-b-2 border-gray-200 px-2 sm:px-4 flex-shrink-0">
            <!-- Scroll Buttons -->
            <button onclick="scrollGuideTabs(-1)" 
                    class="hidden sm:flex absolute left-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white shadow-xl rounded-full items-center justify-center text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition border-2 border-gray-200 hover:border-indigo-300">
                ←
            </button>
            <button onclick="scrollGuideTabs(1)" 
                    class="hidden sm:flex absolute right-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white shadow-xl rounded-full items-center justify-center text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition border-2 border-gray-200 hover:border-indigo-300">
                →
            </button>

            <div id="guide-tabs-container"
                class="flex gap-1 overflow-x-auto pt-2 pb-1 scroll-smooth"
                style="scrollbar-width: none; -ms-overflow-style: none;">
                <style>
                    #guide-tabs-container::-webkit-scrollbar { display: none; }
                </style>

                <button id="guide-btn-xp" onclick="switchGuideTab('xp')"
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-indigo-600 text-indigo-600 bg-indigo-50 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl">
                    ⭐ امتیاز تجربه
                </button>
                <button id="guide-btn-levels" onclick="switchGuideTab('levels')"
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl">
                    🎖️ سطوح
                </button>
                <button id="guide-btn-streak" onclick="switchGuideTab('streak')"
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl">
                    🔥 زنجیره پیروزی
                </button>
                <button id="guide-btn-achievements" onclick="switchGuideTab('achievements')"
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl">
                    🏅 مدال‌ها
                </button>
                <button id="guide-btn-notifications" onclick="switchGuideTab('notifications')"
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl">
                    🔔 اعلان‌ها
                </button>
            </div>

            <div class="sm:hidden flex justify-center gap-1 py-1">
                <div class="text-[10px] text-gray-400 font-medium">← برای مشاهده بیشتر بکشید →</div>
            </div>
        </div>

        <!-- Content -->
        <div class="overflow-y-auto flex-1 p-4 sm:p-6 space-y-4 scrollbar-thin scrollbar-thumb-gray-300">

            <!-- XP Tab -->
            <div id="guide-tab-xp" class="guide-tab-content">
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100/80 border-2 border-indigo-200 rounded-2xl p-4 shadow-sm">
                    <h3 class="font-black text-indigo-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">⭐</span>
                        امتیاز تجربه (XP) چیست؟
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">
                        امتیاز تجربه واحد اندازه‌گیری پیشرفت شماست. با هر فعالیت، امتیاز تجربه کسب می‌کنید و با رسیدن به مقدار مشخصی، به سطح بالاتر ارتقا می‌یابید.
                    </p>
                </div>

                <h4 class="font-black text-gray-800 mt-4 text-sm">📊 نحوه کسب امتیاز تجربه</h4>
                <div class="space-y-2">
                    <?php
                    $xpSources = [
                        ['🎮', 'شرکت در هر بازی', '۵ امتیاز'],
                        ['🏆', 'برد در بازی انفرادی', '۱۵ امتیاز'],
                        ['👥', 'برد در بازی تیمی', '۲۰ امتیاز'],
                        ['🏅', 'کسب مدال افتخار', '۱۰ تا ۱۵۰ امتیاز'],
                        ['🎯', 'تکمیل ماموریت', '۱۵ تا ۵۰۰ امتیاز'],
                    ];
                    foreach ($xpSources as $source):
                    ?>
                        <div class="flex items-center gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                            <span class="text-2xl flex-shrink-0"><?= $source[0] ?></span>
                            <span class="flex-1 text-sm text-gray-700 font-medium"><?= $source[1] ?></span>
                            <span class="font-black text-indigo-600 text-sm"><?= $source[2] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-4 mt-4 shadow-sm">
                    <h4 class="font-black text-yellow-800 mb-2">💡 نکته مهم</h4>
                    <p class="text-gray-700 text-sm font-medium">
                        هرچه سطح شما بالاتر باشد، برای رسیدن به سطح بعدی به امتیاز تجربه بیشتری نیاز دارید. این باعث می‌شود رقابت جذاب‌تر شود!
                    </p>
                </div>
            </div>

            <!-- Levels Tab -->
            <div id="guide-tab-levels" class="guide-tab-content" style="display: none;">
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100/80 border-2 border-indigo-200 rounded-2xl p-4 shadow-sm">
                    <h3 class="font-black text-indigo-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">🎖️</span>
                        سطوح بازیکن
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">
                        با کسب امتیاز تجربه، به سطوح بالاتر ارتقا می‌یابید. هر سطح عنوان و آیکون مخصوص خود را دارد.
                    </p>
                </div>

                <h4 class="font-black text-gray-800 mt-4 text-sm">🗺️ نقشه سطوح</h4>
                <div class="space-y-2">
                    <?php foreach ($allLevels as $level): ?>
                        <div class="flex items-center gap-3 p-3 rounded-2xl border-2 shadow-sm transition hover:shadow-md"
                            style="background: linear-gradient(90deg, <?= $level->color ?>15, <?= $level->color ?>05); border-color: <?= $level->color ?>40;">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl flex-shrink-0 shadow-sm"
                                style="background-color: <?= $level->color ?>; color: white;">
                                <?= $level->icon ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-black text-gray-800 text-sm">سطح <?= $level->level ?>: <?= htmlspecialchars($level->title) ?></div>
                                <div class="text-xs text-gray-500 font-medium"><?= number_format($level->min_xp) ?> تا <?= number_format($level->max_xp) ?> امتیاز</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Streak Tab -->
            <div id="guide-tab-streak" class="guide-tab-content" style="display: none;">
                <div class="bg-gradient-to-r from-orange-50 to-amber-50/80 border-2 border-orange-200 rounded-2xl p-4 shadow-sm">
                    <h3 class="font-black text-orange-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">🔥</span>
                        زنجیره پیروزی چیست؟
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">
                        زنجیره پیروزی تعداد بردهای متوالی شما بدون باخت است. هرچه زنجیره طولانی‌تر باشد، افتخار بیشتری کسب می‌کنید!
                    </p>
                </div>

                <h4 class="font-black text-gray-800 mt-4 text-sm">📈 نحوه کار</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-2xl border-2 border-green-200 shadow-sm">
                        <span class="text-2xl flex-shrink-0">✅</span>
                        <div class="flex-1">
                            <div class="font-black text-sm">برد</div>
                            <div class="text-xs text-gray-600 font-medium">زنجیره +۱ می‌شود</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-red-50 rounded-2xl border-2 border-red-200 shadow-sm">
                        <span class="text-2xl flex-shrink-0">❌</span>
                        <div class="flex-1">
                            <div class="font-black text-sm">باخت</div>
                            <div class="text-xs text-gray-600 font-medium">زنجیره به صفر ریست می‌شود</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-yellow-50 rounded-2xl border-2 border-yellow-200 shadow-sm">
                        <span class="text-2xl flex-shrink-0">⏰</span>
                        <div class="flex-1">
                            <div class="font-black text-sm">۲۴ ساعت بدون برد</div>
                            <div class="text-xs text-gray-600 font-medium">زنجیره به طور خودکار ریست می‌شود</div>
                        </div>
                    </div>
                </div>

                <h4 class="font-black text-gray-800 mt-4 text-sm">🏆 نقاط عطف زنجیره</h4>
                <div class="!grid !grid-cols-2 gap-2">
                    <?php
                    $milestones = [
                        ['🔥', '۳ برد', 'آتشین', '۲۵ امتیاز'],
                        ['⚡', '۵ برد', 'طوفانی', '۵۰ امتیاز'],
                        ['💥', '۱۰ برد', 'شکست‌ناپذیر', '۱۰۰ امتیاز'],
                        ['🌟', '۲۰ برد', 'افسانه‌ای', '۲۰۰ امتیاز'],
                    ];
                    foreach ($milestones as $milestone):
                    ?>
                        <div class="bg-gradient-to-br from-orange-50 to-red-50 border-2 border-orange-200 rounded-2xl p-3 text-center shadow-sm hover:shadow-md transition">
                            <div class="text-3xl"><?= $milestone[0] ?></div>
                            <div class="font-black text-sm mt-1"><?= $milestone[1] ?></div>
                            <div class="text-xs text-gray-600 font-medium"><?= $milestone[2] ?></div>
                            <div class="text-xs text-indigo-600 font-black mt-1"><?= $milestone[3] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Achievements Tab -->
            <div id="guide-tab-achievements" class="guide-tab-content" style="display: none;">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50/80 border-2 border-purple-200 rounded-2xl p-4 shadow-sm">
                    <h3 class="font-black text-purple-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">🏅</span>
                        مدال‌های افتخار
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">
                        مدال‌های افتخار دستاوردهای ویژه‌ای هستند که با رسیدن به اهداف خاص کسب می‌کنید. هر مدال امتیاز تجربه و افتخار خاصی دارد.
                    </p>
                </div>

                <h4 class="font-black text-gray-800 mt-4 text-sm">🎨 دسته‌بندی مدال‌ها</h4>
                <div class="space-y-2">
                    <?php
                    $categories = [
                        ['🎮', 'عمومی', 'مدال‌های مربوط به تعداد بازی‌ها'],
                        ['🏆', 'پیروزی', 'مدال‌های مربوط به تعداد بردها'],
                        ['🔥', 'زنجیره پیروزی', 'مدال‌های مربوط به بردهای متوالی'],
                        ['👥', 'تیمی', 'مدال‌های مربوط به بازی تیمی'],
                        ['⭐', 'ویژه', 'مدال‌های خاص و کمیاب'],
                    ];
                    foreach ($categories as $cat):
                    ?>
                        <div class="flex items-center gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                            <span class="text-2xl flex-shrink-0"><?= $cat[0] ?></span>
                            <div class="flex-1">
                                <div class="font-black text-sm"><?= $cat[1] ?></div>
                                <div class="text-xs text-gray-600 font-medium"><?= $cat[2] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h4 class="font-black text-gray-800 mt-4 text-sm">💎 کمیابی مدال‌ها</h4>
                <div class="space-y-2">
                    <?php
                    $rarities = [
                        ['#94a3b8', 'معمولی', 'به راحتی کسب می‌شوند'],
                        ['#3b82f6', 'کمیاب', 'نیاز به تلاش بیشتر'],
                        ['#a855f7', 'حماسی', 'چالش‌برانگیز'],
                        ['#f59e0b', 'افسانه‌ای', 'بسیار دشوار و ارزشمند'],
                    ];
                    foreach ($rarities as $rarity):
                    ?>
                        <div class="flex items-center gap-3 p-3 rounded-2xl border-2 shadow-sm transition hover:shadow-md"
                            style="background-color: <?= $rarity[0] ?>15; border-color: <?= $rarity[0] ?>40;">
                            <div class="w-8 h-8 rounded-full flex-shrink-0" style="background-color: <?= $rarity[0] ?>"></div>
                            <div class="flex-1">
                                <div class="font-black text-sm" style="color: <?= $rarity[0] ?>"><?= $rarity[1] ?></div>
                                <div class="text-xs text-gray-600 font-medium"><?= $rarity[2] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div id="guide-tab-notifications" class="guide-tab-content" style="display: none;">
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50/80 border-2 border-blue-200 rounded-2xl p-4 shadow-sm">
                    <h3 class="font-black text-blue-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">🔔</span>
                        اعلان‌ها
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">
                        اعلان‌ها شما را از رویدادهای مهم مطلع می‌کنند. مثل کسب مدال جدید، ارتقا به سطح بالاتر، یا تکمیل ماموریت.
                    </p>
                </div>

                <h4 class="font-black text-gray-800 mt-4 text-sm">🔵 نشانگر اعلان خوانده نشده</h4>
                <div class="p-4 bg-gray-50/80 rounded-2xl border-2 border-gray-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-2xl">🔔</div>
                            <div class="absolute top-0 right-0 w-3.5 h-3.5 bg-indigo-500 rounded-full border-2 border-white shadow-lg"></div>
                        </div>
                        <div class="flex-1">
                            <div class="font-black text-sm">نقطه آبی کوچک</div>
                            <div class="text-xs text-gray-600 font-medium">نشان می‌دهد این اعلان هنوز خوانده نشده است</div>
                        </div>
                    </div>
                </div>

                <h4 class="font-black text-gray-800 mt-4 text-sm">📬 انواع اعلان‌ها</h4>
                <div class="space-y-2">
                    <?php
                    $notificationTypes = [
                        ['🏅', 'زرد', 'کسب مدال افتخار جدید'],
                        ['🎖️', 'بنفش', 'کسب عنوان جدید'],
                        ['⬆️', 'نیلی', 'ارتقا به سطح بالاتر'],
                        ['🎯', 'سبز', 'تکمیل ماموریت'],
                        ['🔥', 'نارنجی', 'رکورد زنجیره پیروزی'],
                    ];
                    foreach ($notificationTypes as $type):
                    ?>
                        <div class="flex items-center gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                            <span class="text-2xl flex-shrink-0"><?= $type[0] ?></span>
                            <div class="flex-1">
                                <div class="font-black text-sm"><?= $type[1] ?></div>
                                <div class="text-xs text-gray-600 font-medium"><?= $type[2] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-4 mt-4 shadow-sm">
                    <h4 class="font-black text-yellow-800 mb-2">💡 نکته</h4>
                    <p class="text-gray-700 text-sm font-medium">
                        با کلیک روی دکمه "✓ خواندم"، اعلان به عنوان خوانده شده علامت‌گذاری می‌شود و نقطه آبی ناپدید می‌شود.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t-2 border-gray-200 p-4 bg-gray-50/80 flex-shrink-0">
            <button onclick="closeGuideModal()"
                class="w-full py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl font-black transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02]">
                متوجه شدم 👍
            </button>
        </div>
    </div>
</div>