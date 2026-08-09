<?php

namespace Domain;

class UserStreak
{
    public int $user_id;
    public int $current_streak;
    public int $best_streak;
    public ?string $last_win_at;
    public ?string $streak_broken_at;
    public ?string $updated_at;

    public function __construct(array $data)
    {
        $this->user_id = (int) $data['user_id'];
        $this->current_streak = (int) ($data['current_streak'] ?? 0);
        $this->best_streak = (int) ($data['best_streak'] ?? 0);
        $this->last_win_at = $data['last_win_at'] ?? null;
        $this->streak_broken_at = $data['streak_broken_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    /**
     * آیا استریک فعال است؟ (آخرین برد در ۲۴ ساعت گذشته)
     */
    public function isActive(): bool
    {
        if (!$this->last_win_at) {
            return false;
        }
        
        $lastWinTime = strtotime($this->last_win_at);
        $now = time();
        $hoursDiff = ($now - $lastWinTime) / 3600;
        
        return $hoursDiff < 24;
    }

    /**
     * آیا استریک شکسته شده؟
     */
    public function isBroken(): bool
    {
        return !$this->isActive() && $this->current_streak > 0;
    }
}