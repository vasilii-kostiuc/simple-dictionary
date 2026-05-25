<?php

namespace App\Core\Match\Services;

use App\Core\Match\DTO\ParticipantIdentifier;
use App\Core\Match\Factories\MatchStrategyFactory;
use App\Core\Match\Models\MatchModel;
use App\Core\Match\Models\MatchStep;
use App\Core\Step\Steps\Step;

class MatchStepService
{
    public function __construct(
        private readonly MatchStrategyFactory $strategyFactory
    ) {}

    public function generateNextStepForParticipant(MatchModel $match, ParticipantIdentifier $participant): MatchStep
    {
        $strategy = $this->strategyFactory->make($match);
        $domainStep = $strategy->generateNextStep();

        return $this->create($domainStep, $match, $participant);
    }

    public function create(Step $domainStep, MatchModel $match, ParticipantIdentifier $participant): MatchStep
    {
        return MatchStep::create([
            'match_id' => $match->id,
            'user_id' => $participant->userId,
            'guest_id' => $participant->guestId,
            'step_type_id' => $domainStep->getStepType()->value,
            'step_data' => $domainStep->toArray(),
            'step_number' => $this->calculateNextStepNumber($match, $participant),
            'required_answers_count' => $domainStep->getRequiredAnswersCount(),
        ]);
    }

    public function skip(MatchStep $step): MatchStep
    {
        if ($step->isPassedOrSkipped()) {
            return $step;
        }

        $step->skipped = true;
        $step->skipped_at = now();
        $step->save();

        return $step;
    }

    private function calculateNextStepNumber(MatchModel $match, ParticipantIdentifier $participant): int
    {
        return $match->steps()
            ->where(function ($q) use ($participant) {
                if ($participant->userId) {
                    $q->where('user_id', $participant->userId);
                } elseif ($participant->guestId) {
                    $q->where('guest_id', $participant->guestId);
                }
            })
            ->count() + 1;
    }
}
