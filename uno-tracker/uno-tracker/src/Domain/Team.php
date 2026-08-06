<?php

namespace Domain;

class Team
{
    public int $id;
    public int $game_id;
    public string $name;
    public string $color_hex;
    public array $members = [];

    public static function fromArray(array $data): self
    {
        $team = new self();
        $team->id = $data['id'];
        $team->game_id = $data['game_id'];
        $team->name = $data['name'];
        $team->color_hex = $data['color_hex'] ?? '#3B82F6';
        return $team;
    }

    public function addMember(GameParticipant $participant): void
    {
        $this->members[] = $participant;
    }

    public function getMembers(): array
    {
        return $this->members;
    }

    public function getTotalWins(): int
    {
        $total = 0;
        foreach ($this->members as $member) {
            $total += $member->wins_count;
        }
        return $total;
    }
}