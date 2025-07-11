<?php

namespace App\Domain\Training\StepResolvers;

use App\Domain\Training\Models\TrainingStep;

class EstablishComplianceResolver implements StepResolverInterface
{

    public function resolve(TrainingStep $trainingStep): array
    {
        $correctAnswers = $trainingStep->correctAnswers();

    }
}
