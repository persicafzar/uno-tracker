<?php
use Core\JalaliDate;

// گرفتن متغیرها با مقدار پیش‌فرض
$month = $month ?? $currentJalali['month'] ?? (int)date('m');
$year = $year ?? $currentJalali['year'] ?? (int)date('Y');

// تابع گرفتن تعداد روزهای ماه شمسی
function getJalaliMonthDays(int $month, int $year): int
{
    if ($month <= 6) return 31;
    if ($month <= 11) return 30;
    $remainder = $year % 33;
    $isLeap = in_array($remainder, [1, 5, 9, 13, 17, 22, 26, 30]);
    return $isLeap ? 30 : 29;
}

$daysInMonth = getJalaliMonthDays($month, $year);

// گرفتن روز هفته اولین روز ماه شمسی
$firstDayGregorian = JalaliDate::toGregorian($year, $month, 1);
$firstDayTimestamp = mktime(0, 0, 0, $firstDayGregorian['month'], $firstDayGregorian['day'], $firstDayGregorian['year']);
$firstDayOfWeek = (int)date('w', $firstDayTimestamp);
$startOffset = ($firstDayOfWeek + 1) % 7;

$monthName = JalaliDate::getMonthName($month);
?>

<?php if (empty($calendar)): ?>
    <div class="text-center py-8">
        <div class="text-4xl mb-2"></div>
        <p class="text-gray-600 text-sm">
            در <?= htmlspecialchars($monthName) ?> <?= $year ?> بازی‌ای نداشته‌اید
        </p>
    </div>
<?php else: ?>
    <!-- 🆕 روزهای هفته با فونت کوچک‌تر و فشرده‌تر -->
    <div class="grid grid-cols-7 gap-0.5 sm:gap-1 mb-1 sm:mb-2">
        <?php foreach (JalaliDate::getShortDayNames() as $day): ?>
            <div class="text-center text-[10px] sm:text-xs font-semibold text-gray-500 py-0.5 sm:py-1"><?= $day ?></div>
        <?php endforeach; ?>
    </div>
    
    <!-- 🆕 تقویم با فاصله‌های کمتر -->
    <div class="grid grid-cols-7 gap-0.5 sm:gap-1">
        <?php for ($i = 0; $i < $startOffset; $i++): ?>
            <div class="aspect-square"></div>
        <?php endfor; ?>
        
        <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
            <?php
            $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dayGames = $calendar[$dateKey] ?? [];
            $hasGames = !empty($dayGames);
            $hasWin = false;
            foreach ($dayGames as $g) {
                if (!empty($g['is_winner'])) {
                    $hasWin = true;
                    break;
                }
            }
            ?>
            <div class="aspect-square p-0.5 sm:p-1 rounded border <?= $hasGames ? ($hasWin ? 'bg-green-50 border-green-300' : 'bg-indigo-50 border-indigo-200') : 'bg-white border-gray-200' ?> text-center flex flex-col items-center justify-center">
                <div class="text-[10px] sm:text-xs font-semibold leading-tight <?= $hasGames ? 'text-gray-800' : 'text-gray-400' ?>"><?= $day ?></div>
                <?php if ($hasGames): ?>
                    <div class="text-[9px] sm:text-[10px] mt-0.5 text-gray-600 leading-tight"><?= count($dayGames) ?></div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
    
    <!-- Legend -->
    <div class="flex items-center justify-center gap-3 sm:gap-4 mt-3 sm:mt-4 text-[10px] sm:text-xs">
        <div class="flex items-center gap-1">
            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded bg-green-100 border border-green-300"></div>
            <span class="text-gray-600">روز برد</span>
        </div>
        <div class="flex items-center gap-1">
            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded bg-indigo-100 border border-indigo-200"></div>
            <span class="text-gray-600">روز بازی</span>
        </div>
    </div>
<?php endif; ?>