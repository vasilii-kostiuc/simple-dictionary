<?php

namespace App\Domain\Match\Enums;

enum MatchCompletionReason: string
{
    case TimeExpired = 'time_expired';
    case StepsCompleted = 'steps_completed';
    case AllPlayersLeft = 'all_players_left';
    case Forfeited = 'forfeited';
    case Cancelled = 'cancelled';

    public static function defaultForMatchType(MatchType $matchType): self
    {
        return match ($matchType) {
            MatchType::Time => self::TimeExpired,
            MatchType::Steps => self::StepsCompleted,
        };
    }

    public static function forMatchType(MatchType $matchType): array
    {
        return match ($matchType) {
            MatchType::Time => [self::TimeExpired, self::AllPlayersLeft, self::Forfeited, self::Cancelled],
            MatchType::Steps => [self::StepsCompleted, self::AllPlayersLeft, self::Forfeited, self::Cancelled],
        };
    }
}
