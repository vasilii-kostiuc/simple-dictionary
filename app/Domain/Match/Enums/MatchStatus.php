<?php

namespace App\Domain\Match\Enums;

enum MatchStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
