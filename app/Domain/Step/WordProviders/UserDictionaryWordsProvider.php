<?php

namespace App\Domain\Step\WordProviders;

use Illuminate\Support\Collection;

class UserDictionaryWordsProvider implements WordsProviderInterface
{
    public function __construct(
        private readonly int $dictionaryId,
    ) {}

    public function getRandomWord(array $exceptIds = []): object
    {
        // TODO: implement when DictionaryWord model is available
        throw new \RuntimeException('Not implemented yet');
    }

    public function getRandomWords(int $count, array $exceptIds = []): Collection
    {
        // TODO: implement when DictionaryWord model is available
        throw new \RuntimeException('Not implemented yet');
    }
}
