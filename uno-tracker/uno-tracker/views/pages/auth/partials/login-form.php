<?php $smsEnabled = $smsAuthEnabled ?? false; ?>

<div class="space-y-4 sm:space-y-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">
        ورود به حساب کاربری
        <?php if ($smsEnabled): ?>
            <span class="block text-xs sm:text-sm text-indigo-600 font-medium mt-1">📱 ورود با کد پیامکی</span>
        <?php endif; ?>
    </h2>

    <?php if (!empty($errors) && is_array($errors)): ?>
        <div class="bg-red-50 border-2 border-red-300 text-red-700 px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl mb-4 text-sm sm:text-base">
            <ul class="list-disc list-inside space-y-1">
                <?php foreach ($errors as $field => $message): ?>
                    <li><?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login" 
          hx-post="/login" 
          hx-target="#login-form-container" 
          hx-swap="innerHTML"
          class="space-y-4">
        
        <div>
            <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">شماره تماس</label>
            <input type="tel" name="phone" id="login-phone"
                   value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                   required placeholder="09123456789" pattern="09[0-9]{9}" maxlength="11"
                   class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 text-sm sm:text-base text-left dir-ltr">
            <?php if (!empty($errors['phone'])): ?>
                <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['phone']) ?></p>
            <?php endif; ?>
        </div>

        <?php if ($smsEnabled): ?>
            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">کد تایید پیامکی</label>
                <div class="flex gap-2">
                    <input type="text" name="otp_code" id="login-otp" maxlength="6" inputmode="numeric"
                           placeholder="کد ۶ رقمی"
                           class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-800 focus:outline-none focus:border-indigo-500 text-sm sm:text-base text-center tracking-widest font-mono">
                    <button type="button" id="send-otp-btn-login" onclick="sendOtp('login')"
                            class="px-4 py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-xl transition-all text-sm sm:text-base whitespace-nowrap">
                        ارسال کد
                    </button>
                </div>
                <p class="text-gray-500 text-xs mt-2" id="otp-timer-login">ابتدا شماره را وارد و روی "ارسال کد" کلیک کنید</p>
                <?php if (!empty($errors['otp'])): ?>
                    <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['otp']) ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div>
                <label class="block text-gray-700 mb-2 font-medium text-sm sm:text-base">رمز عبور</label>
                <input type="password" name="password" required
                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 text-sm sm:text-base">
                <?php if (!empty($errors['password'])): ?>
                    <p class="text-red-600 text-xs sm:text-sm mt-2"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

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
                class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold py-2.5 sm:py-3 rounded-xl transition-all text-sm sm:text-base">
            ورود
        </button>

        <p class="text-center text-gray-600 text-sm sm:text-base mt-4">
            حساب کاربری ندارید؟
            <a href="/register" class="text-indigo-600 hover:text-indigo-700 font-semibold transition">ثبت‌نام کنید</a>
        </p>
    </form>
</div>