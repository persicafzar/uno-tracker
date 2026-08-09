<?php

namespace Domain;

class UserXP
{
    public int $user_id;
    public int $total_xp;
    public int $current_level;
    public int $xp_to_next_level;
    public ?string $updated_at;

    public function __construct(array $data)
    {
        $this->user_id = (int) $data['user_id'];
        $this->total_xp = (int) ($data['total_xp'] ?? 0);
        $this->current_level = (int) ($data['current_level'] ?? 1);
        $this->xp_to_next_level = (int) ($data['xp_to_next_level'] ?? 100);
        $this->updated_at = $data['updated_at'] ?? null;
    }
}