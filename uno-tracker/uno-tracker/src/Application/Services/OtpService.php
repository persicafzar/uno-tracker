<?php

namespace Application\Services;

use Core\Database;
use Infrastructure\Repositories\SettingsRepository;

class OtpService
{
    private Database $db;
    private SettingsRepository $settings;
    private SmsService $smsService;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->settings = SettingsRepository::getInstance();
        $this->smsService = new SmsService();
    }
    
    /**
     * تولید و ارسال کد OTP
     */
    public function sendOtp(string $phone, string $purpose = 'login'): array
    {
        $phone = trim($phone);
        
        // اعتبارسنجی شماره
        if (!preg_match('/^09[0-9]{9}$/', $phone)) {
            return [
                'success' => false,
                'error' => 'شماره تماس نامعتبر است',
            ];
        }
        
        // بررسی محدودیت روزانه
        $dailyLimit = (int)$this->settings->get('sms_daily_limit', 10);
        $todayCount = $this->getTodayOtpCount($phone);
        
        if ($todayCount >= $dailyLimit) {
            return [
                'success' => false,
                'error' => "شما امروز {$dailyLimit} پیامک دریافت کرده‌اید. لطفاً فردا دوباره تلاش کنید.",
            ];
        }
        
        // جلوگیری از اسپم: حداقل ۶۰ ثانیه بین هر ارسال
        if ($this->hasRecentOtp($phone, $purpose)) {
            return [
                'success' => false,
                'error' => 'لطفاً ۶۰ ثانیه صبر کنید و دوباره تلاش کنید.',
            ];
        }
        
        // تولید کد
        $length = (int)$this->settings->get('sms_otp_length', 6);
        $code = $this->generateCode($length);
        
        // زمان انقضا
        $expiryMinutes = (int)$this->settings->get('sms_otp_expiry', 5);
        $expiresAt = date('Y-m-d H:i:s', time() + ($expiryMinutes * 60));
        
        // غیرفعال کردن کدهای قبلی
        $this->db->query(
            "UPDATE otp_codes SET used = 1 WHERE phone = ? AND purpose = ? AND used = 0",
            [$phone, $purpose]
        );
        
        // ذخیره کد جدید
        $this->db->insert('otp_codes', [
            'phone' => $phone,
            'code' => $code,
            'purpose' => $purpose,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'expires_at' => $expiresAt,
        ]);
        
        // ارسال پیامک
        $smsResult = $this->smsService->sendOtp($phone, $code);
        
        if (!$smsResult['success']) {
            log_message("❌ [OTP] SMS send failed for {$phone}: " . ($smsResult['error'] ?? 'Unknown'));
            return [
                'success' => false,
                'error' => $smsResult['error'] ?? 'خطا در ارسال پیامک',
            ];
        }
        
        return [
            'success' => true,
            'message' => "کد تایید به شماره {$phone} ارسال شد",
            'expires_in' => $expiryMinutes * 60,
            'test_code' => (isset($smsResult['test_mode']) && $smsResult['test_mode']) ? $code : null, // ✅ اصلاح شده
        ];
    }
    
    /**
     * اعتبارسنجی کد OTP
     */
    public function verifyOtp(string $phone, string $code, string $purpose = 'login'): array
    {
        $phone = trim($phone);
        $code = trim($code);
        
        if (empty($code)) {
            return ['success' => false, 'error' => 'کد تایید را وارد کنید'];
        }
        
        // پیدا کردن آخرین کد معتبر
        $otp = $this->db->fetchOne(
            "SELECT * FROM otp_codes 
             WHERE phone = ? AND purpose = ? AND used = 0 AND expires_at > NOW()
             ORDER BY created_at DESC 
             LIMIT 1",
            [$phone, $purpose]
        );
        
        if (!$otp) {
            return [
                'success' => false,
                'error' => 'کد تایید نامعتبر یا منقضی شده است. لطفاً کد جدید درخواست کنید.',
            ];
        }
        
        // بررسی تعداد تلاش‌ها
        $maxAttempts = (int)$this->settings->get('sms_otp_attempt_limit', 5);
        if ($otp['attempts'] >= $maxAttempts) {
            $this->db->update('otp_codes', ['used' => 1], 'id = ?', [$otp['id']]);
            return [
                'success' => false,
                'error' => "تعداد تلاش‌های شما بیش از حد مجاز است. لطفاً کد جدید درخواست کنید.",
            ];
        }
        
        // افزایش تعداد تلاش
        $this->db->query(
            "UPDATE otp_codes SET attempts = attempts + 1 WHERE id = ?",
            [$otp['id']]
        );
        
        // بررسی کد
        if ($otp['code'] !== $code) {
            $remaining = $maxAttempts - $otp['attempts'] - 1;
            return [
                'success' => false,
                'error' => "کد اشتباه است. {$remaining} تلاش باقی مانده.",
            ];
        }
        
        // علامت‌گذاری به عنوان استفاده شده
        $this->db->update('otp_codes', ['used' => 1], 'id = ?', [$otp['id']]);
        
        return ['success' => true, 'message' => 'کد تایید صحیح است'];
    }
    
    /**
     * تولید کد تصادفی
     */
    private function generateCode(int $length = 6): string
    {
        $min = (int)pow(10, $length - 1);
        $max = (int)pow(10, $length) - 1;
        return (string)random_int($min, $max);
    }
    
    /**
     * تعداد OTP های امروز
     */
    private function getTodayOtpCount(string $phone): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM otp_codes 
             WHERE phone = ? AND DATE(created_at) = CURDATE()",
            [$phone]
        );
        return (int)($result['count'] ?? 0);
    }
    
    /**
     * آیا OTP اخیر (کمتر از ۶۰ ثانیه) دارد؟
     */
    private function hasRecentOtp(string $phone, string $purpose): bool
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM otp_codes 
             WHERE phone = ? AND purpose = ? AND created_at > DATE_SUB(NOW(), INTERVAL 60 SECOND)",
            [$phone, $purpose]
        );
        return (int)($result['count'] ?? 0) > 0;
    }
    
    /**
     * پاکسازی کدهای منقضی شده (cron job)
     */
    public function cleanupExpired(): int
    {
        $stmt = $this->db->query(
            "DELETE FROM otp_codes WHERE expires_at < NOW() OR used = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)"
        );
        return $stmt ? $stmt->rowCount() : 0;
    }
}