<?php
/**
 * 🔧 UNO Tracker - Auto Fixer v3.0
 * 
 * این اسکریپت با تنظیمات زیر کار می‌کند:
 * - جایگزینی مسیرهای مطلق (شروع با /) با asset() و url()
 * - جایگزینی error_log با log_message (قابل غیرفعال‌سازی)
 * - ایجاد بک‌آپ (قابل غیرفعال‌سازی)
 * - تولید گزارش HTML
 * 
 * ⚠️ قبل از اجرا از پروژه بک‌آپ کامل بگیرید.
 */

// ============================================
// 🛠️ تنظیمات کاربر (اینجا را ویرایش کنید)
// ============================================

/**
 * فعال/غیرفعال کردن بخش‌های مختلف
 */
const ENABLE_PATH_REPLACEMENT = true;   // جایگزینی مسیرها با asset() و url()
const ENABLE_ERROR_LOG_REPLACE = true;  // جایگزینی error_log با log_message
const ENABLE_BACKUP = true;             // ایجاد بک‌آپ از فایل‌های اصلاح‌شده

/**
 * پوشه‌های استثنا (اسکن نمی‌شوند)
 */
const EXCLUDE_DIRS = [
    'vendor',
    'storage',
    'backup_before_fix',
    'node_modules',
    '.git',
    'cache',
    'tmp',
    'logs',
    'tests',
];

/**
 * فایل‌های استثنا (اسکن نمی‌شوند)
 */
const EXCLUDE_FILES = [
    'auto-fix-paths.php',
    'fix_report.html',
    '.htaccess',
    'robots.txt',
];

/**
 * پسوندهای فایل برای اسکن
 */
const ALLOWED_EXTENSIONS = ['php', 'js'];

// ============================================
// 🚀 شروع اسکریپت (تغییر ندهید)
// ============================================

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(300);

define('ROOT', __DIR__);
define('BACKUP_DIR', ROOT . '/backup_before_fix');
define('REPORT_FILE', ROOT . '/fix_report.html');

class AutoFixer
{
    private array $stats = [
        'total_files' => 0,
        'modified_files' => 0,
        'path_replacements' => 0,
        'error_log_replacements' => 0,
        'errors' => 0,
        'skipped' => 0,
        'details' => [],
    ];

    private array $backupFiles = [];

    public function run(): void
    {
        echo "\n🔧 UNO Tracker Auto Fixer v3.0\n";
        echo "===============================\n\n";
        $this->showConfig();

        if (ENABLE_BACKUP && !is_dir(BACKUP_DIR)) {
            mkdir(BACKUP_DIR, 0755, true);
        }

        $this->scanDirectory(ROOT);

        $this->generateReport();
        $this->showSummary();
    }

    private function scanDirectory(string $dir): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) continue;
            if (!in_array($file->getExtension(), ALLOWED_EXTENSIONS)) continue;

            $path = $file->getRealPath();

            // بررسی استثناهای پوشه
            foreach (EXCLUDE_DIRS as $exclude) {
                if (strpos($path, DIRECTORY_SEPARATOR . $exclude . DIRECTORY_SEPARATOR) !== false) {
                    continue 2;
                }
            }

            // بررسی استثناهای فایل
            foreach (EXCLUDE_FILES as $excludeFile) {
                if (basename($path) === $excludeFile) {
                    continue 2;
                }
            }

            $this->processFile($path);
        }
    }

    private function processFile(string $filePath): void
    {
        $this->stats['total_files']++;

        try {
            $content = file_get_contents($filePath);
            $originalContent = $content;
            $changes = [];
            $modified = false;

            // ============================================
            // 1. جایگزینی error_log (اگر فعال باشد)
            // ============================================
            if (ENABLE_ERROR_LOG_REPLACE && in_array($filePath, $this->getPhpFiles())) {
                $count = 0;
                $content = preg_replace('/\berror_log\s*\(/i', 'log_message(', $content, -1, $count);
                if ($count > 0) {
                    $modified = true;
                    $this->stats['error_log_replacements'] += $count;
                    $changes[] = "error_log → log_message ({$count}x)";
                }
            }

            // ============================================
            // 2. جایگزینی مسیرها (اگر فعال باشد)
            // ============================================
            if (ENABLE_PATH_REPLACEMENT) {
                $type = $this->detectFileType($filePath);
                $patterns = $this->getPatterns($type);

                foreach ($patterns as $patternData) {
                    $count = 0;
                    $content = preg_replace(
                        $patternData['pattern'],
                        $patternData['replacement'],
                        $content,
                        -1,
                        $count
                    );
                    if ($count > 0) {
                        $modified = true;
                        $this->stats['path_replacements'] += $count;
                        $changes[] = "{$patternData['description']} ({$count}x)";
                    }
                }
            }

            if ($modified) {
                // ایجاد بک‌آپ
                if (ENABLE_BACKUP) {
                    $backupPath = BACKUP_DIR . '/' . basename($filePath) . '.bak';
                    if (!file_exists($backupPath)) {
                        file_put_contents($backupPath, $originalContent);
                        $this->backupFiles[] = $backupPath;
                    }
                }

                file_put_contents($filePath, $content);
                $this->stats['modified_files']++;
                $this->stats['details'][] = [
                    'file' => str_replace(ROOT, '', $filePath),
                    'changes' => $changes,
                ];

                echo "✅ " . basename($filePath) . " → " . implode(', ', $changes) . "\n";
            }

        } catch (Exception $e) {
            $this->stats['errors']++;
            echo "❌ Error: " . basename($filePath) . " - " . $e->getMessage() . "\n";
        }
    }

    private function detectFileType(string $path): string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return $ext === 'js' ? 'js' : 'php';
    }

    private function getPhpFiles(): array
    {
        // فقط برای error_log نیاز است، در عمل همه فایل‌های PHP را شامل می‌شود
        return [];
    }

    private function getPatterns(string $type): array
    {
        $patterns = [];

        if ($type === 'php') {
            $patterns = [
                [
                    'pattern' => '/(src)=["\']\/assets\/([^"\']+)["\']/',
                    'replacement' => '$1="<?= asset("$2") ?>"',
                    'description' => 'src asset',
                ],
                [
                    'pattern' => '/(href)=["\']\/assets\/([^"\']+)["\']/',
                    'replacement' => '$1="<?= asset("$2") ?>"',
                    'description' => 'href asset',
                ],
                [
                    'pattern' => '/action=["\']\/(?!assets|storage|http|#|javascript:)([^"\']+)["\']/',
                    'replacement' => 'action="<?= url("/$1") ?>"',
                    'description' => 'form action',
                ],
                [
                    'pattern' => '/href=["\']\/(?!assets|storage|http|#|javascript:)([^"\']+)["\']/',
                    'replacement' => 'href="<?= url("/$1") ?>"',
                    'description' => 'internal link',
                ],
                [
                    'pattern' => '/src=["\']\/storage\/([^"\']+)["\']/',
                    'replacement' => 'src="<?= url("/storage/$1") ?>"',
                    'description' => 'storage asset',
                ],
                [
                    'pattern' => '/hx-(post|get|put|delete)=["\']\/([^"\']+)["\']/',
                    'replacement' => 'hx-$1="<?= url("/$2") ?>"',
                    'description' => 'HTMX attribute',
                ],
                [
                    'pattern' => '/@click=["\']([^"\']*?)(fetch|axios|\\$fetch)\\s*\\(["\']\/([^"\']+)["\']/',
                    'replacement' => '@click="$1$2((window.BASE_URL||\'\')+\'/$3\')"',
                    'description' => 'Alpine fetch',
                ],
                [
                    'pattern' => '/window\.location\.href\s*=\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'window.location.href = (window.BASE_URL||"")+"/$1"',
                    'description' => 'window.location.href',
                ],
                [
                    'pattern' => '/location\.href\s*=\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'location.href = (window.BASE_URL||"")+"/$1"',
                    'description' => 'location.href',
                ],
                [
                    'pattern' => '/redirect\s*\(\s*["\']\/([^"\']+)["\']\s*\)/',
                    'replacement' => 'redirect(url("/$1"))',
                    'description' => 'redirect()',
                ],
                [
                    'pattern' => '/header\s*\(\s*["\']Location:\s*\/([^"\']+)["\']\s*\)/',
                    'replacement' => 'header("Location: " . url("/$1"))',
                    'description' => 'header Location',
                ],
                [
                    'pattern' => '/new\s+EventSource\s*\(\s*["\']\/([^"\']+)["\']\s*\)/',
                    'replacement' => 'new EventSource((window.BASE_URL||"")+"/$1")',
                    'description' => 'EventSource',
                ],
            ];
        } elseif ($type === 'js') {
            $patterns = [
                [
                    'pattern' => '/(EventSource|WebSocket)\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => '$1((window.BASE_URL||"")+"/$2")',
                    'description' => 'EventSource/WebSocket',
                ],
                [
                    'pattern' => '/(fetch|htmx\.ajax)\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => '$1((window.BASE_URL||"")+"/$2")',
                    'description' => 'fetch/HTMX',
                ],
                [
                    'pattern' => '/location\.href\s*=\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'location.href = (window.BASE_URL||"")+"/$1"',
                    'description' => 'location.href',
                ],
                [
                    'pattern' => '/new\s+URL\s*\(\s*["\']\/([^"\']+)["\']\s*,\s*([^\)]+)\)/',
                    'replacement' => 'new URL((window.BASE_URL||"")+"/$1", $2)',
                    'description' => 'new URL',
                ],
                [
                    'pattern' => '/axios\.(get|post|put|delete)\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'axios.$1((window.BASE_URL||"")+"/$2")',
                    'description' => 'axios',
                ],
                [
                    'pattern' => '/new\s+WebSocket\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'new WebSocket((window.BASE_URL||"")+"/$1")',
                    'description' => 'WebSocket',
                ],
                [
                    'pattern' => '/window\.open\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'window.open((window.BASE_URL||"")+"/$1")',
                    'description' => 'window.open',
                ],
            ];
        }

        return $patterns;
    }

    private function showConfig(): void
    {
        echo "📋 تنظیمات فعلی:\n";
        echo "------------------\n";
        echo "🔁 جایگزینی مسیرها: " . (ENABLE_PATH_REPLACEMENT ? '✅ فعال' : '❌ غیرفعال') . "\n";
        echo "📝 جایگزینی error_log: " . (ENABLE_ERROR_LOG_REPLACE ? '✅ فعال' : '❌ غیرفعال') . "\n";
        echo "💾 ایجاد بک‌آپ: " . (ENABLE_BACKUP ? '✅ فعال' : '❌ غیرفعال') . "\n";
        echo "📂 پوشه‌های استثنا: " . implode(', ', EXCLUDE_DIRS) . "\n";
        echo "📄 فایل‌های استثنا: " . implode(', ', EXCLUDE_FILES) . "\n";
        echo "------------------\n\n";
    }

    private function generateReport(): void
    {
        $html = $this->buildReportHTML();
        file_put_contents(REPORT_FILE, $html);
        echo "\n📄 Report: " . REPORT_FILE . "\n";
    }

    private function buildReportHTML(): string
    {
        $stats = $this->stats;
        $date = date('Y-m-d H:i:s');

        $rows = '';
        foreach ($stats['details'] as $detail) {
            $rows .= "<tr>
                <td><code>{$detail['file']}</code></td>
                <td>" . implode('<br>', $detail['changes']) . "</td>
                <td><span class='badge badge-success'>✅</span></td>
            </tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <title>گزارش Auto Fixer</title>
    <style>
        body { font-family: 'Vazir', sans-serif; background: #f0f4ff; padding: 30px; }
        .container { max-width: 1000px; margin: auto; background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        h1 { color: #4f46e5; display: flex; align-items: center; gap: 10px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px,1fr)); gap: 15px; margin: 20px 0; }
        .stat { background: #f8fafc; padding: 15px; border-radius: 12px; text-align: center; }
        .stat .num { font-size: 26px; font-weight: 900; color: #1e293b; }
        .stat .label { font-size: 13px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f1f5f9; text-align: right; padding: 10px; font-size: 13px; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 12px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #166534; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #e2e8f0; font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 گزارش Auto Fixer</h1>
    <p style="color:#64748b;">{$date}</p>

    <div class="stats">
        <div class="stat"><div class="num">{$stats['total_files']}</div><div class="label">فایل‌های اسکن</div></div>
        <div class="stat"><div class="num">{$stats['modified_files']}</div><div class="label">فایل‌های اصلاح‌شده</div></div>
        <div class="stat"><div class="num">{$stats['path_replacements']}</div><div class="label">جایگزینی مسیر</div></div>
        <div class="stat"><div class="num">{$stats['error_log_replacements']}</div><div class="label">جایگزینی error_log</div></div>
        <div class="stat"><div class="num">{$stats['errors']}</div><div class="label">خطا</div></div>
    </div>

    <h3>📋 جزئیات تغییرات</h3>
    <table>
        <tr><th>فایل</th><th>تغییرات</th><th>وضعیت</th></tr>
        {$rows}
    </table>

    <div class="footer">
        <p>📦 بک‌آپ: backup_before_fix/</p>
        <p>⚙️ تنظیمات: " . (ENABLE_PATH_REPLACEMENT ? 'مسیرها فعال' : 'مسیرها غیرفعال') . " | " . (ENABLE_ERROR_LOG_REPLACE ? 'error_log فعال' : 'error_log غیرفعال') . "</p>
    </div>
</div>
</body>
</html>
HTML;
    }

    private function showSummary(): void
    {
        echo "\n" . str_repeat('=', 50) . "\n";
        echo "📊 خلاصه:\n";
        echo str_repeat('=', 50) . "\n";
        echo "📂 کل فایل‌ها: {$this->stats['total_files']}\n";
        echo "✅ اصلاح‌شده: {$this->stats['modified_files']}\n";
        echo "🔄 جایگزینی مسیر: {$this->stats['path_replacements']}\n";
        echo "📝 error_log: {$this->stats['error_log_replacements']}\n";
        echo "❌ خطا: {$this->stats['errors']}\n";
        echo str_repeat('=', 50) . "\n";
    }
}

// ============================================
// 🚀 اجرا
// ============================================
$fixer = new AutoFixer();
$fixer->run();
echo "\n✅ انجام شد!\n";