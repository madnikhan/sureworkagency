<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRecord extends Model
{
    protected $fillable = ['invoice_id', 'amount_pence', 'paid_on', 'method', 'reference'];

    protected function casts(): array
    {
        return [
            'paid_on' => 'date',
        ];
    }
}
