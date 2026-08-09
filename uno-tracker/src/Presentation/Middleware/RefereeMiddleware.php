<?php

namespace Presentation\Middleware;

use Core\Request;
use Core\Response;
use Application\Services\AuthService;
use Infrastructure\Repositories\GameRepository;

class RefereeMiddleware
{
    /**
     * 🆕 اصلاح شده: گرفتن gameId از پارامترهای route
     */
    public function handle(Request $request, array $params = []): bool
    {
        $auth = new AuthService();

        if (!$auth->check()) {
            $response = new Response();
            $response->redirect('/login');
            return false;
        }

        // اگر ادمین است، اجازه دسترسی دارد
        if ($auth->isAdmin()) {
            return true;
        }

        // 🆕 گرفتن gameId از پارامترهای route
        $gameId = $params['id'] ?? null;
        
        // 🆕 DEBUG
        log_message("🔍 RefereeMiddleware - Game ID from params: " . ($gameId ?? 'null'));
        
        if ($gameId) {
            $gameRepo = new GameRepository();
            $game = $gameRepo->findById((int) $gameId);
            
            log_message("🔍 RefereeMiddleware - Game found: " . ($game ? 'yes' : 'no'));
            log_message("🔍 RefereeMiddleware - Referee ID: " . ($game->referee_id ?? 'null'));
            log_message("🔍 RefereeMiddleware - Auth ID: " . $auth->id());
            
            if ($game && $game->referee_id === $auth->id()) {
                log_message("✅ RefereeMiddleware - Access granted");
                return true;
            }
        }

        log_message("❌ RefereeMiddleware - Access denied");
        
        $response = new Response();
        
        if ($request->isHtmx() || $request->isAjax()) {
            $response->status(403)->json([
                'success' => false,
                'error' => 'شما داور این بازی نیستید'
            ]);
        } else {
            $response->redirect('/dashboard');
        }

        return false;
    }
}