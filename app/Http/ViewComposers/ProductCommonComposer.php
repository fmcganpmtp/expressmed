<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
//use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
class ProductCommonComposer
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
        $wishlist = array();
        if(Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
            if($user_id) {
                $wishlist = Wishlist::select('product_id')->where('user_id',$user_id)->get()->toArray();
            }
        }
        $view->with(['wishlist'=>$wishlist]);
    }
}
