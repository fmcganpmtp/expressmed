<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Generalsetting;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\State;
use App\Models\User;
use App\Models\UserAddress;
use App\Rules\IsValidPassword;
use App\Rules\MatchOldPassword;
use DateTimeZone;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mail;

class MyaccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
    }

    public function myaccount()
    {
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
            if ($user_id) {
                $subview_page = 'frontview_customer.my_profile';
                $user_address = UserAddress::select('user_addresses.*', 'countries.name AS countryname', 'states.name AS state')
                    ->join('countries', 'user_addresses.country_id', 'countries.id')
                    ->join('states', 'user_addresses.state_id', 'states.id')
                    ->where('user_id', $user_id)
                    ->get()->all();
                $country_details = User::where('users.id', $user_id)
                    ->join('countries', 'countries.id', 'users.country_id')->first();

                $primary_address = UserAddress::where('user_id', $user_id)->where('type', 'primary')->get()->first();
                $countries = Country::get()->all();
                // $tot_Purchase = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')->where('orders.user_id', $user_id)->whereNotIn('order_details.status', ['cancelled', 'returned'])->count();
                $tot_Purchase = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')->where('orders.user_id', $user_id)->whereNotIn('order_details.status', ['cancelled', 'returned'])
                    ->whereNotIn('orders.status', ['initiated', 'failed'])
                    ->count();

                return view('frontview_customer.account', compact('subview_page', 'user_address', 'countries', 'primary_address', 'tot_Purchase', 'country_details'));
            } else {
                return view('notfound_frontview')->withErrors('You are not login please login your account.');
            }
        } else {
            return view('notfound_frontview')->withErrors('You are not login please login your account.');
        }
    }

    public function wishlist()
    {
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
            if ($user_id) {
                $subview_page = 'frontview_customer.wishlist';
                $primary_address = UserAddress::where('user_id', $user_id)->where('type', 'primary')->get()->first();
                $tot_Purchase = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')->where('orders.user_id', $user_id)->whereNotIn('order_details.status', ['cancelled', 'returned'])
                    ->whereNotIn('orders.status', ['initiated', 'failed'])
                    ->count();

                //Get wish list products coDe--
                $wishlist_products = Product::leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                    ->select('products.*', 'product_images.product_image')
                    ->join('wishlists', 'wishlists.product_id', 'products.id')
                    ->where('wishlists.user_id', $user_id)
                    ->paginate(20);

                return view('frontview_customer.account', compact('subview_page', 'primary_address', 'tot_Purchase', 'wishlist_products'));
            } else {
                return view('notfound_frontview')->withErrors('You are not login please login your account.');
            }
        } else {
            return view('notfound_frontview')->withErrors('You are not login please login your account.');
        }
    }

    public function orderhistory()
    {
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
            $GMT = new DateTimeZone("GMT");

            if ($user_id) {
                $subview_page = 'frontview_customer.order_history';
                $primary_address = UserAddress::where('user_id', $user_id)->where('type', 'primary')->get()->first();
                $tot_Purchase = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')->where('orders.user_id', $user_id)->whereNotIn('order_details.status', ['cancelled', 'returned'])
                    ->whereNotIn('orders.status', ['initiated', 'failed'])
                    ->count();

                //Get order details coDe--
                $orders = array();
                $order_details = Order::where('orders.user_id', $user_id)
                    ->where('orders.status', '!=', 'initiated')->where('orders.status', '!=', 'failed')->latest()->paginate(20);
                $country_details = User::where('users.id', $user_id)
                    ->join('countries', 'countries.id', 'users.country_id')->first();

                foreach ($order_details as $key => $order_row) {
                    $total_amount = 0;
                    // $localdate = date('Y-m-d H:i:s', strtotime('+5 hour +30 minutes', strtotime($order_row->date)));
                    // $date = new DateTime($localdate, $GMT);
                    // $gmtdate = $date->format('Y-m-d H:i:s');
                    // $gmtdate = new DateTime($date, $GMT);

                    // $date->format('Y-m-d H:i:s');

                    // $date1 = DateTime::createFromFormat('Y-m-d\Th:i:s:u', $date);
                    // dd($date);

                    // $date = date('Y-m-d H:i:s',strtotime($date));
                    $orders[$key]['order_id'] = $order_row->id;
                    $orders[$key]['order_date'] = $order_row->date;
                    $orders[$key]['status'] = $order_row->status;
                    $orders[$key]['total_amount'] = $order_row->total_amount;
                    $orders[$key]['grand_total'] = $order_row->grand_total;
                    $orders[$key]['order_details'] = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')
                        ->join('products', 'products.id', 'order_details.product_id')
                        ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                        ->leftJoin('categories', 'categories.id', 'products.producttypeid')
                    // ->leftJoin('product_reviews','product_reviews.product_id','products.id')
                        ->leftJoin('product_reviews', function ($join) use ($user_id) {
                            $join->on('product_reviews.product_id', 'products.id')
                                ->where('product_reviews.user_id', $user_id);
                        })
                        ->where('order_details.order_id', $order_row->id)
                        ->select('products.product_name', 'products.product_url', 'product_images.product_image', 'categories.name as product_typename', 'order_details.order_id', 'order_details.product_id', 'order_details.quantity', 'order_details.total_tax', 'order_details.price', 'order_details.amount', 'order_details.status as productstatus', 'order_details.status_on', 'product_reviews.id as review_id', 'product_reviews.rating', 'product_reviews.reviews')
                        ->get();

                    if (!empty($orders[$key]['order_details'])) {
                        foreach ($orders[$key]['order_details'] as $products) {
                            if (in_array($products->productstatus, ['ordered', 'shipped', 'delivered', 'return'])) {
                                $total_amount = $total_amount + $products->amount;
                            }

                        }
                    }
                    $orders[$key]['active_grand_total'] = $total_amount;

                }
                // $orders[$key]['active_grand_total']=$active
                //Get product review details coDe--
                $reviewproducts = array();
                $ProductReviewDetails = ProductReview::where('user_id', $user_id)->select('product_id', 'rating')->get();
                foreach ($ProductReviewDetails as $key => $ProductReviewDetails_row) {
                    $reviewproducts[$key] = $ProductReviewDetails_row->product_id;
                }
                // $reviewproducts[$key] = $ProductReviewDetails_row->product_id;

                return view('frontview_customer.account', compact('subview_page', 'primary_address', 'tot_Purchase', 'order_details', 'orders', 'reviewproducts', 'country_details'));
            } else {
                return view('notfound_frontview')->withErrors('You are not login please login your account.');
            }
        } else {
            return view('notfound_frontview')->withErrors('You are not login please login your account.');
        }
    }
    public function changeUserPassword()
    {
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;

            if ($user_id) {
                $subview_page = 'frontview_customer.change_password';
                $tot_Purchase = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')->where('orders.user_id', $user_id)->whereNotIn('order_details.status', ['cancelled', 'returned'])
                    ->whereNotIn('orders.status', ['initiated', 'failed'])
                    ->count();

                return view('frontview_customer.account', compact('subview_page', 'tot_Purchase'));
            } else {
                return view('notfound_frontview')->withErrors('You are not login please login your account.');
            }
        } else {
            return view('notfound_frontview')->withErrors('You are not login please login your account.');
        }
    }

    public function updateUserPassword(Request $request)
    {
        if (Auth::guard('user')->user()) {
            $userID = Auth::guard('user')->user()->id;

            $user_id = User::find($userID);
            if ($user_id) {
                $request->validate([
                    'current_password' => ['required', new MatchOldPassword],
                    'new_password' => ['required', new IsValidPassword()],
                    'confirm_new_password' => ['same:new_password'],
                ]);
                $subview_page = 'frontview_customer.change_password';
                $tot_Purchase = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')->where('orders.user_id', $user_id)->whereNotIn('order_details.status', ['cancelled', 'returned'])
                    ->whereNotIn('orders.status', ['initiated', 'failed'])
                    ->count();

                User::find($userID)->update(['password' => Hash::make($request->new_password)]);

                return redirect()->back()->with('success', 'Succesfully updated your password');
                // return view('frontview_customer.account', compact('subview_page', 'tot_Purchase'))->with('success', 'Succesfully updated your password');
            } else {
                return redirect()->back()->with('error', 'Update failed: Admin profile details not found.');
            }
        } else {
            return redirect()->back()->with('error', 'Update failed: Account is not logged. Please login your account.');
        }
    }

    public function updateProfile(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];
        if (Auth::guard('user')->user()->id) {
            $user_id = Auth::guard('user')->user()->id;
            if (empty($user_id)) {
                $message = "Please login into your account and try again";
                $ajax_status = 'failed';
            } else {
                if ($request->phone != '') {
                    $str = ltrim($request->phone, "0");
                    $request->merge(['phone' => $str]);
                }
                $validation = $this->validatorUser($request->all(), $user_id);
                // dd($validation->errors());

                if ($validation->fails()) {
                    $message = '';
                    foreach ($validation->errors()->toArray() as $value) {
                        $message .= $value[0] . '<br />';
                        $ajax_status = 'failed';

                    }
                    if ($request->country != '101') {
                        $message .= 'Service not available in your country.';
                    }
                    $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
                    return response()->json($return_array);

                } elseif ($request->country != '101') {
                    $message .= 'Service not available in your country.';
                    $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
                    return response()->json($return_array);
                } else {
                    User::find($user_id)->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'country_id' => $request->country,
                    ]);
                    $message = "Profile informations updated successfully";
                    $ajax_status = 'success';
                }
            }
        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

    protected function validatorUser(array $data, $user_id)
    {
        if ($data['phone'] != '') {
            $str = ltrim($data['phone'], "0");
            $data['phone'] = $str;
        }
        return Validator::make($data, [
            'name' => 'required',
            // 'phone' => 'required|regex:/[0-9]{9}/|unique:users,phone,' . $user_id,

            // 'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email,' . $user_id,
            'email' => [
                'required', 'regex:/(.+)@(.+)\.(.+)/i', 'email', Rule::unique('users')->where(function ($query) use ($user_id) {
                    $query->where('id', '!=', $user_id);
                    $query->where('status', '!=', 'deleted');
                }),
            ],
            'phone' => [
                'required', 'numeric', Rule::unique('users')->where(function ($query) use ($user_id) {
                    $query->where('id', '!=', $user_id);
                    $query->where('status', '!=', 'deleted');
                }),
            ],

        ]);
    }

    public function profilepic(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];
        if (Auth::guard('user')->user()->id) {
            $user_id = Auth::guard('user')->user()->id;
            if (empty($user_id)) {
                $message = "Please login into your account and try again";
                $ajax_status = 'failed';
            } else {
                $file = $request->file('file');
                $validation = $this->validatorImage($request->all());
                if ($validation->fails()) {
                    $message = '';
                    foreach ($validation->errors()->toArray() as $value) {
                        $message .= $value[0] . '<br />';
                    }
                    $ajax_status = 'failed';
                    $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
                    return response()->json($return_array);
                } else {
                    if ($file) {
                        if (Auth::guard('user')->user()->profile_pic != '') {
                            $image_path = public_path('/assets/uploads/profile/') . '/' . Auth::guard('user')->user()->profile_pic;
                            File::delete($image_path);
                        }
                        $fileName = 'profile_' . time() . '.' . $request->file->extension();
                        $request->file->move(public_path('/assets/uploads/profile/'), $fileName);
                        User::find($user_id)->update([
                            'profile_pic' => $fileName,
                        ]);
                        $message = "Profile image updated successfully";
                        $ajax_status = 'success';
                    }
                }
            }
        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

    protected function validatorImage(array $data)
    {
        return Validator::make($data, [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    }

    public function add_profileaddress(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];
        if (Auth::guard('user')->user()->id) {
            $user_id = Auth::guard('user')->user()->id;
            if (empty($user_id)) {
                $message = "Please login into your account and try again";
                $ajax_status = 'failed';
            } else {
                $validation = $this->validatorAddress($request->all());
                if ($validation->fails()) {
                    $message = '';
                    foreach ($validation->errors()->toArray() as $value) {
                        $message .= $value[0] . '<br />';
                    }
                    $ajax_status = 'failed';
                    $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
                    return response()->json($return_array);
                } else {
                    $Address_Exist = UserAddress::where('user_id', $user_id)->where('type', $request->address_type)->exists();
                    if (!$Address_Exist) {
                        UserAddress::create([
                            'user_id' => $user_id,
                            'type' => $request->address_type,
                            'name' => $request->address_name,
                            'phone' => $request->address_phone,
                            'pin' => $request->address_pin,
                            'location' => $request->address_location,
                            'address' => $request->address_address,
                            'city' => $request->address_town,
                            'state_id' => $request->state,
                            'country_id' => $request->country,
                            'landmark' => $request->address_landmark,
                        ]);
                        $message = "Address informations updated successfully";
                        $ajax_status = 'success';
                    } else {
                        $message = "Your " . $request->address_type . " address is existing. You can edit the address.";
                        $ajax_status = 'failed';
                    }
                }
            }
        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

    public function get_profileaddress(Request $request)
    {
        $addres_id = $request->addres_id;
        $countries = Country::where('name', 'India')->get();
        $address_data = array();
        $message = "";
        $state = array();
        if ($addres_id) {
            $address_data = UserAddress::find($request->addres_id);

            if ($address_data) {
                $state = State::where('country_id', $address_data->country_id)->get()->toArray();
                $ajax_status = 'success';
            } else {
                $ajax_status = 'failed';
                $message = "Address informations are not available at the moment";
            }
        } else {
            $message = "Invalid request. Address informations are not available.";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'address_data' => $address_data, 'state' => $state, 'countries' => $countries, 'message' => $message);
        return response()->json($return_array);
    }

    public function update_profileaddress(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];
        if (Auth::guard('user')->user()->id) {
            $user_id = Auth::guard('user')->user()->id;
            if (empty($user_id)) {
                $message = "Please login into your account and try again";
                $ajax_status = 'failed';
            } else {
                $validation = $this->validatorAddress($request->all());
                if ($validation->fails()) {
                    $message = '';
                    foreach ($validation->errors()->toArray() as $value) {
                        $message .= $value[0] . '<br />';
                        $ajax_status = 'failed';
                        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
                        return response()->json($return_array);
                    }
                } else {
                    if ($request->address_id != '') {
                        $Address_Exist = UserAddress::where('user_id', $user_id)->where('type', $request->address_type)->where('id', '!=', $request->address_id)->exists();
                        if (!$Address_Exist) {
                            UserAddress::where('id', $request->address_id)->where('user_id', $user_id)->update([
                                'type' => $request->address_type,
                                'name' => $request->address_name,
                                'phone' => $request->address_phone,
                                'pin' => $request->address_pin,
                                'location' => $request->address_location,
                                'address' => $request->address_address,
                                'city' => $request->address_town,
                                'state_id' => $request->state,
                                'country_id' => $request->country,
                                'landmark' => $request->address_landmark,
                            ]);
                            $message = "Address informations updated successfully";
                            $ajax_status = 'success';
                        } else {
                            $message = "Your " . $request->address_type . " address is already existing.";
                            $ajax_status = 'failed';
                        }
                    } else {
                        $message = "Invalid Address ID";
                        $ajax_status = 'failed';
                    }
                }
            }
        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

    protected function validatorAddress(array $data)
    {
        return Validator::make($data, [
            'address_name' => 'required',
            'address_phone' => 'required|regex:/[0-9]{9}/',
            'address_pin' => 'required',
            'address_location' => 'required',
            'address_address' => 'required',
            'address_town' => 'required',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
        ], ['address_address.required' => 'The address field is required.']);
    }

    public function orderstatus_manage(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];

        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;

            if ($request->mode == 'cancelproduct') {
                $status = $statusrequest = 'cancelled';
            } else if ($request->mode == 'returnproduct') {
                $status = 'return';
                $statusrequest = 'requested to return';
            }

            OrderDetails::where('order_id', $request->order_id)
                ->where('product_id', $request->productid)
                ->update([
                    'status' => $status,
                    'status_on' => date('Y-m-d H:i:s'),
                ]);

            $ExistOrderDetails = OrderDetails::where('order_id', $request->order_id);
            if ($status != '') {
                $ExistOrderDetails->where('status', '!=', $status);
            }
            $ExistOrderDetails = $ExistOrderDetails->exists();

            if (!$ExistOrderDetails) {
                Order::find($request->order_id)->update(['status' => 'cancelled']);
            }

            $message = "Item cancelled from order list.";
            $ajax_status = 'success';

            $ProductDetails = Product::leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
                ->where('products.id', $request->productid)
                ->select(DB::raw('products.product_name', '(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), 'products.id as productid', 'products.product_name', 'products.tax_ids', 'GS.value AS email', 'product_images.product_image')
                ->first();

            if ($ProductDetails) {
                $settings = Generalsetting::where('item', '=', 'notification_email')->first();
                if ($settings) {

                    $UserDetails = User::where('id', $user_id)->first();

                    // Order Cancelled or return notification mail to customer--
                    if ($UserDetails) {

                        Mail::send('email.orderedproduct_cancellationMail',
                            array(
                                'mode' => 'Customer_Manageorder',
                                'usertype' => 'Customer',
                                'customername' => $UserDetails->name,
                                'status' => $status,
                                'productname' => $ProductDetails->product_name,
                                'orderid' => $request->order_id,
                                'subject' => 'Your ordered Product successfully ' . $statusrequest . ' Order ID : ' . $request->order_id,
                            ), function ($message) use ($settings, $UserDetails, $ProductDetails) {
                                $message->from($settings->value, 'Expressmed');
                                $message->to($UserDetails->email);
                                $message->subject('You successfully cancelled the product ' . $ProductDetails->product_name);
                            });
                    }

                    // Order Cancelled or return notification mail to seller--
                    Mail::send('email.orderedproduct_cancellationMail',
                        array(
                            'mode' => 'Customer_Manageorder',
                            'usertype' => 'Admin',
                            'customername' => $UserDetails->name,
                            'status' => $status,
                            'productname' => $ProductDetails->product_name,
                            'orderid' => $request->order_id,
                            'subject' => 'Customer successfully ' . $statusrequest . ' Order ID : ' . $request->order_id,
                        ), function ($message) use ($settings, $ProductDetails) {
                            $message->from($settings->value, 'Expressmed');
                            $message->to($settings->value);
                            $message->subject('Notification Mail: Customer successfully cancelled a product ' . $ProductDetails->product_name);
                        });
                    //Notification mail to admin /--
                }
            }

        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

    public function add_productreview(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];

        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
            if (!ProductReview::where('user_id', $user_id)->where('product_id', $request->productid)->exists()) {
                $ProductReview = new ProductReview();
                $ProductReview->user_id = $user_id;
                $ProductReview->product_id = $request->productid;
                $ProductReview->reviews = $request->productreview;
                $ProductReview->rating = $request->starvalue;
                $ProductReview->save();

                $ajax_status = 'success';
            } else {
                $message = "Sorry.. You cannot add review for this product. Added already.";
                $ajax_status = 'failed';
            }
        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

    public function delete_productreview(Request $request)
    {

        $ajax_status = '';
        $message = '';
        $return_array = [];
        $user_id = Auth::guard('user')->user()->id;

        if (!isset($userid) && !empty($request->product_id)) {

            ProductReview::where('product_id', $request->product_id)->where('user_id', $user_id)->forceDelete();

            $ajax_status = 'success';
        } else {
            $message = "Invalid product or user.";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);

    }

    public function delete_account(Request $request)
    {

        if (Auth::guard('user')->user()->id) {
            $user_id = Auth::guard('user')->user()->id;

            if ($user_id) {
                User::find($user_id)->update([
                    'status' => 'deleted',
                ]);

                Auth::guard('user')->logout();
                return redirect()->route('home');
            } else {
                return redirect()->back()->with('error', 'Unable to proceed');
            }

        } else {
            $message = "Please login into your account and try again";
            return redirect()->back()->with('error', 'Please login into your account and try again.');
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

}
