<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producttype extends Model
{
    use HasFactory;

    protected $fillable=[
        'producttype','type'
    ];
}
