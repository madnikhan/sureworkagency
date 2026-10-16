<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #14171C; font-size: 12px; }
        h1 { font-size: 22px; margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { text-align: left; border-bottom: 1px solid #d5d8de; padding: 6px 0; }
        .muted { color: #5c6570; }
    </style>
</head>
<body>
    <p class="muted">{{ config('brand.legal_name') }} · {{ config('brand.email') }}</p>
    <h1>{{ $kind }} {{ $document->number }}</h1>
    <p>{{ $document->title }}</p>
    <p>Prepared for {{ $document->customer->name }}@if($document->customer->company), {{ $document->customer->company }}@endif</p>
    @if ($document->scope)
        <h2>Scope</h2>
        <p>{{ $document->scope }}</p>
    @endif
    @if ($document->exclusions)
        <h2>Excluded</h2>
        <p>{{ $document->exclusions }}</p>
    @endif
    <table>
        <thead>
            <tr><th>Description</th><th>Qty</th><th>Amount</th></tr>
        </thead>
        <tbody>
            @foreach ($document->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ \App\Support\Money::format($item->unit_amount_pence * $item->quantity, $document->currency) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>Net {{ \App\Support\Money::format($document->net_pence, $document->currency) }}</p>
    <p>Tax ({{ number_format($document->tax_rate_bps / 100, 2) }}%) {{ \App\Support\Money::format($document->tax_pence, $document->currency) }}</p>
    <p><strong>Total {{ \App\Support\Money::format($document->gross_pence, $document->currency) }}</strong></p>
    <p class="muted">Tax is the rate stored on this document when it was created. Card numbers are not collected.</p>
</body>
</html>
