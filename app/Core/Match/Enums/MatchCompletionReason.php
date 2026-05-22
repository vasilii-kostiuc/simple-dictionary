<?php

namespace App\Core\Match\Enums;

enum MatchCompletionReason: string
{
    case TimeExpired = 'time_expired';
    case StepsCompleted = 'steps_completed';
    case NotHeld = 'not_held';
    case NoActivity = 'no_activity';
    case AllPlayersLeft = 'all_players_left';
    case Forfeited = 'forfeited';
    case Cancelled = 'cancelled';

    public static function defaultForMatchType(MatchType $matchType): self
    {
        return match ($matchType) {
            MatchType::Time => self::TimeExpired,
            MatchType::Steps, MatchType::Race => self::StepsCompleted,
        };
    }

    public static function forMatchType(MatchType $matchType): array
    {
        return match ($matchType) {
            MatchType::Time => [self::TimeExpired, self::NotHeld, self::AllPlayersLeft, self::Forfeited, self::Cancelled],
            MatchType::Steps, MatchType::Race => [self::StepsCompleted, self::NotHeld, self::NoActivity, self::AllPlayersLeft, self::Forfeited, self::Cancelled],
        };
    }
}
