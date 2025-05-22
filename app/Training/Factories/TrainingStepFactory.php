<?php

namespace App\Training\Factories;

use App\Models\TopWord;
use App\Training\Enums\TrainingStepType;
use App\Training\Enums\TrainingType;
use App\Training\Models\Training;
use App\Training\Steps\ChooseCorrectAnswerStep;
use App\Training\Steps\EstablishComplianceStep;
use App\Training\Steps\WordTrainingStep;
use App\Training\Steps\WriteAnswerStep;
use Illuminate\Support\Collection;

class TrainingStepFactory
{
    const MULTIPLE_CHOICE_OPTIONS_COUNT = 4;
    private TrainingType $trainingType;

    public function __construct(TrainingType $trainingType)
    {
        $this->trainingType = $trainingType;
    }

    public function create(Training $training, TrainingStepType $stepType): WordTrainingStep
    {
        return match ($stepType) {
            TrainingStepType::ChooseCorrectAnswer => $this->createChooseCorrectAnswerStep($training),
            TrainingStepType::WriteCorrectAnswer => $this->createWriteAnswerStep($training),
            TrainingStepType::EstablishCompliance => $this->createEstablishComplianceStep($training),
            default => throw new \InvalidArgumentException("Неподдерживаемый тип шага тренировки: {$stepType->name}")
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
        );

        $allWords = array_merge([$correctWord], $incorrectWords->toArray());
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

        $words = $this->getRandomTopWords($fromLanguageId, $toLanguageId);

        $words = array_map(fn(TopWord $word) => [
            'word_id' => $word->word_id,
            'word' => $word->word,
            'translation' => $word->translation,
            'is_top_word' => true,
        ], $words->toArray());

        $answersOrder = array_column( $words, 'word_id');
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
        $ids = $this->getTopWordsQuery($langFrom, $langTo, $exceptTopWordsIds)->pluck('id');
        $randomIds = $ids->random($count);

        return TopWord::query()->find($randomIds);
    }

}
