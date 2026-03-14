<?php

namespace App\Domain\Training\Service;

use App\Domain\Step\Steps\Step;
use App\Domain\Training\Events\StepSkippedEvent;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Models\TrainingStep;

class TrainingStepService
{
    public function create(Step $wordTrainingStep, Training $training): TrainingStep
    {
        $step = TrainingStep::create([
            'training_id' => $training->id,
            'step_data' => $wordTrainingStep->toArray(),
            'step_type_id' => $wordTrainingStep->getStepType()->value,
            'step_number' => $this->calculateNextStepNumber($training),
            'required_answers_count' => $wordTrainingStep->getRequiredAnswersCount(),
        ]);

        return $step;
    }

    public function skip(TrainingStep $step)
    {
        if ($step->isPassedOrSkipped()) {
            return;
        }

        $step->skipped = true;
        $step->skipped_at = now();
        $step->save();

        event(new StepSkippedEvent($step->training, $step));

        return $step;
    }

    private function calculateNextStepNumber(Training $training): int
    {
        return $training->steps()->count() + 1;
    }
}
