<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career_application extends Model
{
    use HasFactory;

    protected $fillable = [
        'career_id', 'applicant_name', 'phone', 'applicant_email', 'birthdate', 'address', 'pin', 'resume'
    ];
}
