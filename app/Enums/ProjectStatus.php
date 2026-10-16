<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Planning = 'planning';
    case InProgress = 'in_progress';
    case Review = 'review';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * @return array<string, array<int, string>>
     */
    public static function map(): array
    {
        return [
            self::Planning->value => [self::InProgress->value, self::Cancelled->value],
            self::InProgress->value => [self::Review->value, self::Cancelled->value],
            self::Review->value => [self::Completed->value, self::InProgress->value, self::Cancelled->value],
            self::Completed->value => [],
            self::Cancelled->value => [],
        ];
    }
}
