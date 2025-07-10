<?php

namespace App\Domain\Training\StepResolvers;

use App\Domain\Training\Models\TrainingStep;
use App\Domain\Training\Steps\WordTrainingStep;

interface StepResolverInterface
{
    public function resolve(TrainingStep $trainingStep): array;
}
