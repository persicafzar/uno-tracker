<?php

namespace Domain;

class Achievement
{
    public const CATEGORY_GENERAL = 'general';
    public const CATEGORY_WINNING = 'winning';
    public const CATEGORY_STREAK = 'streak';
    public const CATEGORY_TEAM = 'team';
    public const CATEGORY_SPECIAL = 'special';

    public const RARITY_COMMON = 'common';
    public const RARITY_RARE = 'rare';
    public const RARITY_EPIC = 'epic';
    public const RARITY_LEGENDARY = 'legendary';

    public int $id;
    public string $code;
    public string $name;
    public ?string $description;
    public string $icon;
    public string $category;
    public string $rarity;
    public int $xp_reward;
    public string $condition_type;
    public int $condition_value;
    public bool $is_active;
    public string $created_at;

    // فیلدهای اضافی برای کاربر
    public ?int $user_progress = null;
    public bool $user_completed = false;
    public ?string $user_unlocked_at = null;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->code = $data['code'];
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->icon = $data['icon'] ?? '🏅';
        $this->category = $data['category'] ?? self::CATEGORY_GENERAL;
        $this->rarity = $data['rarity'] ?? self::RARITY_COMMON;
        $this->xp_reward = (int) ($data['xp_reward'] ?? 10);
        $this->condition_type = $data['condition_type'];
        $this->condition_value = (int) $data['condition_value'];
        $this->is_active = (bool) ($data['is_active'] ?? true);
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');

        // فیلدهای کاربر
        if (isset($data['user_progress'])) {
            $this->user_progress = (int) $data['user_progress'];
        }
        if (isset($data['user_completed'])) {
            $this->user_completed = (bool) $data['user_completed'];
        }
        if (isset($data['user_unlocked_at'])) {
            $this->user_unlocked_at = $data['user_unlocked_at'];
        }
    }

    /**
     * محاسبه درصد پیشرفت
     */
    public function getProgressPercentage(): float
    {
        if ($this->user_progress === null) {
            return 0;
        }
        return min(100, ($this->user_progress / $this->condition_value) * 100);
    }

    /**
     * آیا قفل است؟
     */
    public function isLocked(): bool
    {
        return !$this->user_completed;
    }

    /**
     * رنگ بر اساس کمیابی
     */
    public function getRarityColor(): string
    {
        return match($this->rarity) {
            self::RARITY_COMMON => '#94a3b8',
            self::RARITY_RARE => '#3b82f6',
            self::RARITY_EPIC => '#a855f7',
            self::RARITY_LEGENDARY => '#f59e0b',
            default => '#94a3b8',
        };
    }

    /**
     * نام فارسی کمیابی
     */
    public function getRarityName(): string
    {
        return match($this->rarity) {
            self::RARITY_COMMON => 'معمولی',
            self::RARITY_RARE => 'کمیاب',
            self::RARITY_EPIC => 'حماسی',
            self::RARITY_LEGENDARY => 'افسانه‌ای',
            default => 'معمولی',
        };
    }

    /**
     * نام فارسی دسته‌بندی
     */
    public function getCategoryName(): string
    {
        return match($this->category) {
            self::CATEGORY_GENERAL => 'عمومی',
            self::CATEGORY_WINNING => 'پیروزی',
            self::CATEGORY_STREAK => 'زنجیره پیروزی',
            self::CATEGORY_TEAM => 'تیمی',
            self::CATEGORY_SPECIAL => 'ویژه',
            default => 'عمومی',
        };
    }
}