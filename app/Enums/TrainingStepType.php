<?php

namespace App\Enums;

enum TrainingStepType: int
{
    case ChooseCorrectAnswer = 1;
    case WriteCorrectAnswer = 2;
    case EstablishCopmliance = 3;
}
