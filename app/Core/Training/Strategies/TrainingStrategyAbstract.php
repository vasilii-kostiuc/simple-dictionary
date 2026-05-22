<?php

namespace App\Core\Training\Strategies;

use App\Core\Step\StepFactory;
use App\Core\Step\Steps\Step;
use App\Core\Step\WordProviders\WordsProviderInterface;
use App\Core\Training\Models\Training;

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
