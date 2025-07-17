<?php

namespace App\Domain\Training\StepResolvers;

use App\Domain\Training\Models\TrainingStep;

class WriteAnswerResolver implements StepResolverInterface
{
    public function resolve(TrainingStep $trainingStep): array
    {
        $acceptableAnswers = $trainingStep->step_data['acceptable_answers'];
        $attemptData['word'] = $acceptableAnswers[array_rand($acceptableAnswers)];

        return $attemptData;
    }
}
