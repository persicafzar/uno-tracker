<?php

namespace Application\Services;

use Core\Database;
use Core\EventDispatcher;
use Domain\Game;
use Domain\GameParticipant;
use Domain\Team;
use Infrastructure\Repositories\GameRepository;
use Infrastructure\Repositories\ParticipantRepository;

class GameService
{
    private Database $db;
    private GameRepository $gameRepo;
    private ParticipantRepository $participantRepo;
    private TeamBuilderService $teamBuilder;
    private EventDispatcher $events;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->gameRepo = new GameRepository();
        $this->participantRepo = new ParticipantRepository();
        $this->teamBuilder = new TeamBuilderService();
        $this->events = EventDispatcher::getInstance();
    }

    /**
     * ایجاد بازی جدید
     */
    public function createGame(array $data): array
    {
        // ... (کد قبلی حفظ می‌شود)
        $refereeId = $data['referee_id'];
        // 🆕 بررسی مجوز ساخت بازی
        $creator = $this->db->fetchOne("SELECT can_create_game FROM users WHERE id = ?", [$refereeId]);
        if ($creator && empty($creator['can_create_game'])) {
            return [
                'success' => false,
                'error' => 'شما مجوز ساخت بازی ندارید. لطفاً با مدیر سیستم تماس بگیرید.',
            ];
        }
        $gameName = trim($data['game_name'] ?? '');
        $gameMode = $data['game_mode'];


        // 🛡️ بررسی محدودیت‌های ضدتقلب
        $antiCheatService = new \Application\Services\AntiCheatService();
        $creationCheck = $antiCheatService->checkGameCreation($refereeId, $gameMode);

        if (!$creationCheck['allowed']) {
            return [
                'success' => false,
                'error' => implode(' ', $creationCheck['reasons']),
            ];
        }

        $targetWins = (int) $data['target_wins'];
        $playerIds = $data['player_ids'] ?? [];
        $teamNames = $data['team_names'] ?? [];
        $teamAlgorithm = $data['team_algorithm'] ?? 'manual';
        $guestPlayers = $data['guest_players'] ?? [];
        $playerTeams = $data['player_teams'] ?? [];

        // 🆕 دریافت ترکیب تیم‌های پیش‌نمایش شده
        $teamAssignments = $data['team_assignments'] ?? [];
        if (is_string($teamAssignments)) {
            $teamAssignments = json_decode($teamAssignments, true) ?? [];
        }

        if (!is_array($playerIds)) $playerIds = [];
        if (!is_array($guestPlayers)) $guestPlayers = [];
        if (!is_array($teamNames)) $teamNames = [];
        if (!is_array($playerTeams)) $playerTeams = [];



        if (empty($gameName)) {
            return ['success' => false, 'error' => 'نام بازی الزامی است'];
        }

        $totalPlayers = count($playerIds) + count($guestPlayers);
        if ($totalPlayers < 2) {
            return [
                'success' => false,
                'error' => 'برای ایجاد بازی حداقل ۲ بازیکن نیاز است (شما ' . $totalPlayers . ' بازیکن انتخاب کرده‌اید)'
            ];
        }

        if ($gameMode === Game::MODE_FRIENDLY) {
            $numTeams = (int) ceil($totalPlayers / 2);
            $validTeamNames = array_filter($teamNames, fn($name) => !empty(trim($name)));
            if (count($validTeamNames) < $numTeams) {
                return ['success' => false, 'error' => 'لطفاً نام تمام تیم‌ها را وارد کنید'];
            }

            if ($teamAlgorithm === 'manual') {
                $teamCounts = array_fill(1, $numTeams, 0);
                foreach ($playerTeams as $key => $teamNumber) {
                    $teamNumber = (int) $teamNumber;
                    if ($teamNumber >= 1 && $teamNumber <= $numTeams) {
                        $teamCounts[$teamNumber]++;
                    }
                }

                foreach ($teamCounts as $teamNum => $count) {
                    if ($count === 0) {
                        return ['success' => false, 'error' => "تیم {$teamNum} هیچ بازیکنی ندارد"];
                    }
                    if ($count > 3) {
                        return ['success' => false, 'error' => "تیم {$teamNum} بیش از ۳ بازیکن دارد"];
                    }
                }

                if (count($playerTeams) !== $totalPlayers) {
                    return ['success' => false, 'error' => 'همه بازیکنان باید به تیم اختصاص داده شوند'];
                }
            }
        }

        foreach ($playerIds as $userId) {
            if ($this->participantRepo->isUserInActiveGame((int) $userId)) {
                $user = $this->db->fetchOne("SELECT nickname FROM users WHERE id = ?", [$userId]);
                return [
                    'success' => false,
                    'error' => "بازیکن " . ($user['nickname'] ?? 'ناشناس') . " در حال حاضر در بازی فعال دیگری حضور دارد"
                ];
            }
        }

        try {
            return $this->db->transaction(function ($db) use (
                $refereeId,
                $gameName,
                $gameMode,
                $targetWins,
                $playerIds,
                $teamNames,
                $teamAlgorithm,
                $guestPlayers,
                $playerTeams,
                $teamAssignments // 🆕 اضافه شد

            ) {
                $gameId = $db->insert('games', [
                    'referee_id' => $refereeId,
                    'name' => $gameName,
                    'game_mode' => $gameMode,
                    'target_wins' => $targetWins,
                    'status' => Game::STATUS_PENDING,
                    'team_builder_algorithm' => $teamAlgorithm,
                ]);

                $teams = [];
                if ($gameMode === Game::MODE_FRIENDLY) {
                    $totalPlayers = count($playerIds) + count($guestPlayers);
                    $numTeams = (int) ceil($totalPlayers / 2);

                    for ($i = 0; $i < $numTeams; $i++) {
                        $teamId = $db->insert('teams', [
                            'game_id' => $gameId,
                            'name' => trim($teamNames[$i] ?? '') ?: "تیم " . ($i + 1),
                            'color_hex' => $this->getTeamColor($i),
                        ]);
                        $teams[] = [
                            'id' => $teamId,
                            'number' => $i + 1,
                            'player_ids' => []
                        ];
                    }
                    // 🆕 اگر team_assignments وجود دارد، از آن استفاده کن
                    if ($teamAlgorithm !== 'manual' && !empty($teamAssignments)) {
                        // استفاده از ترکیب‌های از پیش محاسبه شده
                        foreach ($teamAssignments as $assignment) {
                            $teamNumber = $assignment['team_number'] ?? 0;
                            $assignedPlayerIds = $assignment['player_ids'] ?? [];

                            // پیدا کردن تیم با این شماره
                            foreach ($teams as &$team) {
                                if ($team['number'] === $teamNumber) {
                                    $team['player_ids'] = $assignedPlayerIds;
                                    break;
                                }
                            }
                        }
                        unset($team);

                        log_message("✅ Using pre-calculated team assignments");
                    } else if ($teamAlgorithm !== 'manual') {
                        $teamGroups = $this->teamBuilder->buildTeams($playerIds, $teamAlgorithm, 2);

                        // 🆕 اطمینان از اینکه همه تیم‌ها player_ids دارند
                        foreach ($teams as $index => &$team) {
                            if (!isset($team['player_ids'])) {
                                $team['player_ids'] = [];
                            }
                        }
                        unset($team); // Unset reference

                        // 🆕 اصلاح: استخراج player_ids از teamGroups
                        foreach ($teamGroups as $index => $teamData) {
                            if (isset($teams[$index])) {
                                // 🆕 استخراج player_ids از object
                                $teams[$index]['player_ids'] = $teamData['player_ids'] ?? [];
                            }
                        }
                        log_message("⚠️ Team assignments empty, running algorithm again");
                    }
                }

                foreach ($playerIds as $index => $userId) {
                    $teamId = null;
                    if ($gameMode === Game::MODE_FRIENDLY) {
                        if ($teamAlgorithm === 'manual') {
                            $teamNumber = $playerTeams['user-' . $userId] ?? null;
                            if ($teamNumber) {
                                foreach ($teams as $team) {
                                    if ($team['number'] == $teamNumber) {
                                        $teamId = $team['id'];
                                        break;
                                    }
                                }
                            }
                        } else {
                            foreach ($teams as $team) {
                                if (in_array($userId, $team['player_ids'])) {
                                    $teamId = $team['id'];
                                    break;
                                }
                            }
                        }
                    }

                    $db->insert('game_participants', [
                        'game_id' => $gameId,
                        'user_id' => (int) $userId,
                        'team_id' => $teamId,
                    ]);
                }

                foreach ($guestPlayers as $index => $guestName) {
                    $guestName = trim($guestName);
                    if (empty($guestName)) continue;

                    $teamId = null;
                    if ($gameMode === Game::MODE_FRIENDLY) {
                        if ($teamAlgorithm === 'manual') {
                            $teamNumber = $playerTeams['guest-' . $index] ?? null;
                            if ($teamNumber) {
                                foreach ($teams as $team) {
                                    if ($team['number'] == $teamNumber) {
                                        $teamId = $team['id'];
                                        break;
                                    }
                                }
                            }
                        } else {
                            $teamIndex = ($index + count($playerIds)) % count($teams);
                            $teamId = $teams[$teamIndex]['id'];
                        }
                    }

                    $db->insert('game_participants', [
                        'game_id' => $gameId,
                        'guest_name' => $guestName,
                        'team_id' => $teamId,
                    ]);
                }
                // در انتهای بلوک try در متد createGame، قبل از return:

                $this->events->dispatch('game_created', [
                    'game_id' => $gameId,
                    'user_id' => $refereeId,
                ]);

                return [
                    'success' => true,
                    'game_id' => $gameId,
                    'message' => 'بازی با موفقیت ایجاد شد'
                ];
            });
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'خطا در ایجاد بازی: ' . $e->getMessage()
            ];
        }
    }

    /**
     * شروع بازی
     */
    public function startGame(int $gameId, int $refereeId): array
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
                    return ['success' => false, 'error' => 'فقط داور بازی می‌تواند آن را شروع کند'];
                }

                $game->participants = $this->participantRepo->findByGameId($gameId);

                if ($game->status !== Game::STATUS_PENDING) {
                    $statusLabels = [
                        Game::STATUS_ACTIVE => 'در حال اجرا',
                        Game::STATUS_PAUSED => 'متوقف',
                        Game::STATUS_FINISHED => 'پایان یافته',
                        Game::STATUS_CANCELLED => 'لغو شده',
                    ];
                    $statusLabel = $statusLabels[$game->status] ?? $game->status;
                    return [
                        'success' => false,
                        'error' => "بازی در وضعیت «{$statusLabel}» است و قابل شروع نیست"
                    ];
                }

                if (empty($game->participants)) {
                    return [
                        'success' => false,
                        'error' => 'بازی هیچ بازیکنی ندارد. لطفاً قبل از شروع، بازیکنان را اضافه کنید.'
                    ];
                }

                // $firstPlayer = $game->participants[array_rand($game->participants)];
                // 🆕 انتخاب بازیکن اول بر اساس تنظیمات
                $firstPlayer = $this->selectFirstPlayer($game->participants);

                $db->update('games', [
                    'status' => Game::STATUS_ACTIVE,
                    'started_at' => date('Y-m-d H:i:s'),
                    'first_player_participant_id' => $firstPlayer->id,
                ], 'id = ?', [$gameId]);

                $this->events->dispatch('game_started', [
                    'game_id' => $gameId,
                    'first_player_id' => $firstPlayer->id,
                    'first_player_name' => $firstPlayer->getDisplayName(),
                ]);
                // src/Application/Services/GameService.php
                // در متد startGame، بعد از ساخت $broadcastData:

                $broadcastData = [
                    'game_id' => $gameId,
                    'status' => Game::STATUS_ACTIVE,
                    'first_player' => [
                        'id' => $firstPlayer->id,
                        'name' => $firstPlayer->getDisplayName(),
                    ],
                    'started_at' => date('Y-m-d H:i:s'),
                    'source_user_id' => $refereeId, // 🆕 اضافه شد
                ];

                return [
                    'success' => true,
                    'message' => 'بازی شروع شد',
                    'first_player' => $firstPlayer->getDisplayName()
                ];
            });

            // 🆕 Broadcast **خارج از transaction**
            if ($result['success'] && $broadcastData) {
                try {
                    $this->broadcastGameStarted($gameId, $broadcastData);
                } catch (\Throwable $e) {
                    log_message("❌ SSE broadcast error (game_started): " . $e->getMessage());
                }
            }

            return $result;
        } catch (\Throwable $e) {
            log_message("❌ Error in startGame: " . $e->getMessage());
            return ['success' => false, 'error' => 'خطا در شروع بازی: ' . $e->getMessage()];
        }
    }

    /**
     * ثبت نتیجه یک دور
     */
    public function recordRound(int $gameId, int $winnerParticipantId, ?int $winningCardId = null, ?int $winTypeId = null): array
    {
        $broadcastData = null;
        $result = null;

        try {
            $result = $this->db->transaction(function ($db) use ($gameId, $winnerParticipantId, $winningCardId, $winTypeId, &$broadcastData) {
                $game = $this->gameRepo->lockForUpdate($gameId);

                if (!$game) {
                    return ['success' => false, 'error' => 'بازی یافت نشد'];
                }

                if (!$game->isActive()) {
                    return ['success' => false, 'error' => 'بازی در حالت فعال نیست'];
                }

                $winner = $this->participantRepo->findById($winnerParticipantId);
                if (!$winner || $winner->game_id !== $gameId) {
                    return ['success' => false, 'error' => 'بازیکن برنده در این بازی شرکت ندارد'];
                }

                $roundNumber = $game->total_rounds_played + 1;
                $calculatedScore = 1;

                $roundId = $db->insert('game_rounds', [
                    'game_id' => $gameId,
                    'round_number' => $roundNumber,
                    'winner_participant_id' => $winnerParticipantId,
                    'winning_card_id' => $winningCardId,
                    'win_type_id' => $winTypeId,
                    'calculated_score' => $calculatedScore,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $db->update('games', [
                    'total_rounds_played' => $roundNumber,
                ], 'id = ?', [$gameId]);

                $db->query(
                    "UPDATE game_participants SET wins_count = wins_count + 1 WHERE id = ?",
                    [$winnerParticipantId]
                );

                if ($game->isTeamMode() && $winner->team_id) {
                    $db->query(
                        "UPDATE teams SET wins_count = wins_count + 1 WHERE id = ?",
                        [$winner->team_id]
                    );
                }

                $shouldFinish = $this->checkGameShouldFinish($gameId, $game->target_wins);

                $this->events->dispatch('round_recorded', [
                    'game_id' => $gameId,
                    'round_id' => $roundId,
                    'round_number' => $roundNumber,
                    'winner_id' => $winner->user_id,
                    'winner_participant_id' => $winnerParticipantId,
                    'winner_name' => $winner->getDisplayName(),
                    'card_id' => $winningCardId,
                    'win_type_id' => $winTypeId,
                    'score' => $calculatedScore,
                ]);
                // src/Application/Services/GameService.php
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
                    'card' => $winningCardId ? ['id' => $winningCardId] : null,
                    'score' => $calculatedScore,
                    'should_finish' => $shouldFinish,
                    'recorded_at' => date('Y-m-d H:i:s'),
                    'source_user_id' => $refereeId, // 🆕 اضافه شد
                ];

                return [
                    'success' => true,
                    'message' => 'دور با موفقیت ثبت شد',
                    'round_id' => $roundId,
                    'round_number' => $roundNumber,
                    'should_finish' => $shouldFinish,
                    'winner_name' => $winner->getDisplayName(),
                ];
            });

            // 🆕 Broadcast **خارج از transaction** (بعد از commit)
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
            return ['success' => false, 'error' => 'خطا در ثبت دور: ' . $e->getMessage()];
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

                $db->query(
                    "UPDATE game_participants SET wins_count = GREATEST(0, wins_count - 1) WHERE id = ?",
                    [$lastRound['winner_participant_id']]
                );

                $winner = $this->participantRepo->findById($lastRound['winner_participant_id']);
                if ($game->isTeamMode() && $winner->team_id) {
                    $db->query(
                        "UPDATE teams SET wins_count = GREATEST(0, wins_count - 1) WHERE id = ?",
                        [$winner->team_id]
                    );
                }

                $db->delete('game_rounds', 'id = ?', [$lastRound['id']]);

                $newTotal = $game->total_rounds_played - 1;
                $db->update('games', [
                    'total_rounds_played' => max(0, $newTotal),
                ], 'id = ?', [$gameId]);

                $this->events->dispatch('round_undone', [
                    'game_id' => $gameId,
                    'round_id' => $lastRound['id'],
                    'round_number' => $lastRound['round_number'],
                ]);

                // 🆕 آماده‌سازی داده‌های broadcast
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

            // 🆕 Broadcast **خارج از transaction**
            if ($result['success'] && $broadcastData) {
                try {
                    $this->broadcastRoundUndone($gameId, $broadcastData);
                } catch (\Throwable $e) {
                    log_message("❌ SSE broadcast error (round_undone): " . $e->getMessage());
                }
            }

            return $result;
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'خطا در لغو دور: ' . $e->getMessage()
            ];
        }
    }

    /**
     * بررسی اینکه آیا بازی باید تمام شود
     */
    private function checkGameShouldFinish(int $gameId, int $targetWins): bool
    {
        // بررسی بردهای بازیکنان
        $participants = $this->participantRepo->findByGameId($gameId);
        foreach ($participants as $participant) {
            if ($participant->wins_count >= $targetWins) {
                return true;
            }
        }

        // اگر بازی تیمی است، بررسی بردهای تیم‌ها
        $game = $this->gameRepo->findById($gameId);
        if ($game && $game->isTeamMode()) {
            $teams = $this->db->fetchAll(
                "SELECT wins_count FROM teams WHERE game_id = ?",
                [$gameId]
            );
            foreach ($teams as $team) {
                if ($team['wins_count'] >= $targetWins) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * توقف بازی
     */
    public function pauseGame(int $gameId, int $refereeId): array
    {
        $game = $this->gameRepo->findById($gameId);
        if (!$game) {
            return ['success' => false, 'error' => 'بازی یافت نشد'];
        }

        if ($game->referee_id !== $refereeId) {
            return ['success' => false, 'error' => 'فقط داور بازی می‌تواند آن را متوقف کند'];
        }

        if (!$game->canPause()) {
            return ['success' => false, 'error' => 'بازی قابل توقف نیست'];
        }

        $this->gameRepo->update($gameId, ['status' => Game::STATUS_PAUSED]);
        $this->events->dispatch('game_paused', ['game_id' => $gameId]);
        // در pauseGame:
        $data = [
            'status' => Game::STATUS_PAUSED,
            'game_id' => $gameId,
            'source_user_id' => $refereeId,
        ];



        // 🆕 Broadcast خارج از transaction (اینجا transaction نیست)
        try {
            $this->broadcastGameStatusChanged($gameId, Game::STATUS_PAUSED, $data);
        } catch (\Throwable $e) {
            log_message("❌ SSE broadcast error (game_paused): " . $e->getMessage());
        }

        return ['success' => true, 'message' => 'بازی متوقف شد'];
    }

    /**
     * ادامه بازی
     */
    public function resumeGame(int $gameId, int $refereeId): array
    {
        $game = $this->gameRepo->findById($gameId);
        if (!$game) {
            return ['success' => false, 'error' => 'بازی یافت نشد'];
        }

        if ($game->referee_id !== $refereeId) {
            return ['success' => false, 'error' => 'فقط داور بازی می‌تواند آن را ادامه دهد'];
        }

        if (!$game->canResume()) {
            return ['success' => false, 'error' => 'بازی قابل ادامه نیست'];
        }

        $this->gameRepo->update($gameId, ['status' => Game::STATUS_ACTIVE]);
        $this->events->dispatch('game_resumed', ['game_id' => $gameId]);
        // در resumeGame:
        $data = [
            'status' => Game::STATUS_ACTIVE,
            'game_id' => $gameId,
            'source_user_id' => $refereeId,
        ];

        // 🆕 Broadcast
        try {
            $this->broadcastGameStatusChanged($gameId, Game::STATUS_ACTIVE, $data);
        } catch (\Throwable $e) {
            log_message("❌ SSE broadcast error (game_resumed): " . $e->getMessage());
        }

        return ['success' => true, 'message' => 'بازی ادامه یافت'];
    }

    /**
     * لغو بازی
     */
    public function cancelGame(int $gameId, int $refereeId): array
    {
        $game = $this->gameRepo->findById($gameId);
        if (!$game) {
            return ['success' => false, 'error' => 'بازی یافت نشد'];
        }

        if ($game->referee_id !== $refereeId) {
            return ['success' => false, 'error' => 'فقط داور بازی می‌تواند آن را لغو کند'];
        }

        if ($game->isFinished() || $game->isCancelled()) {
            return ['success' => false, 'error' => 'بازی قبلاً پایان یافته یا لغو شده است'];
        }

        $this->gameRepo->cancel($gameId);
        $this->events->dispatch('game_cancelled', ['game_id' => $gameId]);

        // در cancelGame:
        $data = [
            'status' => Game::STATUS_CANCELLED,
            'game_id' => $gameId,
            'source_user_id' => $refereeId,
        ];
        try {
            $this->broadcastGameStatusChanged($gameId, Game::STATUS_CANCELLED, $data);
        } catch (\Throwable $e) {
            log_message("❌ SSE broadcast error (game_cancelled): " . $e->getMessage());
        }

        return ['success' => true, 'message' => 'بازی لغو شد'];
    }

    /**
     * پایان بازی - 🆕 با محاسبه XP کامل
     */
    public function finishGame(int $gameId, int $refereeId): array
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
                    return ['success' => false, 'error' => 'فقط داور بازی می‌تواند آن را پایان دهد'];
                }

                $game->participants = $this->participantRepo->findByGameId($gameId);

                if ($game->isTeamMode()) {
                    $teams = $this->db->fetchAll(
                        "SELECT * FROM teams WHERE game_id = ?",
                        [$gameId]
                    );
                    foreach ($teams as $teamData) {
                        $team = Team::fromArray($teamData);
                        foreach ($game->participants as $participant) {
                            if ($participant->team_id === $team->id) {
                                $team->addMember($participant);
                            }
                        }
                        $game->addTeam($team);
                    }
                }

                if (!$game->canFinish()) {
                    return ['success' => false, 'error' => 'بازی هنوز برنده‌ای ندارد'];
                }

                $winner = $game->getWinner();

                if (!$winner) {
                    return ['success' => false, 'error' => 'برنده‌ای یافت نشد'];
                }

                $db->update('games', [
                    'status' => Game::STATUS_FINISHED,
                    'winner_participant_id' => $winner->id,
                    'finished_at' => date('Y-m-d H:i:s'),
                ], 'id = ?', [$gameId]);

                // 🆕 محاسبه و به‌روزرسانی XP برای همه بازیکنان
                $scoringService = new ScoringService();

                foreach ($game->participants as $participant) {
                    if ($participant->user_id) {
                        $isGameWinner = ($participant->id === $winner->id);

                        if ($isGameWinner) {
                            // XP برنده بازی
                            $xp = $scoringService->calculateWinnerXP((float) $participant->total_score);
                        } else {
                            // XP عادی
                            $xp = $scoringService->calculatePlayerXP(
                                (float) $participant->total_score,
                                false,
                                false
                            );
                        }

                        $scoringService->updateUserXP($participant->user_id, $xp);

                        log_message("✅ User {$participant->user_id} earned {$xp} XP (winner: " . ($isGameWinner ? 'yes' : 'no') . ")");
                    }
                }

                $statsService = new \Application\Services\UserStatsService();
                if ($winner->user_id) {
                    $statsService->refreshLeaderboardCache($winner->user_id);
                }

                foreach ($game->participants as $participant) {
                    if ($participant->user_id) {
                        $statsService->refreshLeaderboardCache($participant->user_id);
                    }
                }

                $this->participantRepo->setWinner($winner->id);

                $winnerName = $winner->getDisplayName();
                if ($game->isTeamMode() && $winner->team_id) {
                    foreach ($game->teams as $team) {
                        if ($team->id === $winner->team_id) {
                            $winnerName = 'تیم ' . $team->name;
                            break;
                        }
                    }
                }

                $this->events->dispatch('game_finished', [
                    'game_id' => $gameId,
                    'winner_id' => $winner->user_id ?? $winner->id,
                    'winner_name' => $winnerName,
                    'total_rounds' => $game->total_rounds_played,
                ]);

                // src/Application/Services/GameService.php
                // در متد finishGame، بعد از ساخت $broadcastData:

                $broadcastData = [
                    'game_id' => $gameId,
                    'status' => Game::STATUS_FINISHED,
                    'winner' => [
                        'id' => $winner->user_id ?? $winner->id,
                        'name' => $winnerName,
                        'participant_id' => $winner->id,
                    ],
                    'total_rounds' => $game->total_rounds_played,
                    'finished_at' => date('Y-m-d H:i:s'),
                    'source_user_id' => $refereeId, // 🆕 اضافه شد
                ];

                return [
                    'success' => true,
                    'message' => 'بازی پایان یافت',
                    'winner' => $winnerName
                ];
            });

            if ($result['success'] && $broadcastData) {
                try {
                    $this->broadcastGameFinished($gameId, $broadcastData);
                } catch (\Throwable $e) {
                    log_message("❌ SSE broadcast error (game_finished): " . $e->getMessage());
                }
            }

            return $result;
        } catch (\Throwable $e) {
            log_message("❌ Error in finishGame: " . $e->getMessage());
            return ['success' => false, 'error' => 'خطا در پایان بازی: ' . $e->getMessage()];
        }
    }



    /**
     * گرفتن بازی با تمام جزئیات
     */
    public function getGameWithDetails(int $gameId): ?Game
    {
        $game = $this->gameRepo->findById($gameId);
        if (!$game) {
            return null;
        }

        $game->participants = $this->participantRepo->findByGameId($gameId);
        $game->rounds = $this->gameRepo->getRounds($gameId);

        if ($game->isTeamMode()) {
            $teams = $this->db->fetchAll(
                "SELECT * FROM teams WHERE game_id = ?",
                [$gameId]
            );
            foreach ($teams as $teamData) {
                $team = Team::fromArray($teamData);
                foreach ($game->participants as $participant) {
                    if ($participant->team_id === $team->id) {
                        $team->addMember($participant);
                    }
                }
                $game->addTeam($team);
            }
        }

        return $game;
    }

    /**
     * تقسیم بازیکنان به تیم‌های مساوی
     */
    private function splitIntoTeams(array $playerIds, int $numTeams): array
    {
        if ($numTeams <= 0) {
            $numTeams = 2;
        }

        $teams = array_fill(0, $numTeams, []);
        $index = 0;

        foreach ($playerIds as $playerId) {
            $teams[$index][] = $playerId;
            $index = ($index + 1) % $numTeams;
        }

        return $teams;
    }

    /**
     * گرفتن رنگ تیم بر اساس ایندکس
     */
    private function getTeamColor(int $index): string
    {
        $colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
        return $colors[$index % count($colors)];
    }

    /**
     * 🆕 گرفتن لیست بازی‌ها با فیلترهای پیشرفته (اصلاح شده)
     */
    public function getGamesList(?int $playerId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = ['1=1'];
        $params = [];

        // 🆕 فیلتر بازیکن خاص
        $targetUserId = $playerId ?: null;

        // اگر بازیکن خاصی انتخاب شده، فقط بازی‌های او را نشان بده
        if ($targetUserId) {
            $where[] = "(g.referee_id = ? OR EXISTS (SELECT 1 FROM game_participants gp_u WHERE gp_u.game_id = g.id AND gp_u.user_id = ?))";
            $params[] = $targetUserId;
            $params[] = $targetUserId;
        }

        // فیلتر حالت
        if (!empty($filters['mode']) && in_array($filters['mode'], ['solo', 'friendly'])) {
            $where[] = "g.game_mode = ?";
            $params[] = $filters['mode'];
        }

        // فیلتر وضعیت
        if (!empty($filters['status']) && in_array($filters['status'], ['pending', 'active', 'paused', 'finished', 'cancelled'])) {
            $where[] = "g.status = ?";
            $params[] = $filters['status'];
        }

        // فیلتر جستجو
        if (!empty($filters['search'])) {
            $where[] = "(g.name LIKE ? OR g.id = ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = (int) $filters['search'];
        }

        // 🆕 فیلتر نقش (فقط وقتی بازیکن انتخاب شده)
        if ($targetUserId && !empty($filters['role'])) {
            switch ($filters['role']) {
                case 'referee_only':
                    // فقط داور بوده (بازیکن نبوده)
                    $where[] = "g.referee_id = ? AND NOT EXISTS (SELECT 1 FROM game_participants gp_r WHERE gp_r.game_id = g.id AND gp_r.user_id = ?)";
                    $params[] = $targetUserId;
                    $params[] = $targetUserId;
                    break;
                case 'player_any':
                    // بازی کرده (شامل داور+بازیکن یا فقط بازیکن)
                    $where[] = "EXISTS (SELECT 1 FROM game_participants gp_r WHERE gp_r.game_id = g.id AND gp_r.user_id = ?)";
                    $params[] = $targetUserId;
                    break;
                case 'both':
                    // هم داور هم بازی کرده
                    $where[] = "g.referee_id = ? AND EXISTS (SELECT 1 FROM game_participants gp_r WHERE gp_r.game_id = g.id AND gp_r.user_id = ?)";
                    $params[] = $targetUserId;
                    $params[] = $targetUserId;
                    break;
                case 'player_only':
                    // فقط بازی کرده و داور نبوده
                    $where[] = "g.referee_id != ? AND EXISTS (SELECT 1 FROM game_participants gp_r WHERE gp_r.game_id = g.id AND gp_r.user_id = ?)";
                    $params[] = $targetUserId;
                    $params[] = $targetUserId;
                    break;
            }
        }

        // 🆕 فیلتر نتیجه (فقط وقتی بازیکن انتخاب شده)
        if ($targetUserId && !empty($filters['result']) && in_array($filters['result'], ['win', 'loss', 'ongoing'])) {
            switch ($filters['result']) {
                case 'win':
                    // بردها: کاربر بازیکن است و برنده شده
                    $where[] = "EXISTS (SELECT 1 FROM game_participants gp_res WHERE gp_res.game_id = g.id AND gp_res.user_id = ? AND gp_res.is_winner = 1)";
                    $params[] = $targetUserId;
                    break;
                case 'loss':
                    // باخت‌ها: کاربر بازیکن است و باخته (بازی تمام شده)
                    $where[] = "EXISTS (SELECT 1 FROM game_participants gp_res WHERE gp_res.game_id = g.id AND gp_res.user_id = ? AND gp_res.is_winner = 0) AND g.status = 'finished'";
                    $params[] = $targetUserId;
                    break;
                case 'ongoing':
                    // در جریان: کاربر بازیکن است و بازی هنوز تمام نشده
                    $where[] = "EXISTS (SELECT 1 FROM game_participants gp_res WHERE gp_res.game_id = g.id AND gp_res.user_id = ?) AND g.status IN ('pending', 'active', 'paused')";
                    $params[] = $targetUserId;
                    break;
            }
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        // 🆕 Query با پارامترهای شرطی
        $selectRole = $targetUserId ? "
        (g.referee_id = ?) as is_referee,
        (SELECT COUNT(*) FROM game_participants gp3 WHERE gp3.game_id = g.id AND gp3.user_id = ?) as is_player_count,
        (SELECT gp4.total_score FROM game_participants gp4 WHERE gp4.game_id = g.id AND gp4.user_id = ? LIMIT 1) as user_score,
        (SELECT gp4.wins_count FROM game_participants gp4 WHERE gp4.game_id = g.id AND gp4.user_id = ? LIMIT 1) as user_wins,
        (SELECT gp4.is_winner FROM game_participants gp4 WHERE gp4.game_id = g.id AND gp4.user_id = ? LIMIT 1) as user_is_winner
    " : "
        0 as is_referee,
        0 as is_player_count,
        NULL as user_score,
        NULL as user_wins,
        NULL as user_is_winner
    ";

        $roleParams = $targetUserId ? [$targetUserId, $targetUserId, $targetUserId, $targetUserId, $targetUserId] : [];

        $games = $this->db->fetchAll(
            "SELECT g.*,
                (SELECT COUNT(*) FROM game_participants gp WHERE gp.game_id = g.id) as total_players,
                (SELECT COUNT(*) FROM teams t WHERE t.game_id = g.id) as total_teams,
                (SELECT u.nickname FROM users u WHERE u.id = g.referee_id) as referee_name,
                (SELECT COALESCE(u.nickname, gp2.guest_name) 
                 FROM game_participants gp2
                 LEFT JOIN users u ON gp2.user_id = u.id
                 WHERE gp2.game_id = g.id AND gp2.is_winner = 1 
                 LIMIT 1) as winner_name,
                 {$selectRole}
         FROM games g
         WHERE {$whereClause}
         ORDER BY g.created_at DESC
         LIMIT ? OFFSET ?",
            array_merge($roleParams, $params, [$perPage, $offset])
        );

        // محاسبه نقش و نتیجه کاربر
        foreach ($games as &$game) {
            // محاسبه نقش کاربر
            if ($targetUserId) {
                $isReferee = (bool) $game['is_referee'];
                $isPlayer = (int) $game['is_player_count'] > 0;

                if ($isReferee && $isPlayer) {
                    $game['user_role'] = 'both';
                } elseif ($isReferee) {
                    $game['user_role'] = 'referee_only';
                } elseif ($isPlayer) {
                    $game['user_role'] = 'player_only';
                } else {
                    $game['user_role'] = 'none';
                }

                // محاسبه نتیجه
                if ($game['user_role'] === 'player_only' || $game['user_role'] === 'both') {
                    if ($game['user_is_winner']) {
                        $game['user_result'] = 'win';
                    } elseif ($game['status'] === 'finished') {
                        $game['user_result'] = 'loss';
                    } else {
                        $game['user_result'] = 'ongoing';
                    }
                } else {
                    $game['user_result'] = 'none';
                }
            } else {
                $game['user_role'] = 'none';
                $game['user_result'] = 'none';
            }
        }

        // تعداد کل
        $total = (int) $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM games g WHERE {$whereClause}",
            $params
        )['count'];

        return [
            'games' => $games,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];
    }

    /**
     * 🆕 گرفتن لیست بازیکنان برای Modal (AJAX)
     */
    public function getPlayersList(string $search = '', int $limit = 20): array
    {
        $where = ["status = 'active'", "can_join_game = 1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(nickname LIKE ? OR real_name LIKE ? OR phone LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        $whereClause = implode(' AND ', $where);

        return $this->db->fetchAll(
            "SELECT id, nickname, real_name, avatar_path, phone
         FROM users
         WHERE {$whereClause}
         ORDER BY nickname ASC
         LIMIT ?",
            array_merge($params, [$limit])
        );
    }

    /**
     * 🆕 گرفتن اطلاعات یک بازیکن خاص
     */
    public function getPlayerInfo(int $playerId): ?array
    {
        return $this->db->fetchOne(
            "SELECT id, nickname, real_name, avatar_path
         FROM users
         WHERE id = ?",
            [$playerId]
        );
    }

    // ============================================
    // 🆕 SSE Broadcast Methods
    // ============================================

    private function broadcastGameStarted(int $gameId, array $data): void
    {
        try {
            $sseService = new \Application\Services\SSEService();
            $sseService->broadcastGameStarted($gameId, $data);
        } catch (\Throwable $e) {
            log_message("SSE broadcast error (game_started): " . $e->getMessage());
        }
    }

    private function broadcastRoundRecorded(int $gameId, array $data): void
    {
        try {
            $sseService = new \Application\Services\SSEService();
            $sseService->broadcastRoundRecorded($gameId, $data);
        } catch (\Throwable $e) {
            log_message("SSE broadcast error (round_recorded): " . $e->getMessage());
        }
    }

    private function broadcastRoundUndone(int $gameId, array $data): void
    {
        try {
            $sseService = new \Application\Services\SSEService();
            $sseService->broadcastRoundUndone($gameId, $data);
        } catch (\Throwable $e) {
            log_message("SSE broadcast error (round_undone): " . $e->getMessage());
        }
    }

    private function broadcastGameFinished(int $gameId, array $data): void
    {
        try {
            $sseService = new \Application\Services\SSEService();
            $sseService->broadcastGameFinished($gameId, $data);
        } catch (\Throwable $e) {
            log_message("SSE broadcast error (game_finished): " . $e->getMessage());
        }
    }

    // src/Application/Services/GameService.php

    private function broadcastGameStatusChanged(int $gameId, string $status, array $extraData = []): void
    {
        try {
            $sseService = new \Application\Services\SSEService();
            $sseService->broadcastGameStatusChanged($gameId, $status, $extraData);
        } catch (\Throwable $e) {
            log_message("SSE broadcast error (status_changed): " . $e->getMessage());
        }
    }

    /**
     * 🆕 پاک کردن کش بازی
     */
    public function clearGameCache(int $gameId): void
    {
        // اگر کش در حافظه باشد، پاک کن
        // در حال حاضر کش نداریم، اما برای آینده آماده است
        log_message("🗑️ Game cache cleared for game #{$gameId}");
    }
    /**
     * 🆕 گرفتن بازی‌های اخیر کاربر با نقش‌های مختلف
     */
    public function getRecentGamesWithRole(int $userId, int $limit = 10): array
    {
        $games = $this->db->fetchAll(
            "SELECT g.*,
        (SELECT COUNT(*) FROM game_participants gp WHERE gp.game_id = g.id) as total_players,
        (SELECT COUNT(*) FROM teams t WHERE t.game_id = g.id) as total_teams,
        (SELECT gp2.wins_count FROM game_participants gp2 WHERE gp2.game_id = g.id AND gp2.user_id = ? LIMIT 1) as wins_count,
        (SELECT gp2.total_score FROM game_participants gp2 WHERE gp2.game_id = g.id AND gp2.user_id = ? LIMIT 1) as total_score,
        (SELECT gp2.id FROM game_participants gp2 WHERE gp2.game_id = g.id AND gp2.user_id = ? LIMIT 1) as participant_id,
        (g.referee_id = ?) as is_referee,
        (SELECT COUNT(*) FROM game_participants gp3 WHERE gp3.game_id = g.id AND gp3.user_id = ?) as is_player_count
        FROM games g
        WHERE (g.referee_id = ? OR EXISTS (SELECT 1 FROM game_participants gp4 WHERE gp4.game_id = g.id AND gp4.user_id = ?))
        ORDER BY g.created_at DESC
        LIMIT ?",
            [$userId, $userId, $userId, $userId, $userId, $userId, $userId, $limit]
        );

        // محاسبه نقش کاربر در هر بازی
        foreach ($games as &$game) {
            $isReferee = (bool) $game['is_referee'];
            $isPlayer = (int) $game['is_player_count'] > 0;

            if ($isReferee && $isPlayer) {
                $game['user_role'] = 'both';
            } elseif ($isReferee) {
                $game['user_role'] = 'referee_only';
            } elseif ($isPlayer) {
                $game['user_role'] = 'player_only';
            } else {
                $game['user_role'] = 'none';
            }
        }

        return $games;
    }

    /**
     * 🆕 انتخاب بازیکن اول بر اساس تنظیمات سیستم
     *
     * @param array $participants لیست شرکت‌کنندگان
     * @return GameParticipant بازیکن انتخاب شده
     */
    private function selectFirstPlayer(array $participants): GameParticipant
    {
        $settingsRepo = \Infrastructure\Repositories\SettingsRepository::getInstance();
        $method = $settingsRepo->get('first_player_selection', 'random');

        // فیلتر فقط بازیکنان واقعی (user_id دارند)
        $realPlayers = array_filter($participants, fn($p) => !empty($p->user_id));

        // اگر بازیکن واقعی نداشت، از همه استفاده کن
        $pool = !empty($realPlayers) ? $realPlayers : $participants;

        switch ($method) {
            case 'by_score':
                return $this->selectByScore($pool);

            case 'by_xp':
                return $this->selectByXP($pool);

            case 'random':
            default:
                return $pool[array_rand($pool)];
        }
    }

    /**
     * 🆕 انتخاب بازیکن با بیشترین امتیاز (total_score از بازی‌های قبلی)
     * در صورت تساوی، اولین بازیکن در لیست انتخاب می‌شود
     */
    private function selectByScore(array $participants): GameParticipant
    {
        $scores = [];
        foreach ($participants as $participant) {
            if (empty($participant->user_id)) continue;

            $stats = $this->db->fetchOne(
                "SELECT COALESCE(SUM(gp.total_score), 0) as total_score
             FROM game_participants gp
             JOIN games g ON gp.game_id = g.id
             WHERE gp.user_id = ? AND g.status = 'finished'",
                [$participant->user_id]
            );

            $scores[$participant->id] = (float)($stats['total_score'] ?? 0);
        }

        if (empty($scores)) {
            return $participants[array_rand($participants)];
        }

        // مرتب‌سازی نزولی، حفظ کلیدها (اولین بیشترین انتخاب می‌شود)
        arsort($scores);
        $bestParticipantId = array_key_first($scores);

        foreach ($participants as $p) {
            if ($p->id === $bestParticipantId) {
                return $p;
            }
        }

        return $participants[0];
    }

    /**
     * 🆕 انتخاب بازیکن با بیشترین XP
     * در صورت تساوی، اولین بازیکن در لیست انتخاب می‌شود
     */
    private function selectByXP(array $participants): GameParticipant
    {
        $xpScores = [];
        foreach ($participants as $participant) {
            if (empty($participant->user_id)) continue;

            $xpData = $this->db->fetchOne(
                "SELECT total_xp FROM user_xp WHERE user_id = ?",
                [$participant->user_id]
            );

            $xpScores[$participant->id] = (int)($xpData['total_xp'] ?? 0);
        }

        if (empty($xpScores)) {
            return $participants[array_rand($participants)];
        }

        arsort($xpScores);
        $bestParticipantId = array_key_first($xpScores);

        foreach ($participants as $p) {
            if ($p->id === $bestParticipantId) {
                return $p;
            }
        }

        return $participants[0];
    }
}
