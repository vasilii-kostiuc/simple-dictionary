<?php

namespace App\Enums;

enum TrainingType: int
{
    case TopWords = 1;
    case MyWords = 2;
    case AllWords = 3;
}
