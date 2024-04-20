<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferLinkSection extends Model
{
    use HasFactory;
    protected $fillable = [
        'offer_content', 'offer_link','status',
    ];
}
