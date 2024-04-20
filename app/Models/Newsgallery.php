<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsgallery extends Model
{
    use HasFactory;
     protected $table='newsgalleries';

    protected $fillable = [
        'type','news_id', 'url',
    ];
}
