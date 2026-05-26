<?php

namespace App\Core\Step\WordProviders;

use App\Core\Dictionary\Models\TopWord;
use App\Core\Shared\Cache\CacheInterface;
use Illuminate\Support\Collection;

class TopWordsProvider implements WordsProviderInterface
{
    public function __construct(
        private readonly int $langFrom,
        private readonly int $langTo,
        private readonly CacheInterface $cache,
    ) {}

    public function getRandomWord(array $exceptIds = []): TopWord
    {
        $id = $this->getAllIds()
            ->reject(fn ($id) => in_array($id, $exceptIds, true))
            ->random();

        return TopWord::query()->find($id);
    }

    public function getRandomWords(int $count, array $exceptIds = []): Collection
    {
        $ids = $this->getAllIds()
            ->reject(fn ($id) => in_array($id, $exceptIds, true))
            ->random($count);

        return TopWord::query()->whereIn('id', $ids)->get();
    }

    private function getAllIds(): Collection
    {
        return $this->cache->rememberForever(
            "top_words.ids.{$this->langFrom}.{$this->langTo}",
            fn () => TopWord::query()
                ->where('language_from_id', $this->langFrom)
                ->where('language_to_id', $this->langTo)
                ->pluck('id')
        );
    }
}
