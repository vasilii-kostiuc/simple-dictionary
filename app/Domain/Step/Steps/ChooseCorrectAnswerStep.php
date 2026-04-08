<?php

namespace App\Domain\Step\Steps;

use App\Domain\Step\Enums\StepType;

class ChooseCorrectAnswerStep extends Step
{
    private bool $isTopWord;
    private array $answers;
    private int $wordId;
    private string $word;

    public function __construct($wordId, $word, $answers, $isTopWord)
    {
        parent::__construct(StepType::ChooseCorrectAnswer);

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
