<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Requested = 'requested';
    case Confirmed = 'confirmed';
    case Rescheduled = 'rescheduled';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    /**
     * @return array<string, array<int, string>>
     */
    public static function map(): array
    {
        return [
            self::Requested->value => [self::Confirmed->value, self::Cancelled->value],
            self::Confirmed->value => [self::Rescheduled->value, self::Cancelled->value, self::Completed->value],
            self::Rescheduled->value => [self::Confirmed->value, self::Cancelled->value],
            self::Cancelled->value => [],
            self::Completed->value => [],
        ];
    }
}
