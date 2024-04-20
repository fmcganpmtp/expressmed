<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
//use App\Repositories\UserRepository;

use App\Models\Product;
use App\Models\Generalsetting;
use App\Models\Category;
use App\Models\Productbrand;
use App\Models\SocialMedia;
use App\Models\Cart;
use App\Models\Contentpage;
use App\Models\Tax;
use App\Models\Country;
// use App\Models\order;
use App\Models\OfferLinkSection;
use Seshac\Shiprocket\Shiprocket;



use Illuminate\Support\Facades\Auth;
use Request;
use DB;

class SettingsComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct( )
    {
        // Dependencies automatically resolved by service container...
        //$this->users = $users;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // print_r($token);exit;
        // $orderDetails = [
        //     // refer above url for required parameters

        //     'per_page'=>20,
        // ];
        // $response =  Shiprocket::order($token)->getOrders($orderDetails);
        // dd($response);

        $settings = Generalsetting::select('item','value')->get()->toArray();
        $commonCategories = Category::where('parent_id',0)->get();
        $AllCategories = Category::where('parent_id',0)->where('status','active')->orderBy('name', 'asc')->get();
        $limitCategories = Category::where('parent_id',0)->where('status','active')->orderBy('name', 'asc')->limit(10)->get();
        $offerlinksection = OfferLinkSection::where('status','active')->get();
        $countries = Country::all();
    //     foreach($limitCategories as $limitCategories_Row){
    //     if (count($limitCategories_Row->subcategory)>0){
    //       dd($limitCategories_Row->subcategory);
    //     }
    // }

        $socialmediaicons = SocialMedia::get();
        $ContentPages = Contentpage::get()->all();

        $user_id = '';
        $wishlist_count = 0;
        if(Auth::guard('user')->user())
            $user_id = Auth::guard('user')->user()->id;

        $carts=array();

        if(empty($user_id)){//------Guest users product cart--

            if(!isset($_SESSION)){
                session_start();
            }

            $Guest_Cart = (isset($_SESSION['Session_GuestCart'])) ? $_SESSION['Session_GuestCart'] : [];
            if($Guest_Cart) {
                $GuestCart_Array = [];
                $cnt = 0;
                foreach($Guest_Cart as $productID => $value){
                    $GuestCart_Product = Product::leftJoin('product_images','products.thumbnail','product_images.id')
                                        ->where('products.id', $productID)
                                        ->select('products.id as product_id','products.product_name','products.prescription','products.tax_ids','products.product_url','product_images.product_image','products.offer_price','products.price as original_price',DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as price'))
                                        ->first();
                                        $percent=0;
                                        if ($GuestCart_Product->offer_price != 0){
                                         $percent = number_format((($GuestCart_Product->original_price -$GuestCart_Product->offer_price)*100) /$GuestCart_Product->original_price);
                                        }


                    if($GuestCart_Product){
                        if($GuestCart_Product->tax_ids != null && isset($GuestCart_Product->tax_ids)){
                            $tax_ids = explode(',',$GuestCart_Product->tax_ids);
                            $GuestCart_Array[$cnt]['tax_details'] = Tax::whereIn('id',$tax_ids)->get();
                        }
                        $GuestCart_Array[$cnt]['product_id'] = $GuestCart_Product->product_id;
                        $GuestCart_Array[$cnt]['product_url'] = $GuestCart_Product->product_url;
                        $GuestCart_Array[$cnt]['product_name'] = $GuestCart_Product->product_name;
                        $GuestCart_Array[$cnt]['prescription'] = $GuestCart_Product->prescription;
                        $GuestCart_Array[$cnt]['product_image'] = ($GuestCart_Product->product_image != null ? $GuestCart_Product->product_image : null);
                        $GuestCart_Array[$cnt]['ProductPrice'] = $GuestCart_Product->price;
                        $GuestCart_Array[$cnt]['offer_price'] = $GuestCart_Product->offer_price;
                        $GuestCart_Array[$cnt]['original_price'] = $GuestCart_Product->original_price;
                        $GuestCart_Array[$cnt]['offer_percent'] = $percent;

                        $GuestCart_Array[$cnt]['quantity'] = $value['quantity'];
                    }
                    $cnt++;
                }

                $carts = $GuestCart_Array;
            }
        }else{
            $carts = Cart::join('products','carts.product_id','products.id')
                    ->leftjoin('product_images','products.thumbnail','product_images.id')
                    ->select('carts.*','products.product_name','products.prescription','products.tax_ids','products.product_url','product_images.product_image','products.offer_price','products.price as original_price',DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                    ->where('carts.user_id', $user_id)
                    ->get();

            foreach($carts as $key=>$value){
                if($value->tax_ids != null && isset($value->tax_ids)){
                    $tax_ids = explode(',',$value->tax_ids);
                    $carts[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                }
                if ($value->offer_price != 0){
                    $percent = number_format((($value->original_price-$value->offer_price)*100) /$value->original_price);
                    $carts[$key]['offer_percent'] =$percent;
                   }


            }
            // dd($carts[2]['offer_percent']);

            // $wishlist_count = Wishlist::where('user_id',$user_id)->count();
        }

        $view->with(['common_settings'=>$settings,'commonCategories'=>$commonCategories,'socialmediaicons'=>$socialmediaicons,'carts'=>$carts,'wishlist_count'=>$wishlist_count,'ContentPages'=>$ContentPages,'AllCategories'=>$AllCategories,'limitCategories'=>$limitCategories,'offerlinksection'=>$offerlinksection,'countries'=>$countries]);
    }

}
