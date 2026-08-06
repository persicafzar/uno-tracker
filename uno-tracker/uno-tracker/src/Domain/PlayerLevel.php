<?php

namespace Domain;

class PlayerLevel
{
    public int $level;
    public int $min_xp;
    public int $max_xp;
    public ?string $title;
    public string $color;
    public string $icon;

    public function __construct(array $data)
    {
        $this->level = (int) $data['level'];
        $this->min_xp = (int) $data['min_xp'];
        $this->max_xp = (int) $data['max_xp'];
        $this->title = $data['title'] ?? null;
        $this->color = $data['color'] ?? '#6366f1';
        $this->icon = $data['icon'] ?? '⭐';
    }

    /**
     * محاسبه درصد پیشرفت در سطح فعلی
     */
    public static function calculateProgress(int $currentXp, int $minXp, int $maxXp): float
    {
        $range = $maxXp - $minXp;
        if ($range <= 0) return 100;
        
        $progress = (($currentXp - $minXp) / $range) * 100;
        return min(100, max(0, $progress));
    }

    /**
     * XP مورد نیاز برای سطح بعدی
     */
    public function getXpToNextLevel(int $currentXp): int
    {
        return max(0, $this->max_xp - $currentXp + 1);
    }
}