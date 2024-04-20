<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//------------------FrontAPP/Register&Login--
Route::post('register/createuser', [App\Http\Controllers\FrontApp\CustomerController::class, 'registerCustomer']);
Route::post('login/user', [App\Http\Controllers\FrontApp\CustomerController::class, 'userlogin']);
Route::post('user/request_otp', [App\Http\Controllers\FrontApp\CustomerController::class, 'request_otp']);
Route::post('login/user_otp', [App\Http\Controllers\FrontApp\CustomerController::class, 'userlogin_otp']);

//------------------FrontAPP/Home--
Route::get('home/landingpage', [App\Http\Controllers\FrontApp\CustomerController::class, 'home_view']);

//------------------FrontAPP/Product/Listing--
Route::get('product/listing', [App\Http\Controllers\FrontApp\CustomerController::class, 'product_listing']);

//------------------FrontAPP/Product/DetailPage--
Route::get('product/detailpage', [App\Http\Controllers\FrontApp\CustomerController::class, 'product_detail']);
Route::get('product/reviewrating', [App\Http\Controllers\FrontApp\CustomerController::class, 'productreview_list']);
Route::post('add/productreview', [App\Http\Controllers\FrontApp\CustomerController::class, 'add_productreview']);
Route::post('product/prescription', [App\Http\Controllers\FrontApp\CustomerController::class, 'prescription']);


//------------------FrontAPP/Shopping/Products/cart--
Route::get('shopping/listcart', [App\Http\Controllers\FrontApp\CustomerController::class, 'list_cart']);
Route::post('shopping/addtocart', [App\Http\Controllers\FrontApp\CustomerController::class, 'product_addcart']);
Route::post('shopping/deletecart', [App\Http\Controllers\FrontApp\CustomerController::class, 'product_deletecart']);
// Route::post('shopping/cartupdation', [App\Http\Controllers\FrontApp\CustomerController::class, 'cart_updation']);

//------------------FrontAPP/Shopping/checkout--
Route::get('shopping/customer/getaddress', [App\Http\Controllers\FrontApp\CustomerController::class, 'get_useraddress']);
Route::get('getall/countries', [App\Http\Controllers\FrontApp\CustomerController::class, 'get_countries']);
Route::post('getcountry/states', [App\Http\Controllers\FrontApp\CustomerController::class, 'get_states']);
Route::post('checkout/updateaddress', [App\Http\Controllers\FrontApp\CustomerController::class, 'checkout_UpdateAddress']);
Route::post('checkout/placeorder', [App\Http\Controllers\FrontApp\CustomerController::class, 'placeorder']);

//------------------FrontAPP/Content-Pages--
// Route::get('show/contentpage', [App\Http\Controllers\FrontApp\CustomerController::class, 'show_contentpage']);
// Route::get('page/{url}', [App\Http\Controllers\FrontApp\CustomerController::class, 'contentpage_view']);

//------------------FrontAPP/User/details--
Route::get('myaccount/userprofile', [App\Http\Controllers\FrontApp\CustomerController::class, 'useraccount']);
Route::post('myaccount/edit/userprofile', [App\Http\Controllers\FrontApp\CustomerController::class, 'userprofile_edit']);
Route::post('myaccount/edit/profileimage', [App\Http\Controllers\FrontApp\CustomerController::class, 'profileimage_update']);
Route::post('/myaccount/delete', [App\Http\Controllers\FrontApp\CustomerController::class, 'delete_account'])->name('myaccount.delete');


//------------------FrontAPP/User/Wishlist--
Route::get('myaccount/wishlist', [App\Http\Controllers\FrontApp\CustomerController::class, 'wishlist']);
Route::post('wishlist/addremove', [App\Http\Controllers\FrontApp\CustomerController::class, 'manage_wishlist']);

//------------------FrontAPP/User/OrderDetails--
Route::get('myaccount/orderhistory', [App\Http\Controllers\FrontApp\CustomerController::class, 'list_orders']);

//------------------FrontAPP/User/Password--
Route::post('user/changepassword', [App\Http\Controllers\FrontApp\CustomerController::class, 'changepassword']);
Route::post('user/forgotpassword', [App\Http\Controllers\FrontApp\CustomerController::class, 'forgotpassword']);
Route::post('user/resetpassword', [App\Http\Controllers\FrontApp\CustomerController::class, 'resetpassword']);
//search
Route::get('get/producttype', [App\Http\Controllers\FrontApp\CustomerController::class, 'get_producttype']);
Route::get('user/searchfilters', [App\Http\Controllers\FrontApp\CustomerController::class, 'get_filtervalues']);
Route::get('product/searchcondition', [App\Http\Controllers\FrontApp\CustomerController::class, 'search_condition']);
Route::get('home/subcategory', [App\Http\Controllers\FrontApp\CustomerController::class, 'subcategory_products']);
Route::get('payment/response', [App\Http\Controllers\FrontApp\CustomerController::class, 'payment_details']);
Route::get('clear/cart', [App\Http\Controllers\FrontApp\CustomerController::class, 'clear_cart']);
Route::get('getall/stores', [App\Http\Controllers\FrontApp\CustomerController::class, 'get_stores']);

Route::post('paymentdetails/store', [App\Http\Controllers\FrontApp\CustomerController::class, 'online_payment_success']);



