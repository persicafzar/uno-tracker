<?php

namespace Presentation\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Application\Services\AuthService;
use Application\Services\AdminService;
use Infrastructure\Repositories\SettingsRepository; // 🆕 اضافه شده
use Infrastructure\Repositories\AdminRepository;
use Core\Database;

class AdminController
{
    private AuthService $auth;
    private AdminService $adminService;
    private Response $response;
    private AdminRepository $adminRepo;
    private Database $db; // 🆕 اضافه شود



    public function __construct()
    {
        $this->auth = new AuthService();
        $this->adminService = new AdminService();
        $this->response = new Response();
        $this->adminRepo = new AdminRepository();
        $this->db = Database::getInstance(); // 🆕 اضافه شود

    }

    /**
     * داشبورد اصلی ادمین
     */
    public function dashboard(Request $request): void
    {
        $admin = $this->auth->user();
        $data = $this->adminService->getDashboardData();

        $html = View::make('pages.admin.dashboard', [
            'title' => 'پنل مدیریت',
            'admin' => $admin,
            'currentPath' => '/admin',
            'stats' => $data['stats'],
            'userRegistrationStats' => $data['userRegistrationStats'],
            'gamesStats' => $data['gamesStats'],
            'gameModeDistribution' => $data['gameModeDistribution'],
        ], 'admin');

        $this->response->html($html);
    }


    /**
     * لیست کاربران
     */
    public function users(Request $request): void
    {
        $admin = $this->auth->user();
        $page = max(1, (int) $request->get('page', 1));

        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'role' => $request->get('role'),
        ];

        $result = $this->adminService->getUsers($filters, $page, 20);

        $html = View::make('pages.admin.users', [
            'title' => 'مدیریت کاربران',
            'admin' => $admin,
            'currentPath' => '/admin/users',
            'users' => $result['users'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['perPage'],
            'totalPages' => $result['totalPages'],
            'filters' => $filters,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * مسدود کردن کاربر
     */
    public function banUser(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();
        // 🆕 محافظت از super_admin
        $targetUser = $this->adminService->getUserWithStats($userId);
        if ($targetUser && $targetUser['role'] === 'super_admin') {
            $request->flash('error', 'نمی‌توانید مدیر ارشد را مسدود کنید.');
            $this->response->redirect('/admin/users');
            return;
        }
        $result = $this->adminService->banUser(
            $userId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // 🆕 تشخیص صفحه مبدأ برای ریدایرکت هوشمند
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $redirectTo = '/admin/users/' . $userId; // پیش‌فرض: صفحه جزئیات کاربر

        // اگر از لیست کاربران آمده، به لیست برگرد
        if (preg_match('#/admin/users(\?|$)#', $referer)) {
            $redirectTo = '/admin/users';
        }

        if ($request->isHtmx()) {
            $this->response->htmxRedirect($redirectTo);
        } else {
            if ($result) {
                $request->flash('success', 'کاربر با موفقیت مسدود شد.');
            } else {
                $request->flash('error', 'خطا در مسدود کردن کاربر.');
            }
            $this->response->redirect($redirectTo);
        }
    }

    /**
     * فعال‌سازی کاربر
     */
    public function unbanUser(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();

        $result = $this->adminService->unbanUser(
            $userId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // 🆕 تشخیص صفحه مبدأ برای ریدایرکت هوشمند
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $redirectTo = '/admin/users/' . $userId; // پیش‌فرض: صفحه جزئیات کاربر

        // اگر از لیست کاربران آمده، به لیست برگرد
        if (preg_match('#/admin/users(\?|$)#', $referer)) {
            $redirectTo = '/admin/users';
        }

        if ($request->isHtmx()) {
            $this->response->htmxRedirect($redirectTo);
        } else {
            if ($result) {
                $request->flash('success', 'کاربر با موفقیت فعال شد.');
            } else {
                $request->flash('error', 'خطا در فعال‌سازی کاربر.');
            }
            $this->response->redirect($redirectTo);
        }
    }

    /**
     * تغییر نقش کاربر
     */
    public function changeRole(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $newRole = $request->post('role', 'user');
        $admin = $this->auth->user();
        // 🆕 محافظت از super_admin
        $targetUser = $this->adminService->getUserWithStats($userId);
        if ($targetUser && $targetUser['role'] === 'super_admin') {
            $request->flash('error', 'نمی‌توانید نقش مدیر ارشد را تغییر دهید.');
            $this->response->redirect('/admin/users');
            return;
        }
        // جلوگیری از تغییر نقش super_admin توسط admin معمولی
        if ($admin['role'] !== 'super_admin' && $newRole === 'super_admin') {
            $this->response->status(403)->html('شما اجازه این عملیات را ندارید');
            return;
        }

        $result = $this->adminService->changeUserRole(
            $userId,
            $newRole,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // 🆕 به‌روزرسانی session اگر کاربر خودش تغییر نقش داد
        if ($result && $userId === $admin['id']) {
            $_SESSION['user_role'] = $newRole;
        }
        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/users');
        } else {
            $this->response->redirect('/admin/users');
        }
    }

    /**
     * حذف کاربر
     */
    public function deleteUser(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();
        // 🆕 محافظت از super_admin
        $targetUser = $this->adminService->getUserWithStats($userId);
        if ($targetUser && $targetUser['role'] === 'super_admin') {
            $request->flash('error', 'نمی‌توانید مدیر ارشد را حذف کنید.');
            $this->response->redirect('/admin/users');
            return;
        }
        // جلوگیری از حذف خود
        if ($userId === $admin['id']) {
            $this->response->status(400)->html('نمی‌توانید خودتان را حذف کنید');
            return;
        }

        $result = $this->adminService->deleteUser(
            $userId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/users');
        } else {
            $this->response->redirect('/admin/users');
        }
    }

    /**
     * لیست بازی‌ها
     */
    public function games(Request $request): void
    {
        $admin = $this->auth->user();
        $page = max(1, (int) $request->get('page', 1));

        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'mode' => $request->get('mode'),
        ];

        $result = $this->adminService->getGames($filters, $page, 20);

        $html = View::make('pages.admin.games', [
            'title' => 'مدیریت بازی‌ها',
            'admin' => $admin,
            'currentPath' => '/admin/games',
            'games' => $result['games'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['perPage'],
            'totalPages' => $result['totalPages'],
            'filters' => $filters,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * حذف بازی
     */
    public function deleteGame(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $admin = $this->auth->user();

        $result = $this->adminService->deleteGame(
            $gameId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/games');
        } else {
            $this->response->redirect('/admin/games');
        }
    }

    /**
     * لاگ‌های سیستم
     */
    public function logs(Request $request): void
    {
        $admin = $this->auth->user();
        $page = max(1, (int) $request->get('page', 1));

        $filters = [
            'admin_id' => $request->get('admin_id'),
            'action_type' => $request->get('action_type'),
            'target_type' => $request->get('target_type'),
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
        ];

        $result = $this->adminService->getLogs($filters, $page, 50);

        $html = View::make('pages.admin.logs', [
            'title' => 'لاگ‌های سیستم',
            'admin' => $admin,
            'currentPath' => '/admin/logs',
            'logs' => $result['logs'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['perPage'],
            'totalPages' => $result['totalPages'],
            'filters' => $filters,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * تنظیمات سیستم
     */
    public function settings(Request $request): void
    {
        $admin = $this->auth->user();

        // 🆕 استفاده از SettingsRepository
        $settingsRepo = SettingsRepository::getInstance();
        $settings = $settingsRepo->getAllWithDetails();

        $html = View::make('pages.admin.settings', [
            'title' => 'تنظیمات سیستم',
            'admin' => $admin,
            'currentPath' => '/admin/settings',
            'settings' => $settings,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * به‌روزرسانی تنظیمات
     */
    /**
     * به‌روزرسانی تنظیمات
     */
    public function updateSettings(Request $request): void
    {
        $admin = $this->auth->user();
        $settings = $request->post('settings', []);
        $category = $request->post('category', 'general');
        $settingsRepo = SettingsRepository::getInstance();

        // لاگ برای دیباگ
        error_log('Settings received: ' . print_r($settings, true));

        // لیست کلیدهای JSON
        $jsonKeys = ['sse_sound_settings'];

        // لیست کلیدهای Boolean
        $booleanKeys = [
            'sse_fallback_enabled',
            'enable_notifications',
            'notification_sound_enabled',
            'enable_animations',
            'anticheat_enabled',
            'registration_enabled',
            'maintenance_mode',
            'require_special_char',
            'require_number',
            'sms_enabled'
        ];

        // لیست کلیدهای Integer
        $integerKeys = [
            'sse_fallback_refresh_seconds',
            'default_notification_duration',
            'max_login_attempts',
            'lockout_duration',
            'password_min_length',
            'max_players_per_game',
            'max_guest_players',
            'default_target_wins',
            'min_players_solo',
            'min_players_team',
            'players_per_team',
            'xp_game_played',
            'xp_win_solo',
            'xp_win_team',
            'streak_reset_hours',
            'max_upload_size',
            'avatar_max_width',
            'avatar_max_height',
            'items_per_page',
            'scoring_base_score',
            'scoring_win_bonus',
            'scoring_game_bonus',
            'scoring_team_multiplier',
            'scoring_winner_bonus',
            'scoring_min_target_wins',
            'session_lifetime',
            'session_idle_timeout',
            'session_warn_before_expire',
            'session_regenerate_interval',
            'xp_achievement_unlock',
            'xp_challenge_complete',
            'anticheat_min_round_duration',
            'anticheat_min_players',
            'anticheat_max_guests',
            'anticheat_min_members',
            'anticheat_max_win_percentage',
            'anticheat_max_games_per_hour',
            'anticheat_min_target_wins_threshold',
            'anticheat_max_low_target_games',
            'anticheat_new_account_hours',
            'anticheat_max_accounts_per_ip',
            'anticheat_max_games_created_per_day',
            'anticheat_max_solo_games_per_day',
            'anticheat_max_friendly_games_per_day',
            'anticheat_collusion_min_games',
            'anticheat_collusion_max_opponents',
            'sms_otp_length',
            'sms_otp_expiry',
            'sms_daily_limit',
            'sms_otp_attempt_limit'
        ];

        foreach ($settings as $key => $value) {
            // تعیین نوع داده
            $type = 'string';
            $valueToSave = $value;

            // اولویت ۱: کلیدهای JSON
            if (in_array($key, $jsonKeys)) {
                $type = 'json';
                if (is_array($value)) {
                    $valueToSave = json_encode($value, JSON_UNESCAPED_UNICODE);
                } elseif (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $valueToSave = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                    } else {
                        error_log("⚠️ Invalid JSON for {$key}: " . json_last_error_msg());
                        continue;
                    }
                }
            }
            // اولویت ۲: کلیدهای Boolean
            elseif (in_array($key, $booleanKeys)) {
                $type = 'boolean';
                $valueToSave = (int) (bool) $value;
            }
            // اولویت ۳: کلیدهای Integer
            elseif (in_array($key, $integerKeys)) {
                $type = 'integer';
                $valueToSave = (int) $value;
            }
            // تشخیص خودکار
            elseif (is_numeric($value) && strpos((string)$value, '.') === false) {
                $type = 'integer';
            } elseif (is_numeric($value)) {
                $type = 'float';
            } elseif ($value === '0' || $value === '1') {
                $type = 'boolean';
            }

            // ✅ اصلاح: ارسال دسته‌بندی به Repository
            try {
                $settingsRepo->set($key, $valueToSave, $type, null, $category);
                error_log("✅ Saved setting: {$key} = {$valueToSave} ({$type}) in category {$category}");
            } catch (\Throwable $e) {
                error_log("❌ Error saving setting {$key}: " . $e->getMessage());
            }

            // ثبت لاگ (اختیاری)
            $this->adminService->updateSetting(
                $key,
                is_string($valueToSave) ? $valueToSave : json_encode($valueToSave),
                $admin['id'],
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
        }

        // بازنشانی کامل کش
        $settingsRepo->clearCache();

        // ریدایرکت با پیام موفقیت
        $redirectUrl = '/admin/settings?tab=' . $category . '&saved=1';
        if ($request->isHtmx()) {
            $this->response->htmxRedirect($redirectUrl);
        } else {
            $this->response->redirect($redirectUrl);
        }
    }





    /**
     * جزئیات کاربر
     */
    public function userDetail(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();

        $user = $this->adminService->getUserWithStats($userId);

        if (!$user) {
            $this->response->status(404)->html('<h1>کاربر یافت نشد</h1>');
            return;
        }

        $html = View::make('pages.admin.user-detail', [
            'title' => 'جزئیات کاربر: ' . ($user['nickname'] ?? ''),
            'admin' => $admin,
            'currentPath' => '/admin/users',
            'user' => $user,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * جزئیات بازی
     */
    public function gameDetail(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $admin = $this->auth->user();

        $game = $this->adminService->getGameWithDetails($gameId);

        if (!$game) {
            $this->response->status(404)->html('<h1>بازی یافت نشد</h1>');
            return;
        }

        $html = View::make('pages.admin.game-detail', [
            'title' => 'جزئیات بازی: ' . ($game['name'] ?? "بازی #{$gameId}"),
            'admin' => $admin,
            'currentPath' => '/admin/games',
            'game' => $game,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * Export کاربران به Excel
     */
    public function exportUsers(Request $request): void
    {
        $exportService = new \Application\Services\ExportService();

        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'role' => $request->get('role'),
        ];

        $exportService->exportUsers($filters);
    }

    /**
     * Export بازی‌ها به Excel
     */
    public function exportGames(Request $request): void
    {
        $exportService = new \Application\Services\ExportService();

        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'mode' => $request->get('mode'),
        ];

        $exportService->exportGames($filters);
    }

    /**
     * Export لاگ‌ها به Excel
     */
    public function exportLogs(Request $request): void
    {
        $exportService = new \Application\Services\ExportService();

        $filters = [
            'admin_id' => $request->get('admin_id'),
            'action_type' => $request->get('action_type'),
            'target_type' => $request->get('target_type'),
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
        ];

        $exportService->exportLogs($filters);
    }

    /**
     * 🆕 تغییر وضعیت بازی
     */
    public function updateGameStatus(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $status = $request->post('status');
        $admin = $this->auth->user();

        $validStatuses = ['pending', 'active', 'paused', 'finished', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            $this->response->status(400)->json(['error' => 'وضعیت نامعتبر']);
            return;
        }

        $result = $this->adminService->updateGameStatus(
            $gameId,
            $status,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/games');
        } else {
            $this->response->redirect('/admin/games');
        }
    }

    /**
     * 🆕 تغییر داور بازی
     */
    public function updateGameReferee(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $newRefereeId = (int) $request->post('referee_id');
        $admin = $this->auth->user();

        $result = $this->adminService->updateGameReferee(
            $gameId,
            $newRefereeId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/games/' . $gameId);
        } else {
            $this->response->redirect('/admin/games/' . $gameId);
        }
    }

    /**
     * 🆕 ویرایش تعداد دورهای بازی
     */
    public function updateGameRounds(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $totalRounds = (int) $request->post('total_rounds');
        $admin = $this->auth->user();

        $result = $this->adminService->updateGameRounds(
            $gameId,
            $totalRounds,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/games/' . $gameId);
        } else {
            $this->response->redirect('/admin/games/' . $gameId);
        }
    }

    /**
     * 🆕 افزودن بازیکن به بازی
     */
    public function addParticipant(Request $request, array $params): void
    {
        $gameId = (int) $params['id'];
        $admin = $this->auth->user();

        $data = [
            'user_id' => $request->post('user_id') ? (int) $request->post('user_id') : null,
            'guest_name' => $request->post('guest_name') ?: null,
            'team_id' => $request->post('team_id') ? (int) $request->post('team_id') : null,
        ];

        $result = $this->adminService->addParticipant(
            $gameId,
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/games/' . $gameId);
        } else {
            $this->response->redirect('/admin/games/' . $gameId);
        }
    }

    /**
     * 🆕 حذف بازیکن از بازی
     */
    public function removeParticipant(Request $request, array $params): void
    {
        $participantId = (int) $params['participant_id'];
        $admin = $this->auth->user();

        $result = $this->adminService->removeParticipant(
            $participantId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/games/' . $params['id']);
        } else {
            $this->response->redirect('/admin/games/' . $params['id']);
        }
    }

    /**
     * 🆕 عملیات گروهی روی بازی‌ها
     */
    public function bulkAction(Request $request): void
    {
        $gameIds = $request->post('game_ids', []);
        $action = $request->post('action');
        $admin = $this->auth->user();

        if (empty($gameIds) || !is_array($gameIds)) {
            $this->response->status(400)->json(['error' => 'هیچ بازی‌ای انتخاب نشده']);
            return;
        }

        $result = $this->adminService->bulkAction(
            $gameIds,
            $action,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/games');
        } else {
            $this->response->redirect('/admin/games');
        }
    }

// ============================================
// 🆕 مدیریت کارت‌ها
// ============================================

    /**
     * لیست کارت‌ها
     */
    public function cards(Request $request): void
    {
        $admin = $this->auth->user();
        $cards = $this->adminService->getCards();

        $html = View::make('pages.admin.cards', [
            'title' => 'مدیریت کارت‌ها',
            'admin' => $admin,
            'currentPath' => '/admin/cards',
            'cards' => $cards,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * ایجاد کارت جدید
     */
    public function createCard(Request $request): void
    {
        $admin = $this->auth->user();

        $data = [
            'name' => trim($request->post('name', '')),
            'slug' => trim($request->post('slug', '')),
            'emoji' => $request->post('emoji', ''),
            'description' => $request->post('description', ''),
            'score_multiplier' => (float) $request->post('score_multiplier', 1.0),
            'rarity' => $request->post('rarity', 'common'),
            'is_active' => (int) $request->post('is_active', 0), // 🆕 دریافت صحیح
        ];

        // اعتبارسنجی
        if (empty($data['name']) || empty($data['slug'])) {
            $request->flash('error', 'نام و Slug الزامی هستند');
            $this->response->redirect('/admin/cards');
            return;
        }

        $result = $this->adminService->createCard(
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'کارت با موفقیت ایجاد شد');
        } else {
            $request->flash('error', 'خطا در ایجاد کارت');
        }

        $this->response->redirect('/admin/cards');
    }

    /**
     * ویرایش کارت
     */
    public function updateCard(Request $request, array $params): void
    {
        $cardId = (int) $params['id'];
        $admin = $this->auth->user();

        $data = [
            'name' => trim($request->post('name', '')),
            'slug' => trim($request->post('slug', '')),
            'emoji' => $request->post('emoji', ''),
            'description' => $request->post('description', ''),
            'score_multiplier' => (float) $request->post('score_multiplier', 1.0),
            'rarity' => $request->post('rarity', 'common'),
            'is_active' => (int) $request->post('is_active', 0), // 🆕 دریافت صحیح
        ];

        $result = $this->adminService->updateCard(
            $cardId,
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/cards');
        } else {
            $request->flash('success', 'کارت با موفقیت ویرایش شد');
            $this->response->redirect('/admin/cards');
        }
    }

    /**
     * تغییر وضعیت فعال/غیرفعال کارت
     */
    public function toggleCardActive(Request $request, array $params): void
    {
        $cardId = (int) $params['id'];
        $isActive = (int) $request->post('is_active', 0); // 🆕 دریافت صحیح

        $result = $this->adminService->toggleCardActive($cardId, $isActive);

        if ($result) {
            $request->flash('success', 'وضعیت کارت تغییر کرد');
        } else {
            $request->flash('error', 'خطا در تغییر وضعیت');
        }

        $this->response->redirect('/admin/cards');
    }

    /**
     * حذف کارت
     */
    public function deleteCard(Request $request, array $params): void
    {
        $cardId = (int) $params['id'];
        $admin = $this->auth->user();

        $result = $this->adminService->deleteCard(
            $cardId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'کارت با موفقیت حذف شد');
        } else {
            $request->flash('error', 'خطا در حذف کارت');
        }

        $this->response->redirect('/admin/cards');
    }

// ============================================
// 🆕 مدیریت انواع برد
// ============================================

    /**
     * لیست انواع برد
     */
    public function winTypes(Request $request): void
    {
        $admin = $this->auth->user();
        $winTypes = $this->adminService->getWinTypes();

        $html = View::make('pages.admin.win-types', [
            'title' => 'مدیریت انواع برد',
            'admin' => $admin,
            'currentPath' => '/admin/win-types',
            'winTypes' => $winTypes,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * ایجاد نوع برد جدید
     */
    public function createWinType(Request $request): void
    {
        $admin = $this->auth->user();

        $data = [
            'name' => trim($request->post('name', '')),
            'slug' => trim($request->post('slug', '')),
            'icon' => $request->post('icon', ''),
            'description' => $request->post('description', ''),
            'score_multiplier' => (float) $request->post('score_multiplier', 1.0),
            'is_active' => (int) $request->post('is_active', 0), // 🆕 دریافت صحیح
        ];

        // اعتبارسنجی
        if (empty($data['name']) || empty($data['slug'])) {
            $request->flash('error', 'نام و Slug الزامی هستند');
            $this->response->redirect('/admin/win-types');
            return;
        }

        $result = $this->adminService->createWinType(
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'نوع برد با موفقیت ایجاد شد');
        } else {
            $request->flash('error', 'خطا در ایجاد نوع برد');
        }

        $this->response->redirect('/admin/win-types');
    }

    /**
     * ویرایش نوع برد
     */
    public function updateWinType(Request $request, array $params): void
    {
        $winTypeId = (int) $params['id'];
        $admin = $this->auth->user();

        $data = [
            'name' => trim($request->post('name', '')),
            'slug' => trim($request->post('slug', '')),
            'icon' => $request->post('icon', ''),
            'description' => $request->post('description', ''),
            'score_multiplier' => (float) $request->post('score_multiplier', 1.0),
            'is_active' => (int) $request->post('is_active', 0), // 🆕 دریافت صحیح
        ];

        $result = $this->adminService->updateWinType(
            $winTypeId,
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/win-types');
        } else {
            $request->flash('success', 'نوع برد با موفقیت ویرایش شد');
            $this->response->redirect('/admin/win-types');
        }
    }

    /**
     * تغییر وضعیت فعال/غیرفعال نوع برد
     */
    public function toggleWinTypeActive(Request $request, array $params): void
    {
        $winTypeId = (int) $params['id'];
        $isActive = (int) $request->post('is_active', 0); // 🆕 دریافت صحیح

        $result = $this->adminService->toggleWinTypeActive($winTypeId, $isActive);

        if ($result) {
            $request->flash('success', 'وضعیت نوع برد تغییر کرد');
        } else {
            $request->flash('error', 'خطا در تغییر وضعیت');
        }

        $this->response->redirect('/admin/win-types');
    }

    /**
     * حذف نوع برد
     */
    public function deleteWinType(Request $request, array $params): void
    {
        $winTypeId = (int) $params['id'];
        $admin = $this->auth->user();

        $result = $this->adminService->deleteWinType(
            $winTypeId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'نوع برد با موفقیت حذف شد');
        } else {
            $request->flash('error', 'خطا در حذف نوع برد');
        }

        $this->response->redirect('/admin/win-types');
    }
    // ============================================
// 🆕 مدیریت عناوین
// ============================================

    /**
     * لیست عناوین
     */
    public function titles(Request $request): void
    {
        $admin = $this->auth->user();
        $titles = $this->adminService->getTitles();

        $html = View::make('pages.admin.titles', [
            'title' => 'مدیریت عناوین',
            'admin' => $admin,
            'currentPath' => '/admin/titles',
            'titles' => $titles,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * ایجاد عنوان جدید
     */
    public function createTitle(Request $request): void
    {
        $admin = $this->auth->user();

        $data = [
            'code' => trim($request->post('code', '')),
            'name' => trim($request->post('name', '')),
            'description' => $request->post('description', ''),
            'icon' => $request->post('icon', '🏆'),
            'condition_type' => $request->post('condition_type', 'total_wins'),
            'condition_value' => (int) $request->post('condition_value', 10),
            'bonus_points' => (int) $request->post('bonus_points', 0),
            'priority' => (int) $request->post('priority', 0),
            'is_active' => (int) $request->post('is_active', 0),
        ];

        // اعتبارسنجی
        if (empty($data['code']) || empty($data['name'])) {
            $request->flash('error', 'کد و نام الزامی هستند');
            $this->response->redirect('/admin/titles');
            return;
        }

        $result = $this->adminService->createTitle(
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'عنوان با موفقیت ایجاد شد');
        } else {
            $request->flash('error', 'خطا در ایجاد عنوان');
        }

        $this->response->redirect('/admin/titles');
    }

    /**
     * ویرایش عنوان
     */
    public function updateTitle(Request $request, array $params): void
    {
        $titleId = (int) $params['id'];
        $admin = $this->auth->user();

        $data = [
            'code' => trim($request->post('code', '')),
            'name' => trim($request->post('name', '')),
            'description' => $request->post('description', ''),
            'icon' => $request->post('icon', '🏆'),
            'condition_type' => $request->post('condition_type', 'total_wins'),
            'condition_value' => (int) $request->post('condition_value', 10),
            'bonus_points' => (int) $request->post('bonus_points', 0),
            'priority' => (int) $request->post('priority', 0),
            'is_active' => (int) $request->post('is_active', 0),
        ];

        $result = $this->adminService->updateTitle(
            $titleId,
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/titles');
        } else {
            $request->flash('success', 'عنوان با موفقیت ویرایش شد');
            $this->response->redirect('/admin/titles');
        }
    }

    /**
     * حذف عنوان
     */
    public function deleteTitle(Request $request, array $params): void
    {
        $titleId = (int) $params['id'];
        $admin = $this->auth->user();

        $result = $this->adminService->deleteTitle(
            $titleId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'عنوان با موفقیت حذف شد');
        } else {
            $request->flash('error', 'خطا در حذف عنوان');
        }

        $this->response->redirect('/admin/titles');
    }

    // ============================================
// 🛡️ ضدتقلب
// ============================================

    /**
     * لیست بازی‌های مشکوک
     */

    public function suspiciousGames(Request $request): void
    {
        $admin = $this->auth->user();
        $antiCheatService = new \Application\Services\AntiCheatService();
        $page = max(1, (int) $request->get('page', 1));

        // دریافت فیلترها به صورت صریح
        $filters = [];
        $riskLevel = $request->get('risk_level');
        if ($riskLevel !== null && $riskLevel !== '') {
            $filters['risk_level'] = $riskLevel;
        }

        $isReviewed = $request->get('is_reviewed');
        if ($isReviewed !== null && $isReviewed !== '') {
            $filters['is_reviewed'] = (int) $isReviewed; // تبدیل صریح به عدد
        }

        $userId = $request->get('user_id');
        if ($userId !== null && $userId !== '') {
            $filters['user_id'] = (int) $userId;
        }

        $result = $antiCheatService->getSuspiciousGames($filters, $page, 20);

        $html = View::make('pages.admin.suspicious-games', [
            'title' => 'بازی‌های مشکوک',
            'admin' => $admin,
            'currentPath' => '/admin/suspicious-games',
            'suspiciousGames' => $result['games'],
            'total' => $result['total'],
            'page' => $result['page'],
            'totalPages' => $result['totalPages'],
            'filters' => $filters,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * 🆕 باز محاسبه آمار همه کاربران (AJAX endpoint)
     * قابل اجرا از طریق وب (هاست اشتراکی) و CLI
     */
    public function recalculateAllUsersBatch(Request $request): void
    {
        // فقط super_admin اجازه دارد
        if (!$this->auth->isSuperAdmin()) {
            $this->response->json([
                'success' => false,
                'error' => 'فقط مدیر ارشد می‌تواند این عملیات را انجام دهد'
            ]);
            return;
        }

        $startTime = microtime(true);
        $batchSize = (int) $request->post('batch_size', 50);

        $db = \Core\Database::getInstance();
        $recalcService = new \Application\Services\RecalculateUserService();

        // گرفتن همه کاربران فعال
        $users = $db->fetchAll(
            "SELECT id, nickname FROM users WHERE status != 'banned' ORDER BY id ASC"
        );

        $totalUsers = count($users);
        $successCount = 0;
        $failCount = 0;
        $failedUsers = [];

        foreach ($users as $user) {
            try {
                $recalcService->recalculateAll((int)$user['id']);
                $successCount++;
            } catch (\Throwable $e) {
                $failCount++;
                $failedUsers[] = $user['nickname'] . ' (ID: ' . $user['id'] . ')';
                error_log("Recalculate error for user {$user['id']}: " . $e->getMessage());
            }

            // استراحت بین batch ها
            if ($batchSize > 0 && $successCount % $batchSize === 0) {
                usleep(500000); // 0.5 ثانیه
            }
        }

        $duration = round(microtime(true) - $startTime, 2);

        // ثبت لاگ
        $admin = $this->auth->user();
        $this->adminRepo->createLog([
            'admin_id' => $admin['id'],
            'action_type' => 'user_bulk_recalculate',
            'target_type' => 'user',
            'description' => "باز محاسبه گروهی آمار: {$successCount} موفق، {$failCount} ناموفق از {$totalUsers} کاربر ({$duration} ثانیه)",
            'new_data' => [
                'total' => $totalUsers,
                'success' => $successCount,
                'failed' => $failCount,
                'failed_users' => $failedUsers,
                'duration' => $duration,
            ],
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);

        $this->response->json([
            'success' => true,
            'message' => "باز محاسبه آمار {$totalUsers} کاربر انجام شد",
            'stats' => [
                'total' => $totalUsers,
                'success' => $successCount,
                'failed' => $failCount,
                'duration' => $duration,
            ]
        ]);
    }
    // در فایل src/Presentation/Controllers/AdminController.php

    public function recalculateUserStats(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();

        // فراخوانی متد جدید در AdminService که هم محاسبه می‌کند و هم لاگ می‌زند
        $this->adminService->recalculateAndLogUserStats(
            $userId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/users/' . $userId);
        } else {
            $request->flash('success', 'آمار و XP کاربر با موفقیت باز محاسبه و اصلاح شد.');
            $this->response->redirect('/admin/users/' . $userId);
        }
    }

    /**
     * به‌روزرسانی تنظیمات ضدتقلب
     */
    public function updateAntiCheatSettings(Request $request): void
    {
        $admin = $this->auth->user();
        $antiCheatService = new \Application\Services\AntiCheatService();

        $settings = $request->post('settings', []);

        foreach ($settings as $key => $value) {
            $antiCheatService->updateSetting($key, $value);
        }

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/settings?tab=anticheat&saved=1');
        } else {
            $request->flash('success', 'تنظیمات ضدتقلب با موفقیت به‌روزرسانی شد');
            $this->response->redirect('/admin/settings?tab=anticheat');
        }
    }
    /**
     * 🆕 علامت‌گذاری بازی مشکوک به عنوان بررسی شده
     */
    public function markSuspiciousGameReviewed(Request $request): void
    {
        $id = (int) $request->post('id');
        $admin = $this->auth->user();

        $db = \Core\Database::getInstance();

        $result = $db->update('suspicious_games', [
            'is_reviewed' => 1,
            'reviewed_by' => $admin['id'],
            'reviewed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);

        $this->response->json([
            'success' => $result,
            'message' => $result ? 'بازی به عنوان بررسی شده علامت‌گذاری شد' : 'خطا در انجام عملیات'
        ]);
    }

    /**
     * 🆕 حذف یک بازی از لیست بازی‌های مشکوک
     * نکته کلیدی: این متد از AdminService::deleteGame استفاده می‌کند تا 
     * فرآیند باز محاسبه آمار کاربران (RecalculateUserService::recalculateAll) به درستی اجرا شود.
     */
    public function deleteSuspiciousGame(Request $request, array $params): void
    {
        $sgId = (int) $params['id'];

        // ۱. پیدا کردن game_id مرتبط با این رکورد مشکوک
        $sg = $this->db->fetchOne("SELECT game_id FROM suspicious_games WHERE id = ?", [$sgId]);
        if (!$sg) {
            $request->flash('error', 'رکورد بازی مشکوک یافت نشد.');
            $this->response->redirect('/admin/suspicious-games');
            return;
        }

        $gameId = (int) $sg['game_id'];

        // ۲. فراخوانی متد استاندارد حذف که شامل باز محاسبه آمار و لاگ‌گیری است
        $result = $this->adminService->deleteGame(
            $gameId,
            $this->auth->user()['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'بازی با موفقیت حذف و آمار کاربران باز محاسبه شد.');
        } else {
            $request->flash('error', 'خطا در حذف بازی.');
        }

        $this->response->redirect('/admin/suspicious-games');
    }


    /**
     * 🆕 حذف دسته‌ای بازی‌ها از لیست بازی‌های مشکوک
     */
    public function bulkDeleteSuspiciousGames(Request $request): void
    {
        $sgIds = $request->post('sg_ids', []);
        if (empty($sgIds) || !is_array($sgIds)) {
            $request->flash('error', 'هیچ موردی انتخاب نشده است');
            $this->response->redirect('/admin/suspicious-games');
            return;
        }

        // استخراج game_id ها از جدول suspicious_games
        $gameIds = [];
        foreach ($sgIds as $sgId) {
            $sg = $this->db->fetchOne("SELECT game_id FROM suspicious_games WHERE id = ?", [(int)$sgId]);
            if ($sg) {
                $gameIds[] = (int) $sg['game_id'];
            }
        }

        if (empty($gameIds)) {
            $request->flash('error', 'هیچ بازی معتبری برای حذف یافت نشد');
            $this->response->redirect('/admin/suspicious-games');
            return;
        }

        $adminId = $this->auth->user()['id'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $deletedCount = 0;
        // فراخوانی متد استاندارد حذف برای هر بازی (که باز محاسبه را تضمین می‌کند)
        foreach ($gameIds as $gameId) {
            if ($this->adminService->deleteGame($gameId, $adminId, $ip, $userAgent)) {
                $deletedCount++;
            }
        }

        $request->flash('success', "{$deletedCount} بازی با موفقیت حذف و آمار کاربران باز محاسبه شد.");
        $this->response->redirect('/admin/suspicious-games');
    }
          // ============================================
    // 🆕 مدیریت سطوح (Player Levels)
    // ============================================

    /**
     * صفحه مدیریت سطوح
     */
    public function levels(Request $request): void
    {
        $admin = $this->auth->user();
        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();
        $levels = $gamificationRepo->getLevels();

        $html = View::make('pages.admin.levels', [
            'title' => 'مدیریت سطوح',
            'admin' => $admin,
            'currentPath' => '/admin/levels',
            'levels' => $levels,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * ایجاد سطح جدید
     */
    public function createLevel(Request $request): void
    {
        $admin = $this->auth->user();

        $data = [
            'level' => (int) $request->post('level'),
            'min_xp' => (int) $request->post('min_xp'),
            'max_xp' => (int) $request->post('max_xp'),
            'title' => trim($request->post('title', '')),
            'color' => trim($request->post('color', '#6366f1')),
            'icon' => trim($request->post('icon', '⭐')),
        ];

        // ✅ فقط یک خط - تمام منطق در AdminService است
        $result = $this->adminService->createLevel(
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result['success']) {
            $request->flash('success', $result['message']);
        } else {
            $errorMessage = implode(' ', $result['errors'] ?? ['خطا در ایجاد سطح']);
            $request->flash('error', $errorMessage);
        }

        $this->response->redirect('/admin/levels');
    }

    /**
     * ویرایش سطح
     */
    public function updateLevel(Request $request, array $params): void
    {

        $id = (int) $params['id'];
        $admin = $this->auth->user();

        // 🆕 فیلد level حذف شد چون در فرم disabled است و نباید تغییر کند
        $data = [
            'min_xp' => (int) $request->post('min_xp'),
            'max_xp' => (int) $request->post('max_xp'),
            'title' => trim($request->post('title', '')),
            'color' => trim($request->post('color', '#6366f1')),
            'icon' => trim($request->post('icon', '⭐')),
        ];

        $result = $this->adminService->updateLevel(
            $id,
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result['success']) {
            $request->flash('success', 'سطح با موفقیت ویرایش شد.');
        } else {
            $errors = $result['errors'] ?? [];
            $errorMsg = !empty($errors) ? implode(' ', $errors) : 'خطا در ویرایش سطح';
            $request->flash('error', $errorMsg);
        }

        // if ($result['success']) {
        //     $request->flash('success', $result['message']);
        // } else {
        //     // 🆕 تبدیل آرایه خطاها به رشته
        //     $errorMessages = is_array($result['errors'])
        //         ? implode(' | ', $result['errors'])
        //         : ($result['errors'] ?? 'خطای ناشناخته');
        //     $request->flash('error', $errorMessages);
        // }


        $this->response->redirect('/admin/levels');
    }

    /**
     * حذف سطح
     */
    public function deleteLevel(Request $request, array $params): void
    {
        $id = (int) $params['id'];
        $admin = $this->auth->user();
        $gamificationRepo = new \Infrastructure\Repositories\GamificationRepository();

        try {
            $level = $gamificationRepo->getLevelById($id);
            $result = $gamificationRepo->deleteLevel($id);

            if ($result) {
                // 🆕 لاگ‌گیری صحیح با AdminRepository
                $this->adminRepo->createLog([
                    'admin_id' => $admin['id'],
                    'action_type' => 'level_delete',
                    'target_type' => 'level',
                    'target_id' => $id,
                    'description' => "حذف سطح {$level['level']}: {$level['title']}",
                    'old_data' => $level,
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                ]);
                $request->flash('success', 'سطح با موفقیت حذف شد.');
            } else {
                $request->flash('error', 'خطا در حذف سطح.');
            }
        } catch (\Exception $e) {
            $request->flash('error', $e->getMessage());
        }

        $this->response->redirect('/admin/levels');
    }
        // ============================================
    // 🆕 مدیریت نشان‌ها (Achievements CRUD)
    // ============================================

    /**
     * صفحه لیست نشان‌ها (بازنویسی متد موجود)
     */
    public function achievements(Request $request): void
    {
        $admin = $this->auth->user();
        $achievements = $this->adminService->getAchievementsForAdmin();

        $html = View::make('pages.admin.achievements', [
            'title' => 'مدیریت نشان‌ها',
            'admin' => $admin,
            'currentPath' => '/admin/achievements',
            'achievements' => $achievements,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * ایجاد نشان جدید
     */
    public function createAchievement(Request $request): void
    {
        $admin = $this->auth->user();

        $data = [
            'code' => trim($request->post('code', '')),
            'name' => trim($request->post('name', '')),
            'description' => $request->post('description', ''),
            'icon' => $request->post('icon', '🏅'),
            'category' => $request->post('category', 'general'),
            'rarity' => $request->post('rarity', 'common'),
            'xp_reward' => (int) $request->post('xp_reward', 10),
            'condition_type' => $request->post('condition_type', 'total_games'),
            'condition_value' => (int) $request->post('condition_value', 1),
            'is_active' => (int) $request->post('is_active', 1),
        ];

        // اعتبارسنجی
        if (empty($data['code']) || empty($data['name'])) {
            $request->flash('error', 'کد و نام نشان الزامی هستند');
            $this->response->redirect('/admin/achievements');
            return;
        }

        $result = $this->adminService->createAchievementWithLog(
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'نشان با موفقیت ایجاد شد');
        } else {
            $request->flash('error', 'خطا در ایجاد نشان (ممکن است کد تکراری باشد)');
        }

        $this->response->redirect('/admin/achievements');
    }

    /**
     * ویرایش نشان (بازنویسی متد موجود)
     */
    public function updateAchievement(Request $request, array $params): void
    {
        $achievementId = (int) $params['id'];
        $admin = $this->auth->user();

        $data = [
            'code' => trim($request->post('code', '')),
            'name' => trim($request->post('name', '')),
            'description' => $request->post('description', ''),
            'icon' => $request->post('icon', '🏅'),
            'category' => $request->post('category', 'general'),
            'rarity' => $request->post('rarity', 'common'),
            'xp_reward' => (int) $request->post('xp_reward', 10),
            'condition_type' => $request->post('condition_type', 'total_games'),
            'condition_value' => (int) $request->post('condition_value', 1),
            'is_active' => (int) $request->post('is_active', 1),
        ];

        // اعتبارسنجی
        if (empty($data['code']) || empty($data['name'])) {
            $request->flash('error', 'کد و نام نشان الزامی هستند');
            $this->response->redirect('/admin/achievements');
            return;
        }

        $result = $this->adminService->updateAchievementWithLog(
            $achievementId,
            $data,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'نشان با موفقیت ویرایش شد');
        } else {
            $request->flash('error', 'خطا در ویرایش نشان');
        }

        $this->response->redirect('/admin/achievements');
    }

    /**
     * حذف نشان
     */
    public function deleteAchievement(Request $request, array $params): void
    {
        $achievementId = (int) $params['id'];
        $admin = $this->auth->user();

        $result = $this->adminService->deleteAchievementWithLog(
            $achievementId,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'نشان با موفقیت حذف شد');
        } else {
            $request->flash('error', 'خطا در حذف نشان');
        }

        $this->response->redirect('/admin/achievements');
    }

    /**
     * تغییر وضعیت فعال/غیرفعال نشان
     */
    public function toggleAchievementActive(Request $request, array $params): void
    {
        $achievementId = (int) $params['id'];
        $isActive = (int) $request->post('is_active', 0);
        $admin = $this->auth->user();

        $result = $this->adminService->toggleAchievementActiveWithLog(
            $achievementId,
            $isActive,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($result) {
            $request->flash('success', 'وضعیت نشان تغییر کرد');
        } else {
            $request->flash('error', 'خطا در تغییر وضعیت');
        }

        $this->response->redirect('/admin/achievements');
    }
    // ============================================
    // 🆕 مجوزهای بازی و ریست پسورد
    // ============================================

    public function banCreateGame(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();
        $this->adminService->banFromCreatingGame($userId, $admin['id'], $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null);
        $request->flash('success', 'مجوز ساخت بازی سلب شد.');
        $this->response->redirect('/admin/users/' . $userId);
    }

    public function allowCreateGame(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();
        $this->adminService->allowCreateGame($userId, $admin['id'], $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null);
        $request->flash('success', 'مجوز ساخت بازی اعطا شد.');
        $this->response->redirect('/admin/users/' . $userId);
    }

    public function banJoinGame(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();
        $this->adminService->banFromJoiningGame($userId, $admin['id'], $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null);
        $request->flash('success', 'مجوز شرکت در بازی سلب شد (ساخت بازی هم سلب شد).');
        $this->response->redirect('/admin/users/' . $userId);
    }

    public function allowJoinGame(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();
        $this->adminService->allowJoinGame($userId, $admin['id'], $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null);
        $request->flash('success', 'مجوز شرکت در بازی اعطا شد.');
        $this->response->redirect('/admin/users/' . $userId);
    }

    public function resetPassword(Request $request, array $params): void
    {
        $userId = (int) $params['id'];
        $admin = $this->auth->user();
        if ($userId === $admin['id']) {
            $request->flash('error', 'نمی‌توانید پسورد خودتان را ریست کنید.');
            $this->response->redirect('/admin/users/' . $userId);
            return;
        }
        $this->adminService->resetUserPassword($userId, $admin['id'], $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null);
        $request->flash('success', 'پسورد کاربر به 123456 ریست شد.');
        $this->response->redirect('/admin/users/' . $userId);
    }
    // ============================================
// 🆕 مدیریت اعلان‌ها (Notifications)
// ============================================

    /**
     * لیست اعلان‌ها
     */
    public function notifications(Request $request): void
    {
        $admin = $this->auth->user();
        $page = max(1, (int) $request->get('page', 1));

        $filters = [
            'user_id' => $request->get('user_id'),
            'type' => $request->get('type'),
            'is_read' => $request->get('is_read'),
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
        ];

        $result = $this->adminService->getNotifications($filters, $page, 50);

        $html = View::make('pages.admin.notifications', [
            'title' => 'مدیریت اعلان‌ها',
            'admin' => $admin,
            'currentPath' => '/admin/notifications',
            'notifications' => $result['notifications'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['perPage'],
            'totalPages' => $result['totalPages'],
            'filters' => $filters,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * حذف اعلان‌های قدیمی
     */
    public function deleteOldNotifications(Request $request): void
    {
        $daysOld = (int) $request->post('days_old', 30);
        $admin = $this->auth->user();
        $deletedCount = $this->adminService->deleteOldNotifications(
            $daysOld,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // 🆕 اصلاح: ریدایرکت به cleanup به جای notifications
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, '/admin/cleanup') !== false) {
            $request->flash('success', "{$deletedCount} اعلان قدیمی حذف شد.");
            $this->response->redirect('/admin/cleanup');
        } else {
            $request->flash('success', "{$deletedCount} اعلان قدیمی حذف شد.");
            $this->response->redirect('/admin/notifications');
        }
    }

    /**
     * حذف همه اعلان‌ها
     */
    public function deleteAllNotifications(Request $request): void
    {
        $admin = $this->auth->user();
        $deletedCount = $this->adminService->deleteAllNotifications(
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        // 🆕 اصلاح: ریدایرکت به cleanup به جای notifications
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, '/admin/cleanup') !== false) {
            $request->flash('success', "همه اعلان‌ها ({$deletedCount} مورد) حذف شدند.");
            $this->response->redirect('/admin/cleanup');
        } else {
            $request->flash('success', "همه اعلان‌ها ({$deletedCount} مورد) حذف شدند.");
            $this->response->redirect('/admin/notifications');
        }
    }

    /**
     * خروجی اکسل اعلان‌ها
     */
    public function exportNotifications(Request $request): void
    {
        $exportService = new \Application\Services\ExportService();

        $filters = [
            'user_id' => $request->get('user_id'),
            'type' => $request->get('type'),
            'is_read' => $request->get('is_read'),
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
        ];

        $exportService->exportNotifications($filters);
    }

// ============================================
// 🆕 پاک‌سازی جداول لاگ
// ============================================

    /**
     * صفحه مدیریت پاک‌سازی
     */
    public function cleanup(Request $request): void
    {
        $admin = $this->auth->user();
        $stats = $this->adminService->getLogsStats();

        $html = View::make('pages.admin.cleanup', [
            'title' => 'پاک‌سازی دیتابیس',
            'admin' => $admin,
            'currentPath' => '/admin/cleanup',
            'stats' => $stats,
        ], 'admin');

        $this->response->html($html);
    }

    /**
     * حذف لاگ‌های ادمین قدیمی
     */
    public function deleteOldAdminLogs(Request $request): void
    {
        $daysOld = (int) $request->post('days_old', 30);
        $admin = $this->auth->user();

        $deletedCount = $this->adminService->deleteOldAdminLogs(
            $daysOld,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );
        // 🆕 تشخیص صفحه مبدأ برای ریدایرکت هوشمند
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $redirectTo = '/admin/cleanup'; // پیش‌فرض

        if (strpos($referer, '/admin/logs') !== false) {
            $redirectTo = '/admin/logs';
        }
        $request->flash('success', "{$deletedCount} لاگ ادمین قدیمی حذف شد.");
        $this->response->redirect($redirectTo);
    }

    /**
     * حذف همه لاگ‌های ادمین
     */
    public function deleteAllAdminLogs(Request $request): void
    {
        $admin = $this->auth->user();

        $deletedCount = $this->adminService->deleteAllAdminLogs(
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $request->flash('success', "همه لاگ‌های ادمین ({$deletedCount} مورد) حذف شدند.");
        $this->response->redirect('/admin/cleanup');
    }

    /**
     * حذف referee_actions_log قدیمی
     */
    public function deleteOldRefereeActionsLog(Request $request): void
    {
        $daysOld = (int) $request->post('days_old', 30);
        $admin = $this->auth->user();

        $deletedCount = $this->adminService->deleteOldRefereeActionsLog(
            $daysOld,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $request->flash('success', "{$deletedCount} لاگ داور قدیمی حذف شد.");
        $this->response->redirect('/admin/cleanup');
    }

    /**
     * حذف همه referee_actions_log
     */
    public function deleteAllRefereeActionsLog(Request $request): void
    {
        $admin = $this->auth->user();

        $deletedCount = $this->adminService->deleteAllRefereeActionsLog(
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $request->flash('success', "همه لاگ‌های داور ({$deletedCount} مورد) حذف شدند.");
        $this->response->redirect('/admin/cleanup');
    }

    /**
     * حذف sse_events قدیمی
     */
    public function deleteOldSseEvents(Request $request): void
    {
        $hoursOld = (int) $request->post('hours_old', 24);
        $admin = $this->auth->user();

        $deletedCount = $this->adminService->deleteOldSseEvents(
            $hoursOld,
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $request->flash('success', "{$deletedCount} رویداد SSE قدیمی حذف شد.");
        $this->response->redirect('/admin/cleanup');
    }

    /**
     * حذف همه sse_events
     */
    public function deleteAllSseEvents(Request $request): void
    {
        $admin = $this->auth->user();

        $deletedCount = $this->adminService->deleteAllSseEvents(
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $request->flash('success', "همه رویدادهای SSE ({$deletedCount} مورد) حذف شدند.");
        $this->response->redirect('/admin/cleanup');
    }


    /**
     * علامت‌گذاری همه اعلان‌ها به عنوان خوانده شده
     */
    public function markAllNotificationsRead(Request $request): void
    {
        $admin = $this->auth->user();

        $updatedCount = $this->adminService->markAllNotificationsAsRead(
            $admin['id'],
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if ($request->isHtmx()) {
            $this->response->htmxRedirect('/admin/notifications');
        } else {
            $request->flash('success', "{$updatedCount} اعلان به عنوان خوانده شده علامت‌گذاری شد.");
            $this->response->redirect('/admin/notifications');
        }
    }


    // ============================================
// 🎵 مدیریت صداهای SSE
// ============================================

    /**
     * اسکن داینامیک پوشه صداها (پشتیبانی از همه فرمت‌ها)
     */
    private function getAvailableSounds(): array
    {
        $soundsDir = PUBLIC_PATH . '/assets/sounds';
        $sounds = [];
        $extensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];

        if (!is_dir($soundsDir)) {
            return $sounds;
        }

        $files = scandir($soundsDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions)) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                $sounds[$name] = [
                    'filename' => $file,
                    'path' => '/assets/sounds/' . $file,
                    'extension' => $ext,
                    'size' => filesize($soundsDir . '/' . $file),
                ];
            }
        }

        // مرتب‌سازی الفبایی
        ksort($sounds);

        return $sounds;
    }

    /**
     * 🎵 تعریف رویدادهای SSE با متادیتا
     */
    private function getSSEEventsDefinition(): array
    {
        return [
            'game_started' => [
                'label' => 'شروع بازی',
                'icon' => '🎮',
                'description' => 'وقتی بازی شروع می‌شود',
                'color' => 'green',
            ],
            'round_recorded' => [
                'label' => 'ثبت دور',
                'icon' => '📊',
                'description' => 'وقتی یک دور ثبت می‌شود (تماشاچیان)',
                'color' => 'blue',
            ],
            'round_undone' => [
                'label' => 'لغو دور',
                'icon' => '↩️',
                'description' => 'وقتی آخرین دور لغو می‌شود',
                'color' => 'yellow',
            ],
            'game_finished' => [
                'label' => 'پایان بازی',
                'icon' => '🏁',
                'description' => 'وقتی بازی تمام می‌شود',
                'color' => 'purple',
            ],
            'game_status_changed' => [
                'label' => 'تغییر وضعیت بازی',
                'icon' => '🔄',
                'description' => 'توقف یا ادامه بازی (زیرمجموعه دارد)',
                'color' => 'orange',
                'children' => [
                    'paused' => [
                        'label' => 'توقف بازی',
                        'icon' => '⏸️',
                        'description' => 'وقتی بازی متوقف می‌شود',
                    ],
                    'resumed' => [
                        'label' => 'ادامه بازی',
                        'icon' => '▶️',
                        'description' => 'وقتی بازی ادامه می‌یابد',
                    ],
                ],
            ],
            'score_updated' => [
                'label' => 'به‌روزرسانی امتیاز',
                'icon' => '⭐',
                'description' => 'وقتی امتیاز تغییر می‌کند',
                'color' => 'yellow',
            ],
            'notification' => [
                'label' => 'نوتیفیکیشن کاربر',
                'icon' => '🔔',
                'description' => 'نوتیفیکیشن‌های شخصی (دستاوردها، سطح و...)',
                'color' => 'pink',
            ],
            'system_message' => [
                'label' => 'پیام سیستمی',
                'icon' => '📢',
                'description' => 'پیام‌های عمومی سیستم',
                'color' => 'indigo',
            ],
        ];
    }

    /**
     * 🆕 بازمحاسبه القاب برای همه کاربران (بدون تغییر سایر آمار)
     */
    public function recalculateAllTitles(Request $request): void
    {
        // فقط super_admin اجازه دارد
        if (!$this->auth->isSuperAdmin()) {
            $this->response->json([
                'success' => false,
                'error' => 'فقط مدیر ارشد می‌تواند این عملیات را انجام دهد'
            ]);
            return;
        }

        $db = \Core\Database::getInstance();
        $gamificationService = new \Application\Services\GamificationService();

        // دریافت همه کاربران فعال
        $users = $db->fetchAll("SELECT id FROM users WHERE status = 'active'");
        $totalUsers = count($users);
        $successCount = 0;
        $failCount = 0;
        $failedUsers = [];

        foreach ($users as $user) {
            try {
                $result = $gamificationService->checkAndUpdateTitles((int)$user['id']);
                $successCount++;
            } catch (\Throwable $e) {
                $failCount++;
                $failedUsers[] = $user['id'];
                error_log("Error recalculating titles for user {$user['id']}: " . $e->getMessage());
            }
        }

        // ثبت لاگ
        $admin = $this->auth->user();
        $this->adminRepo->createLog([
            'admin_id' => $admin['id'],
            'action_type' => 'title_recalculate',
            'target_type' => 'title',
            'description' => "بازمحاسبه القاب برای {$totalUsers} کاربر (موفق: {$successCount}، ناموفق: {$failCount})",
            'new_data' => [
                'total' => $totalUsers,
                'success' => $successCount,
                'failed' => $failCount,
                'failed_users' => $failedUsers,
            ],
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);

        $this->response->json([
            'success' => true,
            'message' => "بازمحاسبه القاب برای {$totalUsers} کاربر انجام شد. موفق: {$successCount}، ناموفق: {$failCount}",
            'stats' => [
                'total' => $totalUsers,
                'success' => $successCount,
                'failed' => $failCount,
            ]
        ]);
    }
}
