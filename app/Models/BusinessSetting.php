<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = ['company_name', 'email', 'phone', 'address', 'opening_hours', 'currency', 'vat_rate_bps'];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'company_name' => config('brand.legal_name'),
            'email' => config('brand.email'),
            'currency' => 'GBP',
            'vat_rate_bps' => 0,
        ]);
    }
}
