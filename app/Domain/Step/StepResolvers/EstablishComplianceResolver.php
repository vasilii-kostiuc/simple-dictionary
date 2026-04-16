<?php

namespace App\Domain\Step\StepResolvers;

class EstablishComplianceResolver implements StepResolverInterface
{
    public function resolve(array $step_data): array
    {
        // TODO: Need to pass correctAnswers information separately
        // $correctAnswers = $trainingStep->correctAnswers();
        $answers_order = $step_data['answers_order'];

        // $alreadyEstablished = $correctAnswers->pluck('word_id')->toArray();
        // $notEstablished = array_diff($answers_order, $alreadyEstablished);

        $word_id = $answers_order[array_rand($answers_order)];

        return ['word_id' => $word_id, 'answer_id' => $word_id];
    }
}
