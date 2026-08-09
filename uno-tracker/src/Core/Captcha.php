<?php

namespace Core;

class Captcha
{
    public static function generate(): string
    {
        $code = substr(str_shuffle('0123456789'), 0, 5);
        $_SESSION['captcha_code'] = $code;
        return $code;
    }

    public static function verify(string $input): bool
    {
        if (!isset($_SESSION['captcha_code'])) {
            return false;
        }
        
        $valid = trim($input) === $_SESSION['captcha_code'];
        unset($_SESSION['captcha_code']);
        return $valid;
    }

    public static function getSimpleImage(): void
    {
        $code = self::generate();
        
        // 🆕 مرحله 1: ساخت تصویر کوچک (اندازه اصلی)
        $smallWidth = 90;
        $smallHeight = 35;
        $smallImage = imagecreatetruecolor($smallWidth, $smallHeight);
        
        $bgColor = imagecolorallocate($smallImage, 249, 250, 251);
        $textColor = imagecolorallocate($smallImage, 79, 70, 229);
        
        imagefilledrectangle($smallImage, 0, 0, $smallWidth, $smallHeight, $bgColor);
        
        // رسم اعداد روی تصویر کوچک (سایز 5 = حداکثر)
        $charWidth = 14;
        $startX = 5;
        $startY = 9;
        
        for ($i = 0; $i < strlen($code); $i++) {
            $charX = $startX + ($i * $charWidth);
            $charY = $startY + rand(-2, 2);
            imagestring($smallImage, 5, $charX, $charY, $code[$i], $textColor);
        }
        
        // 🆕 مرحله 2: بزرگ‌نمایی تصویر نهایی (2 برابر)
        $finalWidth = $smallWidth * 2;
        $finalHeight = $smallHeight * 2;
        $finalImage = imagecreatetruecolor($finalWidth, $finalHeight);
        
        // پس‌زمینه سفید
        $bgColorFinal = imagecolorallocate($finalImage, 249, 250, 251);
        imagefilledrectangle($finalImage, 0, 0, $finalWidth, $finalHeight, $bgColorFinal);
        
        // 🎯 کپی و بزرگ‌نمایی
        imagecopyresized($finalImage, $smallImage, 0, 0, 0, 0, 
                        $finalWidth, $finalHeight, $smallWidth, $smallHeight);
        
        // 🆕 مرحله 3: اضافه کردن نویز روی تصویر نهایی
        $noiseColor = imagecolorallocate($finalImage, 209, 213, 219);
        for ($i = 0; $i < 15; $i++) {
            imageline($finalImage, rand(0, $finalWidth), rand(0, $finalHeight),
                    rand(0, $finalWidth), rand(0, $finalHeight), $noiseColor);
        }
        
        // خطوط منحنی
        for ($i = 0; $i < 2; $i++) {
            imageline($finalImage, rand(0, $finalWidth), rand(0, $finalHeight),
                    rand(0, $finalWidth), rand(0, $finalHeight), $textColor);
        }
        
        // آزادسازی حافظه
        imagedestroy($smallImage);
        
        header('Content-Type: image/png');
        imagepng($finalImage);
        imagedestroy($finalImage);
        exit;
    }

    public static function renderHTML(): string
    {
        $code = self::generate();
        $html = '<div style="display: inline-flex; gap: 6px; padding: 10px 14px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 8px;">';
        
        foreach (str_split($code) as $char) {
            $rotation = rand(-15, 15);
            $color = ['#4f46e5', '#7c3aed', '#ec4899', '#f59e0b', '#10b981'][rand(0, 4)];
            //  فونت بزرگتر (28px به جای 24px)
            $html .= "<span style='display: inline-block; font-size: 28px; font-weight: bold; color: {$color}; transform: rotate({$rotation}deg); font-family: monospace;'>{$char}</span>";
        }
        
        $html .= '</div>';
        return $html;
    }
}