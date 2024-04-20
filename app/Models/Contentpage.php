<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contentpage extends Model
{
    use HasFactory;

    protected $fillable = [
        'page','title','page_title', 'banner', 'slider','page_content', 'seo_url', 'seo_title', 'seo_description', 'seo_keywords','page_position','banner_type'
    ];
}
