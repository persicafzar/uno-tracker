<?php

namespace Core;

class View
{
    private static string $viewsPath = '';
    private static ?string $layout = null;
    
    // 🆕 داده‌های سراسری که به همه ویوها تزریق می‌شوند
    private static array $sharedData = [];

    public static function setViewsPath(string $path): void
    {
        self::$viewsPath = rtrim($path, '/');
    }

    /**
     * 🆕 افزودن یک داده‌ی سراسری
     * 
     * @param string $key نام متغیر
     * @param mixed $value مقدار
     */
    public static function share(string $key, $value): void
    {
        self::$sharedData[$key] = $value;
    }

    /**
     * 🆕 دریافت یک داده‌ی سراسری (یا همه)
     * 
     * @param string|null $key اگر null باشد همه‌ی داده‌ها را برمی‌گرداند
     * @return mixed
     */
    public static function getShared(string $key = null)
    {
        if ($key === null) {
            return self::$sharedData;
        }
        return self::$sharedData[$key] ?? null;
    }

    /**
     * 🆕 پاک کردن همه داده‌های سراسری (برای تست یا ریست)
     */
    public static function clearShared(): void
    {
        self::$sharedData = [];
    }

    public static function render(string $view, array $data = []): string
    {
        $viewFile = self::$viewsPath . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewFile}");
        }

        // 🆕 ترکیب داده‌های سراسری با داده‌های محلی (داده‌های محلی اولویت دارند)
        $allData = array_merge(self::$sharedData, $data);
        extract($allData);

        // شروع Output Buffering
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // اگر Layout داریم، محتوا را در آن قرار بده
        if (self::$layout !== null) {
            $layoutFile = self::$viewsPath . '/layouts/' . self::$layout . '.php';
            self::$layout = null; // ریست کردن layout
            
            if (file_exists($layoutFile)) {
                // 🆕 در Layout نیز داده‌های سراسری + محلی در دسترس هستند
                $data['content'] = $content;
                $allDataForLayout = array_merge(self::$sharedData, $data);
                extract($allDataForLayout);
                ob_start();
                include $layoutFile;
                $content = ob_get_clean();
            }
        }

        return $content;
    }

    public static function withLayout(string $layout): void
    {
        self::$layout = $layout;
    }

    public static function make(string $view, array $data = [], ?string $layout = null): string
    {
        if ($layout !== null) {
            self::withLayout($layout);
        }
        return self::render($view, $data);
    }
}