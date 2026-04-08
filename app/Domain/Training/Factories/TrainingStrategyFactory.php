<?php

namespace App\Domain\Training\Factories;

use App\Domain\Step\Enums\StepType;
use App\Domain\Step\StepFactory;
use App\Domain\Step\WordProviders\TopWordsProvider;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Strategies\SpecificStepTypeTrainingStrategy;
use App\Domain\Training\Strategies\TrainingStrategyAbstract;

class TrainingStrategyFactory
{
    private StepFactory $stepFactory;

    public function __construct(StepFactory $stepFactory)
    {
        $this->stepFactory = $stepFactory;
    }

    public function create(Training $training): TrainingStrategyAbstract
    {
        $wordsProvider = new TopWordsProvider(
            $training->dictionary->language_from_id,
            $training->dictionary->language_to_id,
        );

        return new SpecificStepTypeTrainingStrategy(
            $training,
            $this->stepFactory,
            $wordsProvider,
            [StepType::ChooseCorrectAnswer, StepType::WriteCorrectAnswer],
        );
    }
}
