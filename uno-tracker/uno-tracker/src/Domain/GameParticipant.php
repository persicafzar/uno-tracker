<?php

namespace Domain;

class GameParticipant
{
    public int $id;
    public int $game_id;
    public ?int $user_id;
    public ?int $team_id;
    public ?string $guest_name;
    public int $wins_count = 0;
    public int $total_score = 0;
    public bool $is_winner = false;
    public string $joined_at;

    // اطلاعات اضافی از JOIN
    public ?string $nickname = null;
    public ?string $real_name = null;
    public ?string $avatar_path = null;
    public ?string $team_name = null;

    public static function fromArray(array $data): self
    {
        $participant = new self();
        $participant->id = $data['id'];
        $participant->game_id = $data['game_id'];
        $participant->user_id = $data['user_id'] ?? null;
        $participant->team_id = $data['team_id'] ?? null;
        $participant->guest_name = $data['guest_name'] ?? null;
        $participant->wins_count = (int) $data['wins_count'];
        $participant->total_score = (int) $data['total_score'];
        $participant->is_winner = (bool) $data['is_winner'];
        $participant->joined_at = $data['joined_at'];
        
        // اطلاعات اضافی
        $participant->nickname = $data['nickname'] ?? null;
        $participant->real_name = $data['real_name'] ?? null;
        $participant->avatar_path = $data['avatar_path'] ?? null;
        $participant->team_name = $data['team_name'] ?? null;
        
        return $participant;
    }

    public function getDisplayName(): string
    {
        if ($this->user_id && $this->nickname) {
            return $this->nickname;
        }
        return $this->guest_name ?? 'بازیکن مهمان';
    }

    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    public function incrementWins(): void
    {
        $this->wins_count++;
    }

    public function addScore(int $score): void
    {
        $this->total_score += $score;
    }
}