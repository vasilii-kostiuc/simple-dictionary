<?php

namespace App\Domain\Training\StepResolvers;

use App\Domain\Training\Models\TrainingStep;

class EstablishComplianceResolver implements StepResolverInterface
{
    public function resolve(TrainingStep $trainingStep): array
    {
        $correctAnswers = $trainingStep->correctAnswers();
        $answers_order = $trainingStep->step_data['answers_order'];

        $alreadyEstablished = $correctAnswers->pluck('word_id')->toArray();

        dd($answers_order);

        $notEstablished = array_diff($answers_order, $alreadyEstablished);
        $word_id = $answers_order[array_rand($notEstablished)];

        return ['word_id' => $word_id, 'answer_id' => $word_id];
    }
}
