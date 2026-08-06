<?php

namespace Infrastructure\Repositories;

use Core\Database;

class WinTypeRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * گرفتن همه نوع‌های برد فعال
     */
    public function findAllActive(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM win_types WHERE is_active = 1 ORDER BY score_multiplier"
        );
    }

    /**
     * گرفتن یک نوع برد با شناسه
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM win_types WHERE id = ? AND is_active = 1",
            [$id]
        );
    }
}
