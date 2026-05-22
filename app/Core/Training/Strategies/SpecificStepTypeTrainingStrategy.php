<?php

namespace App\Core\Training\Strategies;

use App\Core\Step\StepFactory;
use App\Core\Step\Steps\Step;
use App\Core\Step\WordProviders\WordsProviderInterface;
use App\Core\Training\Models\Training;

class SpecificStepTypeTrainingStrategy extends TrainingStrategyAbstract
{
    private array $stepTypes;

    public function __construct(Training $training, StepFactory $trainingStepFactory, WordsProviderInterface $wordsProvider, array $stepTypes)
    {
        $this->stepTypes = $stepTypes;

        parent::__construct($training, $trainingStepFactory, $wordsProvider);
    }

    public function generateNextStep(): Step
    {
        $stepType = $this->stepTypes[array_rand($this->stepTypes)];
        return $this->trainingStepFactory->createStep($stepType, $this->wordsProvider);
    }
}
