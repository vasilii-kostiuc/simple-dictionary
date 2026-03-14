<?php

namespace App\Domain\Step\StepResolvers;

class ChooseCorrectAnswerResolver implements StepResolverInterface
{
    public function resolve(array $step_data): array
    {
        $attemptData['word_id'] = $step_data['word_id'];

        return $attemptData;
    }
}
