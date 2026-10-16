<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case ProposalSent = 'proposal_sent';
    case Won = 'won';
    case Lost = 'lost';

    /**
     * @return array<string, array<int, string>>
     */
    public static function map(): array
    {
        return [
            self::New->value => [self::Contacted->value, self::Lost->value],
            self::Contacted->value => [self::Qualified->value, self::Lost->value],
            self::Qualified->value => [self::ProposalSent->value, self::Lost->value],
            self::ProposalSent->value => [self::Won->value, self::Lost->value],
            self::Won->value => [],
            self::Lost->value => [],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function convertible(): array
    {
        return [self::Qualified->value, self::ProposalSent->value, self::Won->value];
    }
}
