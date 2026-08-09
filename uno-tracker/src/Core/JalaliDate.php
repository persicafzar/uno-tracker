<?php

namespace Core;

/**
 * کلاس تبدیل تاریخ میلادی به شمسی
 * بر اساس الگوریتم استاندارد JDF
 */
class JalaliDate
{
    /**
     * تبدیل تاریخ میلادی به شمسی
     * @return array ['year' => int, 'month' => int, 'day' => int]
     */
    public static function toJalali(int $gy, int $gm, int $gd): array
    {
        $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) 
                + ((int)(($gy2 + 399) / 400)) + $gd + $g_d_m[$gm - 1];
        $jy = -1595 + (33 * ((int)($days / 12053)));
        $days %= 12053;
        $jy += 4 * ((int)($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $jy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        if ($days < 186) {
            $jm = 1 + (int)($days / 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + (int)(($days - 186) / 30);
            $jd = 1 + (($days - 186) % 30);
        }
        return ['year' => $jy, 'month' => $jm, 'day' => $jd];
    }

    /**
     * تبدیل تاریخ شمسی به میلادی
     */
    public static function toGregorian(int $jy, int $jm, int $jd): array
    {
        $jy += 1595;
        $days = -355668 + (365 * $jy) + (((int)($jy / 33)) * 8) + ((int)((($jy % 33) + 3) / 4)) 
                + $jd + (($jm < 7) ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186);
        $gy = 400 * ((int)($days / 146097));
        $days %= 146097;
        if ($days > 36524) {
            $gy += 100 * ((int)(--$days / 36524));
            $days %= 36524;
            if ($days >= 365) $days++;
        }
        $gy += 4 * ((int)($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $gy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        $gd = $days + 1;
        $sal_a = [0, 31, (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0)) ? 29 : 28, 
                  31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        for ($gm = 0; $gm < 13 && $gd > $sal_a[$gm]; $gm++) {
            $gd -= $sal_a[$gm];
        }
        return ['year' => $gy, 'month' => $gm, 'day' => $gd];
    }

    /**
     * فرمت تاریخ شمسی
     */
    public static function format(string $format, ?int $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        $gDate = getdate($timestamp);
        $jDate = self::toJalali($gDate['year'], $gDate['mon'], $gDate['mday']);
        
        $jalaliMonths = [
            1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
            4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
            7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
            10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
        ];
        
        $jalaliDays = [
            'Saturday' => 'شنبه', 'Sunday' => 'یکشنبه', 'Monday' => 'دوشنبه',
            'Tuesday' => 'سه‌شنبه', 'Wednesday' => 'چهارشنبه',
            'Thursday' => 'پنجشنبه', 'Friday' => 'جمعه'
        ];
        
        $dayName = $jalaliDays[$gDate['weekday']] ?? '';
        $monthName = $jalaliMonths[$jDate['month']] ?? '';
        
        $result = $format;
        $result = str_replace('Y', $jDate['year'], $result);
        $result = str_replace('m', str_pad($jDate['month'], 2, '0', STR_PAD_LEFT), $result);
        $result = str_replace('d', str_pad($jDate['day'], 2, '0', STR_PAD_LEFT), $result);
        $result = str_replace('n', $jDate['month'], $result);
        $result = str_replace('j', $jDate['day'], $result);
        $result = str_replace('F', $monthName, $result);
        $result = str_replace('l', $dayName, $result);
        $result = str_replace('H', str_pad($gDate['hours'], 2, '0', STR_PAD_LEFT), $result);
        $result = str_replace('i', str_pad($gDate['minutes'], 2, '0', STR_PAD_LEFT), $result);
        $result = str_replace('s', str_pad($gDate['seconds'], 2, '0', STR_PAD_LEFT), $result);
        
        return $result;
    }

    /**
     * تبدیل timestamp به آرایه تاریخ شمسی
     */
    public static function fromTimestamp(?int $timestamp = null): array
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        $gDate = getdate($timestamp);
        return self::toJalali($gDate['year'], $gDate['mon'], $gDate['mday']);
    }

    /**
     * تبدیل تاریخ میلادی (رشته) به شمسی
     */
    public static function fromGregorian(string $gregorianDate): array
    {
        $parts = explode('-', $gregorianDate);
        if (count($parts) !== 3) {
            return ['year' => 0, 'month' => 0, 'day' => 0];
        }
        return self::toJalali((int)$parts[0], (int)$parts[1], (int)$parts[2]);
    }

    /**
     * گرفتن نام ماه شمسی
     */
    public static function getMonthName(int $month): string
    {
        $months = [
            1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
            4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
            7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
            10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
        ];
        return $months[$month] ?? '';
    }

    /**
     * گرفتن نام روز هفته شمسی
     */
    public static function getDayName(?int $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        $days = [
            'Saturday' => 'شنبه', 'Sunday' => 'یکشنبه', 'Monday' => 'دوشنبه',
            'Tuesday' => 'سه‌شنبه', 'Wednesday' => 'چهارشنبه',
            'Thursday' => 'پنجشنبه', 'Friday' => 'جمعه'
        ];
        
        $gDate = getdate($timestamp);
        return $days[$gDate['weekday']] ?? '';
    }

    /**
     * گرفتن روزهای هفته (کوتاه) برای تقویم
     */
    public static function getShortDayNames(): array
    {
        return ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];
    }
}