<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'device_id', 'type', 'email', 'name', 'phone', 'pin', 'location', 'address', 'city', 'state_id', 'country_id', 'landmark'
    ];
}
