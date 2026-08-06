<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Application\Services\AuthService;

class AuthController
{
    private AuthService $auth;
    private Response $response;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->response = new Response();
    }

    public function showLogin(Request $request): void
    {
        if ($this->auth->check()) {
            $this->response->redirect('/dashboard');
            return;
        }

        $html = View::make('pages.auth.login', [
            'title' => 'ورود به حساب',
            'errors' => $request->getFlash('errors', []),
            'old' => $request->getFlash('old', []),
            'error' => $request->getFlash('error'),
            'smsAuthEnabled' => $this->auth->isSmsAuthEnabled(),
        ], 'auth');

        $this->response->html($html);
    }

    public function login(Request $request): void
    {
        $smsEnabled = $this->auth->isSmsAuthEnabled();

        $data = [
            'phone' => $request->post('phone'),
        ];

        if ($smsEnabled) {
            $data['otp_code'] = $request->post('otp_code');
        } else {
            $data['password'] = $request->post('password');
            $data['captcha'] = $request->post('captcha');
        }

        $result = $this->auth->login($data);

        if ($result['success']) {
            if ($request->isHtmx()) {
                $this->response->htmxRedirect('/dashboard');
            } else {
                $this->response->redirect('/dashboard');
            }
        } else {
            $errorMessage = '';
            if (!empty($result['errors'])) {
                $errorMessage = is_array($result['errors']) ? reset($result['errors']) : $result['errors'];
            } elseif (!empty($result['error'])) {
                $errorMessage = $result['error'];
            }

            if ($request->isHtmx()) {
                $html = View::render('pages.auth.partials.login-form', [
                    'errors' => $result['errors'] ?? [],
                    'error' => $errorMessage,
                    'old' => $request->only(['phone']),
                    'smsAuthEnabled' => $smsEnabled,
                ]);
                $this->response->html($html);
            } else {
                $request->flash('errors', $result['errors'] ?? []);
                $request->flash('error', $errorMessage);
                $request->flash('old', $request->only(['phone']));
                $this->response->redirect('/login');
            }
        }
    }

    /**
     * 🆕 ارسال کد OTP
     */

    public function sendOtp(Request $request): void
    {
        try {
            $phone = $request->post('phone');
            $purpose = $request->post('purpose', 'login');

            if (!in_array($purpose, ['login', 'register'])) {
                $purpose = 'login';
            }

            $result = $this->auth->sendOtp($phone, $purpose);
            $this->response->json($result);
        } catch (\Throwable $e) {
            log_message("❌ [OTP] Exception in sendOtp: " . $e->getMessage());
            $this->response->json([
                'success' => false,
                'error' => 'خطای داخلی سرور: ' . $e->getMessage(),
            ]);
        }
    }


    public function showRegister(Request $request): void
    {
        if ($this->auth->check()) {
            $this->response->redirect('/dashboard');
            return;
        }

        $html = View::make('pages.auth.register', [
            'title' => 'ثبت‌نام',
            'errors' => $request->getFlash('errors', []),
            'old' => $request->getFlash('old', []),
            'smsAuthEnabled' => $this->auth->isSmsAuthEnabled(),
        ], 'auth');

        $this->response->html($html);
    }

    public function register(Request $request): void
    {
        $smsEnabled = $this->auth->isSmsAuthEnabled();

        $data = [
            'real_name' => $request->post('real_name'),
            'nickname' => $request->post('nickname'),
            'phone' => $request->post('phone'),
            'password' => $request->post('password'),
            'password_confirmation' => $request->post('password_confirmation'),
            'tagline' => $request->post('tagline'),
        ];

        if ($smsEnabled) {
            $data['otp_code'] = $request->post('otp_code');
        } else {
            $data['captcha'] = $request->post('captcha');
        }

        $result = $this->auth->register($data);

        if ($result['success']) {
            if ($request->isHtmx()) {
                $this->response->htmxRedirect('/dashboard');
            } else {
                $this->response->redirect('/dashboard');
            }
        } else {
            if ($request->isHtmx()) {
                $html = View::render('pages.auth.partials.register-form', [
                    'errors' => $result['errors'] ?? [],
                    'old' => $request->only(['real_name', 'nickname', 'phone', 'tagline']),
                    'smsAuthEnabled' => $smsEnabled,
                ]);
                $this->response->html($html);
            } else {
                $request->flash('errors', $result['errors'] ?? []);
                $request->flash('old', $request->only(['real_name', 'nickname', 'phone', 'tagline']));
                $this->response->redirect('/register');
            }
        }
    }

    public function logout(Request $request): void
    {
        $this->auth->logout();

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/login');
        } else {
            $this->response->redirect('/login');
        }
    }
}
