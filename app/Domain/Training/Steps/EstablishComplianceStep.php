<?php

namespace App\Domain\Training\Steps;

use App\Domain\Step\Enums\StepType;

class EstablishComplianceStep extends WordTrainingStep
{
    private array $words;
    private array $answersOrder;

    public function __construct(array $words, array $answersOrder)
    {
        parent::__construct(StepType::EstablishCompliance);
        $this->words = $words;
        $this->requiredAnswersCount = count($words);
        $this->answersOrder = $answersOrder;
    }

    public function toArray(): array
    {
        return [
            'words' => $this->words,
            'answers_order' => $this->answersOrder,
        ];
    }
}
