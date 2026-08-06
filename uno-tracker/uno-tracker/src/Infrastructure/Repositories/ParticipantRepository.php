<?php

namespace Infrastructure\Repositories;

use Core\Database;
use Domain\GameParticipant;

class ParticipantRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?GameParticipant
    {
        $data = $this->db->fetchOne(
            "SELECT gp.*, u.nickname, u.real_name, u.avatar_path, t.name as team_name
             FROM game_participants gp
             LEFT JOIN users u ON gp.user_id = u.id
             LEFT JOIN teams t ON gp.team_id = t.id
             WHERE gp.id = ?",
            [$id]
        );

        return $data ? GameParticipant::fromArray($data) : null;
    }

    public function findByGameId(int $gameId): array
    {
        $results = $this->db->fetchAll(
            "SELECT gp.*, u.nickname, u.real_name, u.avatar_path, t.name as team_name
             FROM game_participants gp
             LEFT JOIN users u ON gp.user_id = u.id
             LEFT JOIN teams t ON gp.team_id = t.id
             WHERE gp.game_id = ?
             ORDER BY gp.joined_at",
            [$gameId]
        );

        return array_map(fn($data) => GameParticipant::fromArray($data), $results);
    }

    public function create(array $data): int
    {
        return $this->db->insert('game_participants', $data);
    }

    public function update(int $id, array $data): void
    {
        $this->db->update('game_participants', $data, 'id = ?', [$id]);
    }

    public function incrementWins(int $id): void
    {
        $this->db->query(
            "UPDATE game_participants SET wins_count = wins_count + 1 WHERE id = ?",
            [$id]
        );
    }

    /**
     * 🆕 اصلاح شده: پذیرش float برای امتیاز
     */
    public function addScore(int $id, float $score): void
    {
        // 🆕 گرد کردن به ۲ رقم اعشار برای جلوگیری از مشکلات precision
        $roundedScore = round($score, 2);

        $this->db->query(
            "UPDATE game_participants SET total_score = total_score + ? WHERE id = ?",
            [$roundedScore, $id]
        );
    }

    /**
     * 🆕 متد جدید: تنظیم امتیاز (نه اضافه کردن)
     */
    public function setScore(int $id, float $score): void
    {
        $roundedScore = round($score, 2);

        $this->db->query(
            "UPDATE game_participants SET total_score = ? WHERE id = ?",
            [$roundedScore, $id]
        );
    }

    /**
     * علامت‌گذاری یک participant به عنوان برنده (Solo mode)
     */
    public function setWinner(int $id): void
    {
        $this->db->update('game_participants', ['is_winner' => 1], 'id = ?', [$id]);
    }
    /**
     * 🆕 جدید: علامت‌گذاری همه اعضای یک تیم به عنوان برنده (Team mode)
     * 
     * @param int $gameId شناسه بازی
     * @param int $teamId شناسه تیم برنده
     * @return int تعداد رکوردهای به‌روز شده
     */
    public function setTeamWinners(int $gameId, int $teamId): int
    {
        return $this->db->execute(
            "UPDATE game_participants 
         SET is_winner = 1 
         WHERE game_id = ? AND team_id = ?",
            [$gameId, $teamId]
        );
    }

    /**
     * 🆕 جدید: گرفتن همه اعضای یک تیم
     * 
     * @return GameParticipant[]
     */
    public function findByTeam(int $gameId, int $teamId): array
    {
        $results = $this->db->fetchAll(
            "SELECT gp.*, u.nickname, u.real_name, u.avatar_path, t.name as team_name
         FROM game_participants gp
         LEFT JOIN users u ON gp.user_id = u.id
         LEFT JOIN teams t ON gp.team_id = t.id
         WHERE gp.game_id = ? AND gp.team_id = ?
         ORDER BY gp.joined_at",
            [$gameId, $teamId]
        );

        return array_map(fn($data) => GameParticipant::fromArray($data), $results);
    }
    /**
     * بررسی اینکه آیا کاربر در بازی فعال است
     */
    public function isUserInActiveGame(int $userId): bool
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count
             FROM game_participants gp
             JOIN games g ON gp.game_id = g.id
             WHERE gp.user_id = ?
             AND g.status = 'active'",
            [$userId]
        );

        return $result && $result['count'] > 0;
    }

    public function deleteByGameId(int $gameId): void
    {
        $this->db->delete('game_participants', 'game_id = ?', [$gameId]);
    }

    /**
     * 🆕 متد جدید: حذف بازیکن خاص
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('game_participants', 'id = ?', [$id]);
    }

    /**
     * 🆕 متد جدید: گرفتن آمار بازیکن
     */
    public function getPlayerStats(int $userId): array
    {
        $stats = $this->db->fetchOne(
            "SELECT 
                COUNT(DISTINCT gp.game_id) as total_games,
                SUM(gp.wins_count) as total_wins,
                SUM(gp.total_score) as total_score,
                AVG(gp.total_score) as avg_score
             FROM game_participants gp
             JOIN games g ON gp.game_id = g.id
             WHERE gp.user_id = ?
             AND g.status IN ('active', 'finished')",
            [$userId]
        );

        return $stats ?: [
            'total_games' => 0,
            'total_wins' => 0,
            'total_score' => 0,
            'avg_score' => 0,
        ];
    }
    /**
     * 🆕 اضافه کردن امتیاز به همه اعضای یک تیم
     */
    public function addScoreToTeam(int $teamId, int $gameId, float $score): int
    {
        $stmt = $this->db->query(
            "UPDATE game_participants 
         SET total_score = total_score + ? 
         WHERE team_id = ? AND game_id = ?",
            [$score, $teamId, $gameId]
        );

        return $stmt ? $stmt->rowCount() : 0;
    }

    /**
     * 🆕 کم کردن امتیاز از همه اعضای یک تیم (برای undo)
     */
    public function subtractScoreFromTeam(int $teamId, int $gameId, float $score): int
    {
        $stmt = $this->db->query(
            "UPDATE game_participants 
         SET total_score = GREATEST(0, total_score - ?) 
         WHERE team_id = ? AND game_id = ?",
            [$score, $teamId, $gameId]
        );

        return $stmt ? $stmt->rowCount() : 0;
    }
}
