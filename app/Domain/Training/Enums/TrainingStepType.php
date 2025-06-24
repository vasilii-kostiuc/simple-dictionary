<?php

namespace App\Domain\Training\Enums;

enum TrainingStepType: int
{
    case ChooseCorrectAnswer = 1;
    case WriteCorrectAnswer = 2;
    case EstablishCompliance = 3;

    public static function getRandomInstance()
    {
        $cases = self::cases();
        return $cases[array_rand($cases)];
    }
}
