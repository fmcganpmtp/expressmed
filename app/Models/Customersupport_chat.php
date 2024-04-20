<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customersupport_chat extends Model
{
    protected $fillable = [
        'customersupport_id', 'customer_name', 'customer_email', 'subject', 'time', 'status'
    ];
}
