<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = [
        'customer_id', 'parent_id', 'number', 'version', 'title', 'scope', 'exclusions', 'terms',
        'status', 'currency', 'tax_rate_bps', 'net_pence', 'tax_pence', 'gross_pence',
        'valid_until', 'timeline', 'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'issued_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }
}
