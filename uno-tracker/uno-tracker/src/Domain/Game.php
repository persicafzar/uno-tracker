<?php

namespace Domain;

class Game
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_CANCELLED = 'cancelled';

    public const MODE_SOLO = 'solo';
    public const MODE_FRIENDLY = 'friendly';

    public int $id;
    public int $referee_id;
    public string $name;
    public string $game_mode;
    public int $target_wins;
    public string $status;
    public string $team_builder_algorithm;
    public ?int $first_player_participant_id = null;
    public ?int $winner_participant_id = null;
    public int $total_rounds_played = 0;
    public ?string $started_at = null;
    public ?string $finished_at = null;
    public string $created_at;

    public array $participants = [];
    public array $rounds = [];
    public array $teams = [];

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->referee_id = (int) $data['referee_id'];
        $this->name = $data['name'] ?? '';
        $this->game_mode = $data['game_mode'];
        $this->target_wins = (int) $data['target_wins'];
        $this->status = $data['status'];
        $this->team_builder_algorithm = $data['team_builder_algorithm'] ?? 'manual';
        $this->first_player_participant_id = $data['first_player_participant_id'] ? (int) $data['first_player_participant_id'] : null;
        $this->winner_participant_id = $data['winner_participant_id'] ? (int) $data['winner_participant_id'] : null;
        $this->total_rounds_played = (int) ($data['total_rounds_played'] ?? 0);
        $this->started_at = $data['started_at'];
        $this->finished_at = $data['finished_at'];
        $this->created_at = $data['created_at'];
    }

    public function isTeamMode(): bool
    {
        return $this->game_mode === self::MODE_FRIENDLY;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPaused(): bool
    {
        return $this->status === self::STATUS_PAUSED;
    }

    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canStart(): bool
    {
        return $this->status === self::STATUS_PENDING && !empty($this->participants);
    }

    public function canPause(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function canResume(): bool
    {
        return $this->status === self::STATUS_PAUSED;
    }

    /**
     * 🆕 اصلاح شده: بررسی برنده در بازی تیمی و انفرادی
     */
    public function canFinish(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }
        return $this->getWinner() !== null;
    }

    /**
     * 🆕 اصلاح شده: پیدا کردن برنده در بازی تیمی و انفرادی
     * 
     * در حالت Solo: participant برنده را برمی‌گرداند
     * در حالت Team: اولین عضو تیم برنده را برمی‌گرداند (برای backward compatibility)
     */
    public function getWinner(): ?GameParticipant
    {
        if ($this->isTeamMode()) {
            $winningTeam = $this->getWinningTeam();
            if (!$winningTeam) return null;

            // برای backward compatibility، اولین عضو تیم برنده را برگردان
            $members = $winningTeam->getMembers();
            return $members[0] ?? null;
        } else {
            // در بازی انفرادی
            foreach ($this->participants as $participant) {
                if ($participant->wins_count >= $this->target_wins) {
                    return $participant;
                }
            }
            return null;
        }
    }

    /**
     * 🆕 جدید: گرفتن تیم برنده در حالت تیمی
     */
    public function getWinningTeam(): ?Team
    {
        if (!$this->isTeamMode()) {
            return null;
        }

        foreach ($this->teams as $team) {
            $teamTotalWins = $team->getTotalWins();
            if ($teamTotalWins >= $this->target_wins) {
                return $team;
            }
        }

        return null;
    }

    /**
     * 🆕 جدید: گرفتن همه اعضای تیم برنده
     * 
     * @return GameParticipant[]
     */
    public function getWinningTeamMembers(): array
    {
        $winningTeam = $this->getWinningTeam();
        if (!$winningTeam) {
            return [];
        }

        return $winningTeam->getMembers();
    }

    /**
     * 🆕 جدید: بررسی اینکه آیا یک participant عضو تیم برنده است
     */
    public function isTeamWinner(GameParticipant $participant): bool
    {
        if (!$this->isTeamMode()) {
            return false;
        }

        $winningTeam = $this->getWinningTeam();
        if (!$winningTeam || !$participant->team_id) {
            return false;
        }

        return $participant->team_id === $winningTeam->id;
    }

    /**
     * 🆕 جدید: بررسی اینکه آیا یک participant برنده بازی است
     * 
     * در Solo: تطابق با winner
     * در Team: عضو تیم برنده بودن
     */
    public function isGameWinner(GameParticipant $participant): bool
    {
        if ($this->isTeamMode()) {
            return $this->isTeamWinner($participant);
        }

        $winner = $this->getWinner();
        return $winner && $winner->id === $participant->id;
    }

    public function addTeam(Team $team): void
    {
        $this->teams[] = $team;
    }

    public function addParticipant(GameParticipant $participant): void
    {
        $this->participants[] = $participant;
    }
}
