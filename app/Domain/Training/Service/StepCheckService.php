<?php

namespace App\Domain\Training\Service;

use App\Domain\Training\Models\TrainingStep;
use App\Training\Factories\StepVerifierFactory;

class StepCheckService
{
    public function check(TrainingStep $trainingStep, array $attemptData): bool
    {
        $stepVerifier = $this->stepVerifierFactory->create($trainingStep);

        return $stepVerifier->isPassed($trainingStep, $attemptData);
    }
}
