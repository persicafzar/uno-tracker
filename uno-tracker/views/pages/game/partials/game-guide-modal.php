<!-- ========================================== -->
<!-- ======= مودال راهنمای ایجاد بازی ======= -->
<!-- ========================================== -->
<div id="game-guide-modal"
     class="fixed inset-0 z-50 items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     style="display: none;">

    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col border-2 border-white/20">

        <!-- ======= Header ======= -->
        <div class="relative bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 p-5 sm:p-6 text-white flex-shrink-0 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="absolute top-1/2 left-1/2 w-40 h-40 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black flex items-center gap-2.5 drop-shadow">
                        <span>📖</span>
                        راهنمای ایجاد بازی
                    </h2>
                    <p class="text-white/80 text-xs sm:text-sm font-medium mt-0.5">همه چیز درباره ایجاد بازی جدید</p>
                </div>
                <button onclick="closeCreateGameGuide()" 
                    class="text-white/80 hover:text-white text-2xl w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/20 transition duration-200">
                    ✕
                </button>
            </div>
        </div>

        <!-- ======= Tabs ======= -->
        <div class="relative border-b-2 border-gray-200 px-2 sm:px-4 py-1 flex-shrink-0 bg-gray-50/80">
            <!-- دکمه‌های اسکرول -->
            <button onclick="scrollCreateGuideTabs(-1)" 
                    class="hidden sm:flex absolute left-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white shadow-xl rounded-full items-center justify-center text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition border-2 border-gray-200 hover:border-indigo-300">
                ←
            </button>
            <button onclick="scrollCreateGuideTabs(1)" 
                    class="hidden sm:flex absolute right-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white shadow-xl rounded-full items-center justify-center text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition border-2 border-gray-200 hover:border-indigo-300">
                →
            </button>

            <div id="create-guide-tabs-container"
                class="flex gap-1 overflow-x-auto scroll-smooth py-2"
                style="scrollbar-width: none; -ms-overflow-style: none;">
                <style>
                    #create-guide-tabs-container::-webkit-scrollbar { display: none; }
                </style>

                <button onclick="switchCreateGuideTab('general')" id="gbtn-general" 
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-indigo-600 text-indigo-600 bg-white font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl shadow-sm">
                    🎮 کلیات
                </button>
                <button onclick="switchCreateGuideTab('players')" id="gbtn-players" 
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl">
                    👥 بازیکنان
                </button>
                <button onclick="switchCreateGuideTab('teams')" id="gbtn-teams" 
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl">
                    🏆 تیم‌ها
                </button>
                <button onclick="switchCreateGuideTab('dragdrop')" id="gbtn-dragdrop" 
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl">
                    🖐️ Drag & Drop
                </button>
                <button onclick="switchCreateGuideTab('algorithms')" id="gbtn-algorithms" 
                    class="guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl">
                    ⚙️ الگوریتم‌ها
                </button>
            </div>

            <div class="sm:hidden flex justify-center gap-1 py-0.5">
                <div class="text-[10px] text-gray-400 font-medium">← برای مشاهده بیشتر بکشید →</div>
            </div>
        </div>

        <!-- ======= Content ======= -->
        <div class="overflow-y-auto flex-1 p-4 sm:p-6 space-y-5 scrollbar-thin scrollbar-thumb-gray-300">

            <!-- ===== Tab: کلیات ===== -->
            <div id="gtab-general" class="guide-tab-content space-y-4">

                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100/80 border-2 border-indigo-200 rounded-2xl p-4 shadow-sm mb-4">
                    <h3 class="font-black text-indigo-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">🎮</span>
                        ایجاد بازی جدید
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">با این فرم بازی‌های UNO خود را ثبت کنید. بازی می‌تواند انفرادی یا تیمی باشد.</p>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-1">
                    <span>📝</span> مراحل ایجاد بازی
                </h4>

                <div class="space-y-2.5 mb-4">
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="w-7 h-7 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-full flex items-center justify-center text-xs font-black flex-shrink-0 shadow-sm">۱</div>
                        <div>
                            <div class="font-bold text-sm">نام بازی</div>
                            <div class="text-xs text-gray-600 font-medium">یک نام برای بازی انتخاب کنید (پیش‌فرض: لیگ انفرادی/دوستانه - تاریخ)</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="w-7 h-7 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-full flex items-center justify-center text-xs font-black flex-shrink-0 shadow-sm">۲</div>
                        <div>
                            <div class="font-bold text-sm">حالت بازی</div>
                            <div class="text-xs text-gray-600 font-medium">انفرادی یا تیمی (دوستانه) را انتخاب کنید</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="w-7 h-7 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-full flex items-center justify-center text-xs font-black flex-shrink-0 shadow-sm">۳</div>
                        <div>
                            <div class="font-bold text-sm">هدف برد</div>
                            <div class="text-xs text-gray-600 font-medium">تعداد برد مورد نیاز برای پیروزی (۳ تا ۲۰)</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="w-7 h-7 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-full flex items-center justify-center text-xs font-black flex-shrink-0 shadow-sm">۴</div>
                        <div>
                            <div class="font-bold text-sm">انتخاب بازیکنان</div>
                            <div class="text-xs text-gray-600 font-medium">بازیکنان ثبت‌نام شده یا مهمان را اضافه کنید</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="w-7 h-7 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-full flex items-center justify-center text-xs font-black flex-shrink-0 shadow-sm">۵</div>
                        <div>
                            <div class="font-bold text-sm">تنظیمات تیم‌ها (فقط تیمی)</div>
                            <div class="text-xs text-gray-600 font-medium">نام تیم‌ها و روش گروه‌بندی را مشخص کنید</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-2xl p-4 shadow-sm mb-4 mt-4">
                    <h4 class="font-black text-yellow-800 mb-2 flex items-center gap-2">
                        <span>⚠️</span> محدودیت‌های بازی
                    </h4>
                    <ul class="space-y-1.5 text-sm text-gray-700 font-medium">
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-600">•</span>
                            <div><strong>بازی انفرادی:</strong> حداقل ۲ بازیکن لازم است</div>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-600">•</span>
                            <div><strong>بازی تیمی:</strong> حداقل ۴ بازیکن لازم است</div>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-600">•</span>
                            <div><strong>بازی تیمی:</strong> تعداد بازیکنان باید زوج باشد</div>
                        </li>
                    </ul>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-2xl p-4 shadow-sm mt-4">
                    <h4 class="font-black text-blue-800 mb-2 flex items-center gap-2">
                        <span>💡</span> نکته
                    </h4>
                    <p class="text-gray-700 text-sm font-medium">
                        همه فیلدها قابل ویرایش هستند. نام‌های پیش‌فرض فقط برای راحتی شما هستند و می‌توانید آن‌ها را تغییر دهید.
                    </p>
                </div>
            </div>

            <!-- ===== Tab: بازیکنان ===== -->
            <div id="gtab-players" class="guide-tab-content space-y-4" style="display:none;">

                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100/80 border-2 border-indigo-200 rounded-2xl p-4 shadow-sm mb-4">
                    <h3 class="font-black text-indigo-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">👥</span>
                        بازیکنان
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">دو نوع بازیکن وجود دارد: بازیکنان ثبت‌نام شده و بازیکنان مهمان.</p>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-1">
                    <span>🎯</span> بازیکنان ثبت‌نام شده
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <ul class="space-y-1 text-sm text-gray-700 font-medium">
                        <li class="flex items-start gap-2">• کاربرانی که در سایت ثبت‌نام کرده‌اند</li>
                        <li class="flex items-start gap-2">• آواتار و نام مستعار دارند</li>
                        <li class="flex items-start gap-2">• آمار بازی‌های قبلی آن‌ها نمایش داده می‌شود</li>
                        <li class="flex items-start gap-2">• در لیدربورد شرکت می‌کنند</li>
                        <li class="flex items-start gap-2">• امتیاز تجربه (XP) کسب می‌کنند</li>
                    </ul>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-4">
                    <span>👤</span> بازیکنان مهمان
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <ul class="space-y-1 text-sm text-gray-700 font-medium">
                        <li class="flex items-start gap-2">• کاربرانی که در سایت ثبت‌نام نکرده‌اند</li>
                        <li class="flex items-start gap-2">• فقط نام آن‌ها ثبت می‌شود</li>
                        <li class="flex items-start gap-2">• آواتار ندارند (آیکون پیش‌فرض)</li>
                        <li class="flex items-start gap-2">• می‌توانید آن‌ها را حذف کنید</li>
                        <li class="flex items-start gap-2">• در لیدربورد شرکت نمی‌کنند</li>
                    </ul>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-4">
                    <span>➕</span> افزودن بازیکن مهمان
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700 font-medium">
                        <li>در فیلد "نام بازیکن مهمان" نام را وارد کنید</li>
                        <li>روی دکمه "➕ افزودن" کلیک کنید</li>
                        <li>یا کلید Enter را بزنید</li>
                    </ol>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-4">
                    <span>🗑️</span> حذف بازیکن مهمان
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <p class="text-sm text-gray-700 font-medium">
                        روی دکمه <span class="font-bold text-red-500">✕</span> کنار نام بازیکن مهمان کلیک کنید.
                    </p>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-2xl p-4 shadow-sm mt-4">
                    <h4 class="font-black text-yellow-800 mb-2 flex items-center gap-2">
                        <span>⚠️</span> محدودیت
                    </h4>
                    <p class="text-gray-700 text-sm font-medium">
                        حداقل ۲ بازیکن برای ایجاد بازی لازم است. در بازی تیمی حداقل ۴ بازیکن لازم است.
                    </p>
                </div>
            </div>

            <!-- ===== Tab: تیم‌ها ===== -->
            <div id="gtab-teams" class="guide-tab-content space-y-4" style="display:none;">

                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100/80 border-2 border-indigo-200 rounded-2xl p-4 shadow-sm mb-4">
                    <h3 class="font-black text-indigo-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">🏆</span>
                        تنظیمات تیم‌ها
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">این بخش فقط در حالت تیمی (دوستانه) نمایش داده می‌شود.</p>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-2xl p-3 sm:p-4 shadow-sm mb-4">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-6 h-6 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-xs sm:text-sm text-blue-800 font-medium">
                            <strong>تعداد تیم‌ها:</strong> بر اساس تعداد بازیکنان به صورت خودکار محاسبه می‌شود (هر تیم ۲ نفر). اگر تعداد بازیکنان فرد باشد، یک تیم ۳ نفره خواهد بود.
                            <br>
                            <strong class="text-red-600">محدودیت:</strong> هر تیم باید حداقل ۱ و حداکثر ۲ بازیکن داشته باشد.
                        </div>
                    </div>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-1">
                    <span>🔢</span> تعداد تیم‌ها
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <p class="text-sm text-gray-700 font-medium">
                        تعداد تیم‌ها به صورت خودکار محاسبه می‌شود: <strong class="text-indigo-600">تعداد بازیکنان ÷ ۲</strong>
                    </p>
                    <div class="mt-2 text-xs text-gray-500 font-medium">
                        مثال: ۶ بازیکن = ۳ تیم | ۸ بازیکن = ۴ تیم
                    </div>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-4">
                    <span>👥</span> اعضای هر تیم
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <ul class="space-y-1 text-sm text-gray-700 font-medium">
                        <li class="flex items-start gap-2">• حداقل: <strong class="text-green-600">۱ بازیکن</strong></li>
                        <li class="flex items-start gap-2">• حداکثر: <strong class="text-red-600">۲ بازیکن</strong></li>
                        <li class="flex items-start gap-2">• اگر تعداد بازیکنان فرد باشد، یک تیم ۳ نفره خواهد بود</li>
                    </ul>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-4">
                    <span>✏️</span> نام تیم‌ها
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <p class="text-sm text-gray-700 font-medium">
                        نام‌های پیش‌فرض "تیم ۱"، "تیم ۲" و... هستند. می‌توانید آن‌ها را ویرایش کنید و نام‌های دلخواه بگذارید (مثلاً "تیم قرمز"، "تیم آبی").
                    </p>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-2xl p-4 shadow-sm mt-4">
                    <h4 class="font-black text-yellow-800 mb-2 flex items-center gap-2">
                        <span>💡</span> نکته
                    </h4>
                    <p class="text-gray-700 text-sm font-medium">
                        برای بازی تیمی، تعداد بازیکنان باید زوج باشد. اگر فرد باشد، یک بازیکن دیگر اضافه کنید یا یک بازیکن را حذف کنید.
                    </p>
                </div>
            </div>

            <!-- ===== Tab: Drag & Drop ===== -->
            <div id="gtab-dragdrop" class="guide-tab-content space-y-4" style="display:none;">

                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100/80 border-2 border-indigo-200 rounded-2xl p-4 shadow-sm mb-4">
                    <h3 class="font-black text-indigo-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">🖐️</span>
                        گروه‌بندی دستی (Drag & Drop)
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">با این روش، خودتان بازیکنان را در تیم‌ها قرار می‌دهید.</p>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-3 sm:p-4 shadow-sm mb-4">
                    <div class="flex items-start gap-2.5">
                        <div class="text-3xl flex-shrink-0">💡</div>
                        <div class="text-xs sm:text-sm text-blue-800 font-medium">
                            <strong class="block mb-1">راهنما:</strong>
                            <ul class="list-disc list-inside space-y-0.5">
                                <li>فقط از آیکون <span class="font-bold">⋮⋮</span> بکشید</li>
                                <li>هر تیم <span class="font-bold text-green-600">۱ تا ۲ بازیکن</span></li>
                                <li>برای حذف روی <span class="font-bold text-red-500">✕</span> کلیک کنید</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-1">
                    <span>🖥️</span> نحوه استفاده در دسکتاپ
                </h4>
                <div class="space-y-2.5 mb-4">
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="text-xl flex-shrink-0">۱</div>
                        <div>
                            <div class="font-bold text-sm">گرفتن بازیکن</div>
                            <div class="text-xs text-gray-600 font-medium">روی آیکون ⋮⋮ کلیک کنید و نگه دارید</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="text-xl flex-shrink-0">۲</div>
                        <div>
                            <div class="font-bold text-sm">کشیدن</div>
                            <div class="text-xs text-gray-600 font-medium">بازیکن را به تیم مورد نظر بکشید</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="text-xl flex-shrink-0">۳</div>
                        <div>
                            <div class="font-bold text-sm">رها کردن</div>
                            <div class="text-xs text-gray-600 font-medium">در تیم مورد نظر رها کنید</div>
                        </div>
                    </div>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-4">
                    <span>📱</span> نحوه استفاده در موبایل
                </h4>
                <div class="space-y-2.5 mb-4">
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="text-xl flex-shrink-0">۱</div>
                        <div>
                            <div class="font-bold text-sm">لمس طولانی</div>
                            <div class="text-xs text-gray-600 font-medium">انگشت خود را روی آیکون ⋮⋮ نگه دارید</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="text-xl flex-shrink-0">۲</div>
                        <div>
                            <div class="font-bold text-sm">کشیدن</div>
                            <div class="text-xs text-gray-600 font-medium">انگشت را به سمت تیم مورد نظر بکشید</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50/80 rounded-xl border border-gray-200 hover:shadow-md transition">
                        <div class="text-xl flex-shrink-0">۳</div>
                        <div>
                            <div class="font-bold text-sm">رها کردن</div>
                            <div class="text-xs text-gray-600 font-medium">انگشت را رها کنید</div>
                        </div>
                    </div>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-4">
                    <span>🔄</span> جابجایی بین تیم‌ها
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <p class="text-sm text-gray-700 font-medium">
                        برای جابجایی بازیکن از یک تیم به تیم دیگر، بازیکن را از تیم فعلی بکشید و در تیم جدید رها کنید.
                    </p>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-4">
                    <span>↩️</span> برگشت به لیست در دسترس
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <p class="text-sm text-gray-700 font-medium">
                        برای برگرداندن بازیکن به لیست در دسترس، بازیکن را از تیم بکشید و در "بازیکنان در دسترس" رها کنید.
                    </p>
                </div>

                <h4 class="font-black text-gray-800 text-sm flex items-center gap-2 mb-3 mt-4">
                    <span>✕</span> حذف از تیم
                </h4>
                <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-200 shadow-sm mb-4">
                    <p class="text-sm text-gray-700 font-medium">
                        روی دکمه <span class="font-bold text-red-500">✕</span> کنار بازیکن در تیم کلیک کنید. بازیکن به لیست در دسترس برمی‌گردد.
                    </p>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-2xl p-4 shadow-sm mt-4">
                    <h4 class="font-black text-yellow-800 mb-2 flex items-center gap-2">
                        <span>💡</span> نکته
                    </h4>
                    <p class="text-gray-700 text-sm font-medium">
                        اگر Drag & Drop کار نکرد، صفحه را refresh کنید (Ctrl+F5). همچنین مطمئن شوید که از آیکون ⋮⋮ می‌کشید، نه از کل کارت.
                    </p>
                </div>
            </div>

            <!-- ===== Tab: الگوریتم‌ها ===== -->
            <div id="gtab-algorithms" class="guide-tab-content space-y-4" style="display:none;">

                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100/80 border-2 border-indigo-200 rounded-2xl p-4 shadow-sm mb-4">
                    <h3 class="font-black text-indigo-800 text-lg mb-2 flex items-center gap-2">
                        <span class="text-3xl">⚙️</span>
                        الگوریتم‌های گروه‌بندی
                    </h3>
                    <p class="text-gray-700 text-sm leading-relaxed font-medium">اگر نمی‌خواهید دستی گروه‌بندی کنید، از یکی از الگوریتم‌های خودکار استفاده کنید.</p>
                </div>

                <div class="space-y-3 mb-4">
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl shadow-sm hover:shadow-md transition">
                        <h4 class="font-black text-blue-800 mb-2 flex items-center gap-2.5">
                            <span class="text-2xl">🖐️</span>
                            دستی
                        </h4>
                        <p class="text-sm text-gray-700 font-medium mb-2">
                            خودتان بازیکنان را در تیم‌ها قرار می‌دهید
                        </p>
                        <ul class="list-disc list-inside text-xs text-gray-600 font-medium space-y-0.5">
                            <li>کنترل کامل روی ترکیب تیم‌ها</li>
                            <li>مناسب برای بازی‌های مهم و حساس</li>
                            <li>نیاز به Drag & Drop دارد</li>
                        </ul>
                    </div>

                    <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl shadow-sm hover:shadow-md transition">
                        <h4 class="font-black text-green-800 mb-2 flex items-center gap-2.5">
                            <span class="text-2xl">🎲</span>
                            کاملاً تصادفی
                        </h4>
                        <p class="text-sm text-gray-700 font-medium mb-2">
                            بازیکنان به صورت کاملاً تصادفی در تیم‌ها پخش می‌شوند
                        </p>
                        <ul class="list-disc list-inside text-xs text-gray-600 font-medium space-y-0.5">
                            <li>سریع و آسان</li>
                            <li>مناسب برای بازی‌های سرگرم‌کننده</li>
                            <li>ممکن است تیم‌های نابرابر ایجاد شود</li>
                        </ul>
                    </div>

                    <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-2xl shadow-sm hover:shadow-md transition">
                        <h4 class="font-black text-purple-800 mb-2 flex items-center gap-2.5">
                            <span class="text-2xl">⚖️</span>
                            بالانس (ضعیف با قوی)
                        </h4>
                        <p class="text-sm text-gray-700 font-medium mb-2">
                            قوی‌ترین بازیکن با ضعیف‌ترین بازیکن هم‌تیمی می‌شود
                        </p>
                        <ul class="list-disc list-inside text-xs text-gray-600 font-medium space-y-0.5">
                            <li>تیم‌ها از نظر قدرت متعادل می‌شوند</li>
                            <li>مناسب برای مسابقات رقابتی</li>
                            <li>بر اساس آمار بازی‌های قبلی محاسبه می‌شود</li>
                        </ul>
                    </div>

                    <div class="p-4 bg-gradient-to-br from-orange-50 to-red-50 border-2 border-orange-200 rounded-2xl shadow-sm hover:shadow-md transition">
                        <h4 class="font-black text-orange-800 mb-2 flex items-center gap-2.5">
                            <span class="text-2xl">🔀</span>
                            جداسازی یاران
                        </h4>
                        <p class="text-sm text-gray-700 font-medium mb-2">
                            بازیکنانی که معمولاً با هم بازی می‌کنند، در تیم‌های مختلف قرار می‌گیرند
                        </p>
                        <ul class="list-disc list-inside text-xs text-gray-600 font-medium space-y-0.5">
                            <li>جلوگیری از تقلب و هماهنگی</li>
                            <li>مناسب برای بازی‌های رقابتی جدی</li>
                            <li>بر اساس تاریخچه بازی‌های قبلی محاسبه می‌شود</li>
                        </ul>
                    </div>

                    <div class="p-4 bg-gradient-to-br from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-2xl shadow-sm hover:shadow-md transition">
                        <h4 class="font-black text-yellow-800 mb-2 flex items-center gap-2.5">
                            <span class="text-2xl">🔄</span>
                            جلوگیری از تکرار
                        </h4>
                        <p class="text-sm text-gray-700 font-medium mb-2">
                            از تکرار ترکیب‌های قبلی جلوگیری می‌کند
                        </p>
                        <ul class="list-disc list-inside text-xs text-gray-600 font-medium space-y-0.5">
                            <li>هر بار ترکیب جدیدی ایجاد می‌شود</li>
                            <li>مناسب برای تنوع در بازی‌ها</li>
                            <li>بر اساس تاریخچه بازی‌های قبلی محاسبه می‌شود</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-2xl p-4 shadow-sm mt-4">
                    <h4 class="font-black text-yellow-800 mb-2 flex items-center gap-2">
                        <span>💡</span> کدام الگوریتم را انتخاب کنم؟
                    </h4>
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 font-medium">
                        <li><strong class="text-blue-700">دستی:</strong> اگر می‌خواهید کنترل کامل داشته باشید</li>
                        <li><strong class="text-green-700">تصادفی:</strong> برای بازی‌های سرگرم‌کننده و دوستانه</li>
                        <li><strong class="text-purple-700">بالانس:</strong> برای مسابقات رقابتی و جدی</li>
                        <li><strong class="text-orange-700">جداسازی یاران:</strong> برای جلوگیری از تقلب</li>
                        <li><strong class="text-amber-700">جلوگیری از تکرار:</strong> برای تنوع در بازی‌ها</li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- ======= Footer ======= -->
        <div class="border-t-2 border-gray-200 p-4 bg-gray-50/80 flex-shrink-0">
            <button onclick="closeCreateGameGuide()" 
                class="w-full py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl font-black transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02]">
                متوجه شدم 👍
            </button>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- ======= JavaScript ======= -->
<!-- ========================================== -->
<script>
// ===== باز و بسته کردن مودال =====
function openCreateGameGuide() {
    const modal = document.getElementById('game-guide-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        // اسکرول به تب فعال
        setTimeout(() => {
            const container = document.getElementById('create-guide-tabs-container');
            const activeBtn = container?.querySelector('.border-indigo-600');
            if (container && activeBtn) {
                activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        }, 150);
    }
}

function closeCreateGameGuide() {
    const modal = document.getElementById('game-guide-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// ===== تغییر تب =====
function switchCreateGuideTab(tab) {
    // مخفی کردن همه تب‌ها
    document.querySelectorAll('.guide-tab-content').forEach(el => el.style.display = 'none');
    
    // ریست کردن همه دکمه‌ها
    document.querySelectorAll('.guide-tab-btn').forEach(btn => {
        btn.className = 'guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl';
    });
    
    // نمایش تب انتخاب شده
    const content = document.getElementById('gtab-' + tab);
    const btn = document.getElementById('gbtn-' + tab);
    
    if (content) content.style.display = 'block';
    if (btn) {
        btn.className = 'guide-tab-btn flex-shrink-0 px-4 py-2.5 border-b-2 border-indigo-600 text-indigo-600 bg-white font-black text-xs sm:text-sm whitespace-nowrap transition rounded-t-xl shadow-sm';
        // اسکرول به دکمه فعال
        setTimeout(() => {
            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }, 100);
    }
}

// ===== اسکرول تب‌ها =====
function scrollCreateGuideTabs(direction) {
    const container = document.getElementById('create-guide-tabs-container');
    if (container) {
        container.scrollBy({ left: direction * 150, behavior: 'smooth' });
    }
}

// ===== بستن با کلیک بیرون =====
document.addEventListener('click', function(e) {
    const modal = document.getElementById('game-guide-modal');
    if (modal && e.target === modal) {
        closeCreateGameGuide();
    }
});

// ===== بستن با Escape =====
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateGameGuide();
    }
});
</script>