<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    protected $fillable = [
        'customer_id', 'name', 'channel', 'status', 'starts_on', 'ends_on', 'notes',
        'utm_source', 'utm_medium', 'utm_campaign', 'data_source',
    ];
}
