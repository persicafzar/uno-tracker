<?php

namespace Domain;

class Notification
{
    public const TYPE_ACHIEVEMENT = 'achievement';
    public const TYPE_TITLE = 'title';
    public const TYPE_LEVEL_UP = 'level_up';
    public const TYPE_STREAK = 'streak';
    public const TYPE_SYSTEM = 'system';

    public int $id;
    public int $user_id;
    public string $type;
    public string $title;
    public ?string $message;
    public string $icon;
    public ?string $link;
    public bool $is_read;
    public string $created_at;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->user_id = (int) $data['user_id'];
        $this->type = $data['type'];
        $this->title = $data['title'];
        $this->message = $data['message'] ?? null;
        $this->icon = $data['icon'] ?? '🔔';
        $this->link = $data['link'] ?? null;
        $this->is_read = (bool) ($data['is_read'] ?? false);
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    /**
     * آیکون پیش‌فرض بر اساس نوع
     */
    public static function getDefaultIcon(string $type): string
    {
        return match ($type) {
            self::TYPE_ACHIEVEMENT => '🏅',
            self::TYPE_TITLE => '🎖️',
            self::TYPE_LEVEL_UP => '⬆️',
            self::TYPE_STREAK => '🔥',
            self::TYPE_SYSTEM => '🔔',
            default => '🔔',
        };
    }
}
