<?php

namespace App\Domain\Step\StepResolvers;

interface StepResolverInterface
{
    public function resolve(array $step_data): array;
}
