<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'customer_id', 'lead_id', 'name', 'email', 'phone', 'service', 'starts_at',
        'timezone', 'status', 'meeting_link', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
        ];
    }
}
