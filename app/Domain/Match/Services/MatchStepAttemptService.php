<?php

namespace App\Domain\Match\Services;

use App\Domain\Match\Models\MatchStep;
use App\Domain\Match\Models\MatchStepAttempt;
use App\Domain\Match\Models\MatchUser;
use App\Domain\Step\Enums\StepType;
use App\Domain\Step\StepAttemptVerifierFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MatchStepAttemptService
{
    public function __construct(
        private readonly StepAttemptVerifierFactory $verifierFactory
    ) {
    }

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

        $matchUser = $this->resolveParticipant($step);

        if ($matchUser !== null) {
            if ($matchUser->status->isTerminal()) {
                throw new HttpException(409, 'Participant is already in a terminal status.');
            }

            $matchUser->incrementScore($isCorrect);
        }

        return $attempt;
    }

    public function resolveParticipant(MatchStep $step): ?MatchUser
    {
        return MatchUser::where('match_id', $step->match_id)
            ->where(function ($q) use ($step) {
                if ($step->user_id) {
                    $q->where('user_id', $step->user_id);
                } elseif ($step->guest_id) {
                    $q->where('guest_id', $step->guest_id);
                }
            })
            ->first();
    }
}
