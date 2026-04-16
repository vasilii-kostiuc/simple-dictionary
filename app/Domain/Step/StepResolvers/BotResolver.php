<?php

namespace App\Domain\Step\StepResolvers;

class BotResolver implements StepResolverInterface
{
    public function __construct(
        private StepResolverInterface $innerResolver,
        private float $accuracy // 0.0 - 1.0
    ) {}

    public function resolve(array $step_data): array
    {
        if ((mt_rand() / mt_getrandmax()) <= $this->accuracy) {
            return $this->innerResolver->resolve($step_data);
        }

        return $this->resolveWrong($step_data);
    }

    private function resolveWrong(array $step_data): array
    {
        // ChooseCorrectAnswer: случайный неправильный вариант из answers[]
        if (isset($step_data['answers'])) {
            $wrong = array_filter(
                $step_data['answers'],
                fn($a) => $a['word_id'] !== $step_data['word_id']
            );

            if (!empty($wrong)) {
                $pick = $wrong[array_rand($wrong)];
                return ['word_id' => $pick['word_id']];
            }
        }

        // WriteCorrectAnswer: заведомо неверное слово
        if (isset($step_data['acceptable_answers'])) {
            return ['word' => '__wrong__'];
        }

        // EstablishCompliance: перепутываем пары
        if (isset($step_data['answers_order'])) {
            $order = $step_data['answers_order'];
            $wrongId = $order[array_rand($order)];
            return ['word_id' => $wrongId, 'answer_id' => ($wrongId + 1)];
        }

        return $this->innerResolver->resolve($step_data);
    }
}
