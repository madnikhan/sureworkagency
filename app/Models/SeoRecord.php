<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoRecord extends Model
{
    protected $fillable = ['customer_id', 'title', 'checklist', 'keyword_notes', 'notes', 'recorded_on', 'data_source'];
}
