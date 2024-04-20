<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $guard = 'user';

    protected $fillable = [
        'name','email','password','country_id','phone','profile_pic','status','verified','expiry_time','device_id','api_token','otp_expiry','otp'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    // public function Country()
    // {
    //     return $this->hasMany('App\Models\Country', 'country_id');
    // }

    public function Country()
    {
        return $this->belongsTo('App\Models\Country', 'id');
    }
}
