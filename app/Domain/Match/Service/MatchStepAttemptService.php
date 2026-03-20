<?php

namespace App\Domain\Match\Service;

use App\Domain\Match\Events\MatchUserAnsweredEvent;
use App\Domain\Match\Models\{MatchStep, MatchStepAttempt, MatchUser};
use App\Domain\Step\Enums\StepType;
use App\Domain\Step\StepAttemptVerifierFactory;

class MatchStepAttemptService
{
    public function __construct(
        private StepAttemptVerifierFactory $verifierFactory
    ) {}

    public function submitAnswer(MatchStep $step, array $attemptData, int $attemptNumber): MatchStepAttempt
    {
        $verifier = $this->verifierFactory->create(StepType::from($step->step_type_id));
        $isCorrect = $verifier->verify($step->step_data, $attemptData);

        $attempt = MatchStepAttempt::create([
            'match_step_id' => $step->id,
            'attempt_number' => $attemptNumber,
            'sub_index' => $step->getNextAttemptSubIndex(),
            'attempt_data' => $attemptData,
            'is_correct' => $isCorrect,
        ]);

        // Обновляем счёт участника
        $matchUser = MatchUser::where('match_id', $step->match_id)
            ->where(function ($q) use ($step) {
                if ($step->user_id) {
                    $q->where('user_id', $step->user_id);
                } elseif ($step->guest_id) {
                    $q->where('guest_id', $step->guest_id);
                }
            })
            ->first();

        if ($matchUser) {
            $matchUser->incrementScore($isCorrect);

            event(new MatchUserAnsweredEvent(
                $step->match,
                $step->getParticipantIdentifier(),
                $isCorrect,
                $matchUser
            ));
        }

        return $attempt;
    }
}
