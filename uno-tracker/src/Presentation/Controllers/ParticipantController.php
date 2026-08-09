<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Core\Database;
use Application\Services\AuthService;
use Application\Services\UserService;

class ParticipantController
{
    private AuthService $auth;
    private UserService $userService;
    private Response $response;
    private Database $db;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->userService = new UserService();
        $this->response = new Response();
        $this->db = Database::getInstance();
    }

    /**
     * نمایش پروفایل خلاصه شرکت‌کننده
     */
    public function profilePartial(Request $request, array $params): void
    {
        $participantId = (int) $params['id'];
        
        // گرفتن اطلاعات شرکت‌کننده
        $participant = $this->db->fetchOne(
            "SELECT gp.*, g.name as game_name, g.game_mode, g.target_wins, g.status as game_status
             FROM game_participants gp
             JOIN games g ON gp.game_id = g.id
             WHERE gp.id = ?",
            [$participantId]
        );
        
        if (!$participant) {
            $this->response->status(404)->html('<div class="text-center text-red-600 py-8">شرکت‌کننده یافت نشد</div>');
            return;
        }

        // اگر کاربر ثبت‌نام شده است، پروفایل کامل را بگیر
        if (!empty($participant['user_id'])) {
            $userId = (int) $participant['user_id'];
            $profile = $this->userService->getUserProfile($userId);
            
            if ($profile) {
                // 🆕 گرفتن اطلاعات عنوان فعلی کاربر
                $titleInfo = $this->db->fetchOne(
                    "SELECT t.id, t.name, t.icon, t.bonus_points
                     FROM users u
                     LEFT JOIN titles t ON u.current_title_id = t.id
                     WHERE u.id = ?",
                    [$userId]
                );
                $profile['title_info'] = $titleInfo;
                
                // اضافه کردن اطلاعات بازی فعلی
                $profile['current_game'] = [
                    'name' => $participant['game_name'],
                    'mode' => $participant['game_mode'],
                    'target_wins' => $participant['target_wins'],
                    'status' => $participant['game_status'],
                    'wins_in_game' => $participant['wins_count'],
                    'score_in_game' => $participant['total_score'],
                ];
                
                $profile['user_id'] = $userId;
                $profile['is_guest'] = false;
                
                $html = View::render('pages.partials.user-profile-partial', [
                    'profile' => $profile,
                ]);
                
                $this->response->html($html);
                return;
            }
        }

        // اگر بازیکن مهمان است
        $guestProfile = [
            'id' => $participant['id'],
            'nickname' => $participant['guest_name'] ?? 'بازیکن مهمان',
            'real_name' => 'بازیکن مهمان',
            'tagline' => null,
            'avatar_path' => null,
            'user_id' => null,
            'is_guest' => true,
            'total_games' => 0,
            'total_wins' => $participant['wins_count'],
            'total_losses' => 0,
            'total_points' => $participant['total_score'],
            'win_rate' => 0,
            'points_per_game' => 0,
            'current_streak' => 0,
            'best_streak' => 0,
            'current_game' => [
                'name' => $participant['game_name'],
                'mode' => $participant['game_mode'],
                'target_wins' => $participant['target_wins'],
                'status' => $participant['game_status'],
                'wins_in_game' => $participant['wins_count'],
                'score_in_game' => $participant['total_score'],
            ],
            'title_info' => null, // 🆕 برای بازیکن مهمان عنوان ندارد
        ];
        
        $html = View::render('pages.partials.user-profile-partial', [
            'profile' => $guestProfile,
        ]);
        
        $this->response->html($html);
    }
}