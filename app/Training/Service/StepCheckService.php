<?php

namespace App\Training\Service;

use App\Training\Factories\StepVerifierFactory;
use App\Training\Factories\TrainingStepFactory;
use App\Training\Models\TrainingStep;

class StepCheckService
{
    public function check(TrainingStep $trainingStep, array $attemptData): bool
    {
        $stepVerifier = $this->stepVerifierFactory->create($trainingStep);

        return $stepVerifier->isPassed($trainingStep, $attemptData);
    }
}
