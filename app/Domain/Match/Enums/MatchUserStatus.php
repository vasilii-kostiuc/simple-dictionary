<?php

namespace App\Domain\Match\Enums;

enum MatchUserStatus: string
{
    case Active = 'active';
    case Finished = 'finished';
    case Spectating = 'spectating';
    case Left = 'left';
    case Disconnected = 'disconnected';
    case Forfeited = 'forfeited';

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Active => false,
            default => true,
        };
    }
}
