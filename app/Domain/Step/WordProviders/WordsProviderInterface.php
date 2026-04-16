<?php

namespace App\Domain\Step\WordProviders;

use Illuminate\Support\Collection;

interface WordsProviderInterface
{
    public function getRandomWord(array $exceptIds = []): object;

    public function getRandomWords(int $count, array $exceptIds = []): Collection;
}
