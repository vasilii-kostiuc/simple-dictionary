<?php

namespace App\Domain\Training\Steps;

use App\Domain\Training\Enums\TrainingStepType;

class EstablishComplianceStep extends WordTrainingStep
{
    private array $words;
    private array $answersOrder;

    public function __construct(array $words, array $answersOrder)
    {
        parent::__construct(TrainingStepType::EstablishCompliance);
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
