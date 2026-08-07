<?php

/**
 * 🔄 Recalculate Stats - باز محاسبه آمار همه کاربران
 * 
 * استفاده:
 *   php recalculate-all-users.php
 *   php recalculate-all-users.php --batch=50
 *   php recalculate-all-users.php --user=5        (فقط یک کاربر خاص)
 *   php recalculate-all-users.php --dry-run        (فقط نمایش، بدون تغییر)
 */

// جلوگیری از اجرا از طریق وب
if (php_sapi_name() !== 'cli') {
    die("❌ این اسکریپت فقط از طریق CLI قابل اجرا است.\n");
}

// تنظیم Timezone
date_default_timezone_set('Asia/Tehran');

// تنظیم مسیرها
define('ROOT_PATH', __DIR__);
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Autoloader
require_once SRC_PATH . '/Core/Autoloader.php';
Core\Autoloader::register();

// شروع Session (برای برخی سرویس‌ها لازم است)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// 🎨 توابع کمکی برای خروجی رنگی
// ============================================
function colorize(string $text, string $color): string
{
    $colors = [
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'bold' => "\033[1m",
        'reset' => "\033[0m",
    ];
    return ($colors[$color] ?? '') . $text . $colors['reset'];
}

function printLine(string $text, string $color = 'white'): void
{
    echo colorize($text, $color) . "\n";
}

function printSuccess(string $text): void
{
    printLine("✅ " . $text, 'green');
}
function printError(string $text): void
{
    printLine("❌ " . $text, 'red');
}
function printWarning(string $text): void
{
    printLine("⚠️  " . $text, 'yellow');
}
function printInfo(string $text): void
{
    printLine("ℹ️  " . $text, 'cyan');
}

function printProgress(int $current, int $total, string $nickname): void
{
    $percent = round(($current / $total) * 100, 1);
    $bar = str_repeat('█', (int)($percent / 2)) . str_repeat('░', 50 - (int)($percent / 2));
    echo "\r  [{$bar}] {$percent}% ({$current}/{$total}) - {$nickname}";
    if ($current === $total) echo "\n";
}

// ============================================
// 📥 خواندن آرگومان‌ها
// ============================================
$options = getopt('', ['batch:', 'user:', 'dry-run', 'help']);

if (isset($options['help'])) {
    printLine("\n🔄 Recalculate Stats Script\n", 'bold');
    printLine("استفاده:");
    printLine("  php recalculate-all-users.php                  باز محاسبه همه کاربران");
    printLine("  php recalculate-all-users.php --batch=50      پردازش گروهی ۵۰ تایی");
    printLine("  php recalculate-all-users.php --user=5        فقط کاربر با ID=5");
    printLine("  php recalculate-all-users.php --dry-run       فقط نمایش، بدون تغییر");
    printLine("  php recalculate-all-users.php --help          این راهنما");
    exit(0);
}

$batchSize = isset($options['batch']) ? (int)$options['batch'] : 100;
$specificUser = isset($options['user']) ? (int)$options['user'] : null;
$dryRun = isset($options['dry-run']);

// ============================================
// 🚀 شروع عملیات
// ============================================
printLine("\n" . str_repeat('=', 60), 'cyan');
printLine("🔄 UNO Tracker - Recalculate All Users Stats", 'bold');
printLine(str_repeat('=', 60) . "\n", 'cyan');

if ($dryRun) {
    printWarning("حالت DRY RUN فعال است - هیچ تغییری اعمال نخواهد شد\n");
}

try {
    $db = \Core\Database::getInstance();
    $recalcService = new \Application\Services\RecalculateUserService();
    $adminRepo = new \Infrastructure\Repositories\AdminRepository();

    // 🆕 گرفتن لیست کاربران
    if ($specificUser) {
        $users = $db->fetchAll(
            "SELECT id, nickname FROM users WHERE id = ? AND status != 'banned'",
            [$specificUser]
        );
        printInfo("پردازش کاربر خاص: ID={$specificUser}");
    } else {
        $users = $db->fetchAll(
            "SELECT id, nickname FROM users WHERE status != 'banned' ORDER BY id ASC"
        );
    }

    $totalUsers = count($users);

    if ($totalUsers === 0) {
        printError("هیچ کاربری یافت نشد!");
        exit(1);
    }

    printSuccess("{$totalUsers} کاربر برای پردازش یافت شد");
    printInfo("Batch Size: {$batchSize}");
    printLine("");

    // 🆕 آمار عملیات
    $stats = [
        'processed' => 0,
        'success' => 0,
        'failed' => 0,
        'skipped' => 0,
        'total_xp_added' => 0,
        'start_time' => microtime(true),
    ];

    // 🆕 پردازش کاربران
    foreach ($users as $index => $user) {
        $userId = (int)$user['id'];
        $nickname = $user['nickname'] ?? "User#{$userId}";

        printProgress($index + 1, $totalUsers, $nickname);

        if ($dryRun) {
            $stats['skipped']++;
            continue;
        }

        try {
            // گرفتن XP قبلی برای محاسبه تفاوت
            $oldXpData = $db->fetchOne(
                "SELECT total_xp FROM user_xp WHERE user_id = ?",
                [$userId]
            );
            $oldXp = (int)($oldXpData['total_xp'] ?? 0);

            // باز محاسبه
            $recalcService->recalculateAll($userId);

            // گرفتن XP جدید
            $newXpData = $db->fetchOne(
                "SELECT total_xp FROM user_xp WHERE user_id = ?",
                [$userId]
            );
            $newXp = (int)($newXpData['total_xp'] ?? 0);
            $xpDiff = $newXp - $oldXp;

            $stats['success']++;
            $stats['total_xp_added'] += $xpDiff;

            // لاگ به admin_logs (با admin_id = 1 برای سیستم)
            $adminRepo->createLog([
                'admin_id' => 1, // سیستم
                'action_type' => 'user_stats_recalculated',
                'target_type' => 'user',
                'target_id' => $userId,
                'description' => "باز محاسبه آمار توسط اسکریپت گروهی (XP تغییر: " . ($xpDiff >= 0 ? "+{$xpDiff}" : $xpDiff) . ")",
                'new_data' => ['old_xp' => $oldXp, 'new_xp' => $newXp, 'diff' => $xpDiff],
                'ip_address' => 'CLI_SCRIPT',
                'user_agent' => 'RecalculateAllUsers',
            ]);
        } catch (\Throwable $e) {
            $stats['failed']++;
            printLine("\n  ❌ خطا برای {$nickname}: " . $e->getMessage(), 'red');
            error_log("Recalculate error for user {$userId}: " . $e->getMessage());
        }

        $stats['processed']++;

        // 🆕 استراحت بین batch ها برای جلوگیری از فشار روی DB
        if ($batchSize > 0 && ($index + 1) % $batchSize === 0) {
            printInfo("  💤 استراحت ۱ ثانیه‌ای بعد از {$batchSize} کاربر...");
            sleep(1);
        }
    }

    // ============================================
    // 📊 نمایش آمار نهایی
    // ============================================
    $duration = round(microtime(true) - $stats['start_time'], 2);

    printLine("\n" . str_repeat('=', 60), 'cyan');
    printLine("📊 آمار نهایی", 'bold');
    printLine(str_repeat('=', 60), 'cyan');

    printSuccess("کل کاربران پردازش‌شده: {$stats['processed']}");
    printSuccess("موفق: {$stats['success']}");
    if ($stats['failed'] > 0) {
        printError("ناموفق: {$stats['failed']}");
    }
    if ($stats['skipped'] > 0) {
        printWarning("رد شده (dry-run): {$stats['skipped']}");
    }
    printInfo("مجموع تغییر XP: " . ($stats['total_xp_added'] >= 0 ? "+{$stats['total_xp_added']}" : $stats['total_xp_added']));
    printInfo("مدت زمان: {$duration} ثانیه");

    printLine("\n🎉 عملیات با موفقیت به پایان رسید!\n", 'green');

    exit(0);
} catch (\Throwable $e) {
    printError("خطای بحرانی: " . $e->getMessage());
    printError("File: " . $e->getFile() . ":" . $e->getLine());
    printLine($e->getTraceAsString(), 'yellow');
    exit(1);
}
