<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = ['support_ticket_id', 'user_id', 'visibility', 'body'];
}
