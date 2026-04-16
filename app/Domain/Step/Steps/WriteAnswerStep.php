<?php

namespace App\Domain\Step\Steps;

use App\Domain\Step\Enums\StepType;

class WriteAnswerStep extends Step
{
    private array $acceptableAnswers;
    private int $isTopWord;
    private int $wordId;
    private string $word;

    public function __construct($wordId, $word,$acceptableAnswers, $isTopWord)
    {
        parent::__construct(StepType::WriteCorrectAnswer);

        $this->wordId = $wordId;
        $this->word = $word;
        $this->acceptableAnswers = $acceptableAnswers;
        $this->isTopWord = $isTopWord;
    }

    public function toArray(): array
    {
        return [
            'word_id' => $this->wordId,
            'word' => $this->word,
            'is_top_word' => $this->isTopWord,
            'acceptable_answers' => $this->acceptableAnswers,
        ];
    }
}
