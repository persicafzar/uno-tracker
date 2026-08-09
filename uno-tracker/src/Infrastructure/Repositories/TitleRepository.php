<?php

namespace Infrastructure\Repositories;

use Core\Database;

class TitleRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * گرفتن تمام القاب فعال
     */
    public function findAllActive(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM titles WHERE is_active = 1"
        );
    }

    /**
     * گرفتن یک لقب با ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM titles WHERE id = ?", [$id]);
    }

    /**
     * آپدیت دارنده لقب
     */
    public function updateHolder(int $titleId, int $userId, int $recordValue): void
    {
        $this->db->update('titles', [
            'current_holder_id' => $userId,
            'record_value' => $recordValue,
        ], 'id = ?', [$titleId]);
    }

    /**
     * گرفتن رکورد فعلی یک لقب
     */
    public function getCurrentRecord(int $titleId): ?array
    {
        return $this->db->fetchOne(
            "SELECT current_holder_id, record_value FROM titles WHERE id = ?",
            [$titleId]
        );
    }

    /**
     * گرفتن القاب یک کاربر
     */
    public function getUserTitles(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM titles WHERE current_holder_id = ? AND is_active = 1",
            [$userId]
        );
    }
}