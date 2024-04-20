<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionBannerImage extends Model
{
    use HasFactory;

    protected $table = 'promotionbanner_images';

    protected $fillable = [
        'promotionbanner_id', 'image', 'banner_url'
    ];
}
