<?php

namespace App\Core\Training\Factories;

use App\Core\Shared\Cache\CacheInterface;
use App\Core\Step\Enums\StepType;
use App\Core\Step\StepFactory;
use App\Core\Step\WordProviders\TopWordsProvider;
use App\Core\Training\Models\Training;
use App\Core\Training\Strategies\SpecificStepTypeTrainingStrategy;
use App\Core\Training\Strategies\TrainingStrategyAbstract;

class TrainingStrategyFactory
{
    public function __construct(
        private readonly StepFactory $stepFactory,
        private readonly CacheInterface $cache,
    ) {}

    public function create(Training $training): TrainingStrategyAbstract
    {
        $wordsProvider = new TopWordsProvider(
            $training->dictionary->language_from_id,
            $training->dictionary->language_to_id,
            $this->cache,
        );

        return new SpecificStepTypeTrainingStrategy(
            $training,
            $this->stepFactory,
            $wordsProvider,
            [StepType::ChooseCorrectAnswer, StepType::WriteCorrectAnswer],
        );
    }
}
