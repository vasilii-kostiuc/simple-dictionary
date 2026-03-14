<?php

namespace App\Domain\Training\Strategies;

use App\Domain\Step\StepFactory;
use App\Domain\Step\Steps\Step;
use App\Domain\Step\WordProviders\WordsProviderInterface;
use App\Domain\Training\Models\Training;

abstract class TrainingStrategyAbstract
{
    protected Training $training;
    protected StepFactory $trainingStepFactory;
    protected WordsProviderInterface $wordsProvider;

    public function __construct(Training $training, StepFactory $trainingStepFactory, WordsProviderInterface $wordsProvider)
    {
        $this->training = $training;
        $this->trainingStepFactory = $trainingStepFactory;
        $this->wordsProvider = $wordsProvider;
    }

    public abstract function generateNextStep(): Step;
}
