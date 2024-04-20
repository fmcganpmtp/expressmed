<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotionbanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'section', 'title', 'type', 'position', 'status'
    ];
}
