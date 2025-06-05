<?php

namespace App\Training\Steps;

use App\Training\Enums\TrainingStepType;

class WriteAnswerStep extends WordTrainingStep
{
    private array $acceptableAnswers;
    private int $isTopWord;
    private int $wordId;
    private string $word;

    public function __construct($wordId, $word,$acceptableAnswers, $isTopWord)
    {
        parent::__construct(TrainingStepType::WriteCorrectAnswer);

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
