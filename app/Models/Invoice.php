<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id', 'quote_id', 'number', 'title', 'status', 'currency', 'tax_rate_bps',
        'net_pence', 'tax_pence', 'gross_pence', 'due_on', 'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'due_on' => 'date',
            'issued_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentRecord::class);
    }

    public function balancePence(): int
    {
        return max(0, (int) $this->gross_pence - (int) $this->payments()->sum('amount_pence'));
    }

    public function displayStatus(): string
    {
        if (in_array($this->status, ['sent', 'part_paid'], true)
            && $this->due_on !== null
            && $this->due_on->isBefore(today())
            && $this->balancePence() > 0) {
            return 'overdue';
        }

        return $this->status;
    }
}
