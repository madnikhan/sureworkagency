<?php

namespace App\Actions;

use App\Models\Invoice;
use App\Models\Quote;
use App\Support\Money;

class DocumentTotals
{
    public static function quote(Quote $quote): void
    {
        $totals = Money::document(
            $quote->items()->get(['unit_amount_pence', 'quantity'])->all(),
            (int) $quote->tax_rate_bps,
        );

        $quote->forceFill([
            'net_pence' => $totals['net'],
            'tax_pence' => $totals['tax'],
            'gross_pence' => $totals['gross'],
        ])->save();
    }

    public static function invoice(Invoice $invoice): void
    {
        $totals = Money::document(
            $invoice->items()->get(['unit_amount_pence', 'quantity'])->all(),
            (int) $invoice->tax_rate_bps,
        );

        $invoice->forceFill([
            'net_pence' => $totals['net'],
            'tax_pence' => $totals['tax'],
            'gross_pence' => $totals['gross'],
        ])->save();
    }
}
