<div id="login-form-container">
    <?php
    $smsEnabled = $smsAuthEnabled ?? false;
    ?>
    <div class="space-y-4 sm:space-y-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">
            ورود به حساب کاربری
            <?php if ($smsEnabled): ?>
                <span class="block text-xs sm:text-sm text-indigo-600 font-medium mt-1">📱 ورود با کد پیامکی</span>
            <?php endif; ?>
        </h2>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50/80 border-2 border-red-200 text-red-700 px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl mb-4 flex items-center text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login"
            hx-post="/login"
            hx-target="#login-form-container"
            hx-swap="innerHTML"
            class="space-y-4"
            id="login-form">

            <!-- Phone Field -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">شماره تماس</label>
                <input type="tel"
                    name="phone"
                    id="login-phone"
                    value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                    required
                    placeholder="09123456789"
                    pattern="09[0-9]{9}"
                    maxlength="11"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all duration-300 text-sm sm:text-base text-left dir-ltr">
                <?php if (!empty($errors['phone'])): ?>
                    <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['phone']) ?></p>
                <?php endif; ?>
            </div>

            <?php if ($smsEnabled): ?>
                <!-- 🆕 OTP Section -->
                <div>
                    <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">کد تایید پیامکی</label>
                    <div class="flex gap-2">
                        <input type="text"
                            name="otp_code"
                            id="login-otp"
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder="کد ۶ رقمی"
                            class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all duration-300 text-sm sm:text-base text-center tracking-widest font-mono"
                            autocomplete="one-time-code">
                        <button type="button"
                            id="send-otp-btn-login"
                            onclick="sendOtp('login')"
                            class="px-4 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl transition-all duration-300 text-sm sm:text-base whitespace-nowrap">
                            ارسال کد
                        </button>
                    </div>
                    <p class="text-gray-500 text-xs mt-2" id="otp-timer-login">ابتدا شماره تماس را وارد کرده و روی "ارسال کد" کلیک کنید</p>
                    <?php if (!empty($errors['otp'])): ?>
                        <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['otp']) ?></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Password Field (کلاسیک) -->
                <div>
                    <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">رمز عبور</label>
                    <input type="password"
                        name="password"
                        required
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all duration-300 text-sm sm:text-base">
                    <?php if (!empty($errors['password'])): ?>
                        <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Captcha -->
                <div>
                    <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">کد امنیتی</label>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                        <img src="/captcha"
                            alt="Captcha"
                            id="captcha-image"
                            class="h-12 rounded-lg border-2 border-gray-200 cursor-pointer hover:border-indigo-500 transition self-center sm:self-auto"
                            onclick="this.src='/captcha?' + Math.random()"
                            title="کلیک برای تغییر کد">
                        <input type="text"
                            name="captcha"
                            required
                            maxlength="5"
                            placeholder="کد را وارد کنید"
                            class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all duration-300 text-sm sm:text-base text-center">
                    </div>
                    <?php if (!empty($errors['captcha'])): ?>
                        <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['captcha']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold py-2.5 sm:py-3 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg text-sm sm:text-base">
                ورود
            </button>

            <p class="text-center text-gray-600 text-sm sm:text-base mt-4">
                حساب کاربری ندارید؟
                <a href="/register" class="text-indigo-600 hover:text-indigo-700 font-semibold transition">ثبت‌نام کنید</a>
            </p>
        </form>
    </div>
</div>

<script>
    <?php if ($smsEnabled): ?>
        let otpTimerLogin = null;
        let otpCooldownLogin = 0;

        async function sendOtp(purpose = 'login') {
            const phone = document.getElementById('login-phone').value.trim();

            if (!/^09[0-9]{9}$/.test(phone)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'شماره نامعتبر',
                    text: 'لطفاً شماره تماس معتبر (11 رقمی با 09) وارد کنید',
                    confirmButtonColor: '#6366f1'
                });
                return;
            }

            if (otpCooldownLogin > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'لطفاً صبر کنید',
                    text: `${otpCooldownLogin} ثانیه تا ارسال مجدد`,
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            const btn = document.getElementById('send-otp-btn-login');
            btn.disabled = true;
            btn.innerHTML = '⏳ در حال ارسال...';

            try {
                const response = await fetch('/auth/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `phone=${encodeURIComponent(phone)}&purpose=${purpose}`
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'کد ارسال شد',
                        html: `کد تایید به شماره <strong dir="ltr">${phone}</strong> ارسال شد` +
                            (result.test_code ? `<br><br>🧪 <strong>کد تست:</strong> <code>${result.test_code}</code>` : ''),
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // شروع تایمر
                    otpCooldownLogin = 60;
                    startTimerLogin();

                    document.getElementById('login-otp').focus();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا',
                        text: result.error || 'خطا در ارسال کد',
                        confirmButtonColor: '#dc2626'
                    });
                    btn.disabled = false;
                    btn.innerHTML = 'ارسال کد';
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطای ارتباط',
                    text: 'مشکل در ارتباط با سرور',
                    confirmButtonColor: '#dc2626'
                });
                btn.disabled = false;
                btn.innerHTML = 'ارسال کد';
            }
        }

        function startTimerLogin() {
            const btn = document.getElementById('send-otp-btn-login');
            const timerText = document.getElementById('otp-timer-login');

            if (otpTimerLogin) clearInterval(otpTimerLogin);

            otpTimerLogin = setInterval(() => {
                otpCooldownLogin--;
                timerText.innerHTML = `⏱️ ارسال مجدد کد تا <strong>${otpCooldownLogin}</strong> ثانیه دیگر`;

                if (otpCooldownLogin <= 0) {
                    clearInterval(otpTimerLogin);
                    btn.disabled = false;
                    btn.innerHTML = 'ارسال مجدد کد';
                    timerText.innerHTML = '✅ می‌توانید کد جدید درخواست کنید';
                }
            }, 1000);
        }
    <?php endif; ?>
</script>