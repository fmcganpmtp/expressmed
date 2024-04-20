<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CustomerSupport extends Authenticatable
{
    protected $guard = 'customersupport';

    protected $table = 'customer_support';

    protected $fillable = [
        'name', 'email', 'password', 'password_string', 'remember_token', 'phone', 'profile_pic', 'status'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
