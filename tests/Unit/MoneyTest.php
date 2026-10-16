<?php

use App\Support\Money;

it('rounds tax half up from basis points', function () {
    expect(Money::line(10000, 1, 2000))->toBe(['net' => 10000, 'tax' => 2000, 'gross' => 12000])
        ->and(Money::line(3, 1, 5000))->toBe(['net' => 3, 'tax' => 2, 'gross' => 5])
        ->and(Money::line(1, 1, 0))->toBe(['net' => 1, 'tax' => 0, 'gross' => 1]);
});

it('does not assume a twenty percent rate', function () {
    expect(Money::document([
        ['unit_amount_pence' => 2500, 'quantity' => 2],
    ], 0)['tax'])->toBe(0);
});
