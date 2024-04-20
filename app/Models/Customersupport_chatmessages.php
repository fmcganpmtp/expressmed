<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customersupport_chatmessages extends Model
{
    protected $fillable = [
        'chat_id', 'type', 'text_message', 'time'
    ];
}
