<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_id','user_id', 'reviews', 'rating','approved','spam'
    ];

    public function user()
    {
      return $this->belongsTo('App\Models\User','user_id');
    }

    public function product()
    {
      return $this->belongsTo('App\Models\Product','product_id');
    }

    public function scopeApproved($query)
    {
      return $query->where('approved', true);
    }

    public function scopeSpam($query)
    {
      return $query->where('spam', true);
    }

    public function scopeNotSpam($query)
    {
      return $query->where('spam', false);
    }

    public function scopeOnlyreview($query){
      return $query->where('reviews', '!=' , '');
    }

    public function scopeOnlyRate($query){
      return $query->where('rating', '>' , 0);
    }
}
