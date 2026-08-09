<?php
/**
 * 🔄 اسکریپت جایگزینی error_log با log_message
 * 
 * این اسکریپت تمام فایل‌های PHP پروژه را اسکن کرده و
 * فراخوانی‌های error_log را به log_message تبدیل می‌کند.
 * 
 * ⚠️ قبل از اجرا از پروژه بک‌آپ بگیرید.
 */

// ============================================
// تنظیمات
// ============================================
$rootDir = __DIR__; // ریشه پروژه
$excludeDirs = [
    'vendor',
    'storage',
    'backup_before_fix',
    'node_modules',
    '.git',
    'cache',
    'tmp'
];
$fileExtensions = ['php']; // فقط فایل‌های PHP

// ============================================
// اجرای اسکن و جایگزینی
// ============================================
$totalFiles = 0;
$modifiedFiles = 0;
$totalReplacements = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    // فقط فایل‌های با پسوند مجاز
    if (!$file->isFile()) continue;
    $ext = $file->getExtension();
    if (!in_array($ext, $fileExtensions)) continue;

    $path = $file->getRealPath();
    
    // بررسی استثناها (پوشه‌های نادیده)
    $skip = false;
    foreach ($excludeDirs as $exclude) {
        if (strpos($path, DIRECTORY_SEPARATOR . $exclude . DIRECTORY_SEPARATOR) !== false) {
            $skip = true;
            break;
        }
    }
    if ($skip) continue;

    // خواندن محتوا
    $content = file_get_contents($path);
    $original = $content;

    // 🎯 الگوی جایگزینی: error_log( با log_message(
    // توجه: از \b برای کلمه کامل استفاده می‌کنیم و فاصله‌های احتمالی را پوشش می‌دهیم
    $pattern = '/\berror_log\s*\(/i';
    $replacement = 'log_message(';
    
    $newContent = preg_replace($pattern, $replacement, $content, -1, $count);
    
    if ($count > 0) {
        // ذخیره فایل
        file_put_contents($path, $newContent);
        $modifiedFiles++;
        $totalReplacements += $count;
        echo "✅ $path : $count جایگزینی\n";
    }
    
    $totalFiles++;
}

// ============================================
// گزارش نهایی
// ============================================
echo "\n" . str_repeat('=', 50) . "\n";
echo "📊 گزارش جایگزینی error_log\n";
echo str_repeat('=', 50) . "\n";
echo "📂 کل فایل‌های اسکن‌شده: $totalFiles\n";
echo "✅ فایل‌های اصلاح‌شده: $modifiedFiles\n";
echo "🔄 تعداد جایگزینی‌ها: $totalReplacements\n";
echo str_repeat('=', 50) . "\n";
echo "🎯 عملیات کامل شد.\n";