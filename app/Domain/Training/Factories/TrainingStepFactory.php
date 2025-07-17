<?php

namespace App\Domain\Training\Factories;

use App\Domain\Dictionary\Models\TopWord;
use App\Domain\Training\Enums\TrainingStepType;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Steps\ChooseCorrectAnswerStep;
use App\Domain\Training\Steps\EstablishComplianceStep;
use App\Domain\Training\Steps\WordTrainingStep;
use App\Domain\Training\Steps\WriteAnswerStep;
use Illuminate\Support\Collection;

class TrainingStepFactory
{
    const MULTIPLE_CHOICE_OPTIONS_COUNT = 4;

    public function __construct()
    {
    }

    public function createStep(Training $training, TrainingStepType $stepType): WordTrainingStep
    {
        return match ($stepType) {
//            TrainingStepType::ChooseCorrectAnswer => $this->createChooseCorrectAnswerStep($training),
//            TrainingStepType::WriteCorrectAnswer => $this->createWriteAnswerStep($training),
//            TrainingStepType::EstablishCompliance => $this->createEstablishComplianceStep($training),
            default => $this->createEstablishComplianceStep($training)
        };
    }

    public function createStepFromData(array $trainingStepData, TrainingStepType $stepType): WordTrainingStep
    {
        return match ($stepType) {
            TrainingStepType::ChooseCorrectAnswer => $this->createChooseCorrectAnswerStepFromData($trainingStepData),
            TrainingStepType::WriteCorrectAnswer => $this->createWriteAnswerStepFromData($trainingStepData),
            TrainingStepType::EstablishCompliance => $this->createEstablishComplianceStepFromData($trainingStepData),
            default => throw new \InvalidArgumentException("Unsupported training step type: {$stepType->name}")
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

    private function createChooseCorrectAnswerStepFromData(array $trainingStepData)
    {

    }

    private function createWriteAnswerStepFromData(array $trainingStepData)
    {

    }

    private function createEstablishComplianceStepFromData(array $trainingStepData)
    {
    }

}
