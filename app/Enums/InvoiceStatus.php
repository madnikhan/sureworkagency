<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case PartPaid = 'part_paid';
    case Paid = 'paid';
    case Void = 'void';

    /**
     * @return array<string, array<int, string>>
     */
    public static function map(): array
    {
        return [
            self::Draft->value => [self::Sent->value, self::Void->value],
            self::Sent->value => [self::PartPaid->value, self::Paid->value, self::Void->value],
            self::PartPaid->value => [self::Paid->value, self::Void->value],
            self::Paid->value => [],
            self::Void->value => [],
        ];
    }
}
