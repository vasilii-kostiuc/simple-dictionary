<?php

namespace App\Domain\Step;

use App\Domain\Dictionary\Models\TopWord;
use App\Domain\Step\Enums\StepType;
use App\Domain\Step\Steps\ChooseCorrectAnswerStep;
use App\Domain\Step\Steps\EstablishComplianceStep;
use App\Domain\Step\Steps\Step;
use App\Domain\Step\Steps\WriteAnswerStep;
use App\Domain\Training\Models\Training;
use Illuminate\Support\Collection;

class StepFactory
{
    const MULTIPLE_CHOICE_OPTIONS_COUNT = 4;

    public function __construct()
    {
    }

    public function createStep(Training $training, StepType $stepType): Step
    {
        return match ($stepType) {
            StepType::ChooseCorrectAnswer => $this->createChooseCorrectAnswerStep($training),
            StepType::WriteCorrectAnswer => $this->createWriteAnswerStep($training),
            StepType::EstablishCompliance => $this->createEstablishComplianceStep($training),
            default => $this->createChooseCorrectAnswerStep($training)
        };
    }

    private function createChooseCorrectAnswerStep(Training $training): ChooseCorrectAnswerStep
    {
        $fromLanguageId = $training->dictionary->language_from_id;
        $toLanguageId = $training->dictionary->language_to_id;

        $correctWord = $this->getRandomTopWord(
            $fromLanguageId,
            $toLanguageId
        );

        $incorrectWords = $this->getRandomTopWords(
            $fromLanguageId,
            $toLanguageId,
            [$correctWord->id],
            self::MULTIPLE_CHOICE_OPTIONS_COUNT - 1
        )->all();

        $allWords = array_merge([$correctWord], $incorrectWords);
        shuffle($allWords);

        $answers = array_map(
            fn(TopWord $word) => [
                'word_id' => $word->id,
                'word' => $word->word,
                'translation' => $word->translation,
            ],
            $allWords
        );

        return new ChooseCorrectAnswerStep(
            wordId: $correctWord->id,
            word: $correctWord->word,
            answers: $answers,
            isTopWord: true
        );
    }

    private function createWriteAnswerStep(Training $training): WriteAnswerStep
    {
        $fromLanguageId = $training->dictionary->language_from_id;
        $toLanguageId = $training->dictionary->language_to_id;

        $word = $this->getRandomTopWord(
            $fromLanguageId,
            $toLanguageId
        );

        return new WriteAnswerStep(
            wordId: $word->id,
            word: $word->word,
            acceptableAnswers: [$word->translation], // maybe later there would be synonims
            isTopWord: true
        );
    }

    private function createEstablishComplianceStep(Training $training)
    {
        $fromLanguageId = $training->dictionary->language_from_id;
        $toLanguageId = $training->dictionary->language_to_id;

        $words = $this->getRandomTopWords($fromLanguageId, $toLanguageId, [],self::MULTIPLE_CHOICE_OPTIONS_COUNT)->map(fn(TopWord $word) => [
            'word_id' => $word->id,
            'word' => $word->word,
            'translation' => $word->translation,
            'is_top_word' => true,
        ])->all();

        $answersOrder = array_column($words, 'word_id');
        shuffle($answersOrder);

        return new EstablishComplianceStep(
            words: $words,
            answersOrder: $answersOrder
        );
    }

    private function getTopWordsQuery(int $langFrom, int $langTo, array $exceptIds = [])
    {
        $query = TopWord::query()
            ->where('language_from_id', $langFrom)
            ->where('language_to_id', $langTo)
            ->select('id');

        if (!empty($exceptIds)) {
            $query->whereNotIn('id', $exceptIds);
        }

        return $query;
    }

    private function getRandomTopWord(int $langFrom, int $langTo, array $exceptTopWordsIds = []): TopWord
    {
        $ids = $this->getTopWordsQuery($langFrom, $langTo, $exceptTopWordsIds)->pluck('id');
        $randomId = $ids->random();

        return TopWord::query()->find($randomId);
    }

    private function getRandomTopWords(int $langFrom, int $langTo, array $exceptTopWordsIds = [], int $count = 1): Collection
    {
        $ids = $this->getTopWordsQuery($langFrom, $langTo, $exceptTopWordsIds)->get()->pluck('id');


        $randomIds = $ids->random($count);

        return TopWord::query()->whereIn('id', $randomIds)->get();
    }

}
