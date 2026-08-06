<?php

namespace Presentation\Middleware;

use Core\Request;
use Core\Response;
use Application\Services\AuthService;

class AdminMiddleware
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
        // بررسی لاگین بودن
        if (!$this->auth->check()) {
            $this->response->redirect('/login');
            return false;
        }

        // بررسی نقش ادمین
        $user = $this->auth->user();
        $role = $user['role'] ?? 'user';

        if (!in_array($role, ['admin', 'super_admin'])) {
            // اگر ادمین نیست، به داشبورد عادی هدایت شود
            $this->response->redirect('/dashboard');
            return false;
        }

        return true;
    }
}