<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';

    /**
     * @return array<string, array<int, string>>
     */
    public static function map(): array
    {
        return [
            self::Draft->value => [self::Sent->value],
            self::Sent->value => [self::Accepted->value, self::Rejected->value, self::Expired->value],
            self::Accepted->value => [],
            self::Rejected->value => [],
            self::Expired->value => [],
        ];
    }
}
