<?php

namespace App\Core\Step\StepResolvers;

interface StepResolverInterface
{
    public function resolve(array $step_data): array;
}
