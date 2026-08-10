<?php

declare(strict_types=1);
// ============================================
// UNO Tracker - Application Entry Point
// ============================================

// نمایش خطاها در حالت Development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// تنظیم Timezone
date_default_timezone_set('Asia/Tehran');

// تعریف ثابت‌های مسیر
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('PUBLIC_PATH', __DIR__);

// Autoloader
require_once SRC_PATH . '/Core/Autoloader.php';
Core\Autoloader::register();

// 🆕 لود توابع کمکی Debug (dd, dump, d, sql, trace)
require_once SRC_PATH . '/Core/Helpers.php';

// ============================================
// 🆕 تنظیمات Session (باید قبل از session_start باشد!)
// ============================================
$settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
$sessionLifetime = $settingsRepo->get('session_lifetime', 30); // دقیقه
$sessionDuration = $sessionLifetime * 60; // تبدیل به ثانیه
// 🆕 ۱. تنظیم مسیر ذخیره session به پوشه پروژه (مهم برای هاست اشتراکی)
$sessionPath = ROOT_PATH . '/storage/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);

// 🆕 ۲. تنظیم garbage collection
if ($sessionDuration > 0) {
    ini_set('session.gc_maxlifetime', (string) $sessionDuration);
}

// 🆕 ۳. کاهش احتمال اجرای GC (هر ۱۰۰۰ درخواست یک بار)
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '1000');

// 🆕 ۴. تنظیم cookie lifetime (قبل از session_start)
if ($sessionDuration > 0) {
    session_set_cookie_params([
        'lifetime' => $sessionDuration,
        'path' => '/',
        'secure' => false, // اگر HTTPS دارید، true کنید
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// 🆕 شروع Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// بارگذاری تنظیمات View
// ============================================
Core\View::setViewsPath(VIEWS_PATH);

// ============================================
// 🆕 اشتراک‌گذاری داده‌های سراسری View
// ============================================
$auth = new \Application\Services\AuthService();
if ($auth->check()) {
    $currentUser = $auth->user();
    $isAdmin = isset($currentUser['role']) && in_array($currentUser['role'], ['admin', 'super_admin']);

    $canCreate = true;
    if (isset($currentUser['id'])) {
        $db = \Core\Database::getInstance();
        $perm = $db->fetchOne("SELECT can_create_game FROM users WHERE id = ?", [$currentUser['id']]);
        $canCreate = $perm ? (bool)$perm['can_create_game'] : true;
    }
} else {
    $currentUser = null;
    $isAdmin = false;
    $canCreate = false;
}

\Core\View::share('currentUser', $currentUser);
\Core\View::share('isAdmin', $isAdmin);
\Core\View::share('canCreate', $canCreate);
\Core\View::share('currentPath', $_SERVER['REQUEST_URI'] ?? '/');

// بارگذاری Listenerهای سیستم (فاز ۴)
$events = Core\EventDispatcher::getInstance();
$eventsLoader = require ROOT_PATH . '/config/events.php';
$eventsLoader($events);

// ایجاد Request
$request = new Core\Request();

// ایجاد Router
$router = new Core\Router();

// ============================================
// تعریف Routeها
// ============================================

// ============================================
// 1. Routeهای عمومی (بدون نیاز به لاگین)
// ============================================

// صفحه اصلی
$router->get('/', function ($request, $params) {
    $auth = new \Application\Services\AuthService();
    if ($auth->check()) {
        $response = new Core\Response();
        $response->redirect('/dashboard');
    } else {
        $response = new Core\Response();
        $response->redirect('/login');
    }
});

// Captcha Image
$router->get('/captcha', function ($request, $params) {
    \Core\Captcha::getSimpleImage();
});

// Captcha Refresh (HTML)
$router->get('/captcha/refresh', function ($request, $params) {
    echo \Core\Captcha::renderHTML();
    exit;
});
// 🆕 ارسال کد OTP
$router->post('/auth/send-otp', 'Presentation\Controllers\AuthController@sendOtp');
// ============================================
// 2. Routeهای احراز هویت (بدون نیاز به لاگین)
// ============================================

$router->get('/login', 'Presentation\Controllers\AuthController@showLogin');
$router->post('/login', 'Presentation\Controllers\AuthController@login');
$router->get('/register', 'Presentation\Controllers\AuthController@showRegister');
$router->post('/register', 'Presentation\Controllers\AuthController@register');
$router->post('/logout', 'Presentation\Controllers\AuthController@logout');
$router->get('/logout', 'Presentation\Controllers\AuthController@logout');

// ============================================
// 3. Routeهای پروفایل (نیاز به لاگین)
// ============================================

// پروفایل خود کاربر
$router->get('/profile', 'Presentation\Controllers\ProfileController@showOwn', [\Presentation\Middleware\AuthMiddleware::class]);
$router->get('/profile/edit', 'Presentation\Controllers\ProfileController@edit', [\Presentation\Middleware\AuthMiddleware::class]);
$router->post('/profile/update', 'Presentation\Controllers\ProfileController@update', [\Presentation\Middleware\AuthMiddleware::class]);
$router->post('/profile/avatar', 'Presentation\Controllers\ProfileController@uploadAvatar', [\Presentation\Middleware\AuthMiddleware::class]);

// 🆕 لیست بازیکنان
$router->get('/users', 'Presentation\Controllers\UserController@list', [\Presentation\Middleware\AuthMiddleware::class]);
// 🆕 مشاهده پروفایل کامل کاربر دیگر (جایگزین ProfileController@showOther)
$router->get('/users/{id}', 'Presentation\Controllers\UserController@show', [\Presentation\Middleware\AuthMiddleware::class]);

// 🆕 مشاهده پروفایل خلاصه کاربر (برای Drawer)
$router->get('/users/{id}/partial', 'Presentation\Controllers\UserController@partial', [\Presentation\Middleware\AuthMiddleware::class]);

// 🆕 مشاهده پروفایل خلاصه شرکت‌کننده (برای Drawer)
$router->get('/participants/{id}/profile-partial', 'Presentation\Controllers\ParticipantController@profilePartial', [\Presentation\Middleware\AuthMiddleware::class]);

// ============================================
// 4. Routeهای بازی (نیاز به لاگین)
// ============================================

$router->group('/game', function ($router) {

    // ایجاد بازی
    $router->get('/create', 'Presentation\Controllers\GameController@create');
    $router->post('', 'Presentation\Controllers\GameController@store');

    // لیست بازی‌های فعال
    $router->get('/active', 'Presentation\Controllers\GameController@activeGames');

    // نمایش بازی
    $router->get('/{id}', 'Presentation\Controllers\GameController@show');

    // ============================================
    // Routeهای داور (نیاز به Middleware داور)
    // ============================================

    $router->post('/{id}/start', 'Presentation\Controllers\RefereeController@start', [\Presentation\Middleware\RefereeMiddleware::class]);
    $router->post('/{id}/pause', 'Presentation\Controllers\RefereeController@pause', [\Presentation\Middleware\RefereeMiddleware::class]);
    $router->post('/{id}/resume', 'Presentation\Controllers\RefereeController@resume', [\Presentation\Middleware\RefereeMiddleware::class]);
    $router->post('/{id}/finish', 'Presentation\Controllers\RefereeController@finish', [\Presentation\Middleware\RefereeMiddleware::class]);
    $router->post('/{id}/cancel', 'Presentation\Controllers\RefereeController@cancel', [\Presentation\Middleware\RefereeMiddleware::class]);
    $router->post('/{id}/round', 'Presentation\Controllers\RefereeController@recordRound', [\Presentation\Middleware\RefereeMiddleware::class]);

    // 🆕 لغو آخرین دور
    $router->post('/{id}/undo-round', 'Presentation\Controllers\RefereeController@undoLastRound', [\Presentation\Middleware\RefereeMiddleware::class]);

    // 🆕 Route های ویرایش توسط داور
    $router->post('/{id}/edit-rounds', 'Presentation\Controllers\RefereeController@editRounds', [\Presentation\Middleware\RefereeMiddleware::class]);
    $router->post('/{id}/transfer-referee', 'Presentation\Controllers\RefereeController@transferReferee', [\Presentation\Middleware\RefereeMiddleware::class]);
    $router->post('/{id}/edit-score', 'Presentation\Controllers\RefereeController@editParticipantScore', [\Presentation\Middleware\RefereeMiddleware::class]);
    $router->post('/{id}/edit-wins', 'Presentation\Controllers\RefereeController@editParticipantWins', [\Presentation\Middleware\RefereeMiddleware::class]);


    // 🆕 پیش‌نمایش تیم‌ها
    $router->post('/preview-teams', 'Presentation\Controllers\GameController@previewTeams');
}, [\Presentation\Middleware\AuthMiddleware::class]);

// ============================================
// 🆕 TV Mode (نمایش در تلویزیون)
// ============================================

// ۱. صفحه لیست بازی‌ها
$router->get('/tv', 'Presentation\Controllers\TVController@list', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

// ۲. 🔥 مهم: Route جزئیات partial باید قبل از {id} بیاید
$router->get('/tv/{id}/partial', 'Presentation\Controllers\TVController@partial', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

// ۳. صفحه نمایش کامل بازی (با layout)
$router->get('/tv/{id}', 'Presentation\Controllers\TVController@show', [
    \Presentation\Middleware\AuthMiddleware::class,
]);
// ============================================
// 5. Routeهای داشبورد (نیاز به لاگین)
// ============================================

$router->get('/dashboard', 'Presentation\Controllers\DashboardController@index', [\Presentation\Middleware\AuthMiddleware::class]);

// APIهای داشبورد
$router->get('/dashboard/friends-comparison', 'Presentation\Controllers\DashboardController@friendsComparison', [\Presentation\Middleware\AuthMiddleware::class]);
$router->get('/dashboard/monthly-summary', 'Presentation\Controllers\DashboardController@monthlySummary', [\Presentation\Middleware\AuthMiddleware::class]);

// 🆕 لیست بازی‌ها (برای کاربران عادی)
$router->get('/games', 'Presentation\Controllers\GameController@list', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

// 🆕 AJAX: لیست بازیکنان
$router->get('/games/players', 'Presentation\Controllers\GameController@players', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

// 🆕 Routeهای دستاوردها
$router->get('/achievements', 'Presentation\Controllers\AchievementsController@index', [\Presentation\Middleware\AuthMiddleware::class]);
$router->get('/achievements/notifications', 'Presentation\Controllers\AchievementsController@notifications', [\Presentation\Middleware\AuthMiddleware::class]);
$router->post('/achievements/notifications/read', 'Presentation\Controllers\AchievementsController@markNotificationRead', [\Presentation\Middleware\AuthMiddleware::class]);
$router->post('/achievements/notifications/read-all', 'Presentation\Controllers\AchievementsController@markAllNotificationsRead', [\Presentation\Middleware\AuthMiddleware::class]);
$router->get('/achievements/notifications/unread-count', 'Presentation\Controllers\AchievementsController@unreadCount', [\Presentation\Middleware\AuthMiddleware::class]);

// ============================================
// 6. Routeهای تست (فقط برای Development)
// ============================================

// تست دیتابیس
$router->get('/test-db', function ($request, $params) {
    $response = new Core\Response();
    try {
        $db = Core\Database::getInstance();
        $cards = $db->fetchAll("SELECT * FROM cards WHERE is_active = 1 ORDER BY sort_order");
        $response->json([
            'success' => true,
            'count' => count($cards),
            'cards' => $cards
        ]);
    } catch (\Throwable $e) {
        $response->status(500)->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// تست Event Dispatcher
$router->get('/test-events', function ($request, $params) {
    $response = new Core\Response();
    $dispatcher = Core\EventDispatcher::getInstance();
    $results = [];

    $dispatcher->listen('test.event', function ($data) use (&$results) {
        $results[] = "Listener 1 received: " . json_encode($data);
    });

    $dispatcher->listen('test.event', function ($data) use (&$results) {
        $results[] = "Listener 2 received: " . json_encode($data);
    }, 10);

    $dispatcher->dispatch('test.event', ['message' => 'Hello World!']);

    $response->json([
        'success' => true,
        'results' => $results
    ]);
});

// ============================================
// Routeهای پنل مدیریت (نیاز به نقش ادمین)
// ============================================

$router->get('/admin', 'Presentation\Controllers\AdminController@dashboard', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 Routeهای Export (باید قبل از {id} باشند!)
$router->get('/admin/users/export', 'Presentation\Controllers\AdminController@exportUsers', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->get('/admin/games/export', 'Presentation\Controllers\AdminController@exportGames', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->get('/admin/logs/export', 'Presentation\Controllers\AdminController@exportLogs', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
$router->get('/admin/logs', 'Presentation\Controllers\AdminController@logs', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
// 🆕 Route های مدیریت اعلان‌ها
$router->get('/admin/notifications', 'Presentation\Controllers\AdminController@notifications', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/notifications/delete-old', 'Presentation\Controllers\AdminController@deleteOldNotifications', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/notifications/delete-all', 'Presentation\Controllers\AdminController@deleteAllNotifications', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->get('/admin/notifications/export', 'Presentation\Controllers\AdminController@exportNotifications', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
$router->post('/admin/notifications/mark-all-read', 'Presentation\Controllers\AdminController@markAllNotificationsRead', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 Route های پاک‌سازی
$router->get('/admin/cleanup', 'Presentation\Controllers\AdminController@cleanup', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cleanup/admin-logs/delete-old', 'Presentation\Controllers\AdminController@deleteOldAdminLogs', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cleanup/admin-logs/delete-all', 'Presentation\Controllers\AdminController@deleteAllAdminLogs', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
// 🆕 Route های اعلان‌ها در cleanup
$router->post('/admin/cleanup/notifications/delete-old', 'Presentation\Controllers\AdminController@deleteOldNotifications', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cleanup/notifications/delete-all', 'Presentation\Controllers\AdminController@deleteAllNotifications', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cleanup/referee-actions/delete-old', 'Presentation\Controllers\AdminController@deleteOldRefereeActionsLog', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cleanup/referee-actions/delete-all', 'Presentation\Controllers\AdminController@deleteAllRefereeActionsLog', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cleanup/sse-events/delete-old', 'Presentation\Controllers\AdminController@deleteOldSseEvents', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cleanup/sse-events/delete-all', 'Presentation\Controllers\AdminController@deleteAllSseEvents', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
// 🆕 Routeهای لیست
$router->get('/admin/users', 'Presentation\Controllers\AdminController@users', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->get('/admin/games', 'Presentation\Controllers\AdminController@games', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);



$router->get('/admin/settings', 'Presentation\Controllers\AdminController@settings', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 Route های مدیریت نشان‌ها
$router->get('/admin/achievements', 'Presentation\Controllers\AdminController@achievements', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/achievements/create', 'Presentation\Controllers\AdminController@createAchievement', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/achievements/{id}', 'Presentation\Controllers\AdminController@updateAchievement', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/achievements/{id}/toggle-active', 'Presentation\Controllers\AdminController@toggleAchievementActive', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/achievements/{id}/delete', 'Presentation\Controllers\AdminController@deleteAchievement', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 Route های مدیریت عناوین
$router->get('/admin/titles', 'Presentation\Controllers\AdminController@titles', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/titles/recalculate-titles', 'Presentation\Controllers\AdminController@recalculateAllTitles', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
$router->post('/admin/titles/create', 'Presentation\Controllers\AdminController@createTitle', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/titles/{id}', 'Presentation\Controllers\AdminController@updateTitle', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/titles/{id}/delete', 'Presentation\Controllers\AdminController@deleteTitle', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 Route های مدیریت سطوح
$router->get('/admin/levels', 'Presentation\Controllers\AdminController@levels', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/levels/create', 'Presentation\Controllers\AdminController@createLevel', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/levels/{id}', 'Presentation\Controllers\AdminController@updateLevel', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/levels/{id}/delete', 'Presentation\Controllers\AdminController@deleteLevel', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/users/{id}/recalculate-stats', 'Presentation\Controllers\AdminController@recalculateUserStats', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
// 🆕 Routeهای POST
$router->post('/admin/users/{id}/ban', 'Presentation\Controllers\AdminController@banUser', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/users/{id}/unban', 'Presentation\Controllers\AdminController@unbanUser', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/users/{id}/role', 'Presentation\Controllers\AdminController@changeRole', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/users/{id}/delete', 'Presentation\Controllers\AdminController@deleteUser', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
// 🆕 Route های مجوز بازی و ریست پسورد
$router->post('/admin/users/{id}/ban-create-game', 'Presentation\Controllers\AdminController@banCreateGame', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
$router->post('/admin/users/{id}/allow-create-game', 'Presentation\Controllers\AdminController@allowCreateGame', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
$router->post('/admin/users/{id}/ban-join-game', 'Presentation\Controllers\AdminController@banJoinGame', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
$router->post('/admin/users/{id}/allow-join-game', 'Presentation\Controllers\AdminController@allowJoinGame', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
$router->post('/admin/users/{id}/reset-password', 'Presentation\Controllers\AdminController@resetPassword', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
$router->post('/admin/games/{id}/delete', 'Presentation\Controllers\AdminController@deleteGame', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);


$router->post('/admin/settings', 'Presentation\Controllers\AdminController@updateSettings', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 Routeهای جزئیات (باید بعد از export باشند!)
$router->get('/admin/users/{id}', 'Presentation\Controllers\AdminController@userDetail', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 باز محاسبه گروهی آمار همه کاربران (AJAX)
$router->post('/admin/users/recalculate-all', 'Presentation\Controllers\AdminController@recalculateAllUsersBatch', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);


$router->get('/admin/games/{id}', 'Presentation\Controllers\AdminController@gameDetail', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 Route های مدیریت بازی‌ها
$router->post('/admin/games/{id}/status', 'Presentation\Controllers\AdminController@updateGameStatus', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/games/{id}/referee', 'Presentation\Controllers\AdminController@updateGameReferee', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/games/{id}/rounds', 'Presentation\Controllers\AdminController@updateGameRounds', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/games/{id}/add-participant', 'Presentation\Controllers\AdminController@addParticipant', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/games/{id}/remove-participant/{participant_id}', 'Presentation\Controllers\AdminController@removeParticipant', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/games/bulk', 'Presentation\Controllers\AdminController@bulkAction', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);
// 🆕 Route های مدیریت کارت‌ها
$router->get('/admin/cards', 'Presentation\Controllers\AdminController@cards', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cards/create', 'Presentation\Controllers\AdminController@createCard', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cards/{id}', 'Presentation\Controllers\AdminController@updateCard', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cards/{id}/toggle-active', 'Presentation\Controllers\AdminController@toggleCardActive', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/cards/{id}/delete', 'Presentation\Controllers\AdminController@deleteCard', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 Route های مدیریت انواع برد
$router->get('/admin/win-types', 'Presentation\Controllers\AdminController@winTypes', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/win-types/create', 'Presentation\Controllers\AdminController@createWinType', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/win-types/{id}', 'Presentation\Controllers\AdminController@updateWinType', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/win-types/{id}/toggle-active', 'Presentation\Controllers\AdminController@toggleWinTypeActive', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/win-types/{id}/delete', 'Presentation\Controllers\AdminController@deleteWinType', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// ============================================
// 🆕 Route های مدیریت بازی‌های مشکوک
// ============================================
$router->get('/admin/suspicious-games', 'Presentation\Controllers\AdminController@suspiciousGames', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

$router->post('/admin/suspicious-games/mark-reviewed', 'Presentation\Controllers\AdminController@markSuspiciousGameReviewed', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 این روت حیاتی برای حذف تکی و گروهی در صفحه بازی‌های مشکوک است
$router->post('/admin/suspicious-games/bulk-delete', 'Presentation\Controllers\AdminController@bulkDeleteSuspiciousGames', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);

// 🆕 Route مدیریت ضد تقلب (Alias برای دسترسی از سایدبار)
$router->get('/admin/anti-cheat', 'Presentation\Controllers\AdminController@suspiciousGames', [
    \Presentation\Middleware\AuthMiddleware::class,
    \Presentation\Middleware\AdminMiddleware::class,
]);


// ============================================
// Routeهای SSE (Real-time)
// ============================================

$router->get('/sse/game/{id}', 'Presentation\Controllers\SSEController@game', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

// 🆕 آخرین بازی فعال
$router->get('/game/latest-active', 'Presentation\Controllers\GameController@latestActive', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

// 🆕 آخرین بازی فعال (API برای AJAX)
$router->get('/api/game/latest-active', 'Presentation\Controllers\GameController@latestActive', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

$router->get('/sse/notifications', 'Presentation\Controllers\SSEController@notifications', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

$router->get('/sse/public', 'Presentation\Controllers\SSEController@publicStream');

$router->get('/sse/online-users', 'Presentation\Controllers\SSEController@onlineUsers', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

$router->post('/sse/heartbeat', 'Presentation\Controllers\SSEController@heartbeat', [
    \Presentation\Middleware\AuthMiddleware::class,
]);

// 🆕 Route SSE برای داور
$router->get('/sse/referee', 'Presentation\Controllers\SSEController@referee', [
    \Presentation\Middleware\AuthMiddleware::class,
]);
// 🆕 Route تست SSE
$router->get('/sse/test', 'Presentation\Controllers\SSEController@test');

// ============================================
// Dispatch درخواست
// ============================================

$router->dispatch($request);
