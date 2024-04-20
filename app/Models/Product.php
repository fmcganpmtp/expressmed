<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name','description','how_to_use','benefits','side_effects','category_id','productcontent_id','product_pack','medicine_for','medicine_use','producttypeid','brands','supplier','manufacturer','variant_products','storage','thumbnail','added_by','vendor_type','status','tax_ids','tagline','prescription','features','product_url','quantity','price','offer_price','not_for_sale','hide_from_site','flag','approved_by'
    ];

    public function subcategory(){
        return $this->hasMany('App\Models\Category', 'parent_id');
    }

    public function Category()
    {
        return $this->hasMany('App\Models\Category', 'id');
    }

    public function reviews()
    {
	    return $this->hasMany('App\Models\ProductReview');
    }

    public function currentUserHasSubmittedReview()
    {
        $countOfReviews = 0;
        if(Auth::guard('user')->user()){
            $countOfReviews = $this->reviews()
            ->where('user_id', Auth::guard('user')->user()->id)
            ->get()->count();
        }
        return ($countOfReviews >= 1 ? true : false);
    }

    public function getAverageRatingAttribute()
    {
        $avgratings = 0;

            $avgratings = $this->reviews()
            ->where('rating', '>' , 0)->avg('rating');

        return $avgratings;
    }

    public function getRatingavg($rate = 0)
    {
        $cntratings = 0;

        if($rate != 0){
            $cntratings = $this->reviews()->where('rating', '=' , $rate)->count();
        } else {
            $cntratings = $this->reviews()->count();
        }

        return $cntratings;
    }
}
