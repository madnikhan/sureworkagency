<?php

namespace App\Support;

class Money
{
    /**
     * @return array{net: int, tax: int, gross: int}
     */
    public static function line(int $unitAmountPence, int $quantity, int $taxRateBps): array
    {
        $net = $unitAmountPence * $quantity;
        $tax = intdiv(($net * $taxRateBps) + 5000, 10000);

        return [
            'net' => $net,
            'tax' => $tax,
            'gross' => $net + $tax,
        ];
    }

    /**
     * @param  array<int, array{unit_amount_pence: int, quantity: int}>  $lines
     * @return array{net: int, tax: int, gross: int}
     */
    public static function document(array $lines, int $taxRateBps): array
    {
        $net = 0;
        $tax = 0;

        foreach ($lines as $line) {
            $priced = self::line((int) $line['unit_amount_pence'], (int) $line['quantity'], $taxRateBps);
            $net += $priced['net'];
            $tax += $priced['tax'];
        }

        return [
            'net' => $net,
            'tax' => $tax,
            'gross' => $net + $tax,
        ];
    }

    public static function format(int $pence, string $currency = 'GBP'): string
    {
        $pounds = intdiv($pence, 100);
        $remainder = abs($pence % 100);

        return $currency.' '.number_format($pounds, 0, '.', ',').'.'.str_pad((string) $remainder, 2, '0', STR_PAD_LEFT);
    }
}
