<?php

namespace App\Core\Match\Factories;

use App\Core\Match\Models\MatchModel;
use App\Core\Match\Strategies\{MatchStrategyAbstract, RandomMatchStrategy};
use App\Core\Step\StepFactory;
use App\Core\Step\WordProviders\{TopWordsProvider, WordsProviderInterface};

class MatchStrategyFactory
{
    public function __construct(
        private StepFactory $stepFactory
    ) {}

    public function make(MatchModel $match): MatchStrategyAbstract
    {
        $wordsProvider = $this->createWordsProvider($match);

        // В будущем можно добавить разные стратегии
        return new RandomMatchStrategy(
            $match,
            $this->stepFactory,
            $wordsProvider
        );
    }

    private function createWordsProvider(MatchModel $match): WordsProviderInterface
    {
        return new TopWordsProvider(
            $match->language_from_id,
            $match->language_to_id
        );
    }
}
