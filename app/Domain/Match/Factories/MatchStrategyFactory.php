<?php

namespace App\Domain\Match\Factories;

use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Strategies\{MatchStrategyAbstract, RandomMatchStrategy};
use App\Domain\Step\StepFactory;
use App\Domain\Step\WordProviders\{TopWordsProvider, WordsProviderInterface};

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
        $dictionary = $match->dictionary;

        return new TopWordsProvider(
            $dictionary->language_from_id,
            $dictionary->language_to_id
        );
    }
}
