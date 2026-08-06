<?php

namespace Presentation\Middleware;

use Core\Request;
use Core\Response;
use Application\Services\AuthService;

class RoleMiddleware
{
    private string $requiredRole;

    public function __construct(string $requiredRole = 'user')
    {
        $this->requiredRole = $requiredRole;
    }

    public function handle(Request $request): bool
    {
        $auth = new AuthService();

        if (!$auth->check()) {
            $response = new Response();
            $response->redirect('/login');
            return false;
        }

        $user = $auth->user();

        // بررسی سطح دسترسی
        $hasAccess = false;

        switch ($this->requiredRole) {
            case 'admin':
                $hasAccess = in_array($user['role'], ['admin', 'super_admin']);
                break;

            case 'super_admin':
                $hasAccess = $user['role'] === 'super_admin';
                break;

            default:
                $hasAccess = true;
        }

        if (!$hasAccess) {
            $response = new Response();

            if ($request->isHtmx() || $request->isAjax()) {
                $response->status(403)->json([
                    'success' => false,
                    'message' => 'شما دسترسی به این بخش را ندارید'
                ]);
            } else {
                $response->redirect('/');
            }

            return false;
        }

        return true;
    }
}