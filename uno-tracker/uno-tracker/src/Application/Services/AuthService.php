<?php

namespace Application\Services;

use Core\Database;
use Application\Validators\UserValidator;
use Infrastructure\Repositories\SettingsRepository;

class AuthService
{
    private Database $db;
    private UserValidator $validator;
    private OtpService $otpService;
    private SettingsRepository $settings;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->validator = new UserValidator();
        $this->otpService = new OtpService();
        $this->settings = SettingsRepository::getInstance();
    }

    /**
     * آیا احراز هویت پیامکی فعال است؟
     * ✅ اصلاح: هر دو شرط auth_method و sms_enabled بررسی می‌شوند
     */
    public function isSmsAuthEnabled(): bool
    {
        try {
            return $this->settings->get('auth_method', 'password') === 'sms'
                && (bool)$this->settings->get('sms_enabled', false);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * ثبت‌نام کاربر جدید
     */
    public function register(array $data): array
    {
        // 🛡️ بررسی IP
        $antiCheatService = new AntiCheatService();
        $ipCheck = $antiCheatService->checkIPForRegistration($_SERVER['REMOTE_ADDR'] ?? '');

        if (!$ipCheck['allowed']) {
            return [
                'success' => false,
                'errors' => [
                    'ip' => "شما حداکثر {$ipCheck['max_allowed']} اکانت ساخته‌اید."
                ]
            ];
        }

        // اعتبارسنجی
        if (!$this->validator->validateRegister($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        $data['nickname'] = trim($data['nickname']);
        $data['phone'] = trim($data['phone']);

        // بررسی تکراری نبودن
        $existingUser = $this->db->fetchOne(
            "SELECT id FROM users WHERE phone = ? OR nickname = ?",
            [$data['phone'], $data['nickname']]
        );

        if ($existingUser) {
            return [
                'success' => false,
                'errors' => [
                    'phone' => 'این شماره تماس یا نام مستعار قبلاً ثبت شده است'
                ]
            ];
        }

        // 🆕 اگر SMS فعال است، بررسی OTP
        if ($this->isSmsAuthEnabled()) {
            if (empty($data['otp_code'])) {
                return [
                    'success' => false,
                    'errors' => ['otp' => 'کد تایید پیامکی را وارد کنید']
                ];
            }

            $otpResult = $this->otpService->verifyOtp($data['phone'], $data['otp_code'], 'register');
            if (!$otpResult['success']) {
                return [
                    'success' => false,
                    'errors' => ['otp' => $otpResult['error']]
                ];
            }
        }

        // هش کردن رمز عبور (اگر ارائه شده)
        $passwordHash = !empty($data['password'])
            ? password_hash($data['password'], PASSWORD_BCRYPT)
            : null;

        try {
            $userId = $this->db->insert('users', [
                'real_name' => trim($data['real_name']),
                'nickname' => trim($data['nickname']),
                'phone' => trim($data['phone']),
                'password_hash' => $passwordHash,
                'role' => 'user',
                'status' => 'active',
                'tagline' => $data['tagline'] ?? null,
                // 🆕 مجوزهای پیش‌فرض - غیرفعال تا ادمین تایید کند
                'can_create_game' => 0,
                'can_join_game' => 1,  // یا 1 اگر می‌خواهید شرکت در بازی مجاز باشد
            ]);

            $user = $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
            $this->loginUser($user);

            $antiCheatService->recordUserIP($userId, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');

            $this->db->update('users', [
                'registration_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ], 'id = ?', [$userId]);

            return [
                'success' => true,
                'user_id' => $userId,
                'message' => 'ثبت‌نام با موفقیت انجام شد'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'errors' => ['general' => 'خطا در ثبت‌نام: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * لاگین کاربر - پشتیبانی از هر دو حالت
     * ✅ اصلاح: ثبت IP در هر دو حالت اضافه شد
     */
    public function login(array $data): array
    {
        // اعتبارسنجی
        if (!$this->validator->validateLogin($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        $phone = trim($data['phone']);

        $user = $this->db->fetchOne("SELECT * FROM users WHERE phone = ?", [$phone]);

        // 🆕 حالت SMS
        if ($this->isSmsAuthEnabled()) {
            // اگر کاربر وجود ندارد، خطا
            if (!$user) {
                return [
                    'success' => false,
                    'errors' => ['login' => 'کاربری با این شماره یافت نشد. ابتدا ثبت‌نام کنید.']
                ];
            }

            if ($user['status'] === 'banned') {
                return [
                    'success' => false,
                    'errors' => ['login' => 'این حساب کاربری مسدود شده است.']
                ];
            }

            if (empty($data['otp_code'])) {
                return [
                    'success' => false,
                    'errors' => ['otp' => 'کد تایید پیامکی را وارد کنید']
                ];
            }

            $otpResult = $this->otpService->verifyOtp($phone, $data['otp_code'], 'login');
            if (!$otpResult['success']) {
                return [
                    'success' => false,
                    'errors' => ['otp' => $otpResult['error']]
                ];
            }

            $this->loginUser($user);

            // ✅ ثبت IP کاربر
            $antiCheatService = new AntiCheatService();
            $antiCheatService->recordUserIP($user['id'], $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');

            return [
                'success' => true,
                'user' => $user,
                'message' => 'ورود با موفقیت انجام شد'
            ];
        }

        // 🔐 حالت رمز عبور (کلاسیک)
        if (!$user) {
            return [
                'success' => false,
                'errors' => ['login' => 'کاربری با این مشخصات یافت نشد']
            ];
        }

        if ($user['status'] === 'banned') {
            return [
                'success' => false,
                'errors' => ['login' => 'این حساب کاربری مسدود شده است.']
            ];
        }

        if (empty($user['password_hash'])) {
            return [
                'success' => false,
                'errors' => ['password' => 'این کاربر رمز عبور ندارد. لطفاً با مدیر تماس بگیرید.']
            ];
        }

        if (!password_verify($data['password'], $user['password_hash'])) {
            return [
                'success' => false,
                'errors' => ['password' => 'رمز عبور اشتباه است']
            ];
        }

        $this->loginUser($user);

        // ✅ ثبت IP کاربر
        $antiCheatService = new AntiCheatService();
        $antiCheatService->recordUserIP($user['id'], $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');

        return [
            'success' => true,
            'user' => $user,
            'message' => 'ورود با موفقیت انجام شد'
        ];
    }

    /**
     * 🆕 ارسال کد OTP (endpoint جدید)
     */
    public function sendOtp(string $phone, string $purpose = 'login'): array
    {
        // ✅ اینجا هم از isSmsAuthEnabled استفاده می‌شود که اکنون اصلاح شده
        if (!$this->isSmsAuthEnabled()) {
            return [
                'success' => false,
                'error' => 'سیستم پیامک فعال نیست'
            ];
        }

        $phone = trim($phone);

        if (!preg_match('/^09[0-9]{9}$/', $phone)) {
            return [
                'success' => false,
                'error' => 'شماره تماس نامعتبر است'
            ];
        }

        // برای login: کاربر باید وجود داشته باشد
        if ($purpose === 'login') {
            $user = $this->db->fetchOne("SELECT id FROM users WHERE phone = ?", [$phone]);
            if (!$user) {
                return [
                    'success' => false,
                    'error' => 'کاربری با این شماره یافت نشد. ابتدا ثبت‌نام کنید.'
                ];
            }
        }

        // برای register: کاربر نباید وجود داشته باشد
        if ($purpose === 'register') {
            $user = $this->db->fetchOne("SELECT id FROM users WHERE phone = ?", [$phone]);
            if ($user) {
                return [
                    'success' => false,
                    'error' => 'این شماره قبلاً ثبت شده است. وارد شوید.'
                ];
            }
        }

        return $this->otpService->sendOtp($phone, $purpose);
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $this->db->update('users', [
                'is_online' => 0,
                'last_seen_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$_SESSION['user_id']]);
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    public function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function user(): ?array
    {
        if (!$this->check()) return null;

        $user = $this->db->fetchOne(
            "SELECT * FROM users WHERE id = ?",
            [$_SESSION['user_id']]
        );

        if ($user && $user['status'] === 'banned') {
            $this->logout();
            return null;
        }

        return $user;
    }

    public function id(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION['user_id'] ?? null;
    }

    public function isSuperAdmin(): bool
    {
        $user = $this->user();
        return $user && $user['role'] === 'super_admin';
    }

    public function isAdmin(): bool
    {
        $user = $this->user();
        return $user && in_array($user['role'], ['admin', 'super_admin']);
    }

    private function loginUser(array $user): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        try {
            $sessionLifetime = $this->settings->get('session_lifetime', 30);
            $_SESSION['session_expires'] = time() + ($sessionLifetime * 60);
        } catch (\Throwable $e) {
            $_SESSION['session_expires'] = time() + (30 * 60);
        }

        $this->db->update('users', [
            'is_online' => 1,
            'last_seen_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$user['id']]);
    }

    public function isProtectedUser(int $userId): bool
    {
        $config = require dirname(__DIR__, 3) . '/config/app.php';
        return $userId === $config['super_admin_id'];
    }

    public function validateSession(): bool
    {
        if (!$this->check()) return false;

        try {
            $idleTimeout = $this->settings->get('session_idle_timeout', 15);
            if (isset($_SESSION['last_activity'])) {
                $idleTime = time() - $_SESSION['last_activity'];
                if ($idleTime > ($idleTimeout * 60)) {
                    $this->logout();
                    return false;
                }
            }

            $lifetime = $this->settings->get('session_lifetime', 30);
            if (isset($_SESSION['login_time'])) {
                $sessionAge = time() - $_SESSION['login_time'];
                if ($sessionAge > ($lifetime * 60)) {
                    $this->logout();
                    return false;
                }
            }

            $_SESSION['last_activity'] = time();
        } catch (\Throwable $e) {
            log_message("Session validation error: " . $e->getMessage());
        }

        return true;
    }

    public function getSessionTimeRemaining(): int
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['session_expires'])) return 0;
        return max(0, $_SESSION['session_expires'] - time());
    }
    /**
     * بررسی اینکه آیا کاربر فعلی مجوز ساخت بازی دارد
     */
    public function canCreateGame(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }
        return isset($user['can_create_game']) ? (bool)$user['can_create_game'] : true;
    }
}
