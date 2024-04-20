<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'phone', 'job_title', 'bio', 'profile_pic', 'licence'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
