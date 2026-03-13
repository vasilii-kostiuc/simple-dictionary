<?php

namespace App\Domain\Training\StepResolvers;

use App\Domain\Training\Models\TrainingStep;

interface StepResolverInterface
{
    public function resolve(TrainingStep $trainingStep): array;
}
