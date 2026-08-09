<?php

namespace Core;

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    public static function autoload(string $class): void
    {
        // تبدیل namespace به مسیر فایل
        // Core\Database -> src/Core/Database.php
        // Application\Services\AuthService -> src/Application/Services/AuthService.php
        
        $baseDir = dirname(__DIR__, 2) . '/src/';
        
        $file = $baseDir . str_replace('\\', '/', $class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
}