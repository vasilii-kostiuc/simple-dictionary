<?php

namespace App\Core\Match\Strategies;

use App\Core\Match\Models\MatchModel;
use App\Core\Step\StepFactory;
use App\Core\Step\Steps\Step;
use App\Core\Step\WordProviders\WordsProviderInterface;

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
