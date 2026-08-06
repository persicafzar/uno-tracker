<?php

namespace Application\Services;

use Core\Database;
use Core\EventDispatcher;
use Domain\Game;
use Infrastructure\Repositories\GameRepository;
use Infrastructure\Repositories\ParticipantRepository;
use Infrastructure\Repositories\CardRepository;

class RefereeService
{
    private Database $db;
    private GameRepository $gameRepo;
    private ParticipantRepository $participantRepo;
    private CardRepository $cardRepo;
    private EventDispatcher $events;
    private ScoringService $scoringService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->gameRepo = new GameRepository();
        $this->participantRepo = new ParticipantRepository();
        $this->cardRepo = new CardRepository();
        $this->events = EventDispatcher::getInstance();
        $this->scoringService = new ScoringService();
    }

    /**
     * ثبت نتیجه یک دور - 🆕 با پشتیبانی کامل از بازی تیمی
     */
    public function recordRound(int $gameId, int $refereeId, array $data): array
    {
        $winnerParticipantId = (int) $data['winner_participant_id'];
        $winningCardId = !empty($data['winning_card_id']) ? (int) $data['winning_card_id'] : null;
        $winTypeId = !empty($data['win_type_id']) ? (int) $data['win_type_id'] : null;
        $broadcastData = null;
        $result = null;

        try {
            $result = $this->db->transaction(function ($db) use (
                $gameId,
                $refereeId,
                $winnerParticipantId,
                $winningCardId,
                $winTypeId,
                &$broadcastData
            ) {
                $game = $this->gameRepo->lockForUpdate($gameId);

                if (!$game) {
                    return ['success' => false, 'error' => 'بازی یافت نشد'];
                }

                if ($game->referee_id !== $refereeId) {
                    return ['success' => false, 'error' => 'فقط داور بازی می‌تواند نتیجه را ثبت کند'];
                }

                if (!$game->isActive()) {
                    return ['success' => false, 'error' => 'بازی در حالت فعال نیست'];
                }

                $winner = $this->participantRepo->findById($winnerParticipantId);

                if (!$winner || $winner->game_id !== $gameId) {
                    return ['success' => false, 'error' => 'برنده نامعتبر است'];
                }

                if ($winner->wins_count >= $game->target_wins) {
                    return [
                        'success' => false,
                        'error' => 'بازی به هدف خود رسیده است و نمی‌توان دور دیگری ثبت کرد.'
                    ];
                }

                // 🆕 بررسی تیمی بر اساس مجموع بردهای تیم
                if ($game->isTeamMode() && $winner->team_id) {
                    $teamWins = $db->fetchOne(
                        "SELECT SUM(wins_count) as total_wins FROM game_participants WHERE team_id = ? AND game_id = ?",
                        [$winner->team_id, $gameId]
                    );

                    if ($teamWins && $teamWins['total_wins'] >= $game->target_wins) {
                        return [
                            'success' => false,
                            'error' => 'تیم برنده به هدف خود رسیده است.'
                        ];
                    }
                }

                if ($winningCardId) {
                    $card = $this->cardRepo->findById($winningCardId);
                    if (!$card || !$card->is_active) {
                        return ['success' => false, 'error' => 'کارت برنده نامعتبر یا غیرفعال است'];
                    }
                }

                $roundNumber = $this->gameRepo->getNextRoundNumber($gameId);

                // 🆕 محاسبه امتیاز با ScoringService
                $calculatedScore = $this->scoringService->calculateRoundScore(
                    $winningCardId,
                    $winTypeId,
                    $winner->user_id,
                    $game->isTeamMode()
                );

                $teamName = null;
                if ($game->isTeamMode() && $winner->team_id) {
                    $team = $db->fetchOne(
                        "SELECT name FROM teams WHERE id = ?",
                        [$winner->team_id]
                    );
                    if ($team) {
                        $teamName = $team['name'];
                    }
                }

                $roundId = $this->gameRepo->addRound([
                    'game_id' => $gameId,
                    'round_number' => $roundNumber,
                    'winner_participant_id' => $winnerParticipantId,
                    'winning_card_id' => $winningCardId,
                    'win_type_id' => $winTypeId,
                    'calculated_score' => $calculatedScore,
                    'winner_team_name' => $teamName,
                ]);

                // 🆕 فقط برنده wins_count افزایش می‌یابد
                $this->participantRepo->incrementWins($winnerParticipantId);

                // 🆕 اصلاح مهم: امتیازدهی به همه اعضای تیم
                if ($game->isTeamMode() && $winner->team_id) {
                    // امتیاز به همه اعضای تیم اضافه می‌شود
                    $affectedRows = $this->participantRepo->addScoreToTeam(
                        $winner->team_id,
                        $gameId,
                        $calculatedScore
                    );

                    log_message("✅ Team scoring: Added {$calculatedScore} to {$affectedRows} team members (team_id: {$winner->team_id})");
                } else {
                    // بازی انفرادی: فقط برنده امتیاز می‌گیرد
                    $this->participantRepo->addScore($winnerParticipantId, $calculatedScore);
                }

                $db->update('games', [
                    'total_rounds_played' => $roundNumber
                ], 'id = ?', [$gameId]);

                $winner = $this->participantRepo->findById($winnerParticipantId);
                $card = $winningCardId ? $this->cardRepo->findById($winningCardId) : null;

                $this->events->dispatch('round_recorded', [
                    'game_id' => $gameId,
                    'round_id' => $roundId,
                    'round_number' => $roundNumber,
                    'participant_id' => $winnerParticipantId,
                    'winner_id' => $winner->user_id,
                    'winner_user_id' => $winner->user_id,
                    'winner_name' => $winner->getDisplayName(),
                    'card_id' => $winningCardId,
                    'card_name' => $card ? $card->name : null,
                    'card_emoji' => $card ? $card->emoji : null,
                    'win_type_id' => $winTypeId,
                    'score' => $calculatedScore,
                ]);

                if ($winningCardId && $winner->user_id) {
                    $this->updateCardMastery($winner->user_id, $winningCardId);
                }

                // 🆕 بررسی Match Point بر اساس مجموع بردهای تیم
                if ($game->isTeamMode() && $winner->team_id) {
                    $teamWins = $db->fetchOne(
                        "SELECT SUM(wins_count) as total_wins FROM game_participants WHERE team_id = ? AND game_id = ?",
                        [$winner->team_id, $gameId]
                    );

                    if ($teamWins && $teamWins['total_wins'] + 1 === $game->target_wins) {
                        $this->events->dispatch('match_point', [
                            'game_id' => $gameId,
                            'player_id' => $winnerParticipantId,
                            'player_name' => $winner->getDisplayName(),
                            'wins' => $teamWins['total_wins'] + 1,
                            'target' => $game->target_wins,
                            'team_id' => $winner->team_id,
                        ]);
                    }
                } else if ($winner->wins_count + 1 === $game->target_wins) {
                    $this->events->dispatch('match_point', [
                        'game_id' => $gameId,
                        'player_id' => $winnerParticipantId,
                        'player_name' => $winner->getDisplayName(),
                        'wins' => $winner->wins_count + 1,
                        'target' => $game->target_wins,
                    ]);
                }

                $cardInfo = $this->scoringService->getCardInfo($winningCardId);
                $winTypeInfo = $this->scoringService->getWinTypeInfo($winTypeId);



                // src/Application/Services/RefereeService.php
                // در متد recordRound، بعد از ساخت $broadcastData:

                $broadcastData = [
                    'game_id' => $gameId,
                    'round_id' => $roundId,
                    'round_number' => $roundNumber,
                    'winner' => [
                        'id' => $winner->user_id ?? $winner->id,
                        'name' => $winner->getDisplayName(),
                        'participant_id' => $winnerParticipantId,
                    ],
                    'card' => $cardInfo,
                    'win_type' => $winTypeInfo,
                    'score' => $calculatedScore,
                    'team_name' => $teamName,
                    'recorded_at' => date('Y-m-d H:i:s'),
                    'source_user_id' => $refereeId, // 🆕 اضافه شد
                ];

                return [
                    'success' => true,
                    'message' => 'نتیجه دور ثبت شد',
                    'round_number' => $roundNumber,
                    'winner_name' => $winner->getDisplayName(),
                    'calculated_score' => $calculatedScore,
                ];
            });

            if ($result['success'] && $broadcastData) {
                try {
                    $this->broadcastRoundRecorded($gameId, $broadcastData);
                } catch (\Throwable $e) {
                    log_message("❌ SSE broadcast error (round_recorded): " . $e->getMessage());
                }
            }

            return $result;
        } catch (\Throwable $e) {
            log_message("❌ Error in recordRound: " . $e->getMessage());
            return ['success' => false, 'error' => 'خطا در ثبت نتیجه: ' . $e->getMessage()];
        }
    }

    /**
     * لغو آخرین دور
     */
    public function undoLastRound(int $gameId, int $refereeId): array
    {
        $broadcastData = null;
        $result = null;

        try {
            $result = $this->db->transaction(function ($db) use ($gameId, $refereeId, &$broadcastData) {
                $game = $this->gameRepo->lockForUpdate($gameId);

                if (!$game) {
                    return ['success' => false, 'error' => 'بازی یافت نشد'];
                }

                if ($game->referee_id !== $refereeId) {
                    return ['success' => false, 'error' => 'فقط داور بازی می‌تواند دور را لغو کند'];
                }

                if (!$game->isActive()) {
                    return ['success' => false, 'error' => 'بازی در حالت فعال نیست'];
                }

                $lastRound = $db->fetchOne(
                    "SELECT * FROM game_rounds WHERE game_id = ? ORDER BY round_number DESC LIMIT 1",
                    [$gameId]
                );

                if (!$lastRound) {
                    return ['success' => false, 'error' => 'هیچ دوری برای لغو وجود ندارد'];
                }

                $winner = $this->participantRepo->findById($lastRound['winner_participant_id']);

                // 🆕 اصلاح: کم کردن امتیاز از همه اعضای تیم
                if ($game->isTeamMode() && $winner && $winner->team_id) {
                    $this->participantRepo->subtractScoreFromTeam(
                        $winner->team_id,
                        $gameId,
                        $lastRound['calculated_score']
                    );
                } else {
                    // بازی انفرادی
                    $db->query(
                        "UPDATE game_participants SET total_score = GREATEST(0, total_score - ?) WHERE id = ?",
                        [$lastRound['calculated_score'], $lastRound['winner_participant_id']]
                    );
                }

                // فقط wins_count برنده کاهش می‌یابد
                $db->query(
                    "UPDATE game_participants SET wins_count = GREATEST(0, wins_count - 1) WHERE id = ?",
                    [$lastRound['winner_participant_id']]
                );

                $db->delete('game_rounds', 'id = ?', [$lastRound['id']]);

                $newTotal = $game->total_rounds_played - 1;
                $db->update('games', [
                    'total_rounds_played' => max(0, $newTotal),
                ], 'id = ?', [$gameId]);

                $db->insert('referee_actions_log', [
                    'game_id' => $gameId,
                    'referee_id' => $refereeId,
                    'action_type' => 'round_undo',
                    'target_type' => 'game_round',
                    'target_id' => $lastRound['id'],
                    'old_value' => json_encode($lastRound),
                    'new_value' => null,
                ]);

                $this->events->dispatch('round_undone', [
                    'game_id' => $gameId,
                    'round_id' => $lastRound['id'],
                    'round_number' => $lastRound['round_number'],
                ]);

                $broadcastData = [
                    'game_id' => $gameId,
                    'undone_round' => $lastRound['round_number'],
                    'undone_at' => date('Y-m-d H:i:s'),
                ];

                return [
                    'success' => true,
                    'message' => 'دور با موفقیت لغو شد',
                    'undone_round' => $lastRound['round_number'],
                ];
            });

            if ($result['success'] && $broadcastData) {
                try {
                    $this->broadcastRoundUndone($gameId, $broadcastData);
                } catch (\Throwable $e) {
                    log_message("❌ SSE broadcast error (round_undone): " . $e->getMessage());
                }
            }

            return $result;
        } catch (\Throwable $e) {
            log_message("❌ Error in undoLastRound: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'خطا در لغو دور: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ویرایش نتیجه یک دور
     */
    public function editRound(int $gameId, int $roundId, int $refereeId, array $data): array
    {
        try {
            return $this->db->transaction(function ($db) use ($gameId, $roundId, $refereeId, $data) {
                $game = $this->gameRepo->lockForUpdate($gameId);

                if (!$game) {
                    return ['success' => false, 'error' => 'بازی یافت نشد'];
                }

                if ($game->referee_id !== $refereeId) {
                    return ['success' => false, 'error' => 'فقط داور بازی می‌تواند نتیجه را ویرایش کند'];
                }

                $round = $db->fetchOne("SELECT * FROM game_rounds WHERE id = ? AND game_id = ?", [$roundId, $gameId]);
                if (!$round) {
                    return ['success' => false, 'error' => 'دور یافت نشد'];
                }

                $oldWinnerId = $round['winner_participant_id'];
                $newWinnerId = (int) $data['winner_participant_id'];

                if ($oldWinnerId !== $newWinnerId) {
                    $db->query(
                        "UPDATE game_participants SET wins_count = wins_count - 1 WHERE id = ?",
                        [$oldWinnerId]
                    );

                    $db->query(
                        "UPDATE game_participants SET wins_count = wins_count + 1 WHERE id = ?",
                        [$newWinnerId]
                    );
                }

                $db->update('game_rounds', [
                    'winner_participant_id' => $newWinnerId,
                    'winning_card_id' => !empty($data['winning_card_id']) ? (int) $data['winning_card_id'] : null,
                    'win_type_id' => !empty($data['win_type_id']) ? (int) $data['win_type_id'] : null,
                ], 'id = ?', [$roundId]);

                $db->insert('referee_actions_log', [
                    'game_id' => $gameId,
                    'referee_id' => $refereeId,
                    'action_type' => 'round_edit',
                    'target_type' => 'game_round',
                    'target_id' => $roundId,
                    'old_value' => json_encode($round),
                    'new_value' => json_encode($data),
                ]);

                return ['success' => true, 'message' => 'نتیجه دور ویرایش شد'];
            });
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'خطا در ویرایش نتیجه: ' . $e->getMessage()];
        }
    }

    /**
     * انتقال مقام داوری
     */
    public function handoverReferee(int $gameId, int $currentRefereeId, int $newRefereeId): array
    {
        $game = $this->gameRepo->findById($gameId);

        if (!$game) {
            return ['success' => false, 'error' => 'بازی یافت نشد'];
        }

        if ($game->referee_id !== $currentRefereeId) {
            return ['success' => false, 'error' => 'فقط داور فعلی می‌تواند مقام را منتقل کند'];
        }

        if ($game->isFinished() || $game->isCancelled()) {
            return ['success' => false, 'error' => 'بازی پایان یافته است'];
        }

        $this->gameRepo->update($gameId, ['referee_id' => $newRefereeId]);

        $this->db->insert('referee_actions_log', [
            'game_id' => $gameId,
            'referee_id' => $currentRefereeId,
            'action_type' => 'handover',
            'target_type' => 'user',
            'target_id' => $newRefereeId,
        ]);

        $this->events->dispatch('referee_handover', [
            'game_id' => $gameId,
            'old_referee_id' => $currentRefereeId,
            'new_referee_id' => $newRefereeId,
        ]);

        return ['success' => true, 'message' => 'مقام داوری منتقل شد'];
    }

    /**
     * به‌روزرسانی رکورد کاربر با یک کارت
     */
    private function updateCardMastery(int $userId, int $cardId): void
    {
        $mastery = $this->db->fetchOne(
            "SELECT * FROM card_mastery WHERE user_id = ? AND card_id = ?",
            [$userId, $cardId]
        );

        if ($mastery) {
            $newStreak = (int) $mastery['current_streak'] + 1;
            $newMax = max((int) $mastery['max_streak'], $newStreak);

            $this->db->update('card_mastery', [
                'total_wins' => (int) $mastery['total_wins'] + 1,
                'current_streak' => $newStreak,
                'max_streak' => $newMax,
                'last_won_at' => date('Y-m-d H:i:s'),
            ], 'user_id = ? AND card_id = ?', [$userId, $cardId]);
        } else {
            $this->db->insert('card_mastery', [
                'user_id' => $userId,
                'card_id' => $cardId,
                'total_wins' => 1,
                'current_streak' => 1,
                'max_streak' => 1,
                'last_won_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function broadcastRoundRecorded(int $gameId, array $data): void
    {
        try {
            $sseService = new SSEService();
            $sseService->broadcastRoundRecorded($gameId, $data);
        } catch (\Throwable $e) {
            log_message("❌ SSE broadcast error (round_recorded): " . $e->getMessage());
        }
    }

    private function broadcastRoundUndone(int $gameId, array $data): void
    {
        try {
            $sseService = new SSEService();
            $sseService->broadcastRoundUndone($gameId, $data);
        } catch (\Throwable $e) {
            log_message("❌ SSE broadcast error (round_undone): " . $e->getMessage());
        }
    }
}
