<?php

namespace App\Training\Enums;

enum TrainingStatus: int
{
    case New = 1;
    case InProgress = 2;
    case Finished = 3;
}
