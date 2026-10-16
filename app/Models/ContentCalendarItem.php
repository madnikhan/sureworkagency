<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentCalendarItem extends Model
{
    protected $fillable = ['title', 'channel', 'scheduled_for', 'status', 'body', 'data_source'];
}
