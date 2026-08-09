<?php
/**
 * 🔧 UNO Tracker - Auto Path Fixer & Error Log Replacer
 * 
 * این اسکریپت تمام فایل‌های پروژه را اسکن کرده و:
 * 1. مسیرهای مطلق (شروع با /) را به asset() و url() تبدیل می‌کند
 * 2. error_log را با log_message جایگزین می‌کند
 * 3. بک‌آپ خودکار از فایل‌های اصلاح‌شده ایجاد می‌کند
 * 4. یک گزارش HTML دقیق تولید می‌کند
 * 
 * ⚠️ قبل از اجرا از پروژه بک‌آپ کامل بگیرید.
 * 
 * @author UNO Tracker Team
 * @version 3.0
 */

// ============================================
// 🛡️ تنظیمات اولیه
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(300); // 5 دقیقه زمان اجرا

define('ROOT', __DIR__);
define('BACKUP_DIR', ROOT . '/backup_before_fix');
define('REPORT_FILE', ROOT . '/fix_report.html');

// ============================================
// 📊 کلاس اصلی Fixer
// ============================================
class PathFixer
{
    private array $stats = [
        'total_files' => 0,
        'modified_files' => 0,
        'total_replacements' => 0,
        'errors' => 0,
        'skipped' => 0,
        'details' => [],
        'error_log_replacements' => 0,
        'error_log_files' => 0,
    ];

    private array $excludeDirs = [
        'vendor',
        'storage',
        'backup_before_fix',
        'node_modules',
        '.git',
        'cache',
        'tmp',
        'logs',
    ];

    private array $excludeFiles = [
        'auto-fix-paths.php',
        'fix_report.html',
    ];

    private array $patterns = [];
    private array $backupFiles = [];

    public function __construct()
    {
        $this->patterns = $this->getPatterns();
    }

    /**
     * 🎯 الگوهای جستجو و جایگزینی
     */
    private function getPatterns(): array
    {
        return [
            // ============================================
            // فایل‌های PHP (Views)
            // ============================================
            'php' => [
                // 1. src="/assets/..." → <?= asset("...") ?>
                [
                    'pattern' => '/(src)=["\']\/assets\/([^"\']+)["\']/',
                    'replacement' => '$1="<?= asset("$2") ?>"',
                    'description' => 'Asset in src (PHP)',
                ],
                // 2. href="/assets/..." → <?= asset("...") ?>
                [
                    'pattern' => '/(href)=["\']\/assets\/([^"\']+)["\']/',
                    'replacement' => '$1="<?= asset("$2") ?>"',
                    'description' => 'Asset in href (PHP)',
                ],
                // 3. action="/..." → <?= url("/...") ?>
                [
                    'pattern' => '/action=["\']\/(?!assets|storage|http|#|javascript:)([^"\']+)["\']/',
                    'replacement' => 'action="<?= url("/$1") ?>"',
                    'description' => 'Form action (PHP)',
                ],
                // 4. href="/..." (لینک‌های داخلی) → <?= url("/...") ?>
                [
                    'pattern' => '/href=["\']\/(?!assets|storage|http|#|javascript:)([^"\']+)["\']/',
                    'replacement' => 'href="<?= url("/$1") ?>"',
                    'description' => 'Internal link (PHP)',
                ],
                // 5. img src="/storage/..." → <?= url("/storage/...") ?>
                [
                    'pattern' => '/src=["\']\/storage\/([^"\']+)["\']/',
                    'replacement' => 'src="<?= url("/storage/$1") ?>"',
                    'description' => 'Storage asset (PHP)',
                ],
                // 6. hx-post="/..." → hx-post="<?= url("/...") ?>"
                [
                    'pattern' => '/hx-(post|get|put|delete)=["\']\/([^"\']+)["\']/',
                    'replacement' => 'hx-$1="<?= url("/$2") ?>"',
                    'description' => 'HTMX attribute (PHP)',
                ],
                // 7. @click="fetch('/...')" → با BASE_URL
                [
                    'pattern' => '/@click=["\']([^"\']*?)(fetch|axios|\\$fetch)\\s*\\(["\']\/([^"\']+)["\']/',
                    'replacement' => '@click="$1$2((window.BASE_URL||\'\')+\'/$3\')"',
                    'description' => 'Alpine fetch (PHP)',
                ],
                // 8. window.location.href = "/..." → با BASE_URL (در inline JS)
                [
                    'pattern' => '/window\.location\.href\s*=\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'window.location.href = (window.BASE_URL||"")+"/$1"',
                    'description' => 'window.location.href (PHP inline JS)',
                ],
                // 9. location.href = "/..." → با BASE_URL (در inline JS)
                [
                    'pattern' => '/location\.href\s*=\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'location.href = (window.BASE_URL||"")+"/$1"',
                    'description' => 'location.href (PHP inline JS)',
                ],
                // 10. redirect('/...') → redirect(url('/...'))
                [
                    'pattern' => '/redirect\s*\(\s*["\']\/([^"\']+)["\']\s*\)/',
                    'replacement' => 'redirect(url("/$1"))',
                    'description' => 'redirect() function (PHP)',
                ],
                // 11. header('Location: /...') → header('Location: ' . url('/...'))
                [
                    'pattern' => '/header\s*\(\s*["\']Location:\s*\/([^"\']+)["\']\s*\)/',
                    'replacement' => 'header("Location: " . url("/$1"))',
                    'description' => 'header Location (PHP)',
                ],
                // 12. new EventSource('/sse/...') → با BASE_URL (در inline JS)
                [
                    'pattern' => '/new\s+EventSource\s*\(\s*["\']\/([^"\']+)["\']\s*\)/',
                    'replacement' => 'new EventSource((window.BASE_URL||"")+"/$1")',
                    'description' => 'EventSource (PHP inline JS)',
                ],
            ],

            // ============================================
            // فایل‌های JavaScript
            // ============================================
            'js' => [
                // 1. EventSource("/sse/...") → با BASE_URL
                [
                    'pattern' => '/(EventSource|WebSocket)\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => '$1((window.BASE_URL||"")+"/$2")',
                    'description' => 'EventSource/WebSocket (JS)',
                ],
                // 2. fetch("/api/...") → با BASE_URL
                [
                    'pattern' => '/(fetch|htmx\.ajax)\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => '$1((window.BASE_URL||"")+"/$2")',
                    'description' => 'Fetch/HTMX (JS)',
                ],
                // 3. location.href = "/..." → با BASE_URL
                [
                    'pattern' => '/location\.href\s*=\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'location.href = (window.BASE_URL||"")+"/$1"',
                    'description' => 'location.href (JS)',
                ],
                // 4. new URL("/...", ...) → با BASE_URL
                [
                    'pattern' => '/new\s+URL\s*\(\s*["\']\/([^"\']+)["\']\s*,\s*([^\)]+)\)/',
                    'replacement' => 'new URL((window.BASE_URL||"")+"/$1", $2)',
                    'description' => 'new URL (JS)',
                ],
                // 5. axios.get("/...") → با BASE_URL
                [
                    'pattern' => '/axios\.(get|post|put|delete)\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'axios.$1((window.BASE_URL||"")+"/$2")',
                    'description' => 'Axios (JS)',
                ],
                // 6. new WebSocket("/ws/...") → با BASE_URL
                [
                    'pattern' => '/new\s+WebSocket\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'new WebSocket((window.BASE_URL||"")+"/$1")',
                    'description' => 'WebSocket (JS)',
                ],
                // 7. window.open("/...") → با BASE_URL
                [
                    'pattern' => '/window\.open\s*\(\s*["\']\/([^"\']+)["\']/',
                    'replacement' => 'window.open((window.BASE_URL||"")+"/$1")',
                    'description' => 'window.open (JS)',
                ],
            ],

            // ============================================
            // error_log → log_message (همه فایل‌های PHP)
            // ============================================
            'error_log' => [
                [
                    'pattern' => '/\berror_log\s*\(/i',
                    'replacement' => 'log_message(',
                    'description' => 'error_log to log_message',
                ],
            ],
        ];
    }

    /**
     * 🚀 اجرای اصلی
     */
    public function run(): void
    {
        echo "\n🔧 Starting UNO Tracker Path Fixer\n";
        echo "====================================\n\n";

        // ایجاد پوشه بک‌آپ
        if (!is_dir(BACKUP_DIR)) {
            mkdir(BACKUP_DIR, 0755, true);
        }

        // اسکن فایل‌های PHP
        $this->scanDirectory(ROOT, 'php');
        // اسکن فایل‌های JS
        $this->scanDirectory(ROOT . '/assets/js', 'js');

        // تولید گزارش
        $this->generateReport();

        // نمایش خلاصه
        $this->showSummary();
    }

    /**
     * 🔍 اسکن یک دایرکتوری
     */
    private function scanDirectory(string $dir, string $type): void
    {
        if (!is_dir($dir)) {
            echo "⚠️ Directory not found: $dir\n";
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $ext = ($type === 'php') ? '.php' : '.js';

        foreach ($iterator as $file) {
            if (!$file->isFile()) continue;
            if (!in_array($file->getExtension(), ['php', 'js'])) continue;

            $path = $file->getRealPath();

            // 🚫 رد کردن پوشه‌های استثنا
            $skip = false;
            foreach ($this->excludeDirs as $exclude) {
                if (strpos($path, DIRECTORY_SEPARATOR . $exclude . DIRECTORY_SEPARATOR) !== false) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            // 🚫 رد کردن فایل‌های استثنا
            foreach ($this->excludeFiles as $excludeFile) {
                if (basename($path) === $excludeFile) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            $this->processFile($path, $type);
        }
    }

    /**
     * 📝 پردازش یک فایل
     */
    private function processFile(string $filePath, string $type): void
    {
        $this->stats['total_files']++;

        try {
            $content = file_get_contents($filePath);
            $originalContent = $content;
            $replacements = [];
            $modified = false;
            $errorLogCount = 0;

            // ============================================
            // 1. پردازش error_log (در همه فایل‌های PHP)
            // ============================================
            if ($type === 'php') {
                $errorLogPattern = $this->patterns['error_log'][0];
                $newContent = preg_replace(
                    $errorLogPattern['pattern'],
                    $errorLogPattern['replacement'],
                    $content,
                    -1,
                    $errorLogCount
                );
                if ($errorLogCount > 0) {
                    $content = $newContent;
                    $modified = true;
                    $this->stats['error_log_replacements'] += $errorLogCount;
                    $replacements[] = [
                        'pattern' => $errorLogPattern['description'],
                        'count' => $errorLogCount,
                    ];
                }
            }

            // ============================================
            // 2. پردازش الگوهای مسیر
            // ============================================
            $patterns = $this->patterns[$type] ?? [];

            foreach ($patterns as $patternData) {
                $pattern = $patternData['pattern'];
                $replacement = $patternData['replacement'];
                $description = $patternData['description'];

                $count = 0;
                $newContent = preg_replace($pattern, $replacement, $content, -1, $count);

                if ($count > 0) {
                    // جلوگیری از تداخل با تغییرات قبلی
                    $content = $newContent;
                    $modified = true;
                    $this->stats['total_replacements'] += $count;
                    $replacements[] = [
                        'pattern' => $description,
                        'count' => $count,
                    ];
                }
            }

            if ($modified) {
                // ایجاد بک‌آپ
                $backupPath = BACKUP_DIR . '/' . basename($filePath) . '.bak';
                if (!file_exists($backupPath)) {
                    file_put_contents($backupPath, $originalContent);
                    $this->backupFiles[] = $backupPath;
                }

                // ذخیره فایل جدید
                file_put_contents($filePath, $content);

                // ثبت در آمار
                $this->stats['modified_files']++;
                $this->stats['details'][] = [
                    'file' => str_replace(ROOT, '', $filePath),
                    'type' => $type,
                    'replacements' => $replacements,
                    'backup' => $backupPath,
                    'error_log_count' => $errorLogCount,
                ];

                echo "✅ Modified: " . basename($filePath) . " (" . count($replacements) . " changes)\n";
            }

        } catch (Exception $e) {
            $this->stats['errors']++;
            echo "❌ Error processing: " . basename($filePath) . " - " . $e->getMessage() . "\n";
        }
    }

    /**
     * 📊 تولید گزارش HTML
     */
    private function generateReport(): void
    {
        $html = $this->buildReportHTML();
        file_put_contents(REPORT_FILE, $html);
        echo "\n📄 Report saved: " . REPORT_FILE . "\n";
    }

    /**
     * 🎨 ساخت HTML گزارش
     */
    private function buildReportHTML(): string
    {
        $stats = $this->stats;
        $date = date('Y-m-d H:i:s');

        $successRate = ($stats['total_files'] > 0)
            ? round(($stats['modified_files'] / $stats['total_files']) * 100, 2)
            : 0;

        $detailsRows = '';
        foreach ($stats['details'] as $detail) {
            $replacementsList = '';
            foreach ($detail['replacements'] as $rep) {
                $replacementsList .= "<span class='badge badge-pattern'>{$rep['pattern']}: {$rep['count']}x</span> ";
            }
            if ($detail['error_log_count'] > 0) {
                $replacementsList .= "<span class='badge badge-errorlog'>error_log → log_message: {$detail['error_log_count']}x</span> ";
            }

            $detailsRows .= <<<HTML
            <tr>
                <td><code class="file-path">{$detail['file']}</code></td>
                <td><span class="badge badge-{$detail['type']}">{$detail['type']}</span></td>
                <td>{$replacementsList}</td>
                <td><span class="badge badge-success">✅</span></td>
            </tr>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 گزارش اصلاح مسیرها - UNO Tracker</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Vazir', Tahoma, Arial, sans-serif;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8edf5 100%);
            padding: 30px 20px;
            min-height: 100vh;
            direction: rtl;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 35px 40px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }
        .header .subtitle {
            font-size: 14px;
            opacity: 0.85;
            margin-top: 8px;
            position: relative;
            z-index: 1;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            padding: 30px 40px;
            background: #f8fafc;
            border-bottom: 2px solid #eef2f6;
        }
        .stat-card {
            background: white;
            padding: 18px;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            text-align: center;
            border: 1px solid #eef2f6;
            transition: all 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .stat-card .number {
            font-size: 28px;
            font-weight: 900;
            color: #1e293b;
            line-height: 1.2;
        }
        .stat-card .label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            margin-top: 4px;
        }
        .stat-card .icon { font-size: 24px; display: block; margin-bottom: 4px; }
        .stat-card.success .number { color: #22c55e; }
        .stat-card.danger .number { color: #ef4444; }
        .stat-card.warning .number { color: #f59e0b; }
        .stat-card.info .number { color: #3b82f6; }
        .stat-card.purple .number { color: #8b5cf6; }

        .content { padding: 30px 40px; }
        .section-title {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title .badge {
            background: #eef2f6;
            color: #475569;
            font-size: 12px;
            padding: 2px 12px;
            border-radius: 12px;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            background: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            padding: 12px 16px;
            text-align: right;
            border-bottom: 2px solid #eef2f6;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        table tr:last-child td { border-bottom: none; }
        table tr:hover { background: #fafbff; }

        .file-path {
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            color: #0f172a;
            font-family: 'Courier New', monospace;
            display: inline-block;
            word-break: break-all;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            margin: 2px 2px;
        }
        .badge-php { background: #818cf8; color: white; }
        .badge-js { background: #f59e0b; color: white; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-pattern {
            background: #eef2ff;
            color: #4f46e5;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 8px;
            display: inline-block;
            margin: 2px 2px;
        }
        .badge-errorlog {
            background: #fef3c7;
            color: #92400e;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 8px;
            display: inline-block;
            margin: 2px 2px;
        }

        .footer {
            padding: 20px 40px;
            background: #f8fafc;
            border-top: 2px solid #eef2f6;
            color: #64748b;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .footer .btn {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            transition: all 0.2s;
        }
        .footer .btn:hover { background: #4338ca; transform: scale(1.02); }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }
        .empty-state .icon { font-size: 48px; margin-bottom: 12px; }

        @media (max-width: 640px) {
            .header { padding: 25px 20px; }
            .stats-grid { grid-template-columns: 1fr 1fr; padding: 16px; gap: 10px; }
            .stat-card .number { font-size: 22px; }
            .content { padding: 16px; }
            .footer { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔧 گزارش اصلاح مسیرها</h1>
            <div class="subtitle">
                UNO Tracker - Path Fixer & Error Log Replacer &bull; {$date}
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card info">
                <span class="icon">📂</span>
                <div class="number">{$stats['total_files']}</div>
                <div class="label">کل فایل‌های اسکن‌شده</div>
            </div>
            <div class="stat-card success">
                <span class="icon">✅</span>
                <div class="number">{$stats['modified_files']}</div>
                <div class="label">فایل‌های اصلاح‌شده</div>
            </div>
            <div class="stat-card warning">
                <span class="icon">🔄</span>
                <div class="number">{$stats['total_replacements']}</div>
                <div class="label">جایگزینی مسیرها</div>
            </div>
            <div class="stat-card purple">
                <span class="icon">📝</span>
                <div class="number">{$stats['error_log_replacements']}</div>
                <div class="label">جایگزینی error_log</div>
            </div>
            <div class="stat-card">
                <span class="icon">🎯</span>
                <div class="number">{$successRate}%</div>
                <div class="label">نرخ موفقیت</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="section-title">
                📋 جزئیات تغییرات
                <span class="badge">{$stats['modified_files']} فایل</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>فایل</th>
                            <th>نوع</th>
                            <th>تغییرات</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$detailsRows}
                    </tbody>
                </table>
            </div>

            <!-- فایل‌های بدون تغییر -->
            <div style="margin-top: 30px;">
                <div class="section-title" style="font-size: 15px; color: #64748b;">
                    ℹ️ فایل‌های بدون تغییر
                    <span class="badge">{$stats['total_files'] - $stats['modified_files']}</span>
                </div>
                <p style="color: #94a3b8; font-size: 12px;">
                    این فایل‌ها نیازی به اصلاح نداشتند یا قبلاً به‌روز شده بودند.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <span>
                📦 بک‌آپ فایل‌های اصلاح‌شده در: <code style="background:#eef2f6;padding:2px 8px;border-radius:4px;">backup_before_fix/</code>
            </span>
            <a href="#" onclick="window.print();return false;" class="btn">
                🖨️ چاپ گزارش
            </a>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * 📊 نمایش خلاصه در ترمینال
     */
    private function showSummary(): void
    {
        $stats = $this->stats;
        echo "\n" . str_repeat('=', 50) . "\n";
        echo "📊 SUMMARY\n";
        echo str_repeat('=', 50) . "\n";
        echo "📂 Total files scanned     : {$stats['total_files']}\n";
        echo "✅ Modified files           : {$stats['modified_files']}\n";
        echo "🔄 Path replacements       : {$stats['total_replacements']}\n";
        echo "📝 error_log replacements  : {$stats['error_log_replacements']}\n";
        echo "❌ Errors                  : {$stats['errors']}\n";
        echo "📄 Report saved at         : " . REPORT_FILE . "\n";
        echo "📦 Backups saved in        : " . BACKUP_DIR . "\n";
        echo str_repeat('=', 50) . "\n";
    }
}

// ============================================
// 🚀 اجرای اسکریپت
// ============================================
echo "\n";
$fixer = new PathFixer();
$fixer->run();
echo "\n✅ Done!\n\n";