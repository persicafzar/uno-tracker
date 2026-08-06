<?php $smsEnabled = $smsAuthEnabled ?? false; ?>

<div id="register-form-container">
    <div class="space-y-4 sm:space-y-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">
            ایجاد حساب کاربری
            <?php if ($smsEnabled): ?>
                <span class="block text-xs sm:text-sm text-indigo-600 font-medium mt-1">📱 ثبت‌نام با تایید پیامکی</span>
            <?php endif; ?>
        </h2>

        <form method="POST" action="/register"
            hx-post="/register"
            hx-target="#register-form-container"
            hx-swap="innerHTML"
            class="space-y-4"
            id="register-form">

            <!-- Real Name -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">نام واقعی</label>
                <input type="text" name="real_name"
                    value="<?= htmlspecialchars($old['real_name'] ?? '') ?>"
                    required
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 text-sm sm:text-base">
                <?php if (!empty($errors['real_name'])): ?>
                    <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['real_name']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Nickname (همیشه لازم است) -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">
                    نام مستعار
                </label>
                <input type="text" name="nickname"
                    value="<?= htmlspecialchars($old['nickname'] ?? '') ?>"
                    required
                    title="حروف فارسی/انگلیسی، اعداد، _، فاصله، - و . مجاز است"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 text-sm sm:text-base">
                <?php if (!empty($errors['nickname'])): ?>
                    <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['nickname']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-gray-700  font-medium text-sm sm:text-base">شماره تماس</label>
                <span class="text-xs text-gray-500  font-normal">(برای ورود در آینده)</span>

                <input type="tel" name="phone" id="register-phone"
                    value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                    required placeholder="09123456789" pattern="09[0-9]{9}" maxlength="11"
                    class="mt-2 w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 text-sm sm:text-base text-left dir-ltr">
                <?php if (!empty($errors['phone'])): ?>
                    <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['phone']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Tagline -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">شعار (اختیاری)</label>
                <input type="text" name="tagline"
                    value="<?= htmlspecialchars($old['tagline'] ?? '') ?>"
                    maxlength="200" placeholder="مثال: من بهترین بازیکن UNO هستم!"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 text-sm sm:text-base">
            </div>

            <!-- Password (همیشه لازم است برای fallback) -->
            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">
                    رمز عبور
                    <span class="text-xs text-gray-500 font-normal">(برای ورود بدون پیامک)</span>
                </label>
                <input type="password" name="password" required minlength="6"
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 text-sm sm:text-base">
                <?php if (!empty($errors['password'])): ?>
                    <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">تایید رمز عبور</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 text-sm sm:text-base">
                <?php if (!empty($errors['password_confirmation'])): ?>
                    <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['password_confirmation']) ?></p>
                <?php endif; ?>
            </div>

            <?php if ($smsEnabled): ?>
                <!-- OTP Section -->
                <div>
                    <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">کد تایید پیامکی</label>
                    <div class="flex gap-2">
                        <input type="text" name="otp_code" id="register-otp" maxlength="6" inputmode="numeric"
                            placeholder="کد ۶ رقمی"
                            class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 text-sm sm:text-base text-center tracking-widest font-mono">
                        <button type="button" id="send-otp-btn-register" onclick="sendOtpRegister()"
                            class="px-4 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl text-sm sm:text-base whitespace-nowrap">
                            ارسال کد
                        </button>
                    </div>
                    <p class="text-gray-500 text-xs mt-2" id="otp-timer-register">ابتدا شماره تماس را وارد کرده و روی "ارسال کد" کلیک کنید</p>
                    <?php if (!empty($errors['otp'])): ?>
                        <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['otp']) ?></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Captcha -->
                <div>
                    <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">کد امنیتی</label>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <img src="/captcha" id="captcha-image" class="h-12 rounded-lg border-2 border-gray-200 cursor-pointer"
                            onclick="this.src='/captcha?' + Math.random()">
                        <input type="text" name="captcha" required maxlength="5" placeholder="کد را وارد کنید"
                            class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 text-center">
                    </div>
                    <?php if (!empty($errors['captcha'])): ?>
                        <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['captcha']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <button type="submit"
                class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold py-2.5 sm:py-3 rounded-xl transition-all transform hover:scale-105 shadow-md text-sm sm:text-base">
                ثبت‌نام
            </button>

            <p class="text-center text-gray-600 mt-4 text-sm sm:text-base">
                قبلاً ثبت‌نام کرده‌اید؟
                <a href="/login" class="text-indigo-600 hover:text-indigo-700 font-semibold transition">وارد شوید</a>
            </p>
        </form>
    </div>
</div>

<?php if ($smsEnabled): ?>
    <script>
        let otpTimerRegister = null;
        let otpCooldownRegister = 0;

        async function sendOtpRegister() {
            const phone = document.getElementById('register-phone').value.trim();

            if (!/^09[0-9]{9}$/.test(phone)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'شماره نامعتبر',
                    text: 'لطفاً شماره تماس معتبر (11 رقمی با 09) وارد کنید',
                    confirmButtonColor: '#6366f1'
                });
                return;
            }

            if (otpCooldownRegister > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'لطفاً صبر کنید',
                    text: `${otpCooldownRegister} ثانیه تا ارسال مجدد`,
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            const btn = document.getElementById('send-otp-btn-register');
            btn.disabled = true;
            btn.innerHTML = '⏳ در حال ارسال...';

            try {
                const response = await fetch('/auth/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `phone=${encodeURIComponent(phone)}&purpose=register`
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

                    otpCooldownRegister = 60;
                    startTimerRegister();
                    document.getElementById('register-otp').focus();
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

        function startTimerRegister() {
            const btn = document.getElementById('send-otp-btn-register');
            const timerText = document.getElementById('otp-timer-register');

            if (otpTimerRegister) clearInterval(otpTimerRegister);

            otpTimerRegister = setInterval(() => {
                otpCooldownRegister--;
                timerText.innerHTML = `⏱️ ارسال مجدد کد تا <strong>${otpCooldownRegister}</strong> ثانیه دیگر`;

                if (otpCooldownRegister <= 0) {
                    clearInterval(otpTimerRegister);
                    btn.disabled = false;
                    btn.innerHTML = 'ارسال مجدد کد';
                    timerText.innerHTML = '✅ می‌توانید کد جدید درخواست کنید';
                }
            }, 1000);
        }
    </script>
<?php endif; ?>