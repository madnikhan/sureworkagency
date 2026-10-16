<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileAsset extends Model
{
    protected $fillable = ['uploaded_by', 'subject_type', 'subject_id', 'disk', 'path', 'original_name', 'mime', 'size'];
}
