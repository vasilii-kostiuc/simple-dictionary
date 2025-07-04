<?php

namespace App\Domain\Training\Enums;

enum TrainingStatus: int
{
    case New = 1;
    case InProgress = 2;
    case Completed = 3;
}
