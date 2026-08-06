<?php

namespace Application\Services;

use Core\Database;
use Infrastructure\Repositories\AdminRepository;

class ExportService
{
    private Database $db;
    private AdminRepository $adminRepo;


    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->adminRepo = new AdminRepository();
    }

    /**
     * 🆕 Export به CSV با پشتیبانی از فارسی
     */
    private function exportToCsv(string $filename, array $headers, array $rows): void
    {
        // 🆕 تنظیم هدرها برای دانلود
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // 🆕 باز کردن output stream
        $output = fopen('php://output', 'w');

        // 🆕 اضافه کردن BOM برای UTF-8 (برای Excel)
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // 🆕 نوشتن هدرها
        fputcsv($output, $headers, ',');

        // 🆕 نوشتن داده‌ها
        foreach ($rows as $row) {
            fputcsv($output, $row, ',');
        }

        fclose($output);
        exit;
    }

    /**
     * Export کاربران
     */
    public function exportUsers(array $filters = []): void
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(nickname LIKE ? OR real_name LIKE ? OR phone LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }

        $whereClause = implode(' AND ', $where);

        $users = $this->db->fetchAll(
            "SELECT u.*,
                    (SELECT COUNT(*) FROM games g 
                     JOIN game_participants gp ON g.id = gp.game_id 
                     WHERE gp.user_id = u.id) as total_games,
                    (SELECT COUNT(*) FROM games g 
                     JOIN game_participants gp ON g.id = gp.game_id 
                     WHERE gp.user_id = u.id AND gp.is_winner = 1) as total_wins,
                    (SELECT total_xp FROM user_xp WHERE user_id = u.id) as total_xp,
                    (SELECT current_level FROM user_xp WHERE user_id = u.id) as current_level
             FROM users u
             WHERE {$whereClause}
             ORDER BY u.created_at DESC",
            $params
        );

        $headers = [
            'شناسه',
            'نام مستعار',
            'نام واقعی',
            'شماره تماس',
            'نقش',
            'وضعیت',
            'کل بازی‌ها',
            'کل بردها',
            'نرخ برد (%)',
            'سطح',
            'امتیاز تجربه',
            'تاریخ عضویت',
            'آخرین بازدید',
        ];

        $rows = [];
        $statusLabels = ['active' => 'فعال', 'banned' => 'مسدود', 'pending' => 'در انتظار'];
        $roleLabels = ['user' => 'کاربر', 'admin' => 'مدیر', 'super_admin' => 'مدیر ارشد'];

        foreach ($users as $user) {
            $winRate = $user['total_games'] > 0
                ? round(($user['total_wins'] / $user['total_games']) * 100, 1)
                : 0;

            $rows[] = [
                $user['id'],
                $user['nickname'],
                $user['real_name'],
                $user['phone'],
                $roleLabels[$user['role']] ?? $user['role'],
                $statusLabels[$user['status']] ?? $user['status'],
                $user['total_games'],
                $user['total_wins'],
                $winRate,
                $user['current_level'] ?? 1,
                $user['total_xp'] ?? 0,
                date('Y-m-d H:i:s', strtotime($user['created_at'])),
                $user['last_seen_at'] ? date('Y-m-d H:i:s', strtotime($user['last_seen_at'])) : 'هرگز',
            ];
        }

        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
        $this->exportToCsv($filename, $headers, $rows);
    }

    /**
     * Export بازی‌ها
     */
    public function exportGames(array $filters = []): void
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(g.name LIKE ? OR g.id = ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = (int) $filters['search'];
        }

        if (!empty($filters['status'])) {
            $where[] = "g.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['mode'])) {
            $where[] = "g.game_mode = ?";
            $params[] = $filters['mode'];
        }

        $whereClause = implode(' AND ', $where);

        $games = $this->db->fetchAll(
            "SELECT g.*,
                    (SELECT COUNT(*) FROM game_participants gp WHERE gp.game_id = g.id) as total_players,
                    (SELECT u.nickname FROM users u WHERE u.id = g.referee_id) as referee_name,
                    (SELECT COALESCE(u.nickname, gp2.guest_name) 
                     FROM game_participants gp2
                     LEFT JOIN users u ON gp2.user_id = u.id
                     WHERE gp2.game_id = g.id AND gp2.is_winner = 1 
                     LIMIT 1) as winner_name
             FROM games g
             WHERE {$whereClause}
             ORDER BY g.created_at DESC",
            $params
        );

        $headers = [
            'شناسه',
            'نام بازی',
            'حالت',
            'داور',
            'برنده',
            'تعداد بازیکنان',
            'هدف برد',
            'دورهای بازی شده',
            'وضعیت',
            'الگوریتم گروه‌بندی',
            'تاریخ ایجاد',
            'تاریخ شروع',
            'تاریخ پایان',
        ];

        $rows = [];
        $statusLabels = [
            'pending' => 'در انتظار',
            'active' => 'در حال بازی',
            'paused' => 'متوقف',
            'finished' => 'پایان یافته',
            'cancelled' => 'لغو شده',
        ];
        $modeLabels = ['solo' => 'انفرادی', 'friendly' => 'تیمی'];

        foreach ($games as $game) {
            $rows[] = [
                $game['id'],
                $game['name'] ?: 'بدون نام',
                $modeLabels[$game['game_mode']] ?? $game['game_mode'],
                $game['referee_name'] ?? '-',
                $game['winner_name'] ?? '-',
                $game['total_players'],
                $game['target_wins'],
                $game['total_rounds_played'],
                $statusLabels[$game['status']] ?? $game['status'],
                $game['team_builder_algorithm'] ?? '-',
                date('Y-m-d H:i:s', strtotime($game['created_at'])),
                $game['started_at'] ? date('Y-m-d H:i:s', strtotime($game['started_at'])) : '-',
                $game['finished_at'] ? date('Y-m-d H:i:s', strtotime($game['finished_at'])) : '-',
            ];
        }

        $filename = 'games_' . date('Y-m-d_H-i-s') . '.csv';
        $this->exportToCsv($filename, $headers, $rows);
    }

    /**
     * Export لاگ‌ها
     */
    public function exportLogs(array $filters = []): void
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['admin_id'])) {
            $where[] = "al.admin_id = ?";
            $params[] = (int) $filters['admin_id'];
        }

        if (!empty($filters['action_type'])) {
            $where[] = "al.action_type = ?";
            $params[] = $filters['action_type'];
        }

        if (!empty($filters['target_type'])) {
            $where[] = "al.target_type = ?";
            $params[] = $filters['target_type'];
        }

        if (!empty($filters['from_date'])) {
            $where[] = "al.created_at >= ?";
            $params[] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $where[] = "al.created_at <= ?";
            $params[] = $filters['to_date'] . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $logs = $this->db->fetchAll(
            "SELECT al.*, u.nickname as admin_name
             FROM admin_logs al
             LEFT JOIN users u ON al.admin_id = u.id
             WHERE {$whereClause}
             ORDER BY al.created_at DESC",
            $params
        );

        $headers = [
            'شناسه',
            'ادمین',
            'نوع عملیات',
            'نوع هدف',
            'شناسه هدف',
            'توضیحات',
            'IP',
            'زمان',
        ];

        $rows = [];
        $actionLabels = [
            'user_ban' => 'مسدود کاربر',
            'user_unban' => 'فعال‌سازی کاربر',
            'user_delete' => 'حذف کاربر',
            'user_role_change' => 'تغییر نقش',
            'game_delete' => 'حذف بازی',
            'game_edit' => 'ویرایش بازی',
            'achievement_create' => 'ایجاد نشان',
            'achievement_edit' => 'ویرایش نشان',
            'achievement_delete' => 'حذف نشان',
            'setting_change' => 'تغییر تنظیمات',
            'login' => 'ورود',
            'logout' => 'خروج',
        ];

        foreach ($logs as $log) {
            $rows[] = [
                $log['id'],
                $log['admin_name'] ?? 'نامشخص',
                $actionLabels[$log['action_type']] ?? $log['action_type'],
                $log['target_type'] ?? '-',
                $log['target_id'] ?? '-',
                $log['description'] ?? '-',
                $log['ip_address'] ?? '-',
                date('Y-m-d H:i:s', strtotime($log['created_at'])),
            ];
        }

        $filename = 'logs_' . date('Y-m-d_H-i-s') . '.csv';
        $this->exportToCsv($filename, $headers, $rows);
    }
    /**
     * Export اعلان‌ها به CSV
     */
    public function exportNotifications(array $filters = []): void
    {
        $result = $this->adminRepo->getNotifications($filters, 10000, 0);
        $notifications = $result['notifications'];

        $filename = 'notifications_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM for UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($output, [
            'ID',
            'شناسه کاربر',
            'نام کاربر',
            'نوع',
            'عنوان',
            'پیام',
            'آیکون',
            'لینک',
            'وضعیت',
            'تاریخ ایجاد'
        ]);

        // Data
        foreach ($notifications as $notif) {
            fputcsv($output, [
                $notif['id'],
                $notif['user_id'],
                $notif['user_nickname'] ?? 'نامشخص',
                $notif['type'],
                $notif['title'],
                $notif['message'] ?? '',
                $notif['icon'] ?? '',
                $notif['link'] ?? '',
                $notif['is_read'] ? 'خوانده شده' : 'خوانده نشده',
                $notif['created_at']
            ]);
        }

        fclose($output);
        exit;
    }
}
