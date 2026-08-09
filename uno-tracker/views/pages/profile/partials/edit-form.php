<div class="max-w-4xl mx-auto">
    <div class="relative overflow-hidden bg-white rounded-2xl shadow-2xl border border-gray-200/70 p-4 sm:p-6">
        <!-- حلقه‌های تزئینی -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-indigo-500/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-violet-500/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        
        <div class="relative z-10">
            <h1 class="text-2xl sm:text-3xl font-black text-gray-800 mb-6 tracking-tight flex items-center gap-3">
                <span class="text-3xl sm:text-4xl drop-shadow-lg">✏️</span>
                ویرایش پروفایل
            </h1>

            <?php if (!empty($success)): ?>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 text-green-800 px-4 py-3.5 rounded-2xl mb-5 flex items-center text-sm sm:text-base shadow-md">
                    <svg class="w-6 h-6 ml-2 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-bold"><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-300 text-red-800 px-4 py-3.5 rounded-2xl mb-5 flex items-center text-sm sm:text-base shadow-md">
                    <svg class="w-6 h-6 ml-2 flex-shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-bold"><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- 🆕 فرم با autocomplete="off" و فیلد مخفی فریبنده -->
            <form id="profile-edit-form"
                method="POST"
                action="/profile/update"
                hx-post="/profile/update"
                hx-target="#profile-edit-container"
                hx-swap="innerHTML"
                autocomplete="off"
                class="space-y-6">

                <!-- 🆕 فیلد مخفی برای فریب auto-fill مرورگر -->
                <input type="text" name="fake_username" style="display:none" aria-hidden="true" autocomplete="off" tabindex="-1">
                <input type="password" name="fake_password" style="display:none" aria-hidden="true" autocomplete="off" tabindex="-1">

                <!-- اطلاعات شخصی -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100/80 rounded-2xl p-4 sm:p-6 border-2 border-gray-200 shadow-sm">
                    <h2 class="text-base sm:text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
                        <span class="text-2xl">👤</span>
                        اطلاعات شخصی
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 mb-2 font-bold text-sm sm:text-base">نام واقعی</label>
                            <input type="text"
                                name="real_name"
                                value="<?= htmlspecialchars($profile['real_name'] ?? '') ?>"
                                required
                                autocomplete="off"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-sm sm:text-base font-medium">
                            <?php if (!empty($errors['real_name'])): ?>
                                <p class="text-red-600 text-xs sm:text-sm mt-1 font-medium"><?= htmlspecialchars($errors['real_name']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2 font-bold text-sm sm:text-base">نام مستعار</label>
                            <input type="text"
                                name="nickname"
                                value="<?= htmlspecialchars($profile['nickname'] ?? '') ?>"
                                required
                                autocomplete="off"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-sm sm:text-base font-medium">
                            <?php if (!empty($errors['nickname'])): ?>
                                <p class="text-red-600 text-xs sm:text-sm mt-1 font-medium"><?= htmlspecialchars($errors['nickname']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2 font-bold text-sm sm:text-base">شعار (اختیاری)</label>
                            <input type="text"
                                name="tagline"
                                value="<?= htmlspecialchars($profile['tagline'] ?? '') ?>"
                                maxlength="200"
                                placeholder="مثال: من بهترین بازیکن UNO هستم!"
                                autocomplete="off"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-sm sm:text-base font-medium placeholder-gray-400">
                        </div>
                    </div>
                </div>

                <!-- آواتار -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100/80 rounded-2xl p-4 sm:p-6 border-2 border-gray-200 shadow-sm">
                    <h2 class="text-base sm:text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
                        <span class="text-2xl">🖼️</span>
                        تصویر پروفایل
                    </h2>

                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <?php if (!empty($profile['avatar_path'])): ?>
                            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-indigo-500 shadow-2xl overflow-hidden flex-shrink-0">
                                <img src="/storage/uploads/avatars/<?= htmlspecialchars($profile['avatar_path']) ?>"
                                    alt="آواتار"
                                    class="w-full h-full aspect-square rounded-full object-cover">
                            </div>
                        <?php else: ?>
                            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-4xl border-4 border-gray-300 shadow-md flex-shrink-0">
                                👤
                            </div>
                        <?php endif; ?>

                        <div class="flex-1 text-center sm:text-right">
                            <label class="inline-block cursor-pointer group">
                                <input type="file"
                                    name="avatar"
                                    accept="image/*"
                                    id="avatar-input"
                                    autocomplete="off"
                                    class="hidden">
                                <span class="inline-block px-5 sm:px-7 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white rounded-2xl transition-all duration-300 font-bold text-sm sm:text-base shadow-md hover:shadow-xl hover:scale-[1.02] group-hover:shadow-lg">
                                    📤 آپلود تصویر جدید
                                </span>
                            </label>
                            <p class="text-gray-500 text-xs sm:text-sm mt-2 font-medium">فرمت‌های مجاز: JPG, PNG, GIF, WEBP (حداکثر ۲ مگابایت)</p>

                            <div id="avatar-loading" class="hidden mt-2">
                                <div class="animate-spin inline-block w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
                                <span class="text-gray-600 text-xs mr-2 font-medium">در حال آپلود...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تغییر رمز عبور -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100/80 rounded-2xl p-4 sm:p-6 border-2 border-gray-200 shadow-sm">
                    <h2 class="text-base sm:text-lg font-black text-gray-800 mb-4 flex items-center gap-2.5 tracking-tight">
                        <span class="text-2xl">🔒</span>
                        تغییر رمز عبور
                    </h2>
                    <p class="text-gray-600 text-xs sm:text-sm font-medium mb-4">اگر می‌خواهید رمز عبور خود را تغییر دهید، فیلدهای زیر را پر کنید. در غیر این صورت، خالی بگذارید.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 mb-2 font-bold text-sm sm:text-base">رمز عبور فعلی</label>
                            <input type="password"
                                name="current_password"
                                autocomplete="off"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-sm sm:text-base font-medium">
                            <?php if (!empty($errors['current_password'])): ?>
                                <p class="text-red-600 text-xs sm:text-sm mt-1 font-medium"><?= htmlspecialchars($errors['current_password']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2 font-bold text-sm sm:text-base">رمز عبور جدید</label>
                            <input type="password"
                                name="new_password"
                                minlength="6"
                                autocomplete="new-password"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-sm sm:text-base font-medium">
                            <p class="text-gray-500 text-xs mt-1 font-medium">حداقل ۶ کاراکتر</p>
                            <?php if (!empty($errors['new_password'])): ?>
                                <p class="text-red-600 text-xs sm:text-sm mt-1 font-medium"><?= htmlspecialchars($errors['new_password']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2 font-bold text-sm sm:text-base">تکرار رمز عبور جدید</label>
                            <input type="password"
                                name="new_password_confirmation"
                                autocomplete="new-password"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 text-sm sm:text-base font-medium">
                            <?php if (!empty($errors['new_password_confirmation'])): ?>
                                <p class="text-red-600 text-xs sm:text-sm mt-1 font-medium"><?= htmlspecialchars($errors['new_password_confirmation']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- دکمه‌ها -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 hover:from-indigo-700 hover:via-violet-700 hover:to-purple-700 text-white font-black py-3.5 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-[1.02] text-sm sm:text-base flex items-center justify-center gap-2 group">
                        <span class="text-xl group-hover:rotate-12 transition-transform duration-300">💾</span>
                        ذخیره تغییرات
                    </button>
                    <a href="/profile"
                        class="flex-1 sm:flex-none text-center px-6 py-3.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-2xl transition-all duration-200 font-bold text-sm sm:text-base shadow-md hover:shadow-lg">
                        انصراف
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // آپلود آواتار با JavaScript خالص
    (function() {
        const avatarInput = document.getElementById('avatar-input');
        const avatarLoading = document.getElementById('avatar-loading');

        if (avatarInput) {
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا',
                        text: 'فرمت فایل نامعتبر است. فقط JPG, PNG, GIF و WEBP مجاز هستند.'
                    });
                    avatarInput.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا',
                        text: 'حجم فایل نباید بیشتر از ۲ مگابایت باشد'
                    });
                    avatarInput.value = '';
                    return;
                }

                if (avatarLoading) avatarLoading.classList.remove('hidden');

                const formData = new FormData();
                formData.append('avatar', file);

                fetch('/profile/avatar', {
                        method: 'POST',
                        headers: {
                            'HX-Request': 'true',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.text())
                    .then(html => {
                        const container = document.getElementById('profile-edit-container');
                        if (container) {
                            container.innerHTML = html;
                            if (typeof htmx !== 'undefined') {
                                htmx.process(container);
                            }
                        }
                        const newAvatarInput = document.getElementById('avatar-input');
                        if (newAvatarInput) {
                            newAvatarInput.value = '';
                            newAvatarInput.addEventListener('change', arguments.callee);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطا',
                            text: 'خطا در آپلود تصویر'
                        });
                        if (avatarLoading) avatarLoading.classList.add('hidden');
                    });
            });
        }
    })();
</script>