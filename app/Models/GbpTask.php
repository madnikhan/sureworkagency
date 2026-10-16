<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GbpTask extends Model
{
    protected $fillable = ['customer_id', 'title', 'status', 'notes', 'data_source'];
}
