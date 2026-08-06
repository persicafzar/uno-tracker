<?php

namespace Application\Services;

class SmsService
{
    private array $config;
    private string $username;
    private string $password;
    
    public function __construct()
    {
        $this->config = require dirname(__DIR__, 3) . '/config/melipayamak.php';
        $this->username = $this->config['username'];
        $this->password = $this->config['password'];
    }
    
    /**
     * ارسال OTP (کد تایید)
     */
    public function sendOtp(string $phone, string $code): array
    {
        // حالت تست - بدون ارسال واقعی
        if ($this->config['test_mode']) {
            log_message("🧪 [SMS TEST MODE] Code {$code} would be sent to {$phone}");
            return [
                'success' => true,
                'message' => 'پیامک در حالت تست ارسال شد',
                'test_mode' => true,
                'code' => $code,
            ];
        }
        
        // اگر پترن داریم، از BaseNumber استفاده می‌کنیم (سریع‌تر)
        if ($this->config['otp']['use_base_number'] && !empty($this->config['otp']['pattern_code'])) {
            return $this->sendOtpViaPattern($phone, $code);
        }
        
        // در غیر این صورت، ارسال عادی
        return $this->sendOtpViaSimpleSms($phone, $code);
    }
    
    /**
     * ارسال OTP با پترن (BaseNumber) - REST API
     * ✅ اصلاح نهایی بر اساس مستندات REST
     */
    private function sendOtpViaPattern(string $phone, string $code): array
    {
        // فقط یک متغیر داریم، پس مستقیماً مقدار را به text می‌دهیم
        $text = $code;
        
        // اگر چند متغیر داشتید، با ; جدا کنید:
        // $text = $code . ';' . $otherValue;
        
        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'to' => $phone,
            'bodyId' => $this->config['otp']['pattern_code'],
            'text' => $text,
        ];
        
        $url = $this->config['api']['send_by_base'];
        return $this->sendRequest($url, $data);
    }
    
    /**
     * ارسال OTP با پیامک ساده (از خط عمومی)
     */
    private function sendOtpViaSimpleSms(string $phone, string $code): array
    {
        $message = "کد تایید شما در UNO Tracker:\n\n{$code}\n\nاین کد را در اختیار دیگران قرار ندهید.";
        
        $data = [
            'UserName' => $this->username,
            'PassWord' => $this->password,
            'To' => $phone,
            'From' => $this->config['from_public'],
            'Text' => $message,
            'IsFlash' => false,
        ];
        
        $url = $this->config['api']['send_sms'];
        return $this->sendRequest($url, $data);
    }
    
    /**
     * بررسی اعتبار خط (آیا در بلک‌لیست است؟)
     */
    public function isBlackList(string $phone): bool
    {
        if ($this->config['test_mode']) {
            return false;
        }
        
        $data = [
            'UserName' => $this->username,
            'PassWord' => $this->password,
            'MobileNumber' => $phone,
        ];
        
        $result = $this->sendRequest($this->config['api']['is_black_list'], $data);
        
        return $result['success'] && ($result['value'] ?? '') === 'true';
    }
    
    /**
     * گرفتن موجودی حساب
     */
    public function getCredit(): float
    {
        if ($this->config['test_mode']) {
            return 999999.0;
        }
        
        $data = [
            'UserName' => $this->username,
            'PassWord' => $this->password,
        ];
        
        $result = $this->sendRequest($this->config['api']['get_credit'], $data);
        
        return $result['success'] ? (float)($result['value'] ?? 0) : 0;
    }
    
    /**
     * ارسال درخواست HTTP به REST API
     */
    private function sendRequest(string $url, array $data): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->config['timeout'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            log_message("❌ [SMS] CURL Error: {$error}");
            return [
                'success' => false,
                'error' => "خطا در ارتباط با سرور پیامک: {$error}",
            ];
        }
        
        // لاگ در حالت debug
        if ($this->config['debug']) {
            log_message("📱 [SMS] URL: {$url}");
            log_message("📱 [SMS] Response: {$response}");
        }
        
        // ملی پیامک معمولاً JSON برمی‌گرداند
        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // ممکن است پاسخ عددی ساده باشد (مثل Value=1)
            if (is_numeric($response) && (int)$response > 0) {
                return [
                    'success' => true,
                    'value' => $response,
                    'rec_id' => $response,
                ];
            }
            
            return [
                'success' => false,
                'error' => 'پاسخ نامعتبر از سرور پیامک',
                'raw_response' => $response,
            ];
        }
        
        // بررسی کدهای موفقیت بر اساس مستندات REST
        $value = $decoded['Value'] ?? $decoded['value'] ?? null;
        $retStatus = $decoded['RetStatus'] ?? null;
        $strRetStatus = $decoded['StrRetStatus'] ?? null;
        
        // موفقیت: RetStatus = 1 یا Value یک عدد بزرگتر از 15 رقم
        if ($retStatus == 1) {
            return [
                'success' => true,
                'rec_id' => $value,
                'raw' => $decoded,
            ];
        }
        
        if (is_numeric($value) && (int)$value > 100) {
            return [
                'success' => true,
                'rec_id' => $value,
                'raw' => $decoded,
            ];
        }
        
        // ترجمه کدهای خطا (بر اساس مستندات)
        $errorCodes = [
            '-111' => 'IP درخواست کننده نامعتبر است',
            '-110' => 'الزام استفاده از ApiKey به جای رمز عبور',
            '-109' => 'الزام تنظیم IP مجاز برای استفاده از API',
            '-108' => 'مسدود شدن IP به دلیل تلاش ناموفق استفاده از API',
            '-10' => 'ممنوعیت ارسال لینک در متغیرها',
            '-6' => 'خطای داخلی رخ داده است، با پشتیبانی تماس بگیرید',
            '-5' => 'متن ارسالی با متغیرهای مشخص شده در متن پیشفرض همخوانی ندارد',
            '-4' => 'کد متن ارسالی صحیح نمی باشد یا تایید نشده است',
            '-3' => 'خط ارسالی در سیستم تعریف نشده است',
            '-2' => 'محدودیت تعداد شماره (هر بار ۱ شماره)',
            '-1' => 'دسترسی به وبسرویس غیرفعال است',
            '0' => 'نام کاربری یا رمز عبور صحیح نمی باشد',
            '2' => 'اعتبار کافی نمی باشد',
            '6' => 'سامانه در حال بروزرسانی می باشد',
            '7' => 'متن حاوی کلمه فیلتر شده می باشد',
            '10' => 'کاربر مورد نظر فعال نمی باشد',
            '11' => 'ارسال نشده',
            '12' => 'مدارک کاربر کامل نمی باشد',
            '18' => 'شماره موبایل معتبر نمی باشد',
            '19' => 'سقف محدودیت روزانه ارسال از وبسرویس',
        ];
        
        $errorValue = is_numeric($value) ? (int)$value : 0;
        $errorKey = (string)$errorValue;
        $errorMessage = $errorCodes[$errorKey] ?? ($strRetStatus ?? 'خطای ناشناخته');
        
        log_message("❌ [SMS] Error Code {$errorValue}: {$errorMessage}");
        
        return [
            'success' => false,
            'error' => "خطا در ارسال پیامک: {$errorMessage}",
            'error_code' => $errorValue,
            'raw' => $decoded,
        ];
    }
    
    /**
     * بررسی فعال بودن سرویس
     */
    public function isEnabled(): bool
    {
        try {
            $settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
            return (bool)$settingsRepo->get('sms_enabled', false);
        } catch (\Throwable $e) {
            return false;
        }
    }
}