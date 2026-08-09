<?php
namespace Presentation\Middleware;

use Core\Request;
use Core\Response;
use Application\Services\AuthService;

class AuthMiddleware
{
    private AuthService $auth;
    private Response $response;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->response = new Response();
    }

    public function handle(Request $request): bool
    {
        // ۱. بررسی لاگین بودن
        if (!$this->auth->check()) {
            $request->flash('error', 'برای دسترسی به این بخش باید وارد حساب کاربری خود شوید.');
            $this->response->redirect('/login');
            return false;
        }

        $user = $this->auth->user();

        // 🆕 ۲. بررسی مسدود بودن کاربر (مهم‌ترین بخش)
        if ($user && $user['status'] === 'banned') {
            // نابود کردن کامل جلسه کاربر مسدود شده
            $this->auth->logout(); 
            
            // ثبت پیام خطا برای نمایش در صفحه لاگین
            $request->flash('error', 'حساب کاربری شما توسط مدیر مسدود شده است. لطفاً با پشتیبانی تماس بگیرید.');
            
            // ارسال ریدایرکت استاندارد (HTMX به طور خودکار این را دنبال می‌کند)
            $this->response->redirect('/login');
            return false;
        }

        // ۳. بررسی اعتبار Session (انقضا)
        if (!$this->auth->validateSession()) {
            $this->auth->logout();
            $request->flash('error', 'نشست شما به پایان رسیده است. لطفاً دوباره وارد شوید.');
            $this->response->redirect('/login');
            return false;
        }

        return true;
    }
}