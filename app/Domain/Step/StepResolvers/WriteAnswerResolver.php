<?php

namespace App\Domain\Step\StepResolvers;

class WriteAnswerResolver implements StepResolverInterface
{
    public function resolve(array $step_data): array
    {
        $acceptableAnswers = $step_data['acceptable_answers'];
        $attemptData['word'] = $acceptableAnswers[array_rand($acceptableAnswers)];

        return $attemptData;
    }
}
