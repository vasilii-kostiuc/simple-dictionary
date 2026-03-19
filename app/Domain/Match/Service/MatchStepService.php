<?php

namespace App\Domain\Match\Service;

use App\Domain\Match\Events\MatchStepSkippedEvent;
use App\Domain\Match\Factories\MatchStrategyFactory;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Models\MatchStep;
use App\Domain\Step\Steps\Step;

class MatchStepService
{
    public function __construct(
        private MatchStrategyFactory $strategyFactory
    ) {
    }

    public function generateNextStepForParticipant(MatchModel $match, ?int $userId, ?string $guestId): MatchStep
    {
        $strategy = $this->strategyFactory->make($match);
        $domainStep = $strategy->generateNextStep();

        return $this->create($domainStep, $match, $userId, $guestId);
    }

    public function create(Step $domainStep, MatchModel $match, ?int $userId, ?string $guestId): MatchStep
    {
        return MatchStep::create([
            'match_id' => $match->id,
            'user_id' => $userId,
            'guest_id' => $guestId,
            'step_type_id' => $domainStep->getStepType()->value,
            'step_data' => $domainStep->toArray(),
            'step_number' => $this->calculateNextStepNumber($match, $userId, $guestId),
            'required_answers_count' => $domainStep->getRequiredAnswersCount(),
        ]);
    }

    private function calculateNextStepNumber(MatchModel $match, ?int $userId, ?string $guestId): int
    {
        return $match->steps()
            ->where(function ($q) use ($userId, $guestId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } elseif ($guestId) {
                    $q->where('guest_id', $guestId);
                }
            })
            ->count() + 1;
    }

    public function skip(MatchStep $step): MatchStep
    {
        if ($step->isPassedOrSkipped()) {
            return $step;
        }

        $step->skipped = true;
        $step->skipped_at = now();
        $step->save();

        event(new MatchStepSkippedEvent($step->match, $step));

        return $step;
    }
}
