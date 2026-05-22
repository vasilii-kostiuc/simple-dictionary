<?php

namespace App\Core\Training\Enums;

enum TrainingCompletionType: string
{
    case Time = 'time';
    case Steps = 'steps';
    case Unlimited = 'unlimited';
}
