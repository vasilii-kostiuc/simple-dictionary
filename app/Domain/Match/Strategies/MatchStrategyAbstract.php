<?php

namespace App\Domain\Match\Strategies;

use App\Domain\Match\Models\MatchModel;
use App\Domain\Step\StepFactory;
use App\Domain\Step\Steps\Step;
use App\Domain\Step\WordProviders\WordsProviderInterface;

abstract class MatchStrategyAbstract
{
    protected MatchModel $match;
    protected StepFactory $stepFactory;
    protected WordsProviderInterface $wordsProvider;

    public function __construct(
        MatchModel $match,
        StepFactory $stepFactory,
        WordsProviderInterface $wordsProvider
    ) {
        $this->match = $match;
        $this->stepFactory = $stepFactory;
        $this->wordsProvider = $wordsProvider;
    }

    abstract public function generateNextStep(): Step;
}
