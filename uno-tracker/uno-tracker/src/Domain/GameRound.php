<?php

namespace Domain;

class GameRound
{
    public int $id;
    public int $game_id;
    public int $round_number;
    public int $winner_participant_id;
    public ?int $winning_card_id;
    public ?int $win_type_id;
    public int $calculated_score = 0;
    public string $created_at;

    // اطلاعات اضافی
    public ?string $winner_name = null;
    public ?string $card_name = null;
    public ?string $card_emoji = null;
    public ?string $win_type_name = null;

    public static function fromArray(array $data): self
    {
        $round = new self();
        $round->id = $data['id'];
        $round->game_id = $data['game_id'];
        $round->round_number = (int) $data['round_number'];
        $round->winner_participant_id = (int) $data['winner_participant_id'];
        $round->winning_card_id = $data['winning_card_id'] ? (int) $data['winning_card_id'] : null;
        $round->win_type_id = $data['win_type_id'] ? (int) $data['win_type_id'] : null;
        $round->calculated_score = (int) $data['calculated_score'];
        $round->created_at = $data['created_at'];
        
        // اطلاعات اضافی
        $round->winner_name = $data['winner_name'] ?? null;
        $round->card_name = $data['card_name'] ?? null;
        $round->card_emoji = $data['card_emoji'] ?? null;
        $round->win_type_name = $data['win_type_name'] ?? null;
        
        return $round;
    }

    public function toArray(): array
    {
        return [
            'game_id' => $this->game_id,
            'round_number' => $this->round_number,
            'winner_participant_id' => $this->winner_participant_id,
            'winning_card_id' => $this->winning_card_id,
            'win_type_id' => $this->win_type_id,
            'calculated_score' => $this->calculated_score,
        ];
    }
}