<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','device_id','api_token', 'api_token_expiry', 'otp','otp_expiry'
    ];
}
