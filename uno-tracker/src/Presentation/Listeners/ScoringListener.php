<?php

namespace Presentation\Listeners;

use Application\Services\ScoringService;
use Infrastructure\Repositories\ParticipantRepository;

class ScoringListener
{
    private ScoringService $scoringService;
    private ParticipantRepository $participantRepo;

    public function __construct()
    {
        $this->scoringService = new ScoringService();
        $this->participantRepo = new ParticipantRepository();
    }

    /**
     * محاسبه و ثبت امتیاز پس از هر دور
     * 🆕 اصلاح: فقط game_rounds.calculated_score را به‌روز می‌کند
     * و از اضافه‌کردن مجدد به game_participants جلوگیری می‌کند
     */
    public function handle(array $data): void
    {
        $winnerId = $data['winner_id'] ?? null;
        $cardId = $data['card_id'] ?? null;
        $winTypeId = $data['win_type_id'] ?? null;
        $participantId = $data['participant_id'] ?? null;

        if (!$winnerId || !$participantId) {
            return;
        }

        // محاسبه امتیاز (برای اطمینان از هماهنگی)
        $score = $this->scoringService->calculateRoundScore($cardId, $winTypeId, $winnerId);

        // 🆕 فقط game_rounds.calculated_score را به‌روز کن
        // (اگر قبلاً ذخیره نشده باشد)
        \Core\Database::getInstance()->update(
            'game_rounds',
            [
                'calculated_score' => $score,
            ],
            'game_id = ? AND winner_participant_id = ? AND calculated_score = 0',
            [$data['game_id'], $participantId]
        );

        // ❌ حذف خط زیر: امتیاز قبلاً در RefereeService اضافه شده است
        // $this->scoringService->addScoreToParticipant($participantId, $score);
    }
}
