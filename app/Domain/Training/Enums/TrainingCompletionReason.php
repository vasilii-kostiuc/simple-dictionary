<?php

namespace App\Domain\Training\Enums;

enum TrainingCompletionReason: int
{
    case Expired = 1;
    case Leaved = 2;
    case AllStepsCompleted = 3;
    case Terminated = 4;

    public static function forCompletionType(TrainingCompletionType $type): array
    {
        return match ($type) {
            TrainingCompletionType::Time => [self::Expired, self::Leaved, self::Terminated],
            TrainingCompletionType::Steps => [self::Leaved, self::AllStepsCompleted, self::Terminated],
            TrainingCompletionType::Unlimited => [self::Leaved, self::Terminated],
        };
    }

    public static function defaultForCompletionType(TrainingCompletionType $type): self{
        return match ($type) {
            TrainingCompletionType::Time => self::Expired,
            TrainingCompletionType::Steps => self::AllStepsCompleted,
            TrainingCompletionType::Unlimited => self::Terminated,
        };
    }

}
