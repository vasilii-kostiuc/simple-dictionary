<?php

namespace App\Core\Match\Enums;

enum MatchType: string
{
    case Time = 'time';
    case Steps = 'steps';
    case Race = 'race';
}
