<?php

namespace App\Domain\Step;

use App\Domain\Step\Enums\StepType;
use App\Domain\Step\Steps\ChooseCorrectAnswerStep;
use App\Domain\Step\Steps\EstablishComplianceStep;
use App\Domain\Step\Steps\Step;
use App\Domain\Step\Steps\WriteAnswerStep;
use App\Domain\Step\WordProviders\WordsProviderInterface;

class StepFactory
{
    const MULTIPLE_CHOICE_OPTIONS_COUNT = 4;

    public function __construct() {}

    public function createStep(StepType $stepType, WordsProviderInterface $wordsProvider): Step
    {
        return match ($stepType) {
            StepType::ChooseCorrectAnswer => $this->createChooseCorrectAnswerStep($wordsProvider),
            StepType::WriteCorrectAnswer => $this->createWriteAnswerStep($wordsProvider),
            StepType::EstablishCompliance => $this->createEstablishComplianceStep($wordsProvider),
            default => $this->createChooseCorrectAnswerStep($wordsProvider)
        };
    }

    private function createChooseCorrectAnswerStep(WordsProviderInterface $wordsProvider): ChooseCorrectAnswerStep
    {
        $correctWord = $wordsProvider->getRandomWord();

        $incorrectWords = $wordsProvider->getRandomWords(
            self::MULTIPLE_CHOICE_OPTIONS_COUNT - 1,
            [$correctWord->id]
        )->all();

        $allWords = array_merge([$correctWord], $incorrectWords);
        shuffle($allWords);

        $answers = array_map(
            fn($word) => [
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

    private function createWriteAnswerStep(WordsProviderInterface $wordsProvider): WriteAnswerStep
    {
        $word = $wordsProvider->getRandomWord();

        return new WriteAnswerStep(
            wordId: $word->id,
            word: $word->word,
            acceptableAnswers: [$word->translation], // maybe later there would be synonyms
            isTopWord: true
        );
    }

    private function createEstablishComplianceStep(WordsProviderInterface $wordsProvider): EstablishComplianceStep
    {
        $words = $wordsProvider->getRandomWords(self::MULTIPLE_CHOICE_OPTIONS_COUNT)->map(fn($word) => [
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
}
