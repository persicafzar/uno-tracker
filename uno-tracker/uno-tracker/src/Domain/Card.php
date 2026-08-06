<?php

namespace Domain;

class Card
{
    public int $id;
    public string $name;
    public string $slug;
    public ?string $icon_path;
    public ?string $emoji;
    public ?string $description;
    public string $rarity;
    public float $score_multiplier;
    public bool $is_action_card;
    public bool $is_active;

    public static function fromArray(array $data): self
    {
        $card = new self();
        $card->id = $data['id'];
        $card->name = $data['name'];
        $card->slug = $data['slug'];
        $card->icon_path = $data['icon_path'] ?? null;
        $card->emoji = $data['emoji'] ?? null;
        $card->description = $data['description'] ?? null;
        $card->rarity = $data['rarity'];
        $card->score_multiplier = (float) $data['score_multiplier'];
        $card->is_action_card = (bool) $data['is_action_card'];
        $card->is_active = (bool) $data['is_active'];
        return $card;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon_path' => $this->icon_path,
            'emoji' => $this->emoji,
            'description' => $this->description,
            'rarity' => $this->rarity,
            'score_multiplier' => $this->score_multiplier,
            'is_action_card' => $this->is_action_card,
            'is_active' => $this->is_active,
        ];
    }
}