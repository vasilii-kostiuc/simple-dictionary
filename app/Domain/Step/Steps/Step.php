<?php

namespace App\Domain\Step\Steps;

use App\Domain\Step\Enums\StepType;

abstract class Step
{
    private readonly StepType $stepType;

    protected int $requiredAnswersCount = 1;

    public function __construct(StepType $stepType)
    {
        $this->stepType = $stepType;
    }

    public function getStepType(): StepType
    {
        return $this->stepType;
    }

    public function getRequiredAnswersCount(): int
    {
        return $this->requiredAnswersCount;
    }

    public abstract function toArray(): array;
}
