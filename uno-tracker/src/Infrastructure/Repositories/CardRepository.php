<?php

namespace Infrastructure\Repositories;

use Core\Database;
use Domain\Card;

class CardRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?Card
    {
        $data = $this->db->fetchOne("SELECT * FROM cards WHERE id = ?", [$id]);
        return $data ? Card::fromArray($data) : null;
    }

    public function findAllActive(): array
    {
        $results = $this->db->fetchAll(
            "SELECT * FROM cards WHERE is_active = 1 ORDER BY score_multiplier"
        );
        return array_map(fn($data) => Card::fromArray($data), $results);
    }

    public function findBySlug(string $slug): ?Card
    {
        $data = $this->db->fetchOne("SELECT * FROM cards WHERE slug = ?", [$slug]);
        return $data ? Card::fromArray($data) : null;
    }
}
