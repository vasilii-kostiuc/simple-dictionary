<?php

namespace App\Domain\Training\Steps;

use App\Domain\Training\Enums\TrainingStepType;

class ChooseCorrectAnswerStep extends WordTrainingStep
{
    private bool $isTopWord;
    private array $answers;
    private int $wordId;
    private string $word;

    public function __construct($wordId, $word, $answers, $isTopWord)
    {
        parent::__construct(TrainingStepType::ChooseCorrectAnswer);

        $this->answers = $answers;
        $this->isTopWord = $isTopWord;
        $this->wordId = $wordId;
        $this->word = $word;
    }

    public function toArray(): array
    {
        return [
            'word_id' => $this->wordId,
            'word' => $this->word,
            'answers' => $this->answers,
            'is_top_word' => $this->isTopWord,
        ];
    }
}
