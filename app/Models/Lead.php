<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $fillable = [
        'customer_id', 'assigned_to', 'name', 'company', 'email', 'phone', 'service',
        'budget_band', 'description', 'source', 'status', 'follow_up_on', 'contact_locked', 'marketing_consent',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_on' => 'date',
            'contact_locked' => 'boolean',
            'marketing_consent' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class);
    }
}
