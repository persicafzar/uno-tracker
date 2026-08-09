<?php

namespace Domain;

class Title
{
    public int $id;
    public string $code;
    public string $name;
    public ?string $description;
    public string $icon;
    public string $condition_type;
    public int $condition_value;
    public int $priority;
    public bool $is_active;
    public string $created_at;

    // فیلدهای کاربر
    public bool $user_unlocked = false;
    public bool $user_active = false;
    public ?string $user_unlocked_at = null;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->code = $data['code'];
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->icon = $data['icon'] ?? '🎖️';
        $this->condition_type = $data['condition_type'];
        $this->condition_value = (int) $data['condition_value'];
        $this->priority = (int) ($data['priority'] ?? 0);
        $this->is_active = (bool) ($data['is_active'] ?? true);
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');

        // فیلدهای کاربر
        if (isset($data['user_unlocked'])) {
            $this->user_unlocked = (bool) $data['user_unlocked'];
        }
        if (isset($data['user_active'])) {
            $this->user_active = (bool) $data['user_active'];
        }
        if (isset($data['user_unlocked_at'])) {
            $this->user_unlocked_at = $data['user_unlocked_at'];
        }
    }
}