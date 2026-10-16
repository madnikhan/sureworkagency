<?php

namespace App\Support;

use App\Exceptions\InvalidStatusTransition;

class StatusTransition
{
    /**
     * @param  array<string, array<int, string>>  $map
     */
    public static function assert(string $from, string $to, array $map): void
    {
        $allowed = $map[$from] ?? [];

        if (! in_array($to, $allowed, true)) {
            throw new InvalidStatusTransition("Cannot change status from {$from} to {$to}.");
        }
    }
}
