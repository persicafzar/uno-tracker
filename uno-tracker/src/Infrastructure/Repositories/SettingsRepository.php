<?php

namespace Infrastructure\Repositories;

use Core\Database;

class SettingsRepository
{
    private static ?SettingsRepository $instance = null;
    private Database $db;
    private array $cache = [];
    private bool $cacheLoaded = false;

    private function __construct()
    {
        $this->db = Database::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * گرفتن یک تنظیم
     */

    /**
     * گرفتن یک تنظیم
     */
    public function get(string $key, $default = null)
    {
        $this->loadCache();

        if (!isset($this->cache[$key])) {
            return $default;
        }

        $value = $this->cache[$key]['value'];
        $type = $this->cache[$key]['type'] ?? 'string';

        // 🆕 Decode هوشمند: اگر string است و شبیه JSON، decode کن
        if (is_string($value) && !empty($value)) {
            $trimmed = trim($value);

            // 🆕 بررسی شروع و پایان JSON (با یا بدون backslash)
            $startsWithJson = ($trimmed[0] === '{' || $trimmed[0] === '[' ||
                ($trimmed[0] === '"' && strlen($trimmed) > 1 &&
                    ($trimmed[1] === '{' || $trimmed[1] === '[')));

            if ($startsWithJson) {
                // 🆕 ابتدا stripslashes برای حذف backslash های اضافی
                $cleanValue = stripslashes($value);

                // اگر هنوز string با quote بود، دوباره stripslashes کن
                if (is_string($cleanValue) && strlen($cleanValue) > 0 && $cleanValue[0] === '"') {
                    $cleanValue = stripslashes($cleanValue);
                }

                $decoded = json_decode($cleanValue, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }

                // اگر باز هم شکست خورد، مقدار اصلی را امتحان کن
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }

                error_log("⚠️ JSON decode failed for {$key}: " . json_last_error_msg());
            }
        }

        return $this->castValue($value, $type);
    }

    /**
     * تنظیم یک مقدار
     */
    public function set(string $key, $value, string $type = 'string', ?string $description = null, ?string $category = 'general'): void
    {
        // ✅ ابتدا کش را بارگذاری کن (اگر بارگذاری نشده)
        $this->loadCache();

        $exists = $this->db->fetchOne(
            "SELECT setting_key FROM system_settings WHERE setting_key = ?",
            [$key]
        );

        $data = [
            'setting_value' => is_array($value) ? json_encode($value) : $value,
            'setting_type' => $type,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($description !== null) {
            $data['description'] = $description;
        }

        if ($category !== null) {
            $data['category'] = $category;
        }

        if ($exists) {
            $this->db->update('system_settings', $data, 'setting_key = ?', [$key]);
        } else {
            $data['setting_key'] = $key;
            $this->db->insert('system_settings', $data);
        }

        // ✅ آپدیت کش با مقدار جدید
        $this->cache[$key] = [
            'value' => is_array($value) ? json_encode($value) : $value,
            'type' => $type,
            'description' => $description,
            'category' => $category,
        ];

        // ✅ همچنین اطمینان از اینکه cacheLoaded true است
        $this->cacheLoaded = true;
    }

    /**
     * بارگذاری کش از دیتابیس
     */
    private function loadCache(): void
    {
        // ✅ اگر قبلاً بارگذاری شده، برنگرد
        if ($this->cacheLoaded) {
            return;
        }

        $results = $this->db->fetchAll("SELECT * FROM system_settings");

        $this->cache = [];
        foreach ($results as $row) {
            $this->cache[$row['setting_key']] = [
                'value' => $row['setting_value'],
                'type' => $row['setting_type'],
                'description' => $row['description'],
                'category' => $row['category'],
            ];
        }

        $this->cacheLoaded = true;
    }

    /**
     * پاک کردن کش
     */
    public function clearCache(): void
    {
        $this->cache = [];
        $this->cacheLoaded = false;
    }

    /**
     * گرفتن تنظیمات بر اساس دسته
     */
    public function getByCategory(string $category): array
    {
        $this->loadCache();

        $result = [];
        foreach ($this->cache as $key => $data) {
            if (($data['category'] ?? 'general') === $category) {
                $result[$key] = $this->castValue($data['value'], $data['type']);
            }
        }

        return $result;
    }

    /**
     * گرفتن همه تنظیمات با جزئیات کامل
     */
    public function getAllWithDetails(): array
    {
        $results = $this->db->fetchAll(
            "SELECT * FROM system_settings ORDER BY category, setting_key"
        );

        $grouped = [];
        foreach ($results as $row) {
            $category = $row['category'] ?? 'general';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = [
                'key' => $row['setting_key'],
                'value' => $this->castValue($row['setting_value'], $row['setting_type']),
                'type' => $row['setting_type'],
                'description' => $row['description'],
                'category' => $category,
            ];
        }

        return $grouped;
    }

    /**
     * گرفتن تمام تنظیمات
     */
    public function all(): array
    {
        $this->loadCache();

        $result = [];
        foreach ($this->cache as $key => $data) {
            $result[$key] = $this->castValue($data['value'], $data['type']);
        }

        return $result;
    }

    /**
     * حذف یک تنظیم
     */
    public function delete(string $key): bool
    {
        $result = $this->db->delete('system_settings', 'setting_key = ?', [$key]);

        // حذف از کش
        unset($this->cache[$key]);

        return $result;
    }

    /**
     * تبدیل نوع داده
     */
    private function castValue($value, string $type)
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                // 🆕 مدیریت escaped JSON
                if (is_string($value)) {
                    // ابتدا stripslashes کن
                    $cleanValue = stripslashes($value);
                    $decoded = json_decode($cleanValue, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    }

                    // اگر شکست خورد، مقدار اصلی را امتحان کن
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    }

                    error_log("⚠️ JSON cast failed: " . json_last_error_msg() . " | Value: " . substr($value, 0, 100));
                }
                return is_array($value) ? $value : [];
            default:
                return $value;
        }
    }

    // Singleton protection
    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
