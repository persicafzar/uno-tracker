<?php

namespace Infrastructure\Repositories;

use Core\Database;

class LeaderboardRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * گرفتن بازیکنان برتر
     */
    public function getTopPlayers(string $period = 'all', int $limit = 10): array
    {
        $whereClause = "g.status = 'finished'";
        $params = [];

        switch ($period) {
            case 'week':
                $whereClause .= " AND g.finished_at >= ?";
                $params[] = date('Y-m-d', strtotime('-7 days'));
                break;
            case 'month':
                $whereClause .= " AND g.finished_at >= ?";
                $params[] = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'year':
                $whereClause .= " AND g.finished_at >= ?";
                $params[] = date('Y-m-d', strtotime('-365 days'));
                break;
        }

        return $this->db->fetchAll(
            "SELECT 
                u.id,
                u.nickname,
                u.real_name,
                u.avatar_path,
                COUNT(DISTINCT g.id) as total_games,
                SUM(CASE WHEN g.winner_participant_id = gp.id THEN 1 ELSE 0 END) as total_wins,
                SUM(gp.total_score) as total_points,
                ROUND(
                    (SUM(CASE WHEN g.winner_participant_id = gp.id THEN 1 ELSE 0 END) * 100.0) / 
                    NULLIF(COUNT(DISTINCT g.id), 0), 
                    1
                ) as win_rate,
                MAX(g.finished_at) as last_game
             FROM users u
             JOIN game_participants gp ON u.id = gp.user_id
             JOIN games g ON gp.game_id = g.id
             WHERE {$whereClause}
             GROUP BY u.id, u.nickname, u.real_name, u.avatar_path
             HAVING total_games >= 1
             ORDER BY total_points DESC, total_wins DESC, win_rate DESC
             LIMIT ?",
            array_merge($params, [$limit])
        );
    }

    /**
     * گرفتن رتبه کاربر
     */
    public function getUserRank(int $userId, string $period = 'all'): ?int
    {
        $whereClause = "g.status = 'finished'";
        $params = [];

        switch ($period) {
            case 'week':
                $whereClause .= " AND g.finished_at >= ?";
                $params[] = date('Y-m-d', strtotime('-7 days'));
                break;
            case 'month':
                $whereClause .= " AND g.finished_at >= ?";
                $params[] = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'year':
                $whereClause .= " AND g.finished_at >= ?";
                $params[] = date('Y-m-d', strtotime('-365 days'));
                break;
        }

        $allPlayers = $this->db->fetchAll(
            "SELECT 
                u.id,
                SUM(gp.total_score) as total_points
             FROM users u
             JOIN game_participants gp ON u.id = gp.user_id
             JOIN games g ON gp.game_id = g.id
             WHERE {$whereClause}
             GROUP BY u.id
             HAVING COUNT(DISTINCT g.id) >= 1
             ORDER BY total_points DESC, 
                      SUM(CASE WHEN g.winner_participant_id = gp.id THEN 1 ELSE 0 END) DESC",
            $params
        );

        foreach ($allPlayers as $index => $player) {
            if ((int)$player['id'] === $userId) {
                return $index + 1;
            }
        }

        return null;
    }
}