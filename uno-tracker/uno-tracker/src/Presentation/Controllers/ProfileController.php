<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Core\Database;
use Application\Services\AuthService;
use Application\Services\UserService;

class ProfileController
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
     * نمایش پروفایل خود کاربر
     */
    public function showOwn(Request $request): void
    {
        $userId = $this->auth->id();
        $profile = $this->userService->getUserProfile($userId);

        if (!$profile) {
            $this->response->status(404)->html('<h1>کاربر یافت نشد</h1>');
            return;
        }
        // ✅ اضافه کردن مجوزهای کاربر
        $permissions = $this->db->fetchOne(
            "SELECT can_create_game, can_join_game FROM users WHERE id = ?",
            [$userId]
        );
        $profile['can_create_game'] = (int)($permissions['can_create_game'] ?? 0);
        $profile['can_join_game'] = (int)($permissions['can_join_game'] ?? 1); // پیش‌فرض ۱

        // گرفتن دستاوردها
        $gamificationService = new \Application\Services\GamificationService();
        $achievements = $gamificationService->getAchievementService()->getCompletedAchievements($userId);
        $profile['achievements'] = $achievements;

        // 🆕 گرفتن اطلاعات عنوان فعلی و بونوس آن
        $titleInfo = $this->db->fetchOne(
            "SELECT t.id, t.name, t.icon, t.bonus_points, t.description, t.condition_type, t.condition_value
             FROM users u
             LEFT JOIN titles t ON u.current_title_id = t.id
             WHERE u.id = ?",
            [$userId]
        );
        $profile['title_info'] = $titleInfo;

        // 🆕 گرفتن همه عناوین کسب شده توسط کاربر
        $userTitles = $this->db->fetchAll(
            "SELECT t.id, t.name, t.icon, t.bonus_points, t.description, t.condition_type, t.condition_value,
                    ut.unlocked_at
             FROM user_titles ut
             JOIN titles t ON ut.title_id = t.id
             WHERE ut.user_id = ?
             ORDER BY ut.unlocked_at DESC",
            [$userId]
        );
        $profile['user_titles'] = $userTitles;

        // 🆕 گرفتن آمار برای نمودارها
        $statsService = new \Application\Services\UserStatsService();
        $profile['stats_by_status'] = $statsService->getStatsByStatus($userId);
        $profile['stats_by_mode'] = $statsService->getStatsByMode($userId);
        $profile['daily_stats'] = $statsService->getDailyStats($userId, 30);
        $profile['day_of_week_stats'] = $statsService->getDayOfWeekStats($userId);
        $profile['card_stats'] = $statsService->getCardUsageStats($userId);

        // گرفتن همه بازی‌ها با Pagination
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $allGames = $this->db->fetchAll(
            "SELECT
                g.id,
                g.name,
                g.game_mode,
                g.target_wins,
                g.status,
                g.created_at,
                g.finished_at,
                g.winner_participant_id,
                gp.wins_count,
                gp.total_score,
                gp.id as participant_id,
                (SELECT COUNT(*) FROM game_participants gp2 WHERE gp2.game_id = g.id) as total_players,
                (SELECT COUNT(*) FROM teams t WHERE t.game_id = g.id) as total_teams,
                (g.winner_participant_id = gp.id) as is_winner
             FROM games g
             INNER JOIN game_participants gp ON g.id = gp.game_id
             WHERE gp.user_id = ?
             ORDER BY g.created_at DESC
             LIMIT ? OFFSET ?",
            [$userId, $perPage + 1, $offset]
        );

        $hasMore = count($allGames) > $perPage;
        $games = array_slice($allGames, 0, $perPage);
        $history = $games;

        foreach ($history as &$game) {
            if (!isset($game['is_winner'])) {
                $game['is_winner'] = false;
            }
        }

        $html = View::make('pages.profile.show', [
            'title' => 'پروفایل من',
            'profile' => $profile,
            'isOwn' => true,
            'stats' => [
                'total_games' => $profile['total_games'],
                'total_wins' => $profile['total_wins'],
            ],
            'history' => $history,
            'games' => $games,
            'page' => $page,
            'hasMore' => $hasMore,
            'achievements' => $achievements,
        ], 'main');

        $this->response->html($html);
    }

    /**
     * نمایش فرم ویرایش پروفایل
     */
    public function edit(Request $request): void
    {
        $userId = $this->auth->id();
        $profile = $this->userService->getUserProfile($userId);

        if (!$profile) {
            $this->response->status(404)->html('<h1>کاربر یافت نشد</h1>');
            return;
        }

        $html = View::make('pages.profile.edit', [
            'title' => 'ویرایش پروفایل',
            'profile' => $profile,
            'success' => $request->getFlash('success'),
            'error' => $request->getFlash('error'),
            'errors' => $request->getFlash('errors', []),
        ], 'main');

        $this->response->html($html);
    }

    /**
     * به‌روزرسانی پروفایل
     */
    public function update(Request $request): void
    {
        $userId = $this->auth->id();

        $realName = trim($request->post('real_name', ''));
        $nickname = trim($request->post('nickname', ''));
        $tagline = trim($request->post('tagline', ''));

        // فیلدهای تغییر رمز عبور
        $currentPassword = $request->post('current_password', '');
        $newPassword = $request->post('new_password', '');
        $newPasswordConfirmation = $request->post('new_password_confirmation', '');

        // اعتبارسنجی
        $errors = [];

        if (empty($realName)) {
            $errors['real_name'] = 'نام واقعی الزامی است';
        }

        if (empty($nickname)) {
            $errors['nickname'] = 'نام مستعار الزامی است';
        } elseif (!preg_match('/^[\p{L}\p{N}_\s\-\.]+$/u', $nickname)) {
            $errors['nickname'] = 'نام مستعار فقط می‌تواند شامل حروف، اعداد، _، فاصله، - و . باشد';
        } elseif ($this->userService->isNicknameTaken($nickname, $userId)) {
            $errors['nickname'] = 'این نام مستعار قبلاً استفاده شده است';
        }

        // اعتبارسنجی تغییر رمز عبور
        $passwordChanged = false;
        if (!empty($currentPassword) || !empty($newPassword) || !empty($newPasswordConfirmation)) {
            if (empty($currentPassword)) {
                $errors['current_password'] = 'رمز عبور فعلی الزامی است';
            }
            if (empty($newPassword)) {
                $errors['new_password'] = 'رمز عبور جدید الزامی است';
            }
            if (empty($newPasswordConfirmation)) {
                $errors['new_password_confirmation'] = 'تکرار رمز عبور جدید الزامی است';
            }
            if (!empty($newPassword) && strlen($newPassword) < 6) {
                $errors['new_password'] = 'رمز عبور جدید باید حداقل ۶ کاراکتر باشد';
            }
            if (!empty($newPassword) && $newPassword !== $newPasswordConfirmation) {
                $errors['new_password_confirmation'] = 'رمز عبور جدید و تکرار آن مطابقت ندارند';
            }

            // بررسی صحت رمز فعلی
            if (!empty($currentPassword) && empty($errors['current_password'])) {
                $user = $this->db->fetchOne(
                    "SELECT password_hash FROM users WHERE id = ?",
                    [$userId]
                );
                if ($user && !password_verify($currentPassword, $user['password_hash'])) {
                    $errors['current_password'] = 'رمز عبور فعلی صحیح نیست';
                } else {
                    $passwordChanged = true;
                }
            }
        }

        if (!empty($errors)) {
            $profile = $this->userService->getUserProfile($userId);
            $html = View::render('pages.profile.partials.edit-form', [
                'profile' => $profile,
                'errors' => $errors,
                'error' => 'لطفاً خطاهای فرم را اصلاح کنید',
            ]);
            $this->response->status(400)->html($html);
            return;
        }

        try {
            // به‌روزرسانی اطلاعات پروفایل
            $this->db->update('users', [
                'real_name' => $realName,
                'nickname' => $nickname,
                'tagline' => $tagline,
            ], 'id = ?', [$userId]);

            // به‌روزرسانی رمز عبور در صورت لزوم
            if ($passwordChanged) {
                $this->db->update('users', [
                    'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
                ], 'id = ?', [$userId]);
            }

            $successMessage = 'پروفایل با موفقیت به‌روز شد';
            if ($passwordChanged) {
                $successMessage .= ' و رمز عبور تغییر کرد';
            }

            $profile = $this->userService->getUserProfile($userId);
            $html = View::render('pages.profile.partials.edit-form', [
                'profile' => $profile,
                'success' => $successMessage,
            ]);
            $this->response->html($html);
        } catch (\Throwable $e) {
            $profile = $this->userService->getUserProfile($userId);
            $html = View::render('pages.profile.partials.edit-form', [
                'profile' => $profile,
                'error' => 'خطا در به‌روزرسانی پروفایل: ' . $e->getMessage(),
            ]);
            $this->response->status(500)->html($html);
        }
    }

    /**
     * آپلود آواتار
     */
    public function uploadAvatar(Request $request): void
    {
        $userId = $this->auth->id();

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $profile = $this->userService->getUserProfile($userId);
            $html = View::render('pages.profile.partials.edit-form', [
                'profile' => $profile,
                'error' => 'خطا در آپلود تصویر',
            ]);
            $this->response->status(400)->html($html);
            return;
        }

        $file = $_FILES['avatar'];

        // اعتبارسنجی فایل
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            $error = 'فرمت فایل نامعتبر است. فقط JPG, PNG, GIF و WEBP مجاز هستند.';
            $profile = $this->userService->getUserProfile($userId);
            $html = View::render('pages.profile.partials.edit-form', [
                'profile' => $profile,
                'error' => $error,
            ]);
            $this->response->status(400)->html($html);
            return;
        }

        // بررسی حجم فایل (حداکثر ۲ مگابایت)
        if ($file['size'] > 2 * 1024 * 1024) {
            $error = 'حجم فایل نباید بیشتر از ۲ مگابایت باشد';
            $profile = $this->userService->getUserProfile($userId);
            $html = View::render('pages.profile.partials.edit-form', [
                'profile' => $profile,
                'error' => $error,
            ]);
            $this->response->status(400)->html($html);
            return;
        }

        try {
            // ایجاد نام فایل یکتا
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $userId . '_' . time() . '.' . $extension;

            // مسیر ذخیره‌سازی
            $uploadDir = ROOT_PATH . '/storage/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destination = $uploadDir . $filename;

            // انتقال فایل
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new \Exception('خطا در ذخیره فایل');
            }

            // حذف آواتار قبلی
            $oldUser = $this->db->fetchOne(
                "SELECT avatar_path FROM users WHERE id = ?",
                [$userId]
            );
            if ($oldUser && !empty($oldUser['avatar_path'])) {
                $oldPath = $uploadDir . $oldUser['avatar_path'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // به‌روزرسانی دیتابیس
            $this->db->update('users', [
                'avatar_path' => $filename,
            ], 'id = ?', [$userId]);

            $successMessage = 'تصویر پروفایل با موفقیت آپلود شد';
            $profile = $this->userService->getUserProfile($userId);
            $html = View::render('pages.profile.partials.edit-form', [
                'profile' => $profile,
                'success' => $successMessage,
            ]);
            $this->response->html($html);
        } catch (\Throwable $e) {
            $profile = $this->userService->getUserProfile($userId);
            $html = View::render('pages.profile.partials.edit-form', [
                'profile' => $profile,
                'error' => 'خطا در آپلود تصویر: ' . $e->getMessage(),
            ]);
            $this->response->status(500)->html($html);
        }
    }
}
