<?php

namespace App\Core\Match\Enums;

enum MatchStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
