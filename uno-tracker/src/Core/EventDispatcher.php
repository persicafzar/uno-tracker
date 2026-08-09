<?php

namespace Core;

class EventDispatcher
{
    private static ?EventDispatcher $instance = null;
    private array $listeners = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * ثبت یک Listener برای یک Event
     * 
     * @param string $event نام رویداد
     * @param callable|array $listener تابع یا آرایه [ClassName::class, 'methodName']
     * @param int $priority اولویت (بالاتر = زودتر اجرا شود)
     */
    public function listen(string $event, $listener, int $priority = 0): void
    {
        // اگر آرایه [class, method] بود، آن را به callable تبدیل کن
        if (is_array($listener) && count($listener) === 2) {
            [$class, $method] = $listener;
            
            // تبدیل به closure که instance را lazy می‌سازد
            $listener = function($data) use ($class, $method) {
                $instance = new $class();
                return $instance->$method($data);
            };
        }

        // بررسی که listener واقعاً callable باشد
        if (!is_callable($listener)) {
            throw new \InvalidArgumentException("Listener must be callable");
        }

        $this->listeners[$event][] = [
            'callback' => $listener,
            'priority' => $priority,
        ];

        // مرتب‌سازی بر اساس اولویت
        usort($this->listeners[$event], function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
    }

    /**
     * Dispatch یک Event
     */
    public function dispatch(string $event, array $data = []): void
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $listener) {
            try {
                call_user_func($listener['callback'], $data);
            } catch (\Throwable $e) {
                // لاگ خطا اما ادامه دادن به بقیه Listenerها
                log_message("Error in event listener for '{$event}': " . $e->getMessage());
                
                // در حالت development، خطا را نشان بده
                if (getenv('APP_DEBUG') || true) {
                    throw $e;
                }
            }
        }
    }

    /**
     * حذف تمام Listenerهای یک Event
     */
    public function forget(string $event): void
    {
        unset($this->listeners[$event]);
    }

    /**
     * بررسی وجود Listener برای یک Event
     */
    public function hasListeners(string $event): bool
    {
        return !empty($this->listeners[$event]);
    }

    /**
     * گرفتن تمام Listenerهای یک Event (برای debug)
     */
    public function getListeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }

    // Singleton protection
    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}