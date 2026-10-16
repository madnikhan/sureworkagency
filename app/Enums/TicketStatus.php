<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Waiting = 'waiting';
    case Resolved = 'resolved';
    case Closed = 'closed';

    /**
     * @return array<string, array<int, string>>
     */
    public static function map(): array
    {
        return [
            self::Open->value => [self::InProgress->value, self::Waiting->value, self::Resolved->value],
            self::InProgress->value => [self::Waiting->value, self::Resolved->value],
            self::Waiting->value => [self::InProgress->value, self::Resolved->value],
            self::Resolved->value => [self::Closed->value, self::InProgress->value],
            self::Closed->value => [],
        ];
    }
}
