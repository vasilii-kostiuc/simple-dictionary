<?php

namespace App\Domain\Training\StepResolvers;

use App\Domain\Training\Models\TrainingStep;

class ChooseCorrectAnswerResolver implements StepResolverInterface
{
    public function resolve(TrainingStep $trainingStep): array
    {
        $attemptData['word_id'] = $trainingStep->step_data['word_id'];

        return $attemptData;
    }
}
