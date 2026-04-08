<?php

namespace App\Domain\Step\WordProviders;

use App\Domain\Dictionary\Models\TopWord;
use Illuminate\Support\Collection;

class TopWordsProvider implements WordsProviderInterface
{
    public function __construct(
        private readonly int $langFrom,
        private readonly int $langTo,
    ) {}

    public function getRandomWord(array $exceptIds = []): TopWord
    {
        $ids = $this->getQuery($exceptIds)->pluck('id');

        return TopWord::query()->find($ids->random());
    }

    public function getRandomWords(int $count, array $exceptIds = []): Collection
    {
        $ids = $this->getQuery($exceptIds)->pluck('id');

        return TopWord::query()->whereIn('id', $ids->random($count))->get();
    }

    private function getQuery(array $exceptIds = [])
    {
        $query = TopWord::query()
            ->where('language_from_id', $this->langFrom)
            ->where('language_to_id', $this->langTo)
            ->select('id');

        if (!empty($exceptIds)) {
            $query->whereNotIn('id', $exceptIds);
        }

        return $query;
    }
}
