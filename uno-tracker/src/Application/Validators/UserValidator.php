<?php

namespace Application\Validators;

use Infrastructure\Repositories\SettingsRepository;

class UserValidator
{
    private array $errors = [];
    
    private function isSmsAuthEnabled(): bool
    {
        try {
            $settings = SettingsRepository::getInstance();
            return $settings->get('auth_method', 'password') === 'sms';
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function validateRegister(array $data): bool
    {
        $this->errors = [];
        $smsEnabled = $this->isSmsAuthEnabled();

        // نام واقعی
        if (empty(trim($data['real_name'] ?? ''))) {
            $this->errors['real_name'] = 'نام واقعی الزامی است';
        } elseif (mb_strlen(trim($data['real_name'])) < 2) {
            $this->errors['real_name'] = 'نام واقعی باید حداقل ۲ کاراکتر باشد';
        } elseif (mb_strlen(trim($data['real_name'])) > 100) {
            $this->errors['real_name'] = 'نام واقعی نباید بیشتر از ۱۰۰ کاراکتر باشد';
        }

        // نام مستعار (همیشه لازم است)
        $nickname = trim($data['nickname'] ?? '');
        if (empty($nickname)) {
            $this->errors['nickname'] = 'نام مستعار الزامی است';
        } elseif (mb_strlen($nickname) < 2) {
            $this->errors['nickname'] = 'نام مستعار باید حداقل ۲ کاراکتر باشد';
        } elseif (mb_strlen($nickname) > 50) {
            $this->errors['nickname'] = 'نام مستعار نباید بیشتر از ۵۰ کاراکتر باشد';
        } elseif (!preg_match('/^[\p{L}\p{N}_ \-\.]+$/u', $nickname)) {
            $this->errors['nickname'] = 'نام مستعار فقط می‌تواند شامل حروف (فارسی/انگلیسی)، اعداد، _، فاصله، - و . باشد';
        }

        // شماره تماس
        $phone = trim($data['phone'] ?? '');
        if (empty($phone)) {
            $this->errors['phone'] = 'شماره تماس الزامی است';
        } elseif (!preg_match('/^09[0-9]{9}$/', $phone)) {
            $this->errors['phone'] = 'شماره تماس باید ۱۱ رقم و با ۰۹ شروع شود';
        }

        // رمز عبور (همیشه لازم است - برای fallback)
        if (empty($data['password'])) {
            $this->errors['password'] = 'رمز عبور الزامی است';
        } elseif (mb_strlen($data['password']) < 6) {
            $this->errors['password'] = 'رمز عبور باید حداقل ۶ کاراکتر باشد';
        }

        if (empty($data['password_confirmation'])) {
            $this->errors['password_confirmation'] = 'تایید رمز عبور الزامی است';
        } elseif (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) {
            $this->errors['password_confirmation'] = 'رمز عبور و تایید آن مطابقت ندارند';
        }

        // 🆕 OTP یا Captcha
        if ($smsEnabled) {
            if (empty($data['otp_code'])) {
                $this->errors['otp'] = 'کد تایید پیامکی الزامی است';
            } elseif (!preg_match('/^[0-9]{4,8}$/', $data['otp_code'])) {
                $this->errors['otp'] = 'کد تایید نامعتبر است';
            }
        } else {
            if (empty($data['captcha'])) {
                $this->errors['captcha'] = 'کد امنیتی الزامی است';
            } elseif (!isset($_SESSION['captcha_code']) || 
                     strtolower($data['captcha']) !== strtolower($_SESSION['captcha_code'])) {
                $this->errors['captcha'] = 'کد امنیتی اشتباه است';
            }
        }

        return empty($this->errors);
    }

    public function validateLogin(array $data): bool
    {
        $this->errors = [];
        $smsEnabled = $this->isSmsAuthEnabled();

        $phone = trim($data['phone'] ?? '');
        if (empty($phone)) {
            $this->errors['phone'] = 'شماره تماس الزامی است';
        } elseif (!preg_match('/^09[0-9]{9}$/', $phone)) {
            $this->errors['phone'] = 'شماره تماس نامعتبر است';
        }

        if ($smsEnabled) {
            // در حالت SMS، OTP لازم است
            if (empty($data['otp_code'])) {
                $this->errors['otp'] = 'کد تایید پیامکی الزامی است';
            } elseif (!preg_match('/^[0-9]{4,8}$/', $data['otp_code'])) {
                $this->errors['otp'] = 'کد تایید نامعتبر است';
            }
        } else {
            // در حالت password
            if (empty($data['password'])) {
                $this->errors['password'] = 'رمز عبور الزامی است';
            }

            if (empty($data['captcha'])) {
                $this->errors['captcha'] = 'کد امنیتی الزامی است';
            } elseif (!isset($_SESSION['captcha_code']) || 
                     strtolower($data['captcha']) !== strtolower($_SESSION['captcha_code'])) {
                $this->errors['captcha'] = 'کد امنیتی اشتباه است';
            }
        }

        return empty($this->errors);
    }

    public function validateProfileUpdate(array $data): bool
    {
        $this->errors = [];

        if (!empty($data['real_name']) && mb_strlen(trim($data['real_name'])) > 100) {
            $this->errors['real_name'] = 'نام واقعی نباید بیشتر از ۱۰۰ کاراکتر باشد';
        }

        if (!empty($data['nickname'])) {
            $nickname = trim($data['nickname']);
            if (mb_strlen($nickname) < 2) {
                $this->errors['nickname'] = 'نام مستعار باید حداقل ۲ کاراکتر باشد';
            } elseif (mb_strlen($nickname) > 50) {
                $this->errors['nickname'] = 'نام مستعار نباید بیشتر از ۵۰ کاراکتر باشد';
            } elseif (!preg_match('/^[\p{L}\p{N}_ \-\.]+$/u', $nickname)) {
                $this->errors['nickname'] = 'نام مستعار فقط می‌تواند شامل حروف (فارسی/انگلیسی)، اعداد، _، فاصله، - و . باشد';
            }
        }

        if (!empty($data['tagline']) && mb_strlen($data['tagline']) > 200) {
            $this->errors['tagline'] = 'شعار نباید بیشتر از ۲۰۰ کاراکتر باشد';
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
}