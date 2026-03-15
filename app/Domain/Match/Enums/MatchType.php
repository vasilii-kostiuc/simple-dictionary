<?php

namespace App\Domain\Match\Enums;

enum MatchType: string
{
    case Time = 'time';
    case Steps = 'steps';
}
