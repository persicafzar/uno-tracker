<?php

namespace Infrastructure\Repositories;

use Core\Database;

class GameRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?\Domain\Game
    {
        $game = $this->db->fetchOne("SELECT * FROM games WHERE id = ?", [$id]);
        return $game ? new \Domain\Game($game) : null;
    }

    public function lockForUpdate(int $id): ?\Domain\Game
    {
        $game = $this->db->fetchOne("SELECT * FROM games WHERE id = ? FOR UPDATE", [$id]);
        return $game ? new \Domain\Game($game) : null;
    }

    public function update(int $id, array $data): void
    {
        $sets = [];
        $values = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $this->db->query("UPDATE games SET " . implode(', ', $sets) . " WHERE id = ?", $values);
    }

    public function cancel(int $id): void
    {
        $this->db->update('games', [
            'status' => \Domain\Game::STATUS_CANCELLED,
            'finished_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    public function findActiveGames(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM games WHERE status IN ('pending', 'active', 'paused') ORDER BY created_at DESC"
        );
    }

    public function getNextRoundNumber(int $gameId): int
    {
        $result = $this->db->fetchOne(
            "SELECT MAX(round_number) as max_round FROM game_rounds WHERE game_id = ?",
            [$gameId]
        );
        return ($result['max_round'] ?? 0) + 1;
    }

    /**
     * 🆕 اصلاح شده: ذخیره winner_team_name
     */
    public function addRound(array $data): int
    {
        return $this->db->insert('game_rounds', [
            'game_id' => $data['game_id'],
            'round_number' => $data['round_number'],
            'winner_participant_id' => $data['winner_participant_id'],
            'winning_card_id' => $data['winning_card_id'] ?? null,
            'win_type_id' => $data['win_type_id'] ?? null,
            'calculated_score' => $data['calculated_score'],
            'winner_team_name' => $data['winner_team_name'] ?? null, // 🆕
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 🆕 اصلاح شده: برگرداندن winner_team_name
     */
    public function getRounds(int $gameId): array
    {
        $rounds = $this->db->fetchAll(
            "SELECT r.*, u.nickname as winner_nickname, u.avatar_path as winner_avatar,
                    c.name as card_name, c.emoji as card_emoji,
                    wt.name as win_type_name, wt.icon as win_type_icon
             FROM game_rounds r
             LEFT JOIN game_participants gp ON r.winner_participant_id = gp.id
             LEFT JOIN users u ON gp.user_id = u.id
             LEFT JOIN cards c ON r.winning_card_id = c.id
             LEFT JOIN win_types wt ON r.win_type_id = wt.id
             WHERE r.game_id = ?
             ORDER BY r.round_number ASC",
            [$gameId]
        );

        // 🆕 تبدیل به object با تمام properties
        return array_map(function($round) {
            $obj = new \stdClass();
            $obj->id = (int) $round['id'];
            $obj->game_id = (int) $round['game_id'];
            $obj->round_number = (int) $round['round_number'];
            $obj->winner_participant_id = (int) $round['winner_participant_id'];
            $obj->winning_card_id = $round['winning_card_id'] ? (int) $round['winning_card_id'] : null;
            $obj->win_type_id = $round['win_type_id'] ? (int) $round['win_type_id'] : null;
            $obj->calculated_score = (int) $round['calculated_score'];
            $obj->winner_team_name = $round['winner_team_name'] ?? null; // 🆕
            $obj->created_at = $round['created_at'];
            
            // نام برنده
            if ($round['winner_nickname']) {
                $obj->winner_name = $round['winner_nickname'];
            } else {
                // اگر بازیکن مهمان است، نام را از game_participants بگیر
                $participant = $this->db->fetchOne(
                    "SELECT guest_name FROM game_participants WHERE id = ?",
                    [$round['winner_participant_id']]
                );
                $obj->winner_name = $participant['guest_name'] ?? 'بازیکن مهمان';
            }
            
            // اطلاعات کارت
            $obj->card_name = $round['card_name'];
            $obj->card_emoji = $round['card_emoji'];
            
            // اطلاعات نوع برد
            $obj->win_type_name = $round['win_type_name'];
            $obj->win_type_icon = $round['win_type_icon'];
            
            return $obj;
        }, $rounds);
    }
}