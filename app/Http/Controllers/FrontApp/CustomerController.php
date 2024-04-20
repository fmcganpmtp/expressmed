<?php

namespace App\Http\Controllers\FrontApp;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Country;
use App\Models\DeviceToken;
use App\Models\Generalsetting;
use App\Models\Invoice;
use App\Models\MedicineUse;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PaymentDetail;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\Productbrand;
use App\Models\Productcontent;
use App\Models\ProductManufacturer;
use App\Models\ProductMedicineuse;
use App\Models\ProductReview;
use App\Models\Producttype;
use App\Models\Product_image;
use App\Models\Promotionbanner;
use App\Models\PromotionBannerImage;
use App\Models\Resetpassword;
use App\Models\State;
use App\Models\Store;
use App\Models\Tax;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Wishlist;
use App\Rules\IsValidPassword;
use Auth;
use DB;
use File;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mail;
use PDF;

class CustomerController extends Controller
{

    //----------------Login and Registeration Section--

    public function registerCustomer(Request $request)
    {

        if (!empty($request->header('device-id'))) {

            $validate = $this->validateuser($request->all());

            if ($validate->fails()) {
                $error_msg = '';
                foreach ($validate->errors()->toArray() as $error) {
                    $error_msg .= $error[0] . '<br />';
                }
                $returnArray = array('result' => false, 'message' => $error_msg);
            } else {
                $string_password = base64_decode($request->password);
                $password = preg_replace('/^o63s/', '', $string_password);
                $now = date('Y-m-d H:i:s');

                $country_code = preg_replace('/[^A-Za-z0-9\-]/', '', $request->countrycode); // Removes special chars.
                $phone_num = preg_replace('/[^A-Za-z0-9\-]/', '', $request->phone); // Removes special chars.
                $country = Country::where('phonecode', $country_code)->first();
                // $phone = '+' . $country_code . $phone_num;

                $userID = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $phone_num,
                    'country_id' => $country->id,
                    'password' => Hash::make($password),
                ])->id;

                if ($userID) {
                    $guard = Config::get('constants.guards.user');
                    if ($guard == 'user') {
                        $attempt = Auth::guard($guard)->attempt([
                            'email' => $request->email,
                            'password' => $password,
                        ],
                            $request->get('remember')
                        );

                        if ($attempt) {
                            $user_id = Auth::guard('user')->user()->id;
                            if ($user_id) {
                                $expiry_time = date('Y-m-d H:i:s', strtotime('+1 month'));
                                $access_token = Str::random(60);

                                // User::find($user_id)->update([
                                //     'expiry_time' =>  $expiry_time,
                                //     'device_id' =>  $request->header('device-id'),
                                //     'api_token' =>  $access_token
                                // ]);

                                $token = DeviceToken::create([
                                    'user_id' => $userID,
                                    'api_token_expiry' => $expiry_time,
                                    'device_id' => $request->header('device-id'),
                                    'api_token' => $access_token,
                                ])->id;

                                // $UserDetails = User::find($user_id);
                                $UserDetails = User::select('users.*', 'device_tokens.*')
                                    ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                                    ->where('users.id', $user_id)
                                    ->where('device_tokens.device_id', $request->header('device-id'))
                                    ->where('device_tokens.api_token_expiry', '>=', $now)
                                    ->first();

                                if ($UserDetails) {

                                    $settings = Generalsetting::where('item', '=', 'notification_email')->first();
                                    if ($settings) {
                                        // Account Verification mail to customer --
                                        // $verification_code = $userID;
                                        // Mail::send('email.mail_verfication',
                                        //     array(
                                        //         'name' => $request->name,
                                        //         'email' => $request->email,
                                        //         'type' => 'customer',
                                        //         'verification_code' =>  $verification_code,
                                        //     ), function($message) use ($request, $settings) {
                                        //         $message->from($settings->value,'Medcliq');
                                        //         $message->to($request->email);
                                        //         $message->subject('verification mail: Verify your mail.');
                                        //     });
                                        // Account Verification mail to customer /--

                                        //Notification mail to admin--
                                        // Mail::send('email.mail_notification',
                                        // array(
                                        //     'mode' => 'Registration',
                                        //     'usertype' => 'Admin',
                                        //     'name' => $request->name,
                                        //     'email' => $request->email,
                                        //     'subject' => 'Medcliq Notification mail - New customer '.$request->name.' registered',
                                        // ), function($message) use ($request, $settings) {
                                        //     $message->from($settings->value,'Medcliq');
                                        //     $message->to($settings->value);
                                        //     $message->subject('Notification Mail: New Customer Registered ');
                                        // });
                                        //Notification mail to admin /--
                                    }

                                    $returnArray = array('result' => true, 'message' => 'Your account created successfully.', 'customerdetails' => $UserDetails);
                                } else {
                                    $returnArray = array('result' => false, 'message' => 'Customer details not found.');
                                }
                            } else {
                                $returnArray = array('result' => false, 'message' => 'Customer not logged.');
                            }
                        } else {
                            $returnArray = array('result' => false, 'message' => 'Customer login attemp failed.');
                        }
                    }
                } else {
                    $returnArray = array('result' => false, 'message' => 'Customer registration failed.');
                }
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    private function validateuser(array $data)
    {

        // dd($data['phone']);

        return Validator::make($data, [
            'name' => 'required',
            // 'email' => 'required|email|unique:users,email|regex:/(.+)@(.+)\.(.+)/i',
            'email' => [
                'required', 'regex:/(.+)@(.+)\.(.+)/i', 'email', Rule::unique('users')->where(function ($query) {
                    $query->where('status', '!=', 'deleted');
                }),
            ],
            'email' => [
                'required', Rule::unique('users')->where(function ($query) {
                    $query->where('status', '!=', 'deleted');
                }),
            ],

            'countrycode' => 'required',
            'phone' => [
                'required', Rule::unique('users')->where(function ($query) {
                    $query->where('status', '!=', 'deleted');
                }),
            ],

            // 'phone' => 'required|unique:users,phone',
            // 'password' => 'required|min:6',
            'password' => ['required', new IsValidPassword()],
            'confirm_password' => ['same:password'],
        ]);
        $country_code = preg_replace('/[^A-Za-z0-9\-]/', '', $data['countrycode']); // Removes special chars.
        $phone_num = preg_replace('/[^A-Za-z0-9\-]/', '', $data['phone']); // Removes special chars.
        $phone = '+' . $country_code . $phone_num;
        $data['phone'] = $phone;
    }

    public function userlogin(Request $request)
    {
        if (!empty($request->header('device-id'))) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
                'password' => 'required|min:6',
            ]);
            if ($validator->fails()) {
                $error_msg = '';
                foreach ($validator->errors()->toArray() as $value) {
                    $error_msg .= $value[0];
                }
                $returnArray = array('result' => false, 'message' => $error_msg);
            } else {
                $string_password = base64_decode($request->password);
                $password = preg_replace('/^o63s/', '', $string_password);
                $guard = Config::get('constants.guards.user');
                if ($guard == 'user') {
                    $attempt = Auth::guard($guard)->attempt([
                        'email' => $request->email,
                        'password' => $password,
                    ],
                        $request->get('remember')
                    );

                    if ($attempt) {
                        $user_id = Auth::guard('user')->user()->id;
                        if ($user_id) {
                            if (Auth::guard('user')->user()->status != 'active') {
                                Auth::guard('user')->logout();
                                return response()->json(['result' => false, 'message' => 'Your account is no longer exist.']);
                            }
                            $expiry_time = date('Y-m-d H:i:s', strtotime('+1 month'));
                            $access_token = Str::random(60);
                            $now = date('Y-m-d H:i:s');
                            $late_expiry_date = date('Y-m-d H:i:s', strtotime('-1 days', strtotime(str_replace('/', '-', $now))));

                            // User::find($user_id)->update([
                            //     'expiry_time' =>  $expiry_time,
                            //     'device_id' =>  $request->header('device-id'),
                            //     'api_token' =>  $access_token
                            // ]);

                            DeviceToken::where('user_id', $user_id)
                                ->where('device_id', $request->header('device-id'))
                                ->where('api_token_expiry', '>=', $now)
                                ->update([
                                    'api_token_expiry' => $late_expiry_date,
                                ]);

                            $token = DeviceToken::create([
                                'user_id' => $user_id,
                                'device_id' => $request->header('device-id'),
                                'api_token' => $access_token,
                                'api_token_expiry' => $expiry_time,

                            ]);

                            // $UserDetails = User::find($user_id);
                            $UserDetails = User::select('users.*', 'device_tokens.*')
                                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                                ->where('users.id', $user_id)
                                ->where('device_tokens.device_id', $request->header('device-id'))
                                ->where('device_tokens.api_token_expiry', '>=', $now)
                                ->first();

                            if ($UserDetails) {

                                $settings = Generalsetting::where('item', '=', 'notification_email')->first();
                                if ($settings) {
                                    // Account Verification mail to customer --
                                    // $verification_code = $userID;
                                    // Mail::send('email.mail_verfication',
                                    //     array(
                                    //         'name' => $request->name,
                                    //         'email' => $request->email,
                                    //         'type' => 'customer',
                                    //         'verification_code' =>  $verification_code,
                                    //     ), function($message) use ($request, $settings) {
                                    //         $message->from($settings->value,'Medcliq');
                                    //         $message->to($request->email);
                                    //         $message->subject('verification mail: Verify your mail.');
                                    //     });
                                    // Account Verification mail to customer /--

                                    //Notification mail to admin--
                                    // Mail::send('email.mail_notification',
                                    // array(
                                    //     'mode' => 'Registration',
                                    //     'usertype' => 'Admin',
                                    //     'name' => $request->name,
                                    //     'email' => $request->email,
                                    //     'subject' => 'Medcliq Notification mail - New customer '.$request->name.' registered',
                                    // ), function($message) use ($request, $settings) {
                                    //     $message->from($settings->value,'Medcliq');
                                    //     $message->to($settings->value);
                                    //     $message->subject('Notification Mail: New Customer Registered ');
                                    // });
                                    //Notification mail to admin /--
                                }

                                $returnArray = array('result' => true, 'message' => 'Login Successfully', 'customerdetails' => $UserDetails);
                            } else {
                                $returnArray = array('result' => false, 'message' => 'Customer details not found.');
                            }
                        } else {
                            $returnArray = array('result' => false, 'message' => 'Customer is not logged.');
                        }
                    } else {
                        $returnArray = array('result' => false, 'message' => 'Customer login attemp failed. Email or Password wrong.');
                    }
                }
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    //----------------Login section with Phone and OTP--

    public function request_otp(Request $request)
    {

        if (!empty($request->header('device-id'))) {
            if ($request->phone != '') {

                $country_code = preg_replace('/[^A-Za-z0-9\-]/', '', $request->countrycode); // Removes special chars.
                $phone_num = preg_replace('/[^A-Za-z0-9\-]/', '', $request->phone); // Removes special chars.
                // $phone = '+' . $country_code . $phone_num;
                $country = Country::where('phonecode', $country_code)->first();
                if ($country) {

                    $userdetails = User::where('phone', $phone_num)->where('country_id', $country->id)->where('status', '!=', 'deleted')->first();
                    $otp_number = rand(100000, 999999);
                    $otp_expiry = date('Y-m-d H:i:s', strtotime('+4 minutes'));
                    $now = date('Y-m-d H:i:s');
                    $late_expiry_date = date('Y-m-d H:i:s', strtotime('-1 days', strtotime(str_replace('/', '-', $now))));

                    if ($userdetails) {
                        if ($userdetails->status == 'active') {

                            $up = DeviceToken::where('user_id', $userdetails->id)
                                ->where('device_id', $request->header('device-id'))
                                ->update([
                                    'otp_expiry' => $late_expiry_date,
                                ]);

                            $token = DeviceToken::create([
                                'user_id' => $userdetails->id,
                                'device_id' => $request->header('device-id'),
                                'otp' => $otp_number,
                                'otp_expiry' => $otp_expiry,

                            ]);
                            $this->sendSms($request->phone, $otp_number);
                            //--When OTP update success, generated OTP send by sms to the given phone ~ SMS Gateway--
                            //---Send by sms codebe here....;
                            //--SMS Gateway-//-

                            $returnArray = array('result' => true, 'message' => 'Successfully. Enter given OTP.', 'OTP' => $otp_number, 'phone' => $phone_num);
                        } else {
                            $returnArray = array('result' => false, 'message' => 'Your account is no longer exist.');
                        }
                    } else {
                        $validator = Validator::make($request->all(), [
                            // 'phone' => 'required|numeric|unique:users,phone',
                            'phone' => [
                                'required', 'numeric', Rule::unique('users')->where(function ($query) {
                                    $query->where('status', '!=', 'deleted');
                                }),
                            ],
                        ]);
                        if ($validator->fails()) {
                            $returnArray = array('result' => false, 'message' => $validator->errors());
                        } else {
                            $userID = User::create([
                                'phone' => $phone_num,
                                'country_id' => $country->id,
                            ])->id;

                            $up = DeviceToken::where('user_id', $userID)
                                ->where('device_id', $request->header('device-id'))
                                ->update([
                                    'otp_expiry' => $late_expiry_date,
                                ]);

                            $token = DeviceToken::create([
                                'user_id' => $userID,
                                'device_id' => $request->header('device-id'),
                                'otp' => $otp_number,
                                'otp_expiry' => $otp_expiry,

                            ]);
                            $this->sendSms($request->phone, $otp_number);

                            $returnArray = array('result' => true, 'message' => 'Successfully Registered. Enter given OTP.', 'OTP' => $otp_number, 'phone' => $phone_num);
                        }
                    }
                } else {
                    $returnArray = array('result' => false, 'message' => 'Attempt failed: Your Country code is not exist.');
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: Your phone number not found. Please try again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    public function userlogin_otp(Request $request)
    {
        if (!empty($request->header('device-id'))) {
            $deviceID = $request->header('device-id');

            $validator = Validator::make($request->all(), [
                'phone' => 'required|regex:/[0-9]{9}/',
                'otp' => 'required|digits:6',
                'countrycode' => 'required',
            ]);
            // $country_code = preg_replace('/[^A-Za-z0-9\-]/', '', $request->countrycode); // Removes special chars.
            $phone_num = preg_replace('/[^A-Za-z0-9\-]/', '', $request->phone); // Removes special chars.
            // $phone = '+' . $country_code . $phone_num;

            if ($validator->fails()) {
                $error_msg = '';
                foreach ($validator->errors()->toArray() as $value) {
                    $error_msg .= $value[0];
                }
                $returnArray = array('result' => false, 'message' => $error_msg);
            } else {

                $userdetails = User::select('users.*')
                    ->where('users.phone', $phone_num)
                    ->join('device_tokens', 'device_tokens.user_id', 'users.id')

                    ->where('device_tokens.device_id', $deviceID)
                    ->where('device_tokens.otp', $request->otp)
                    ->where('device_tokens.otp_expiry', '>=', date('Y-m-d H:i:s'))
                    ->first();

                if ($userdetails) {
                    $attempt = Auth::loginUsingId($userdetails->id);
                    if ($attempt) {
                        if ($userdetails->status != 'active') {
                            Auth::guard('user')->logout();
                            return response()->json(['result' => false, 'message' => 'Your account is no longer exist.']);
                        }
                        $expiry_time = date('Y-m-d H:i:s', strtotime('+1 month'));
                        $access_token = Str::random(60);
                        $now = date('Y-m-d H:i:s');
                        $late_expiry_date = date('Y-m-d H:i:s', strtotime('-1 days', strtotime(str_replace('/', '-', $now))));

                        // DeviceToken::where('user_id', $userdetails->id)->update([
                        //     'api_token_expiry' => $expiry_time,
                        //     'device_id' => $deviceID,
                        //     'api_token' => $access_token,
                        // ]);

                        DeviceToken::where('user_id', $userdetails->id)
                            ->where('device_id', $deviceID)
                        // ->where('api_token_expiry', '>=', $now)
                            ->update([
                                'api_token_expiry' => $late_expiry_date,
                            ]);

                        $token = DeviceToken::create([
                            'user_id' => $userdetails->id,
                            'device_id' => $deviceID,
                            'api_token' => $access_token,
                            'api_token_expiry' => $expiry_time,

                        ]);

                        // $UserDetails = User::find($userdetails->id);
                        $UserDetails = User::select('users.*', 'device_tokens.*')
                            ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                            ->where('users.id', $userdetails->id)
                            ->where('device_tokens.device_id', $request->header('device-id'))
                            ->where('device_tokens.api_token_expiry', '>=', $now)
                            ->first();

                        if ($UserDetails) {
                            $settings = Generalsetting::where('item', '=', 'notification_email')->first();
                            if ($settings) {
                                // Account Verification mail to customer --
                                // $verification_code = $userID;
                                // Mail::send('email.mail_verfication',
                                //     array(
                                //         'name' => $request->name,
                                //         'email' => $request->email,
                                //         'type' => 'customer',
                                //         'verification_code' =>  $verification_code,
                                //     ), function($message) use ($request, $settings) {
                                //         $message->from($settings->value,'Medcliq');
                                //         $message->to($request->email);
                                //         $message->subject('verification mail: Verify your mail.');
                                //     });
                                // Account Verification mail to customer /--

                                //Notification mail to admin--
                                // Mail::send('email.mail_notification',
                                // array(
                                //     'mode' => 'Registration',
                                //     'usertype' => 'Admin',
                                //     'name' => $request->name,
                                //     'email' => $request->email,
                                //     'subject' => 'Medcliq Notification mail - New customer '.$request->name.' registered',
                                // ), function($message) use ($request, $settings) {
                                //     $message->from($settings->value,'Medcliq');
                                //     $message->to($settings->value);
                                //     $message->subject('Notification Mail: New Customer Registered ');
                                // });
                                //Notification mail to admin /--
                            }
                            $returnArray = array('result' => true, 'message' => 'Login Successfully', 'customerdetails' => $UserDetails);
                        } else {
                            $returnArray = array('result' => false, 'message' => 'Customer details not found.');
                        }
                    } else {
                        $returnArray = array('result' => false, 'message' => 'Customer login attemp failed.');
                    }
                } else {
                    $returnArray = array('result' => false, 'message' => 'Login attempt failed. OTP expired or given details wrong. Please request again for OTP.');
                }
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    //----------------Front view landing page--
    public function home_view(Request $request)
    {
        if ($request->header('device-id') != '') {
            $Category_imagepath = '/assets/uploads/category/';
            $Brand_imagepath = '/assets/uploads/brands/';

            $userID = 0;
            $deviceID = $request->header('device-id');
            if ($request->header('api-token') != '') {
                $apiToken = $request->header('api-token');

                // $userDetails = DeviceToken::where('device_id', $request->header('device-id'))->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();
                $userDetails = User::select('users.*')
                    ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                    ->where('device_tokens.device_id', $deviceID)
                    ->where('device_tokens.api_token', $apiToken)
                    ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                    ->first();
                // dd($userDetails);
                if ($userDetails) {
                    $userID = $userDetails->id;
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
                }
            }

            $cartCount = Cart::select('carts.id');
            if ($userID == 0) {
                $cartCount->where('carts.ip', $request->header('device-id'))->where('carts.user_id', $userID);
            } else {
                $cartCount->where('carts.user_id', $userID);
            }
            $cartCount = $cartCount->count();

            $ParentCategories = Category::select('id as categoryid', 'name', DB::raw('CONCAT("' . $Category_imagepath . '", image) AS categoryimage'), 'description', 'status')->where('parent_id', 0)->where('status', 'active')->get();
            $ProductBands = Productbrand::select('id as productbrandid', 'name', DB::raw('CONCAT("' . $Brand_imagepath . '", image) AS brandimage'))->limit(20)->get();

            //Main Body Slider--
            $Banner_imagepath = '/assets/uploads/promotionbanner/';

            $mainSlider['Details'] = '';
            $mainSlider['Images'] = '/front_view/images/slider-01.png';
            $MainSliderRow = Promotionbanner::where('section', 'mobile')->where('position', 'maintop')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
            if ($MainSliderRow) {
                $mainSlider['Details'] = $MainSliderRow;
                $MainSliderImages = PromotionBannerImage::select('id as productbannerimageid', 'promotionbanner_id', DB::raw('CONCAT("' . $Banner_imagepath . '", image) AS bannerimage'))->where('promotionbanner_id', $MainSliderRow->id)->get();
                if ($MainSliderImages->isNotEmpty()) {
                    $mainSlider['Images'] = $MainSliderImages;
                }
            }

            $middleBanner = [];
            $middleBannerRow = Promotionbanner::where('section', 'mainbody')->where('position', 'middle')->where('type', 'plain')->where('status', 'active')->limit(2)->latest()->get(['id', 'title'])->toArray();
            $middleBanner['Images'] = [];
            if ($middleBannerRow) {
                $middleBannerImages = PromotionBannerImage::select('id as productbannerimageid', 'promotionbanner_id', DB::raw('CONCAT("' . $Banner_imagepath . '", image) AS bannerimage'))->whereIn('promotionbanner_id', array_column($middleBannerRow, 'id'))->get();
                $middleBanner['Images'] = $middleBannerImages;
            }

            $Home_categories = Category::select('categories.*')->join('home_categories', 'home_categories.category_id', 'categories.id')->get();
            $homepageProductListings = [];

            if ($Home_categories) {
                $Product_imagepath = '/assets/uploads/products/';

                $homepageProductListings = [];
                foreach ($Home_categories as $data) {
                    $products = Product::select('products.id', 'products.product_name', 'products.price', 'products.offer_price', 'products.created_at', DB::raw('CONCAT("' . $Product_imagepath . '", product_images.product_image) AS productimage'));
                    if ($userID != 0) {
                        $products->addSelect(DB::raw('(CASE WHEN wishlists.id != "" THEN 1 ELSE 0 END) AS wishlist'));
                        $products->leftJoin('wishlists', function ($query) use ($userID) {
                            $query->on('wishlists.product_id', 'products.id');
                            $query->on('wishlists.user_id', '=', DB::raw($userID));
                        });
                    }
                    $products = $products->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                        ->where('products.category_id', $data->id)
                        ->where('products.hide_from_site', '!=', '1')
                        ->where('products.status', 'active')
                        ->limit(4)->latest('products.created_at')->get();

                    if ($products->isNotEmpty()) {
                        $homepageProductListings[] = array(
                            'category_name' => $data->name,
                            'category_image' => $data->image,
                            'category_id' => $data->id,
                            'products' => $products,
                        );
                    }
                }
            }

            $top_selling = OrderDetails::join('products', 'products.id', 'order_details.product_id')
                ->leftJoin('categories as type1', 'type1.id', 'products.producttypeid')
                ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                ->select('products.id as product_id', 'products.product_name', 'type1.name as product_type', 'products.price', 'products.offer_price', DB::raw('COUNT(products.id) AS product_cnt'), 'products.manufacturer', DB::raw('CONCAT("' . $Product_imagepath . '", product_images.product_image) AS productimage'));

            if ($userID != 0) {
                $top_selling->addSelect(DB::raw('(CASE WHEN wishlists.id != "" THEN 1 ELSE 0 END) AS wishlist'));
                $top_selling->leftJoin('wishlists', function ($on) use ($userID) {
                    $on->on('wishlists.product_id', 'products.id');
                    $on->on('wishlists.user_id', '=', DB::raw($userID));
                });
            }
            $top_selling->where('type1.name', '!=', 'All Medicines')
                ->where('products.hide_from_site', '!=', '1')
                ->where('products.status', 'active');
            $top_selling = $top_selling->groupBy('order_details.product_id')->orderBy('product_cnt', 'DESC')->limit(10)->get();

            $returnArray = array('result' => true, 'ParentCategories' => $ParentCategories, 'ProductBands' => $ProductBands, 'mainSlider' => $mainSlider, 'middleBanner' => $middleBanner, 'productListings' => $homepageProductListings, 'cartCount' => $cartCount, 'top_selling' => $top_selling);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    //----------------Front view Product Detail Page--
    public function product_detail(Request $request)
    {
        if ($request->header('device-id') != '') {
            if (!empty($request->product_id)) {
                $img_path = '/assets/uploads/products/';

                $userID = 0;
                $deviceID = $request->header('device-id');
                if ($request->header('api-token') != '') {
                    $apiToken = $request->header('api-token');
                    // $userDetails = DeviceToken::where('device_id', $request->header('device-id'))->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
                    $userDetails = User::select('users.*')
                        ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                        ->where('device_tokens.device_id', $deviceID)
                        ->where('device_tokens.api_token', $apiToken)
                        ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                        ->first();

                    if ($userDetails) {
                        $userID = $userDetails->id;
                    } else {
                        return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
                    }
                }

                $product = Product::select('products.*', 'type1.name as producttype', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS productimage'), 'medicine_uses.name as medicine_use_name', 'productbrands.name as brand_name', 'type2.name as category', 'product_manufacturers.name as manufacturer')
                    ->Where('products.id', $request->product_id)
                    ->where('products.hide_from_site', '!=', '1')
                    ->where('products.status', 'active');

                if ($userID != 0) {
                    $product->addSelect(DB::raw('(CASE WHEN wishlists.id != "" THEN 1 ELSE 0 END) AS wishlist'));
                    $product->leftJoin('wishlists', function ($query) use ($userID) {
                        $query->on('wishlists.product_id', 'products.id');
                        $query->on('wishlists.user_id', '=', DB::raw($userID));
                    });
                }

                $product = $product->join('categories as type1', 'type1.id', 'products.producttypeid')
                    ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                    ->leftJoin('categories as type2', 'type2.id', 'products.category_id')
                    ->leftJoin('productsuppliers', 'productsuppliers.id', 'products.supplier')
                    ->leftJoin('medicine_uses', 'medicine_uses.id', 'products.medicine_use')
                    ->leftJoin('productbrands', 'productbrands.id', 'products.brands')
                    ->leftJoin('product_manufacturers', 'product_manufacturers.id', 'products.manufacturer')
                    ->first();
                if ($product) {
                    $medicine_uses = array();

                    $medicine_uses = ProductMedicineuse::join('medicine_uses', 'medicine_uses.id', 'product_medicineuses.medicine_use')
                        ->where('product_medicineuses.product_id', $product->id)
                        ->select('product_medicineuses.medicine_for', 'medicine_uses.id as usesid', 'medicine_uses.name as use')
                        ->get();

                    $show_review = true;
                    if ($product->producttype == "All Medicines") {
                        $show_review = false;
                    }
                    $brand_details = array();
                    if ($product->brands) {
                        $brand_details = Productbrand::find($product->brands);
                    }

                    $product_images = Product_image::select('product_images.id AS imageid', 'product_images.product_id', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS Images'))->leftjoin('products', 'products.thumbnail', 'product_images.id')->where('product_id', $product->id)->get();

                    $parentCategory = array();
                    $category = Category::find($product->category_id);
                    if ($category && $category->parent_id != 0) {
                        $parentCategory = $category->getParentsNames();
                    }

                    $productcontentIds = explode(',', $product->productcontent_id);
                    $ProductContents = Productcontent::select('id as productcontentid', 'name')->whereIn('id', $productcontentIds)->get()->toArray();

                    $total_reviews = $product->reviews()->with('user')->approved()->notSpam()->Onlyreview()->orderBy('created_at', 'desc')->count();
                    $avg_rating = bcdiv($product->getAverageRatingAttribute(), 1, 1);

                    $review_allow = false;
                    if ($userID != 0) {
                        $ReviewExist = ProductReview::where('user_id', $userID)->where('product_id', $product->id)->exists();

                        $orders = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')
                            ->where('orders.user_id', $userID)
                            ->where('order_details.product_id', $product->id)
                            ->where('order_details.status', 'delivered')
                            ->count();
                        if (!$ReviewExist && $orders > 0) {
                            $review_allow = true;
                        }
                    }
                    $product_variant_ids = explode(',', $product->variant_products);
                    $product_variants = Product::select('products.*', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS productimage'))
                        ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
                        ->where('products.hide_from_site', '!=', '1')
                        ->where('products.status', 'active')
                        ->whereIn('products.id', $product_variant_ids)->limit(4)->get();
                    $relatedProducts = Product::select('products.*', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS productimage'))
                        ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
                        ->where('products.hide_from_site', '!=', '1')
                        ->where('products.status', 'active')
                        ->where('products.category_id', $product->category_id)
                        ->where('products.category_id', '<>', null)
                        ->where('products.id', '<>', $product->id);
                    $relatedProducts = $relatedProducts->limit(4)->get();

                    $medicineusesIds = array();
                    foreach ($medicine_uses as $row) {
                        $medicineusesIds[] = $row->usesid;
                    }

                    $similarUse = Product::select('products.*', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS productimage'))
                        ->Join('product_medicineuses', 'product_medicineuses.product_id', 'products.id')
                        ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
                        ->where('products.hide_from_site', '!=', '1')
                        ->where('products.status', 'active')
                        ->whereIn('product_medicineuses.medicine_use', $medicineusesIds)
                        ->where('products.id', '<>', $product->id);
                    $similarUse = $similarUse->groupBy('products.id')->limit(4)->get();

                    $similarcontentproducts = array();
                    if ($product->productcontent_id != '') {
                        $contentId = explode(',', $product->productcontent_id);
                        $similarcontentproducts = Product::select('products.*', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS productimage'))
                            ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
                            ->leftJoin('productcontents', DB::raw('FIND_IN_SET(productcontents.id, products.productcontent_id)'), '>', DB::raw("'0'"))
                            ->where('products.hide_from_site', '!=', '1')
                            ->where('products.status', 'active')
                            ->whereIn('productcontents.id', $contentId)
                            ->where('products.id', '<>', $product->id)->limit(4)->distinct()->get();
                    }

                    $returnArray = array('result' => true, 'product' => $product, 'brand_details' => $brand_details, 'product_images' => $product_images, 'ProductContents' => $ProductContents, 'parentCategory' => $parentCategory, 'medicine_uses' => $medicine_uses, 'total_reviews' => $total_reviews, 'avg_rating' => $avg_rating, 'review_allow' => $review_allow, 'show_review' => $show_review, 'product_variants' => $product_variants, 'related_products' => $relatedProducts, 'similaruse_products' => $similarUse, 'similarcontentproducts' => $similarcontentproducts);
                } else {
                    $returnArray = array('result' => false, 'message' => 'Product details not found.');
                }
            } else {
                $returnArray = array('result' => 'failed', 'message' => 'Product id not found.');
            }
        } else {
            $returnArray = array('result' => 'failed', 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    public function prescription(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {

                $userID = $userDetails->id;
                if (!empty($request->product_id)) {

                    $product_id = $request->product_id;

                    $validator = Validator::make($request->all(), [
                        'prescription_file' => 'required|mimes:jpeg,jpg,png,pdf,doc',
                    ]);

                    if ($validator->fails()) {
                        $returnArray = array('result' => false, 'message' => $validator->errors()->first());
                    } else {
                        if (!Prescription::where('user_id', $userID)->where('product_id', $request->product_id)->whereIn('status', [1, 2])->exists()) {

                            $pre_file = $request->file('prescription_file');
                            // dd($imagefile->extension());
                            $fileName = 'presc_' . time() . '.' . $pre_file->extension();
                            $pre_file->move(public_path('/assets/uploads/prescription/'), $fileName);
                            $prescription_id = Prescription::create([
                                'user_id' => $userID,
                                'product_id' => $product_id,
                                'file' => $fileName,
                                'status' => 1,
                            ])->id;
                            $returnArray = array('result' => true, 'message' => 'Prescription uploaded successfully.', 'prescription_id' => $prescription_id);

                        } else {

                            $prescription = Prescription::where('user_id', $userID)->where('product_id', $request->product_id)
                            // ->whereIn('status', [1, 2])
                                ->where('order_id', '=', null)
                                ->first();

                            $prescription_id = $prescription->id;

                            $returnArray = array('result' => false, 'message' => 'Prescription file already uploaded.', 'prescription_id' => $prescription_id);
                        }
                    }
                } else {
                    $returnArray = array('result' => false, 'message' => 'Product not found.');
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login.');
            }

        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }
    public function productreview_list(Request $request)
    {
        if ($request->header('device-id') != '') {
            if (!empty($request->product_id)) {
                $product = Product::find($request->product_id);
                if ($product) {
                    $reviews = $product->reviews()->with('user')->approved()->notSpam()->orderBy('created_at', 'desc')->paginate(10);

                    $returnArray = array('result' => true, 'message' => 'Succesfully', 'productreviews' => $reviews);
                } else {
                    $returnArray = array('result' => false, 'message' => 'Product details not found.');
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Product id not found.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    public function add_productreview(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {

                $validate = Validator::make($request->all(), [
                    'product_id' => 'required|integer',
                    'rating' => 'required_without:review|nullable|integer|min:1|lte:5',
                    'review' => 'required_without:rating',
                ]);

                if ($validate->fails()) {
                    return response()->json(['result' => false, 'message' => $validate->errors()->first()]);
                } else {
                    $userID = $userDetails->id;
                    $Product = Product::find($request->product_id);
                    if ($Product) {
                        $orders = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')
                            ->where('orders.user_id', $userID)
                            ->where('order_details.product_id', $Product->id)
                            ->where('order_details.status', 'delivered')
                            ->count();

                        if (!ProductReview::where('user_id', $userID)->where('product_id', $Product->id)->exists() && $orders > 0) {

                            $ProductReview = new ProductReview();
                            $ProductReview->user_id = $userID;
                            $ProductReview->product_id = $Product->id;
                            $ProductReview->reviews = $request->review;
                            $ProductReview->rating = $request->rating;
                            $ProductReview->save();

                            $returnArray = array('result' => true, 'message' => 'Successfully added your product review.');
                        } else {
                            $returnArray = array('result' => false, 'message' => 'Sorry.. You are not eligible for review this product.');
                        }
                    } else {
                        $returnArray = array('result' => false, 'message' => 'Attempt failed: Requested product not found.');
                    }
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    //----------------Front view/Shopping/cart--
    public function list_cart(Request $request)
    {
        if ($request->header('device-id') != '') {
            $img_path = '/assets/uploads/products/';
            $deviceID = $request->header('device-id');
            $userID = 0;
            if ($request->header('api-token') != '') {
                $apiToken = $request->header('api-token');
                // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();
                $userDetails = User::select('users.*')
                    ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                    ->where('device_tokens.device_id', $deviceID)
                    ->where('device_tokens.api_token', $apiToken)
                    ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                    ->first();

                if ($userDetails) {
                    $userID = $userDetails->id;
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
                }
            }

            $cartdata = Cart::join('products', 'carts.product_id', 'products.id')
                ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                ->select('carts.*', 'products.product_name', 'products.tax_ids', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS Images'), 'products.prescription', DB::raw('ROUND((CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END),2) as ProductPrice'));
            if ($userID == 0) {
                $cartdata->where('carts.ip', $deviceID)->where('carts.user_id', $userID);
            } else {
                $cartdata->where('carts.user_id', $userID);
            }
            $cartdata = $cartdata->get();

            if ($cartdata) {
                $cart_ids = array();
                foreach ($cartdata as $key => $value) {
                    array_push($cart_ids, $value->id);
                    if ($value->tax_ids != null && isset($value->tax_ids)) {
                        $tax_ids = explode(',', $value->tax_ids);
                        $cartdata[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                    }
                }
                $shipping_charge = null;
                if (count($cartdata) > 0) {
                    $shipping_charge_149 = Generalsetting::where('item', '=', 'shipping_charge_149')->first();
                    $shipping_charge_499 = Generalsetting::where('item', '=', 'shipping_charge_499')->first();
                    if (!empty($cart_ids)) {
                        $ProductPrice = Cart::select('products.id', 'products.tax_ids', 'carts.quantity', DB::raw('ROUND((CASE WHEN products.offer_price!=0 THEN products.offer_price ELSE products.price END) * carts.quantity,2) AS ProductPrice'))
                            ->join('products', 'products.id', 'carts.product_id')
                            ->whereIn('carts.id', $cart_ids)->get()->toArray();

                        if ($ProductPrice) {
                            $products_data = array();
                            $totalAmount = $grandTotal = 0;
                            $total_taxRate = $total_tax = 0;
                            foreach ($ProductPrice as $ProductPrice_row) {

                                $cart_products[] = $ProductPrice_row['id'];

                                if ($ProductPrice_row['tax_ids'] != '') {
                                    $tax_ids = explode(',', $ProductPrice_row['tax_ids']);
                                    $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                    foreach ($TaxDetails as $value) {
                                        $total_taxRate += ($ProductPrice_row['ProductPrice'] * $value->percentage) / 100;

                                    }

                                }
                                $totalAmount += $ProductPrice_row['ProductPrice'] - $total_taxRate;
                                $total_tax = $total_tax + $total_taxRate;

                                $grandTotal += $ProductPrice_row['ProductPrice'];
                                $total_taxRate = 0;
                            }

                            $pre_grand_total = $grandTotal;
                            // dd($pre_grand_total);

                            if ($pre_grand_total < 149) {
                                $shipping_charge = $shipping_charge_149->value;

                            } elseif (($pre_grand_total > 149) && ($pre_grand_total < 499)) {
                                $shipping_charge = $shipping_charge_499->value;

                            } else {
                                $shipping_charge = 0;
                            }
                        }
                    }
                }
            }

            return response()->json(['result' => true, 'message' => 'successfully', 'cartproducts' => $cartdata, 'shipping_charge' => $shipping_charge]);

        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function product_addcart(Request $request)
    {
        if ($request->header('device-id') != '') {
            $img_path = '/assets/uploads/products/';
            $deviceID = $request->header('device-id');
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:1|gt:0',
            ]);
            if ($validator->fails()) {
                return ['result' => false, "errorMsg" => $validator->errors()->first()];
            } else {
                $userID = 0;
                $Product = Product::find($request->product_id);
                if ($Product) {
                    if ($Product->flag == '1') {
                        return response()->json(['result' => false, 'meesage' => 'Selected product Sold-out']);
                    }
                    if ($Product->not_for_sale == '1') {
                        return response()->json(['result' => false, 'meesage' => 'Selected Product not for online sale']);
                    }

                    if ($request->header('api-token') != '') {
                        ///---------User cart--
                        $apiToken = $request->header('api-token');
                        // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
                        $userDetails = User::select('users.*')
                            ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                            ->where('device_tokens.device_id', $deviceID)
                            ->where('device_tokens.api_token', $apiToken)
                            ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                            ->first();

                        if ($userDetails) {
                            $userID = $userDetails->id;

                            $carts = Cart::Where('product_id', $request->product_id)->where('user_id', $userID)->exists();
                            if (!$carts) {
                                $cart = Cart::create([
                                    'user_id' => $userID,
                                    'product_id' => $request->product_id,
                                    'quantity' => $request->quantity,
                                    'ip' => $deviceID,
                                ]);
                            } else {
                                $cart = Cart::where('user_id', $userID)
                                    ->where('product_id', $request->product_id)
                                    ->increment('quantity', $request->quantity);
                            }

                            $prescription = Prescription::where('user_id', $userID)->where('product_id', $request->product_id)
                                ->where('order_id', '=', null)->first();
                            $prescription_id = (($prescription) ? $prescription->id : '');

                            $cartdata = Cart::join('products', 'carts.product_id', 'products.id')
                                ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                                ->select('carts.*', 'products.product_name', 'products.tax_ids', 'products.prescription', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS Images'), DB::raw('ROUND((CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END),2) as ProductPrice'))
                                ->where('carts.user_id', $userID)
                                ->get();
                            $cart_ids = array();

                            if (count($cartdata) > 0) {
                                foreach ($cartdata as $key => $value) {
                                    array_push($cart_ids, $value->id);

                                    if ($value->tax_ids != null && isset($value->tax_ids)) {
                                        $tax_ids = explode(',', $value->tax_ids);
                                        $cartdata[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                                    }
                                }
                                $shipping_charge = null;

                                $shipping_charge_149 = Generalsetting::where('item', '=', 'shipping_charge_149')->first();
                                $shipping_charge_499 = Generalsetting::where('item', '=', 'shipping_charge_499')->first();
                                if (!empty($cart_ids)) {
                                    $ProductPrice = Cart::select('products.id', 'products.tax_ids', 'carts.quantity', DB::raw('ROUND((CASE WHEN products.offer_price!=0 THEN products.offer_price ELSE products.price END) * carts.quantity,2) AS ProductPrice'))
                                        ->join('products', 'products.id', 'carts.product_id')
                                        ->whereIn('carts.id', $cart_ids)->get()->toArray();

                                    if ($ProductPrice) {
                                        $products_data = array();
                                        $totalAmount = $grandTotal = 0;
                                        $total_taxRate = $total_tax = 0;
                                        foreach ($ProductPrice as $ProductPrice_row) {

                                            $cart_products[] = $ProductPrice_row['id'];

                                            if ($ProductPrice_row['tax_ids'] != '') {
                                                $tax_ids = explode(',', $ProductPrice_row['tax_ids']);
                                                $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                                foreach ($TaxDetails as $value) {
                                                    $total_taxRate += ($ProductPrice_row['ProductPrice'] * $value->percentage) / 100;

                                                }

                                            }
                                            $totalAmount += $ProductPrice_row['ProductPrice'] - $total_taxRate;
                                            $total_tax = $total_tax + $total_taxRate;

                                            $grandTotal += $ProductPrice_row['ProductPrice'];
                                            $total_taxRate = 0;
                                        }

                                        $pre_grand_total = $grandTotal;
                                        // dd($pre_grand_total);

                                        if ($pre_grand_total < 149) {
                                            $shipping_charge = $shipping_charge_149->value;

                                        } elseif (($pre_grand_total > 149) && ($pre_grand_total < 499)) {
                                            $shipping_charge = $shipping_charge_499->value;

                                        } else {
                                            $shipping_charge = 0;
                                        }
                                    }
                                }
                            }

                            $returnArray = array('result' => true, 'message' => 'Product added cart.', 'cartproducts' => $cartdata, 'shipping_charge' => $shipping_charge, 'prescription_id' => $prescription_id);
                        } else {
                            $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
                        }
                    } else {

                        ///---------Guest cart--
                        $carts = Cart::Where('product_id', $request->product_id)->where('user_id', $userID)->where('ip', $deviceID)->exists();
                        if (!$carts) {
                            $cart = Cart::create([
                                'user_id' => $userID,
                                'product_id' => $request->product_id,
                                'quantity' => $request->quantity,
                                'ip' => $deviceID,
                            ]);
                        } else {
                            $cart = Cart::where('user_id', $userID)
                                ->where('ip', $deviceID)
                                ->where('product_id', $request->product_id)
                                ->increment('quantity', $request->quantity);
                        }

                        $cartdata = Cart::join('products', 'carts.product_id', 'products.id')
                            ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                            ->select('carts.*', 'products.product_name', 'products.tax_ids', 'products.prescription', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS Images'), DB::raw('ROUND((CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END),2) as ProductPrice'))
                            ->where('carts.ip', $deviceID)
                            ->where('carts.user_id', $userID)
                            ->get();
                        $cart_ids = array();

                        if (count($cartdata) > 0) {
                            foreach ($cartdata as $key => $value) {
                                array_push($cart_ids, $value->id);

                                if ($value->tax_ids != null && isset($value->tax_ids)) {
                                    $tax_ids = explode(',', $value->tax_ids);
                                    $cartdata[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                                }
                            }

                            $shipping_charge = null;

                            $shipping_charge_149 = Generalsetting::where('item', '=', 'shipping_charge_149')->first();
                            $shipping_charge_499 = Generalsetting::where('item', '=', 'shipping_charge_499')->first();
                            if (!empty($cart_ids)) {
                                $ProductPrice = Cart::select('products.id', 'products.tax_ids', 'carts.quantity', DB::raw('ROUND((CASE WHEN products.offer_price!=0 THEN products.offer_price ELSE products.price END) * carts.quantity,2) AS ProductPrice'))
                                    ->join('products', 'products.id', 'carts.product_id')
                                    ->whereIn('carts.id', $cart_ids)->get()->toArray();

                                if ($ProductPrice) {
                                    $products_data = array();
                                    $totalAmount = $grandTotal = 0;
                                    $total_taxRate = $total_tax = 0;
                                    foreach ($ProductPrice as $ProductPrice_row) {

                                        $cart_products[] = $ProductPrice_row['id'];

                                        if ($ProductPrice_row['tax_ids'] != '') {
                                            $tax_ids = explode(',', $ProductPrice_row['tax_ids']);
                                            $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                            foreach ($TaxDetails as $value) {
                                                $total_taxRate += ($ProductPrice_row['ProductPrice'] * $value->percentage) / 100;

                                            }

                                        }
                                        $totalAmount += $ProductPrice_row['ProductPrice'] - $total_taxRate;
                                        $total_tax = $total_tax + $total_taxRate;

                                        $grandTotal += $ProductPrice_row['ProductPrice'];
                                        $total_taxRate = 0;
                                    }

                                    $pre_grand_total = $grandTotal;
                                    // dd($pre_grand_total);

                                    if ($pre_grand_total < 149) {
                                        $shipping_charge = $shipping_charge_149->value;

                                    } elseif (($pre_grand_total > 149) && ($pre_grand_total < 499)) {
                                        $shipping_charge = $shipping_charge_499->value;

                                    } else {
                                        $shipping_charge = 0;
                                    }
                                }
                            }

                        }

                        $returnArray = array('result' => true, 'message' => 'Product added cart.', 'cartproducts' => $cartdata, 'shipping_charge' => $shipping_charge, 'prescription_id' => '');
                    }

                } else {
                    $returnArray = array('result' => false, 'message' => 'Attempt failed: Requested product not found.');
                }
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    public function product_deletecart(Request $request)
    {
        if ($request->header('device-id') != '') {
            $img_path = '/assets/uploads/products/';
            $deviceID = $request->header('device-id');
            $validator = Validator::make($request->all(), [
                'product_id' => 'required_if:type,single,product|integer',
            ]);
            if ($validator->fails()) {
                return ['result' => false, "errorMsg" => $validator->errors()->first()];
            } else {
                $userID = 0;

                if ($request->header('api-token') != '') {
                    ///---------User cart--
                    $apiToken = $request->header('api-token');
                    // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
                    $userDetails = User::select('users.*')
                        ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                        ->where('device_tokens.device_id', $deviceID)
                        ->where('device_tokens.api_token', $apiToken)
                        ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                        ->first();
                    if ($userDetails) {
                        $userID = $userDetails->id;
                    } else {
                        return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
                    }
                }

                $message = '';
                if ($request->type != '' && ($request->type == 'single' || $request->type == 'product' || $request->type == 'all')) {
                    switch ($request->type) {
                        case 'single':
                            $Product = Product::find($request->product_id);

                            if ($Product) {
                                $existCart = Cart::where('product_id', $request->product_id);
                                if ($userID == 0) {
                                    $existCart->where('carts.ip', $deviceID)->where('carts.user_id', $userID);
                                } else {
                                    $existCart->where('carts.user_id', $userID);
                                }
                                $existCart = $existCart->first();

                                if ($existCart) {

                                    if ($existCart->quantity <= 1) {
                                        return response()->json(['result' => false, 'message' => 'Attempt failed: You are reached minimum quantity.']);
                                    }

                                    $cartupdation = Cart::where('product_id', $request->product_id);
                                    if ($userID == 0) {
                                        $cartupdation->where('carts.ip', $deviceID)->where('carts.user_id', $userID);
                                    } else {
                                        $cartupdation->where('carts.user_id', $userID);
                                    }
                                    $cartupdation->decrement('quantity', 1);

                                    $message = 'Updated Successfully.';
                                } else {
                                    return response()->json(['result' => false, 'message' => 'Attempt failed: Requested product not found in cart.']);
                                }
                            } else {
                                return response()->json(['result' => false, 'message' => 'Attempt failed: Requested product not found.']);
                            }
                            break;
                        case 'product':
                            $Product = Product::find($request->product_id);

                            if ($Product) {
                                $existCart = Cart::where('product_id', $request->product_id);
                                if ($userID == 0) {
                                    $existCart->where('carts.ip', $deviceID)->where('carts.user_id', $userID);
                                } else {
                                    $existCart->where('carts.user_id', $userID);
                                }
                                $existCart = $existCart->exists();

                                if ($existCart) {
                                    if ($userID == 0) {
                                        Cart::where('user_id', $userID)->where('ip', $deviceID)->where('product_id', $request->product_id)->delete();
                                    } else {
                                        Cart::where('user_id', $userID)->where('product_id', $request->product_id)->delete();
                                    }
                                    $message = 'The Product ' . $Product->product_name . ' removed from cart.';
                                } else {
                                    return response()->json(['result' => false, 'message' => 'Attempt failed: Requested product not found in cart.']);
                                }
                            } else {
                                return response()->json(['result' => false, 'message' => 'Attempt failed: Requested product not found.']);
                            }
                            break;
                        case 'all':
                            if ($userID == 0) {
                                Cart::where('user_id', $userID)->where('ip', $deviceID)->delete();
                            } else {
                                Cart::where('user_id', $userID)->delete();
                            }
                            $message = 'Your cart empty.';
                            break;
                    }

                    $cartdata = Cart::join('products', 'carts.product_id', 'products.id')
                        ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                        ->select('carts.*', 'products.product_name','products.prescription','products.tax_ids', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS Images'), DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'));
                    if ($userID == 0) {
                        $cartdata->where('carts.ip', $deviceID)->where('carts.user_id', $userID);
                    } else {
                        $cartdata->where('carts.user_id', $userID);
                    }
                    $cartdata = $cartdata->get();
                    $cart_ids = array();
                    if ($cartdata) {
                        foreach ($cartdata as $key => $value) {
                            array_push($cart_ids, $value->id);

                            if ($value->tax_ids != null && isset($value->tax_ids)) {
                                $tax_ids = explode(',', $value->tax_ids);
                                $cartdata[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                            }
                        }

                        $shipping_charge = null;

                        $shipping_charge_149 = Generalsetting::where('item', '=', 'shipping_charge_149')->first();
                        $shipping_charge_499 = Generalsetting::where('item', '=', 'shipping_charge_499')->first();
                        if (!empty($cart_ids)) {
                            $ProductPrice = Cart::select('products.id', 'products.tax_ids', 'carts.quantity', DB::raw('ROUND((CASE WHEN products.offer_price!=0 THEN products.offer_price ELSE products.price END) * carts.quantity,2) AS ProductPrice'))
                                ->join('products', 'products.id', 'carts.product_id')
                                ->whereIn('carts.id', $cart_ids)->get()->toArray();

                            if ($ProductPrice) {
                                $products_data = array();
                                $totalAmount = $grandTotal = 0;
                                $total_taxRate = $total_tax = 0;
                                foreach ($ProductPrice as $ProductPrice_row) {

                                    $cart_products[] = $ProductPrice_row['id'];

                                    if ($ProductPrice_row['tax_ids'] != '') {
                                        $tax_ids = explode(',', $ProductPrice_row['tax_ids']);
                                        $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                        foreach ($TaxDetails as $value) {
                                            $total_taxRate += ($ProductPrice_row['ProductPrice'] * $value->percentage) / 100;

                                        }

                                    }
                                    $totalAmount += $ProductPrice_row['ProductPrice'] - $total_taxRate;
                                    $total_tax = $total_tax + $total_taxRate;

                                    $grandTotal += $ProductPrice_row['ProductPrice'];
                                    $total_taxRate = 0;
                                }

                                $pre_grand_total = $grandTotal;
                                // dd($pre_grand_total);

                                if ($pre_grand_total < 149) {
                                    $shipping_charge = $shipping_charge_149->value;

                                } elseif (($pre_grand_total > 149) && ($pre_grand_total < 499)) {
                                    $shipping_charge = $shipping_charge_499->value;

                                } else {
                                    $shipping_charge = 0;
                                }
                            }
                        }
                    }

                    return response()->json(['result' => true, 'message' => $message, 'cartproducts' => $cartdata, 'shipping_charge' => $shipping_charge]);
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: Something went wrong. Request type not found.']);
                }
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    //----------------Front view/Get/UserAddress--
    public function get_useraddress(Request $request)
    {
        if ($request->header('device-id') != '') {
            $deviceID = $request->header('device-id');
            $userID = 0;
            if ($request->header('api-token') != '') {
                ///---------User cart--
                $apiToken = $request->header('api-token');
                // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();
                $userDetails = User::select('users.*')
                    ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                    ->where('device_tokens.device_id', $deviceID)
                    ->where('device_tokens.api_token', $apiToken)
                    ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                    ->first();
                if ($userDetails) {
                    $userID = $userDetails->id;
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
                }
            }

            $user_address = UserAddress::select('user_addresses.*', 'states.name AS state', 'countries.name AS country')
                ->leftjoin('countries', 'user_addresses.country_id', 'countries.id')
                ->leftjoin('states', 'user_addresses.state_id', 'states.id');
            if ($userID == 0) {
                $user_address->where('device_id', $deviceID)->where('user_id', $userID);
            } else {
                $user_address->where('user_id', $userID);
            }
            $user_address = $user_address->get();

            $primaryAddress = $homeAddress = $workAddress = (object) [];

            if ($user_address->isNotEmpty()) {
                foreach ($user_address as $user_address_row) {
                    if ($user_address_row->type == 'primary') {
                        $primaryAddress = $user_address_row;
                    } elseif ($user_address_row->type == 'home') {
                        $homeAddress = $user_address_row;
                    } elseif ($user_address_row->type == 'work') {
                        $workAddress = $user_address_row;
                    }
                }
            }
            return response()->json(['result' => true, 'message' => 'Successfully', 'primaryAddress' => $primaryAddress, 'homeAddress' => $homeAddress, 'workAddress' => $workAddress]);
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function get_countries(Request $request)
    {
        if ($request->header('device-id') != '') {
            $flag_path = '/assets/uploads/countries_flag/';
            $countries = Country::select('countries.id as country_id', 'countries.name as country_name', 'countries.phonecode', DB::raw('CONCAT("' . $flag_path . '", countries.flag_icon) AS countryflag'))->get();
            if ($countries) {
                return response()->json(['result' => true, 'message' => 'Successfully', 'countries' => $countries]);
            } else {
                return response()->json(['result' => false, 'message' => 'Sorry.. Cannot find countries list.']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function get_states(Request $request)
    {
        if ($request->header('device-id') != '') {
            $country = Country::find($request->country_id);
            if ($country) {
                $states = State::where('country_id', $country->id)->get();
                if ($states) {
                    return response()->json(['result' => true, 'message' => 'Successfully', 'states' => $states]);
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: States not found.']);
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: Requested County not found.']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function checkout_UpdateAddress(Request $request)
    {
        if ($request->header('device-id') != '') {
            $deviceID = $request->header('device-id');

            $userID = 0;
            $userEmail = ($request->email != '' ? $request->email : '');
            if ($request->header('api-token') != '') {
                ///---------User cart--
                $apiToken = $request->header('api-token');
                // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
                $userDetails = User::select('users.*')
                    ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                    ->where('device_tokens.device_id', $deviceID)
                    ->where('device_tokens.api_token', $apiToken)
                    ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                    ->first();
                if ($userDetails) {
                    $userID = $userDetails->id;
                    $userEmail = $userDetails->email;
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
                }
            }

            if ($userID == 0) {
                $validator = Validator::make($request->all(), [
                    'address_type' => 'required',
                    'name' => 'required',
                    'phone' => 'required|regex:/[0-9]{9}/',
                    'address' => 'required',
                    'location' => 'required',
                    'city' => 'required',
                    'pin' => 'required',
                    'country_id' => 'required|numeric',
                    'state_id' => 'required|numeric',
                    'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'address_type' => 'required',
                    'name' => 'required',
                    'phone' => 'required|regex:/[0-9]{9}/',
                    'address' => 'required',
                    'location' => 'required',
                    'city' => 'required',
                    'pin' => 'required',
                    'country_id' => 'required|numeric',
                    'state_id' => 'required|numeric',
                ]);
            }

            if ($validator->fails()) {
                return ['success' => false, "message" => $validator->errors()->first()];
            } else {
                $address_exist = UserAddress::where('type', $request->address_type);
                if ($userID == 0) {
                    $address_exist->where('user_id', $userID)->where('device_id', $deviceID);
                } else {
                    $address_exist->where('user_id', $userID);
                }
                $address_exist = $address_exist->first();

                $addressID = null;
                if ($address_exist) {
                    $addressID = $address_exist->id;
                    UserAddress::find($addressID)->update([
                        'email' => $userEmail,
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'pin' => $request->pin,
                        'location' => $request->location,
                        'address' => $request->address,
                        'city' => $request->city,
                        'state_id' => $request->state_id,
                        'country_id' => $request->country_id,
                        'landmark' => $request->landmark,
                    ]);
                } else {
                    $addressID = UserAddress::create([
                        'user_id' => $userID,
                        'device_id' => $deviceID,
                        'type' => $request->address_type,
                        'email' => $userEmail,
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'pin' => $request->pin,
                        'location' => $request->location,
                        'address' => $request->address,
                        'city' => $request->city,
                        'state_id' => $request->state_id,
                        'country_id' => $request->country_id,
                        'landmark' => $request->landmark,
                    ])->id;
                }

                if ($addressID != null) {
                    $userAddress = UserAddress::select('user_addresses.*', 'states.name AS state', 'countries.name AS country')
                        ->leftjoin('countries', 'user_addresses.country_id', 'countries.id')
                        ->leftjoin('states', 'user_addresses.state_id', 'states.id')
                        ->where('user_addresses.id', $addressID)
                        ->first();

                    if ($userAddress) {
                        return response()->json(['result' => true, 'message' => 'Successfully', 'userAddress' => $userAddress]);
                    } else {
                        return response()->json(['result' => false, 'message' => 'Attempt failed: Address not found.']);
                    }
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: Your address not updated.']);
                }
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function placeorder(Request $request)
    {
        if ($request->header('device-id') != '') {
            $deviceID = $request->header('device-id');

            $userID = 0;
            if ($request->header('api-token') != '') {
                ///---------User cart--
                $apiToken = $request->header('api-token');

                // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
                $userDetails = User::select('users.*')
                    ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                    ->where('device_tokens.device_id', $deviceID)
                    ->where('device_tokens.api_token', $apiToken)
                    ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                    ->first();

                if ($userDetails) {
                    $userID = $userDetails->id;
                    $userEmail = $userDetails->email;
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
                }
            }

            $validator = Validator::make($request->all(), [
                'address_id' => 'required|not_in:0',
                'checkout_type' => 'required|in:buynow,cart',
                'payment_method' => 'required|in:cod,online',
                'id' => 'required|not_in:0',
                'pickup_store' => 'required|in:yes,no',
                'store_id' => 'required_if:pickup_store,yes',
                'prescription_file.*' => 'nullable|mimes:jpeg,jpg,png,pdf,doc',
                'comment' => 'nullable',

            ]);
            // if ($request->payment_method == 'online') {
            //     $validator2 = Validator::make($request->all(), [
            //         'payment_gateway' => 'required',
            //         'transaction_id' => 'required',
            //         'online_payment_method' => 'required',
            //         'currency_code' => 'required',
            //         'amount' => 'required',
            //         'transaction_date' => 'required',
            //         'transaction_status' => 'required',
            //     ]);

            // }

            if ($validator->fails()) {
                $message = '';
                foreach ($validator->errors()->toArray() as $errors) {
                    $message .= $errors[0] . '<br />';
                }

                return response()->json(['result' => false, 'message' => $message]);
            }
            // elseif (($request->payment_method == 'online') && $validator2->fails()) {
            //     $message = '';
            //     foreach ($validator2->errors()->toArray() as $errors) {
            //         $message .= $errors[0] . '<br />';
            //     }

            //     return response()->json(['result' => false, 'message' => $message]);

            // }
            else {
                $prescription_ids = '';
                if (!empty($request->prescription_ids)) {
                    $prescription_ids = implode(',', $request->prescription_ids);
                }

                if ($prescription_ids == '' && (!empty($request->file('prescription_file')) && (is_array($request->file('prescription_file'))))) {

                    if ($userID != 0) {
                        if ($request->checkout_type == 'cart') {
                            $cart_ids = explode(',', $request->id);
                            if (!Cart::whereIn('carts.id', $cart_ids)->exists()) {
                                return response()->json(['result' => false, 'message' => 'Cart products not found. Something went wrong with requested products.']);

                            }

                            foreach ($request->file('prescription_file') as $key => $file) {

                                $fileName = 'presc_' . time() . '.' . $file->extension();
                                $file->move(public_path('/assets/uploads/prescription/'), $fileName);
                                $prescription = new Prescription();
                                $prescription->user_id = $userID;
                                $prescription->type = 'bulk';
                                $prescription->file = $fileName;
                                // $prescription->order_id = $order->id;
                                $prescription->status = 1;
                                $prescription->comment = $request->comment;
                                $prescription->save();
                                $bulk_prescription_arr[] = $prescription->id;

                            }

                            $prescription_ids = implode(',', $bulk_prescription_arr);

                        } else {
                            return response()->json(['result' => false, 'message' => 'The Bulk Prescription file is upload only if checkout type is cart']);

                        }
                    } else {
                        return response()->json(['result' => false, 'message' => 'Please login for prescription upload']);

                    }

                }
                if ($prescription_ids == '') {
                    if ($request->checkout_type == 'cart') {

                        $cart_ids = explode(',', $request->id);
                        if (!Cart::whereIn('carts.id', $cart_ids)->exists()) {
                            return response()->json(['result' => false, 'message' => 'Cart products not found. Something went wrong with requested products.']);

                        }
                        $cart_productids=Cart::whereIn('carts.id', $cart_ids)->get()->pluck('product_id');

                        if(Product::whereIn('id',$cart_productids)->where('prescription',1)->exists()){
                            return response()->json(['result' => false, 'message' => 'Prescription required for this product .']);
                            }

                    } else {
                        if(Product::where('id',$request->id)->where('prescription',1)->exists()){
                        return response()->json(['result' => false, 'message' => 'Prescription required for this product .']);
                        }
                    }
                }
                // else {

                //     return response()->json(['result' => false, 'message' => 'The Bulk Prescription file is upload when checkout type is cart']);

                // }

                $quantity = ($request->quantity != '' && $request->quantity != 0) ? $request->quantity : 1;

                $OrderPlace = $this->set_order($deviceID, $userID, $request->address_id, $request->checkout_type, $request->id, $quantity, $prescription_ids, $request->payment_method, $request->pickup_store, $request->store_id);
                // if ($request->payment_method == 'online') {
                //     $payment_store = PaymentDetail::create([
                //         'order_id' => $OrderPlace['order']['id'],
                //         'payment_gateway' => $request->payment_gateway,
                //         'transaction_id' => $request->transaction_id,
                //         'payment_method' => $request->online_payment_method,
                //         "currency_code" => $request->currency_code,
                //         "amount" => $request->amount,
                //         "transaction_date" => $request->transaction_date,
                //         "transaction_status" => $request->transaction_status,
                //     ]);
                // }
                if ($OrderPlace['result']) {
                    $order_id = $OrderPlace['order']['id'];
                    if ($request->payment_method == 'cod') {
                        $invoice_number = $this->invoiceNumber();
                        Invoice::create([
                            'user_id' => $userID,
                            'order_id' => $order_id,
                            'invoice_number' => $invoice_number,
                        ]);
                        $this->customer_invoice_mail($order_id, $invoice_number);
                        $this->Admin_orderConfirmation_mail($order_id, $invoice_number);
                    }
                }

                return response()->json($OrderPlace);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    private function set_order($deviceID, $userID, $addressID = null, $checkType = null, $itemID = '', $quantity = null, $prescription_ids = null, $payment_mathod = null, $pickup_store = null, $store_id = null)
    {
        if ($addressID) {
            $img_path = '/assets/uploads/products/';
            $address_exist = UserAddress::where('id', $addressID)->first();
            $shipping_charge_149 = Generalsetting::where('item', '=', 'shipping_charge_149')->first();
            $shipping_charge_499 = Generalsetting::where('item', '=', 'shipping_charge_499')->first();

            if ($address_exist) {

                if ($checkType == 'cart') {
                    $cart_ids = explode(',', $itemID);

                    if (!empty($cart_ids)) {
                        $ProductPrice = Cart::select('products.id', 'products.tax_ids', 'carts.quantity',DB::raw('(CASE WHEN products.offer_price!=0 THEN products.offer_price ELSE products.price END) AS Price'), DB::raw('(CASE WHEN products.offer_price!=0 THEN products.offer_price ELSE products.price END) * carts.quantity AS ProductPrice'))
                            ->join('products', 'products.id', 'carts.product_id')
                            ->whereIn('carts.id', $cart_ids)->get()->toArray();

                        if ($ProductPrice) {
                            $products_data = array();
                            $totalAmount = $grandTotal =$total_amount=0;
                            $total_taxRate = $total_tax =$total_tax_percent_value=$totalvalue=$total_tax_percent= 0;
                            foreach ($ProductPrice as $ProductPrice_row) {

                                $cart_products[] = $ProductPrice_row['id'];

                                if ($ProductPrice_row['tax_ids'] != '') {
                                    $tax_ids = explode(',', $ProductPrice_row['tax_ids']);
                                    $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                    foreach ($TaxDetails as $value) {
                                        $total_tax_percent = $total_tax_percent + $value->percentage;

                                        // $total_taxRate += ($ProductPrice_row['ProductPrice'] * $value->percentage) / 100;

                                    }
                                    $total_tax_percent_value = ($ProductPrice_row['Price'] * 100) / ($total_tax_percent + 100);
                                    $totalvalue = $totalvalue + ($ProductPrice_row['Price'] - $total_tax_percent_value) * $ProductPrice_row['quantity'];
                                }
                                $total_amount += $ProductPrice_row['quantity'] * $ProductPrice_row['Price'] - $totalvalue;
                                $total_tax = $totalvalue;


                                $totalAmount += $ProductPrice_row['ProductPrice'] - $total_taxRate;
                                // $total_tax = $total_tax + $total_taxRate;

                                $grandTotal += $ProductPrice_row['ProductPrice'];
                                $total_taxRate = 0;
                            }

                            $pre_grand_total = $grandTotal;
                            // dd($pre_grand_total);

                            if ($pre_grand_total < 149) {
                                $shipping_charge = $shipping_charge_149->value;

                            } elseif (($pre_grand_total > 149) && ($pre_grand_total < 499)) {
                                $shipping_charge = $shipping_charge_499->value;

                            } else {
                                $shipping_charge = 0;
                            }
                            if ($pickup_store == "yes") {
                                $shipping_charge = 0;
                            }
                            $grandTotal = $grandTotal + $shipping_charge;

                            $order = new Order();
                            $order->user_id = $userID;
                            $order->device_id = $deviceID;
                            $order->address_id = $address_exist->id;
                            $order->status = ($payment_mathod == 'cod') ? 'ordered' : 'initiated';
                            $order->payment_method = $payment_mathod;
                            $order->total_amount = $total_amount;
                            $order->total_tax_amount = $total_tax;
                            $order->shipping_charge = $shipping_charge;
                            $order->grand_total = $grandTotal;
                            $order->prescription_ids = $prescription_ids;
                            $order->delivery_type = ($store_id != '' ? 'pickup' : 'direct');
                            $order->store_id = $store_id;
                            $order->date = date('Y-m-d H:i:s');

                            $order->save();

                            if ($order->id) {
                                $order_Master = Order::join('user_addresses', 'orders.address_id', 'user_addresses.id')
                                    ->join('countries', 'user_addresses.country_id', 'countries.id')
                                    ->join('states', 'user_addresses.state_id', 'states.id')
                                    ->leftjoin('stores', 'stores.id', 'orders.store_id')
                                    ->where('orders.id', $order->id)
                                    ->select('orders.*', 'user_addresses.name', 'user_addresses.address', 'user_addresses.phone', 'user_addresses.pin', 'user_addresses.location', 'user_addresses.city',
                                        'user_addresses.landmark', 'user_addresses.email', 'states.name as state_name', 'countries.name as country_name', 'stores.name as store_name', 'stores.location as store_location', 'stores.address as store_address', 'stores.contact_number as store_contact_number', 'stores.map_location_code as store_location_map')
                                    ->first();
                                $order->order_date = $order_Master->date;
                                $order->name = $order_Master->name;
                                $order->address = $order_Master->address;
                                $order->phone = $order_Master->phone;
                                $order->pin = $order_Master->pin;
                                $order->city = $order_Master->city;
                                $order->location = $order_Master->location;
                                $order->landmark = $order_Master->landmark;
                                $order->state_name = $order_Master->state_name;
                                $order->country_name = $order_Master->country_name;
                                $order->email = $order_Master->email;
                                $order->store_name = $order_Master->store_name;
                                $order->store_location = $order_Master->store_location;
                                $order->store_address = $order_Master->store_address;
                                $order->store_contact_number = $order_Master->store_contact_number;
                                $order->store_location_map = $order_Master->store_location_map;

                                foreach ($cart_ids as $key => $cartid) {
                                    $total_taxRate = $total_tax_percent = $total_tax = $total_tax_percent = $totalvalue = $total_tax_percent_value = 0;

                                    $cartproduct = Cart::where('id', $cartid)->first();

                                    if ($cartproduct) {
                                        $order_details = new OrderDetails();
                                        $order_details->order_id = $order->id;
                                        $order_details->product_id = $cartproduct->product_id;
                                        $order_details->quantity = $cartproduct->quantity;

                                        $Productdetails = Product::leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                                            ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
                                            ->where('products.id', $cartproduct->product_id)
                                            ->select(DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), 'products.id as productid', 'products.product_name', 'products.tax_ids', 'GS.value AS email', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS product_image'))
                                            ->first();

                                        $order_details->tax_ids = $Productdetails->tax_ids;

                                        $total_taxRate = 0;
                                        if ($Productdetails->tax_ids != '') {
                                            $tax_ids = explode(',', $Productdetails->tax_ids);
                                            $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                            foreach ($TaxDetails as $value) {
                                                $total_tax_percent = $total_tax_percent + $value->percentage;

                                                // $total_taxRate += ($Productdetails->ProductPrice * $value->percentage) / 100;
                                            }
                                            $total_tax_percent_value = ($Productdetails->ProductPrice * 100) / ($total_tax_percent + 100);
                                            $totalvalue = $totalvalue + ($Productdetails->ProductPrice - $total_tax_percent_value) * $cartproduct->quantity;

                                        }else{
                                            $total_tax_percent_value=$Productdetails->ProductPrice;
                                        }
                                        $total_tax = $total_tax + $totalvalue;
                                        // $order_details->total_tax = ($total_taxRate * $cartproduct->quantity);
                                        // $order_details->price = ($Productdetails->ProductPrice - $order_details->total_tax);
                                        $order_details->total_tax = $total_tax;
                                        $order_details->price=$total_tax_percent_value;
                                        $order_details->amount = ($cartproduct->quantity * $Productdetails->ProductPrice);

                                        $order_details->save();
                                        $Productdetails['quantity'] = $cartproduct->quantity;
                                        $Productdetails['totaltax'] = $order_details->total_tax;
                                        $Productdetails['totalamount'] = $order_details->amount;

                                        $products_data[] = $Productdetails;
                                        if ($prescription_ids != '') {
                                            $prescriptionUpdate = Prescription::whereIn('id', explode(',', $prescription_ids))->update([
                                                'order_id' => $order->id,
                                            ]);
                                        }
                                        if ($payment_mathod == "cod") {
                                            // Cart::where('id', $cartid)->delete();
                                        }
                                    }

                                }

                                return array('result' => true, 'message' => 'Order placed successfully.', 'order' => $order, 'productdetails' => $products_data);
                            }
                        } else {
                            return array('result' => false, 'message' => 'Cart products not found. Something went wrong with requested products.');
                        }
                    } else {
                        return array('result' => false, 'message' => 'Cart ids not found.');
                    }
                } elseif ($checkType == 'buynow') {

                    if ($itemID != '') {
                        $Productdetails = Product::leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                            ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
                            ->where('products.id', $itemID)
                            ->select('products.id as productid', 'products.product_name', 'products.tax_ids', 'GS.value AS email', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS product_image'), DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) * ' . $quantity . ' as TotProductPrice'))
                            ->first();

                        if ($Productdetails) {
                            $products_data = array();

                            $total_taxRate = $totalAmount = 0;
                            $total_taxRate =$total_tax_percent_value=$total_tax_percent=$totalvalue=$total_tax=$total_amount=0;

                            if ($Productdetails->tax_ids != '') {
                                $tax_ids = explode(',', $Productdetails->tax_ids);
                                $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                foreach ($TaxDetails as $value) {
                                    $total_tax_percent = $total_tax_percent + $value->percentage;

                                    // $total_taxRate += ($Productdetails->TotProductPrice * $value->percentage) / 100;
                                }
                                $total_tax_percent_value = ($Productdetails->ProductPrice * 100) / ($total_tax_percent + 100);
                                $totalvalue = $totalvalue + ($Productdetails->ProductPrice - $total_tax_percent_value) * $quantity;

                                $total_amount += $quantity * $Productdetails->ProductPrice - $totalvalue;

                                $total_tax = $total_tax + $totalvalue;


                            }else{
                                $total_amount=$Productdetails->TotProductPrice;
                            }
                            // $totalAmount = $Productdetails->TotProductPrice - $total_taxRate;
                            $grandTotal = $Productdetails->TotProductPrice;
                            $pre_grand_total = $grandTotal;
                            if ($pre_grand_total < 149) {
                                $shipping_charge = $shipping_charge_149->value;
                            } elseif (($pre_grand_total > 149) && ($pre_grand_total < 499)) {
                                $shipping_charge = $shipping_charge_499->value;
                            } else {
                                $shipping_charge = 0;
                            }
                            if ($pickup_store == "yes") {
                                $shipping_charge = 0;
                            }

                            $grandTotal = $Productdetails->TotProductPrice + $shipping_charge;
                            // dd($grandTotal);
                            $order = new Order();
                            $order->user_id = $userID;
                            $order->device_id = $deviceID;
                            $order->address_id = $address_exist->id;
                            $order->status = ($payment_mathod == 'cod') ? 'ordered' : 'initiated';
                            $order->payment_method = $payment_mathod;
                            $order->total_amount = $total_amount;
                            $order->total_tax_amount = $total_tax;
                            $order->shipping_charge = $shipping_charge;
                            $order->grand_total = $grandTotal;
                            $order->delivery_type = ($store_id != '' ? 'pickup' : 'direct');
                            $order->store_id = $store_id;
                            $order->date = date('Y-m-d H:i:s');

                            $order->save();

                            if ($order->id) {
                                $total_taxRate =$total_tax_percent_value=$total_tax_percent=$totalvalue=$total_tax=0;

                                $order_details = new OrderDetails();
                                $order_details->order_id = $order->id;
                                $order_details->product_id = $Productdetails->productid;
                                $order_details->quantity = $quantity;
                                $order_details->tax_ids = $Productdetails->tax_ids;

                                 if ($Productdetails->tax_ids != '') {
                                    $tax_ids = explode(',', $Productdetails->tax_ids);
                                    $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                    foreach ($TaxDetails as $value) {
                                        $total_tax_percent = $total_tax_percent + $value->percentage;

                                        $total_taxRate += ($Productdetails->ProductPrice * $value->percentage) / 100;
                                    }
                                    $total_tax_percent_value = ($Productdetails->ProductPrice * 100) / ($total_tax_percent + 100);
                                    $totalvalue = $totalvalue + ($Productdetails->ProductPrice - $total_tax_percent_value) * $quantity;

                                }else{
                                    $total_tax_percent_value = $Productdetails->ProductPrice ;

                                }

                                $total_tax = $total_tax + $totalvalue;
                                // dd($total_tax);

                                $order_details->total_tax = $total_tax;
                                $order_details->price = $total_tax_percent_value;
                                $order_details->amount = $Productdetails->TotProductPrice;

                                $order_details->save();
                                $Productdetails['quantity'] = $order_details->quantity;
                                $Productdetails['totaltax'] = $order_details->total_tax;
                                $Productdetails['totalamount'] = $order_details->amount;

                                $order_Master = Order::join('user_addresses', 'orders.address_id', 'user_addresses.id')
                                    ->join('countries', 'user_addresses.country_id', 'countries.id')
                                    ->join('states', 'user_addresses.state_id', 'states.id')
                                    ->leftjoin('stores', 'stores.id', 'orders.store_id')
                                    ->where('orders.id', $order->id)
                                    ->select('orders.*', 'user_addresses.name', 'user_addresses.address', 'user_addresses.phone', 'user_addresses.pin', 'user_addresses.location', 'user_addresses.city',
                                        'user_addresses.landmark', 'user_addresses.email', 'states.name as state_name', 'countries.name as country_name', 'stores.name as store_name', 'stores.location as store_location', 'stores.address as store_address', 'stores.contact_number as store_contact_number', 'stores.map_location_code as store_location_map')
                                    ->first();
                                if ($order_Master) {
                                    $order_details = OrderDetails::join('products', 'products.id', 'order_details.product_id')
                                        ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                                        ->where('order_details.order_id', $order_Master->id)
                                        ->select('order_details.*', 'products.product_name')
                                        ->get();

                                    if ($order_details) {
                                        $orders = new \stdClass();

                                        $order->order_date = $order_Master->date;
                                        $order->name = $order_Master->name;
                                        $order->address = $order_Master->address;
                                        $order->phone = $order_Master->phone;
                                        $order->pin = $order_Master->pin;
                                        $order->city = $order_Master->city;
                                        $order->location = $order_Master->location;
                                        $order->landmark = $order_Master->landmark;
                                        $order->state_name = $order_Master->state_name;
                                        $order->country_name = $order_Master->country_name;
                                        $order->email = $order_Master->email;
                                        $order->store_name = $order_Master->store_name;
                                        $order->store_location = $order_Master->store_location;
                                        $order->store_address = $order_Master->store_address;
                                        $order->store_contact_number = $order_Master->store_contact_number;
                                        $order->store_location_map = $order_Master->store_location_map;
                                    }
                                }
                                if ($prescription_ids != '') {
                                    $prescriptionUpdate = Prescription::whereIn('id', explode(',', $prescription_ids))->update([
                                        'order_id' => $order->id,
                                    ]);
                                }
                                $products_data[] = $Productdetails;

                                return array('result' => true, 'message' => 'Order placed successfully.', 'order' => $order, 'productdetails' => $products_data);
                            }
                        } else {
                            return array('result' => false, 'message' => 'Product details not found.');
                        }
                    } else {
                        return array('result' => false, 'message' => 'Something wrong with your selected product.');
                    }
                }
            } else {
                return array('result' => false, 'message' => 'Address not found.');
            }
        } else {
            return array('result' => false, 'message' => 'Something wrong with address id.');
        }
    }

    public function product_listing(Request $request)
    {
        // $all_brands = Productbrand::select('id as brand_id', 'name as brand_name')->orderBy('name', 'asc')->get();
        // $all_categories = Category::select('id as category_id', 'name as category_name', 'parent_id')->where('status', 'active')->orderBy('name', 'asc')->get();
        // $all_producttypes = Producttype::select('id as producttype_id', 'producttype as producttype_name')->orderBy('type', 'asc')->orderBy('producttype', 'asc')->get();
        // $all_medicineuse = MedicineUse::select('id as medicineuse_id', 'name as medicineuse_name')->orderBy('name', 'asc')->get();

        //Get all child categories id--
        $categoryIds = [];
        if ($request->productcategory != '' && $request->productcategory != 0) {
            $categoryArray = explode(',', $request->productcategory);
            foreach ($categoryArray as $val) {
                array_push($categoryIds, $val);
            }
        }

        $obj_category = new Category();
        $child_category = $obj_category->getCategories($categoryIds);

        $child_categoryIds = [];
        $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);

        if ($request->productcategory != '' && $request->productcategory != 0) {
            $categoryArray = explode(',', $request->productcategory);
            foreach ($categoryArray as $categoryvalue) {
                array_push($child_categoryIds, (int) $categoryvalue);
            }
        }

        $userID = 0;
        if ($request->header('api-token') != '') {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
        }

        $img_path = '/assets/uploads/products/';
        //--Get product listing under choosed category--
        $products = Product::select('products.id as productid', 'products.brands', 'products.manufacturer', 'products.productcontent_id', 'products.flag', 'products.flag', 'products.not_for_sale', 'products.product_name', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) AS producprice'), DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS productimage'));

        if ($userID != 0) {
            $products->addSelect(DB::raw('(CASE WHEN wishlists.id != "" THEN 1 ELSE 0 END) AS wishlist'));
            $products->leftJoin('wishlists', function ($query) use ($userID) {
                $query->on('wishlists.product_id', 'products.id');
                $query->on('wishlists.user_id', '=', DB::raw($userID));
            });
        }

        $products = $products->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
            ->where('products.hide_from_site', '!=', '1')
            ->where('products.status', 'active');

        if ($request->has('productbrand') && $request->productbrand != '') {
            $brandIds = explode(',', $request->productbrand);
            $products->whereIn('products.brands', $brandIds);
        }

        if (!empty($child_categoryIds)) {
            $products->whereIn('products.category_id', $child_categoryIds);
        }

        if ($request->has('producttype') && $request->producttype != '') {
            $typeIds = explode(',', $request->producttype);
            $products->whereIn('products.producttypeid', $typeIds);
        }

        if ($request->has('medicineuse') && $request->medicineuse != '') {
            $medicineuseIds = explode(',', $request->medicineuse);
            $products->whereIn('products.medicine_use', $medicineuseIds);
        }

        if ($request->has('search_keyword') && $request->search_keyword != '') {
            $content_ids = Productcontent::select('productcontents.id')
                ->where('productcontents.name', 'LIKE', '%' . $request->search_keyword . '%')->get();
            $manufacturers_ids = ProductManufacturer::select('product_manufacturers.id')
                ->where('product_manufacturers.name', 'LIKE', '%' . $request->search_keyword . '%')->get();
            $medicine_use_ids = MedicineUse::select('medicine_uses.id', 'product_medicineuses.product_id')
                ->leftjoin('product_medicineuses', 'product_medicineuses.medicine_use', 'medicine_uses.id')
                ->where('medicine_uses.name', 'LIKE', '%' . $request->search_keyword . '%')
                ->get();
            //

            $products->where(function ($query) use ($request, $content_ids, $products) {
                $query->where('products.product_name', 'LIKE', '%' . $request->search_keyword . '%');
                // ->orWhere('medicine_uses.name', 'LIKE', '%' . $request->search_term . '%');

            });
            if (count($content_ids) > 0) {
                foreach ($content_ids as $content_id) {
                    $products->orWhereRaw("find_in_set('" . $content_id->id . "',products.productcontent_id)");
                }
            }
            if (count($manufacturers_ids) > 0) {
                foreach ($manufacturers_ids as $manufacturers_id) {
                    $products->orWhereRaw("find_in_set('" . $manufacturers_id->id . "',products.manufacturer)");
                }
            }
            if (count($medicine_use_ids) > 0) {
                foreach ($medicine_use_ids as $medicine_use_id) {
                    $products->orWhereRaw("find_in_set('" . $medicine_use_id->product_id . "',products.id)");
                }
            }

        }

        if ($request->sort_order != '' && ($request->sort_order == 'ASC' || $request->sort_order == 'DESC') && $request->sort_field != '' && ($request->sort_field == 'product_name' || $request->sort_field == 'producprice')) {
            $products->orderBy($request->sort_field, $request->sort_order);
        }

        $products = $products->get();

        $ReturnArray['result'] = true;
        $ReturnArray['message'] = 'Successfully';
        // $ReturnArray['all_brands'] = $all_brands;
        // $ReturnArray['all_categories'] = $all_categories;
        // $ReturnArray['all_producttypes'] = $all_producttypes;
        // $ReturnArray['all_medicineuse'] = $all_medicineuse;
        $ReturnArray['products'] = $products;

        return response()->json($ReturnArray);
    }

    private function getCategoryIds($child_category, $child_categoryIds)
    {
        foreach ($child_category as $value) {
            array_push($child_categoryIds, $value->id);

            if ($value->child_categories) {
                $child_categoryIds = $this->getCategoryIds($value->child_categories, $child_categoryIds);
            }
        }
        return $child_categoryIds;
    }

    //------User account details--

    public function useraccount(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $img_path = '/assets/uploads/profile/';

            $userDetails = User::select('users.id AS userid', 'users.email', 'users.phone', DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profileimage'))
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)->where('device_tokens.api_token', $apiToken)->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
            if ($userDetails) {
                $returnArray = array('result' => true, 'message' => 'Successfully', 'userDetails' => $userDetails);
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function userprofile_edit(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            // $userDetails = User::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    // 'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email,' . $userDetails->id,
                    'email' => [
                        'required', 'regex:/(.+)@(.+)\.(.+)/i', 'email', Rule::unique('users')->where(function ($query) use ($userDetails) {
                            $query->where('id', '!=', $userDetails->id);
                            $query->where('status', '!=', 'deleted');
                        }),
                    ],
                    // 'phone' => 'required|numeric|unique:users,phone,' . $userDetails->id,
                    'phone' => [
                        'required', 'numeric', Rule::unique('users')->where(function ($query) use ($userDetails) {
                            $query->where('id', '!=', $userDetails->id);
                            $query->where('status', '!=', 'deleted');
                        }),
                    ],
                ]);

                if ($validator->fails()) {
                    $returnArray = array('result' => false, 'message' => $validator->errors());
                } else {
                    User::find($userDetails->id)->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                    ]);
                    $userdetails = User::find($userDetails->id, ['name', 'email', 'phone']);

                    $returnArray = array('result' => true, 'message' => 'Successfully updated the profile details.', 'userDetails' => $userdetails);
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function profileimage_update(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $img_path = '/assets/uploads/profile/';

            // $userDetails = User::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $validator = Validator::make($request->all(), [
                    'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);

                if ($validator->fails()) {
                    $returnArray = array('result' => false, 'message' => $validator->errors()->first());
                } else {
                    $imagefile = $request->file('profile_image');
                    // dd($imagefile->extension());

                    if ($userDetails->profile_pic != '') {
                        $image_path = public_path('/assets/uploads/profile/') . '/' . $userDetails->profile_pic;
                        File::delete($image_path);
                    }

                    $fileName = 'profile_' . time() . '.' . $imagefile->extension();
                    $imagefile->move(public_path('/assets/uploads/profile/'), $fileName);

                    User::find($userDetails->id)->update([
                        'profile_pic' => $fileName,
                    ]);

                    $userdetails = User::find($userDetails->id, [DB::raw('CONCAT("' . $img_path . '", profile_pic) AS profileimage')]);

                    $returnArray = array('result' => true, 'message' => 'Successfully updated the profile details.', 'userDetails' => $userdetails);
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function wishlist(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $img_path = '/assets/uploads/products/';

                //Get Wishlist products coDe--
                $wishlist = Wishlist::select('wishlists.id AS wishlist_id', 'wishlists.user_id', 'wishlists.product_id', 'products.product_name', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) AS producprice'), DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS productimage'))
                    ->join('products', 'products.id', 'wishlists.product_id')
                    ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                    ->where('wishlists.user_id', $userDetails->id)
                    ->latest('wishlists.created_at')->paginate(20);

                $returnArray = array('result' => true, 'message' => 'Successfull', 'wishlist' => $wishlist);
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function manage_wishlist(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $data['id'] = $request->product_id;
                $validate = Validator::make($data, [
                    'id' => [
                        'required', 'numeric', Rule::exists('products')->where(function ($query) {
                            $query->where('products.status', 'active');
                        }),
                    ],
                ], [
                    'id.required' => 'The product id field is required',
                    'id.numeric' => 'The product id field must be an integer.',
                    'id.exists' => 'The product not found.',
                ]);

                if ($validate->fails()) {
                    $returnArray = array('result' => false, 'message' => $validate->errors()->first());
                } else {
                    $message = 'Successfull';
                    if (Wishlist::where('user_id', $userDetails->id)->where('product_id', $request->product_id)->exists()) {
                        Wishlist::where('user_id', $userDetails->id)->where('product_id', $request->product_id)->delete();
                        $message = 'Product removed from wishlist';
                    } else {
                        $data = Wishlist::create([
                            'user_id' => $userDetails->id,
                            'product_id' => $request->product_id,
                        ]);
                        $message = 'Product added to wishlist';
                    }
                    $count = Wishlist::where('user_id', $userDetails->id)->count();

                    $returnArray = array('result' => true, 'message' => $message, 'wishlist_count' => $count);
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function list_orders(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $img_path = '/assets/uploads/products/';

                //Get order details coDe--
                $orders = [];
                $order_details = Order::where('orders.user_id', $userDetails->id)
                    ->whereNotIn('orders.status', ['initiated', 'failed'])
                    ->latest()->paginate(20);

                foreach ($order_details as $key => $order_row) {
                    $orders[$key]['order_id'] = $order_row->id;
                    $orders[$key]['order_date'] = $order_row->date;
                    $orders[$key]['status'] = $order_row->status;
                    $orders[$key]['total_amount'] = $order_row->total_amount;
                    $orders[$key]['total_tax_amount'] = $order_row->total_tax_amount;
                    $orders[$key]['shipping_charge'] = $order_row->shipping_charge;
                    $orders[$key]['grand_total'] = $order_row->grand_total;
                    $orders[$key]['order_details'] = OrderDetails::join('products', 'products.id', 'order_details.product_id')
                        ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                        ->where('order_details.order_id', $order_row->id)
                        ->select('products.product_name', 'products.product_name', DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS productimage'), 'order_details.product_id', 'order_details.quantity', 'order_details.total_tax', 'order_details.price', 'order_details.amount', 'order_details.status as productstatus', 'order_details.status_on')
                        ->get();
                }

                $returnArray = array('result' => true, 'message' => 'Successfull', 'orders' => $orders);
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    //------User Password--
    public function changepassword(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $current_password = base64_decode($request->currentpassword);
            $currentpassword = preg_replace('/^o63s/', '', $current_password);

            $new_password = base64_decode($request->newpassword);
            $newpassword = preg_replace('/^o63s/', '', $new_password);

            $confirm_password = base64_decode($request->confirmpassword);
            $confirmpassword = preg_replace('/^o63s/', '', $confirm_password);

            $data = (array) $request->all();
            $data['currentpassword'] = $currentpassword;
            $data['newpassword'] = $newpassword;
            $data['confirmpassword'] = $confirmpassword;

            $userDetails = User::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();

            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $validate = Validator::make($data, [
                    'newpassword' => ['required', new IsValidPassword()],
                    'confirmpassword' => 'required|same:newpassword',
                    'currentpassword' => ['required', function ($attribute, $value, $fail) use ($userDetails) {
                        if (!\Hash::check($value, $userDetails->password)) {
                            return $fail(__('The current password is incorrect.'));
                        }
                    }],
                ]);

                if ($validate->fails()) {
                    $message = '';
                    foreach ($validate->errors()->toArray() as $error) {
                        $message .= $error[0];
                    }
                    return response()->json(['result' => false, 'message' => $validate->errors()->first()]);
                } else {
                    User::where('id', $userDetails->id)->update([
                        'password' => Hash::make($newpassword),
                    ]);

                    return response()->json(['result' => true, 'message' => 'Password successfully changed.']);
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.']);
        }
    }

    public function forgotpassword(Request $request)
    {
        if (!empty($request->header('device-id'))) {
            $deviceID = $request->header('device-id');
            $validate = Validator::make($request->all(), [
                'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|exists:users,email',
            ]);
            if ($validate->fails()) {
                return response()->json(['result' => false, 'message' => $validate->errors()->first()]);
            } else {
                $user = User::where('email', $request->email)->where('status', 'active')->first();
                if ($user) {
                    //Create Password Reset Token
                    Resetpassword::insert([
                        'usertype' => 'customer',
                        'email' => $request->email,
                        'token' => Str::random(32),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    $tokenData = Resetpassword::where('email', $request->email)->latest()->first();
                    if ($tokenData) {
                        $settings = Generalsetting::where('item', 'notification_email')->first();
                        if ($settings) {
                            Mail::send('email.passwordresetCustomer',
                                array(
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'subject' => 'Reset your customer profile password',
                                    'token' => $tokenData->token,
                                ), function ($message) use ($user, $settings) {
                                    $message->from($settings->value, 'Expressmed');
                                    $message->to($user->email);
                                    $message->subject('Password reset - Expressmed');
                                });
                        }
                        return response()->json(['result' => true, 'message' => 'Please check email and follow the instructions. Thank You...']);

                    } else {
                        return response()->json(['result' => false, 'message' => 'Sorry... Your request is failed.']);

                    }
                } else {
                    return response()->json(['result' => false, 'message' => 'You are not registered with us. Please register account']);
                }
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Invalid request. Device id not found.']);

        }
    }

    // public function forgotpassword(Request $request)
    // {
    //     if (!empty($request->header('device-id'))) {
    //         $deviceID = $request->header('device-id');

    //         $validate = Validator::make($request->all(), [
    //             'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|exists:users,email',
    //         ]);

    //         if ($validate->fails()) {
    //             return response()->json(['result' => false, 'message' => $validate->errors()->first()]);
    //         } else {
    //             $userDetails = User::where('email', $request->email)->where('status', 'active')->first();
    //             if ($userDetails) {
    //                 $otp_number = rand(100000, 999999);
    //                 $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    //                 $userdata = User::where('phone', $userDetails->phone)->first();
    //                 // if (isset($userdata)) {

    //                 //     DeviceToken::where('user_id', $userdata->id)->update([
    //                 //         'device_id' => $deviceID,
    //                 //         'otp_expiry' => $otp_expiry,
    //                 //         'otp' => $otp_number,
    //                 //     ]);
    //                 // }

    //                 User::where('phone', $userDetails->phone)->update([
    //                     'device_id' => $deviceID,
    //                     'otp_expiry' => $otp_expiry,
    //                     'otp' => $otp_number,
    //                 ]);

    //                 $settings = Generalsetting::where('item', '=', 'notification_email')->first();
    //                 if ($settings) {
    //                     Mail::send('email.forgetpassword',
    //                         array(
    //                             'otp_number' => $otp_number,
    //                             'customername' => $userDetails->name,
    //                         ), function ($message) use ($userDetails, $settings) {
    //                             $message->from($settings->value, 'Expressmed');
    //                             $message->to($userDetails->email);
    //                             $message->subject('Forget password OTP number');
    //                         });
    //                 }
    //                 $details = User::find($userDetails->id, ['email', 'otp']);

    //                 return response()->json(['result' => true, 'message' => 'Successfully', 'Details' => $details]);
    //             } else {
    //                 return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
    //             }
    //         }
    //     } else {
    //         return response()->json(['result' => false, 'message' => 'Attempt failed: Invalid request. Device id not found.']);
    //     }
    // }

    public function resetpassword(Request $request)
    {
        if (!empty($request->header('device-id'))) {
            $deviceID = $request->header('device-id');

            $new_password = base64_decode($request->newpassword);
            $newpassword = preg_replace('/^o63s/', '', $new_password);

            $confirm_password = base64_decode($request->confirmpassword);
            $confirmpassword = preg_replace('/^o63s/', '', $confirm_password);

            $data = (array) $request->all();
            $data['newpassword'] = $newpassword;
            $data['confirmpassword'] = $confirmpassword;

            $validate = Validator::make($request->all(), [
                'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|exists:users,email',
                'otp' => 'required|digits:6',
                'newpassword' => 'required|min:6',
                'confirmpassword' => 'required|same:newpassword',
            ]);

            if ($validate->fails()) {
                return response()->json(['result' => false, 'message' => $validate->errors()->first()]);
            } else {
                $userDetails = User::where('email', $request->email)->where('device_id', $deviceID)->where('otp', $request->otp)->where('otp_expiry', '>=', date('Y-m-d H:i:s'))->first();
                // $userDetails = User::select('users.*')
                //     ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                //     ->where('device_tokens.device_id', $deviceID)
                //     ->where('device_tokens.otp', $request->otp)
                //     ->where('device_tokens.otp_expiry', '>=', date('Y-m-d H:i:s'))
                //     ->first();

                if ($userDetails) {
                    User::where('id', $userDetails->id)->update(['password' => Hash::make($newpassword)]);

                    return response()->json(['result' => true, 'message' => 'Password successfully reset.']);
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: something went wrong. Please try again to request new otp.']);
                }
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Invalid request. Device id not found.']);
        }
    }

    public function get_producttype(Request $request)
    {
        if ($request->header('device-id') != '') {

            $product_types = Category::select('categories.*')->where('parent_id', 0)->where('status', 'active')->get();
            if ($product_types) {
                return response()->json(['result' => true, 'message' => 'Successfully', 'product_types' => $product_types]);
            } else {
                return response()->json(['result' => false, 'message' => 'Sorry.. Cannot find producttypes list.']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function search_condition(Request $request)
    {
        if ($request->header('device-id') != '') {
            if ($request->producttype_id) {
                $type = Category::find($request->producttype_id);
                if ($type) {
                    if ($type->name == "All Medicines") {
                        //         $all_med_child_categoryIds = [];
                        //     $all_med_categoryIds = [];
                        //     array_push($all_med_categoryIds, $type->id);
                        //     array_push($all_med_child_categoryIds, $type->id);
                        //     $obj_category = new Category();
                        //     $all_med_child_category = $obj_category->getCategories($all_med_categoryIds);
                        //    $all_med_child_categoryIds = $this->getCategoryIds($all_med_child_category, $all_med_child_categoryIds);
                        $all_medicineuse = MedicineUse::select('id as medicineuse_id', 'name as medicineuse_name')->orderBy('name', 'asc')->get();
                        $all_subcategories = Category::where('parent_id', $type->id)->where('status', 'active')->orderBy('name', 'asc')->get();
                        return response()->json(['result' => true, 'message' => 'Successfully', 'all_medicineuse' => $all_medicineuse, 'all_subcategories' => $all_subcategories]);
                    } else {
                        $all_brands = Productbrand::select('id as brand_id', 'name as brand_name')->orderBy('name', 'asc')->get();
                        $all_subcategories = Category::where('parent_id', $type->id)->where('status', 'active')->orderBy('name', 'asc')->get();
                        return response()->json(['result' => true, 'message' => 'Successfully', 'all_brands' => $all_brands, 'all_subcategories' => $all_subcategories]);
                    }
                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: Requested Product type not found.']);
                }
            } else {
                return response()->json(['result' => false, 'message' => 'product type id required']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    //---------------------Freezed Functionality---------

    // public function show_contentpage(Request $request)
    // {
    //     if($request->header('device-id') != '') {
    //         if(!empty($request->id)){
    //             $ban_path = '/assets/uploads/contents/';
    //             $slider_path = '/assets/uploads/sliders/';

    //             $contentPage = Contentpage::select('page','title','page_content','seo_url','seo_title','seo_description','page_position','banner_type', DB::raw('(CASE WHEN banner != "" THEN CONCAT("'.$ban_path.'", banner) ELSE "" END) AS bannerimage'),'slider')
    //                 ->where('id', $request->id)->first();

    //             if($contentPage){
    //                 $sliders = array();
    //                 if($contentPage->slider != 0) {
    //                     $sliders = Sliderimage::select(DB::raw('CONCAT("'.$slider_path.'", image) AS sliderimage'),'title','description','target')
    //                         ->where('slider_id', $contentPage->slider)->get()->all();
    //                 }
    //                 return response()->json(['result'=>true,'message'=>'Successfully', 'contentPage'=>$contentPage, 'sliders'=>$sliders]);
    //             } else {
    //                 return response()->json(['result'=>false,'message'=>'Attempt failed: Content page not found.']);
    //             }
    //         } else {
    //             return response()->json(['result'=>false,'message'=>'Attempt failed: Wrong request url.']);
    //         }
    //     } else {
    //         return response()->json(['result'=>false,'message'=>'Attempt failed: Device not detect. Something wrong with device id.']);
    //     }
    // }

    // public function cart_updation(Request $request)
    // {
    //     if($request->header('device-id') != '') {
    //         $img_path = '/assets/uploads/products/';
    //         $deviceID = $request->header('device-id');
    //         $validator = Validator::make($request->all(), [
    //             'product_id'    =>   'required|integer',
    //             'quantity'      =>   'required|integer'
    //         ]);
    //         if ($validator->fails()) {
    //             return ['result' => false, "errorMsg" => $validator->errors()->first()];
    //         } else {
    //             $userID = 0;

    //             if($request->header('api-token') != ''){
    //                 ///---------User cart--
    //                 $apiToken = $request->header('api-token');
    //                 $userDetails = User::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time','>=', date('Y-m-d H:i:s'))->first();
    //                 if($userDetails){
    //                     $userID = $userDetails->id;
    //                 } else {
    //                     return response()->json(['result'=>false,'message'=>'Attempt failed: User details not found. Please login again.']);
    //                 }
    //             }

    //             $message = '';
    //             if($request->type != '' && ($request->type == 'minus' || $request->type == 'plus')){

    //                 $Product = Product::find($request->product_id);
    //                 if($Product){
    //                     $existCart = Cart::where('product_id',$request->product_id);
    //                     if($userID == 0){
    //                         $existCart->where('carts.ip',$deviceID)->where('carts.user_id',$userID);
    //                     } else {
    //                         $existCart->where('carts.user_id',$userID);
    //                     }
    //                     $existCart = $existCart->first();

    //                     if($existCart){
    //                         if($request->type == 'minus' && $existCart->quantity <= 1){
    //                             return response()->json(['result'=>false,'message'=>'Attempt failed: You are reached minimum quantity.']);
    //                         }
    //                         $cartupdation = Cart::where('product_id', $request->product_id);
    //                         if($userID == 0){
    //                             $cartupdation->where('carts.ip',$deviceID)->where('carts.user_id',$userID);
    //                         } else {
    //                             $cartupdation->where('carts.user_id',$userID);
    //                         }
    //                         if($request->type == 'plus'){
    //                             $cartupdation->increment('quantity', $request->quantity);
    //                         } elseif($request->type == 'minus' && $existCart->quantity > 1){
    //                             $cartupdation->decrement('quantity', $request->quantity);
    //                         }

    //                     } else {
    //                         return response()->json(['result'=>false,'message'=>'Attempt failed: Requested product not found in cart.']);
    //                     }
    //                 } else {
    //                     return response()->json(['result'=>false,'message'=>'Attempt failed: Requested product not found.']);
    //                 }

    //                 $cartdata = Cart::join('products','carts.product_id','products.id')
    //                     ->leftjoin('product_images','products.thumbnail','product_images.id')
    //                     ->select('carts.*','products.product_name','products.tax_ids',DB::raw('CONCAT("'.$img_path.'", product_images.product_image) AS Images'),DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'));
    //                     if($userID == 0){
    //                         $cartdata->where('carts.ip',$deviceID)->where('carts.user_id',$userID);
    //                     } else {
    //                         $cartdata->where('carts.user_id',$userID);
    //                     }
    //                 $cartdata = $cartdata->get();

    //                 if($cartdata){
    //                     foreach($cartdata as $key=>$value) {
    //                         if($value->tax_ids != null && isset($value->tax_ids)){
    //                             $tax_ids = explode(',',$value->tax_ids);
    //                             $cartdata[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
    //                         }
    //                     }
    //                 }

    //                 return response()->json(['result'=>true,'message'=>'Cart product updated successfully.', 'cartproducts'=>$cartdata]);
    //             } else {
    //                 return response()->json(['result'=>false,'message'=>'Attempt failed: Something went wrong. Request type not found.']);
    //             }
    //         }
    //     } else {
    //         return response()->json(['result'=>false,'message'=>'Attempt failed: Device not detect. Something wrong with device id.']);
    //     }
    // }

    public function sendSms($mobile, $otp)
    {
        $message = 'OTP for Login Transaction on ExpressMed is  ' . $otp . ' and valid till 2 minutes. Do not share this OTP to anyone for security reasons';
        $url = 'http://sms.mithraitsolutions.com/httpapi/httpapi?token=a6deb2ecca12cb4687126eddf5c18bdd&sender=EXPRMD&number=' . $mobile . '&route=2&type=1&sms=' . $message . '&templateid=1107164602892456673';
        $response = Http::get($url);

        /*  $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec ($ch);
        $err = curl_error($ch);  //if you need
        curl_close ($ch);  */
        return $response;
    }
    public function get_filtervalues(Request $request)
    {
        if ($request->header('device-id') != '') {

            $product_brands = Productbrand::select('productbrands.id', 'productbrands.name')->orderBy('name', 'asc')->get();
            $product_types = Category::select('categories.id', 'categories.name')->where('parent_id', 0)->where('status', 'active')->orderBy('name', 'asc')->get();
            $medicineuses = MedicineUse::select('medicine_uses.id', 'medicine_uses.name')->orderBy('name', 'asc')->get();

            return response()->json(['result' => true, 'message' => 'Successfully', 'product_types' => $product_types, 'product_brands' => $product_brands, 'medicineuses' => $medicineuses]);

        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function subcategory_products(Request $request)
    {
        if ($request->header('device-id') != '') {
            $Category_imagepath = '/assets/uploads/category/';
            $img_path = '/assets/uploads/products/';
            $cat_array = array();
            if ($request->parentcategoryId != '') {
                $Categories = Category::select('id as categoryid')->where('parent_id', $request->parentcategoryId)->where('status', 'active')->get();
                if (count($Categories) > 0) {
                    foreach ($Categories as $Subcategory) {
                        // if($Subcategory->categoryid=='12'){
                        $subCategories = Category::select('id as categoryid', 'name', DB::raw('CONCAT("' . $Category_imagepath . '", image) AS categoryimage'), 'description', 'status')->where('id', $Subcategory->categoryid)->get();

                        $categoryIds = [];

                        if ($Subcategory->categoryid != '' && $Subcategory->categoryid != 0) {
                            $categoryArray = explode(',', $Subcategory->categoryid);
                            foreach ($categoryArray as $val) {
                                array_push($categoryIds, $val);
                            }

                        }
                        $obj_category = new Category();
                        $child_category = $obj_category->getCategories($categoryIds);

                        $child_categoryIds = [];
                        array_push($child_categoryIds, $Subcategory->categoryid);
                        $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);

                        if ($request->productcategory != '' && $request->productcategory != 0) {
                            $categoryArray = explode(',', $request->productcategory);
                            foreach ($categoryArray as $categoryvalue) {
                                array_push($child_categoryIds, (int) $categoryvalue);
                            }
                        }
                        $products = Product::select('products.id as productid', 'products.brands', 'products.manufacturer', 'products.productcontent_id', 'products.category_id', 'products.flag', 'products.flag', 'products.not_for_sale', 'products.product_name', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) AS producprice'), DB::raw('CONCAT("' . $img_path . '", product_images.product_image) AS productimage'))
                            ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                            ->whereIn('products.category_id', $child_categoryIds)
                            ->where('products.hide_from_site', '!=', '1')
                            ->where('products.status', 'active')->limit(6)->get();

                        array_push($cat_array, ['category' => $subCategories, 'products' => $products]);

                    }
                    $returnArray = array('result' => true, 'subCategories' => $cat_array);
                } else {
                    $returnArray = array('result' => false, 'message' => 'Attempt failed: No Subcategories found.');
                }

            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: Please choose parent category.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    public function payment_details(Request $request)
    {
        if ($request->header('device-id') != '') {

            if ($request->order_id != '') {

                $merchant_id = Config::get('constants.payment_constants.merchant_id');
                $client_id = Config::get('constants.payment_constants.client_id');
                $secret_key = Config::get('constants.payment_constants.secret_key');

                $headers = ["alg" => "HS256", "clientid" => $client_id];

                $order_id = $request->order_id;
                $current_time = strtotime("now");
                $trace_id = $current_time . 'EXP';

                $ch_headers = array(
                    "content-type: application/jose",
                    "bd-timestamp: $current_time",
                    "accept: application/jose",
                    "bd-traceid: $trace_id",
                );
                $transition_attr = [
                    "mercid" => $merchant_id,
                    "orderid" => $order_id,
                ];
                $curl_trans = JWT::encode($transition_attr, $secret_key, "HS256", null, $headers);
                // print_r($curl_trans);
                 $transition_url = "https://api.billdesk.com/payments/ve1_2/transactions/get";
                //$transition_url = "https://pguat.billdesk.io/payments/ve1_2/transactions/get";
                $ch = curl_init($transition_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_trans);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $transaction = curl_exec($ch);
                curl_close($ch);
                $transition_return = json_decode($transaction);

                $jwt_TransitionResponse = "";

                $tokenParts = explode(".", $transaction);
                $tokenHeader = base64_decode($tokenParts[0]);
                $tokenPayload = base64_decode($tokenParts[1]);
                $jwt_TransitionHeader = json_decode($tokenHeader);
                $jwt_TransitionResponse = json_decode($tokenPayload);
                if (!(isset($jwt_TransitionResponse->status))) {

                    return response()->json(['result' => true, 'message' => 'Success', 'payment_response' => $jwt_TransitionResponse]);

                } else {
                    return response()->json(['result' => false, 'message' => $jwt_TransitionResponse->message]);
                }

            } else {
                return response()->json(['result' => false, 'message' => 'Order Id not found']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }
    public function clear_cart(Request $request)
    {
        if ($request->header('device-id') != '') {
            if ($request->cart_id) {
                if (Cart::find($request->cart_id)) {
                    Cart::where('id', $request->cart_id)->delete();
                    return response()->json(['result' => true, 'message' => 'Cart Cleared Successfully']);

                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed: Cart does not exist.']);

                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: Cart Id not detect. Something wrong with Cart id.']);

            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function online_payment_success(Request $request)
    {
        if ($request->header('device-id') != '') {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'payment_gateway' => 'required|in:billdesk,razorpay',
                'transaction_id' => 'required',
                'payment_method' => 'required',
                'currency_code' => 'required',
                'amount' => 'required',
                'transaction_date' => 'required',
                'transaction_status' => 'required',
                'checkout_type' => 'required|in:buynow,cart',
                'cart_ids' => 'required_if:checkout_type,cart',
            ]);
            if ($validator->fails()) {
                $error_msg = '';
                foreach ($validator->errors()->toArray() as $value) {
                    $error_msg .= $value[0] . '<br />';
                }
                return response()->json(['result' => false, 'message' => $error_msg]);
            } else {
                $order = Order::find($request->order_id);
                if ($order) {
                    $payment_store = PaymentDetail::create([
                        'order_id' => $request->order_id,
                        'payment_gateway' => $request->payment_gateway,
                        'transaction_id' => $request->transaction_id,
                        'payment_method' => $request->payment_method,
                        "currency_code" => $request->currency_code,
                        "amount" => $request->amount,
                        "transaction_date" => $request->transaction_date,
                        "transaction_status" => $request->transaction_status,
                    ]);

                    if ($payment_store) {
                        Order::find($request->order_id)->update([
                            'status' => 'ordered',
                        ]);

                        if ($request->checkout_type == 'cart') {
                            $cart_ids = explode(',', $request->cart_ids);

                            if (!empty($cart_ids)) {
                                Cart::whereIn('id', $cart_ids)->delete();
                            }
                        }
                        $invoice_number = $this->invoiceNumber();
                        Invoice::create([
                            'user_id' => $order->user_id,
                            'order_id' => $order->id,
                            'invoice_number' => $invoice_number,
                        ]);
                        $this->customer_invoice_mail($order->id, $invoice_number);
                        $this->Admin_orderConfirmation_mail($order->id, $invoice_number);

                        $order_items = OrderDetails::where('order_id', $order->id)->get();

                        return response()->json(['result' => true, 'message' => 'payment details stored Successfully', 'order' => $order, 'order_items' => $order_items]);
                    } else {
                        return response()->json(['result' => false, 'message' => 'Order not found']);

                    }

                } else {
                    return response()->json(['result' => false, 'message' => 'Attempt failed:Something wrong with payment details.']);

                }
            }

        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);

        }

    }

    public function invoiceNumber()
    {
        $latest = Invoice::latest()->first();

        if (!$latest) {
            return 'med0001';
        }

        $string = preg_replace("/[^0-9\.]/", '', $latest->invoice_number);

        return 'med' . sprintf('%04d', $string + 1);
    }

    public function get_stores(Request $request)
    {
        if ($request->header('device-id') != '') {
            $stores = Store::all();
            if (count($stores) > 0) {
                return response()->json(['result' => true, 'message' => 'Successfully', 'stores' => $stores]);
            } else {
                return response()->json(['result' => false, 'message' => 'Sorry.. No pickup stores available.']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function customer_invoice_mail($OrderID = null, $invoice_number = null)
    {

        $orders = array();
        $userType = "customer";
        if ($OrderID) {
            $userID = 0;
            if (Auth::guard('user')->user()) {
                $userID = Auth::guard('user')->user()->id;
            }

            //Get order details coDe--
            $order_Master = Order::join('user_addresses', 'orders.address_id', 'user_addresses.id')
                ->join('countries', 'user_addresses.country_id', 'countries.id')
                ->join('states', 'user_addresses.state_id', 'states.id')
                ->leftjoin('stores', 'stores.id', 'orders.store_id')
            // ->where('orders.id', $OrderID)->where('orders.user_id', $userID)
                ->where('orders.id', $OrderID)
                ->select('orders.*', 'user_addresses.name', 'user_addresses.address', 'user_addresses.email', 'user_addresses.phone', 'user_addresses.pin', 'user_addresses.location', 'user_addresses.city',
                    'user_addresses.landmark', 'states.name as state_name', 'countries.name as country_name', 'stores.name as store_name', 'stores.location as store_location', 'stores.address as store_address', 'stores.contact_number as store_contact_number', 'stores.map_location_code as store_location_map')
                ->first();

            if ($order_Master) {
                $order_details = OrderDetails::join('products', 'products.id', 'order_details.product_id')
                    ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                    ->where('order_details.order_id', $order_Master->id)
                    ->select('order_details.*', 'products.product_name')
                    ->get();

                if ($order_details) {
                    $orders = new \stdClass();
                    $orders->order_id = $order_Master->id;
                    $orders->order_date = $order_Master->date;
                    $orders->name = $order_Master->name;
                    $orders->phone = $order_Master->phone;
                    $orders->address = $order_Master->address;
                    $orders->pin = $order_Master->pin;
                    $orders->city = $order_Master->city;
                    $orders->location = $order_Master->location;
                    $orders->landmark = $order_Master->landmark;
                    $orders->state_name = $order_Master->state_name;
                    $orders->country_name = $order_Master->country_name;
                    $orders->delivery_type = $order_Master->delivery_type;
                    $orders->store_id = $order_Master->store_id;
                    $orders->store_name = $order_Master->store_name;
                    $orders->store_location = $order_Master->store_location;
                    $orders->store_address = $order_Master->store_address;
                    $orders->store_contact_number = $order_Master->store_contact_number;
                    $orders->store_location_map = $order_Master->store_location_map;
                    $orders->status = $order_Master->status;
                    $orders->total_amount = $order_Master->total_amount;
                    $orders->total_tax_amount = $order_Master->total_tax_amount;
                    $orders->shipping_charge = $order_Master->shipping_charge;
                    $orders->grand_total = $order_Master->grand_total;
                    $orders->order_details = $order_details;
                    $orders->invoice_number = $invoice_number;
                }
                $settings = Generalsetting::where('item', '=', 'notification_email')->first();

                // $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__]);

                $pdf = PDF::loadView('email.pdf-invoice', compact('orders'));
                $path = public_path('assets/uploads/invoice');
                if (!File::exists($path)) {
                    File::makeDirectory($path);
                }
                $fileName = $invoice_number . '.' . 'pdf';
                //  $pdf->save( public_path('assets/uploads/invoice') . '/'. $fileName);
                $pdf->save($path . '/' . $fileName);

                if ($settings && $order_Master->email) {
                    Mail::send('email.invoice_mail',
                        array(
                            'customername' => $order_Master->name,
                            // 'adminname' => Auth::guard('admin')->user()->name,
                            // 'productname' => $product->product_name,
                            // 'quantity' => $request->quantity,
                            // 'productprice' => $productPrice,
                        ), function ($message) use ($settings, $pdf, $invoice_number, $order_Master) {
                            $message->from($settings->value, 'Expressmed');
                            $message->to($order_Master->email);
                            $message->subject('Expressmed');
                            $message->attachData($pdf->output(), $invoice_number . '.pdf');
                            // $message->subject('Prescription '.$status_msg);
                        });
                }
                return true;
            }

        }

        return false;

    }
    public function Admin_orderConfirmation_mail($OrderID = null, $invoice_number = null)
    {

        $orders = array();
        $userType = "Admin";
        $mode = "Customer_Manageorder";
        if ($OrderID) {
            //Get order details coDe--
            $order_Master = Order::join('user_addresses', 'orders.address_id', 'user_addresses.id')
                ->join('countries', 'user_addresses.country_id', 'countries.id')
                ->join('states', 'user_addresses.state_id', 'states.id')
            // ->where('orders.id', $OrderID)->where('orders.user_id', $userID)
                ->where('orders.id', $OrderID)
                ->select('orders.*', 'user_addresses.name', 'user_addresses.address', 'user_addresses.email', 'user_addresses.phone', 'user_addresses.pin', 'user_addresses.location', 'user_addresses.city',
                    'user_addresses.landmark', 'states.name as state_name', 'countries.name as country_name')
                ->first();

            if ($order_Master) {
                $order_details = OrderDetails::join('products', 'products.id', 'order_details.product_id')
                    ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                    ->where('order_details.order_id', $order_Master->id)
                    ->select('order_details.*', 'products.product_name')
                    ->get();

                if ($order_details) {
                    $orders = new \stdClass();
                    $orders->order_id = $order_Master->id;
                    $orders->order_date = $order_Master->date;
                    $orders->name = $order_Master->name;
                    $orders->phone = $order_Master->phone;
                    $orders->address = $order_Master->address;
                    $orders->pin = $order_Master->pin;
                    $orders->city = $order_Master->city;
                    $orders->location = $order_Master->location;
                    $orders->landmark = $order_Master->landmark;
                    $orders->state_name = $order_Master->state_name;
                    $orders->country_name = $order_Master->country_name;

                    $orders->status = $order_Master->status;
                    $orders->total_amount = $order_Master->total_amount;
                    $orders->grand_total = $order_Master->grand_total;
                    $orders->order_details = $order_details;
                    $orders->invoice_number = $invoice_number;
                }
                $settings = Generalsetting::where('item', '=', 'notification_email')->first();

                if ($settings) {
                    Mail::send('email.order_cancellationMail',
                        array(
                            'orderid' => $OrderID,
                            'customername' => $order_Master->name,
                            'status' => 'ordered',
                            'usertype' => $userType,
                            'mode' => $mode,
                            'subject' => 'Customer successfully ordered as order from your store.',

                        ), function ($message) use ($settings, $order_Master) {
                            $message->from($settings->value, 'Expressmed');
                            $message->to($settings->value);
                            $message->subject('Customer order Received.');
                        });
                }
                return true;
            }

        }

        return false;

    }
    public function delete_account(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $userDetails = User::select('users.id AS userid', 'users.email', 'users.phone')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)->where('device_tokens.api_token', $apiToken)->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();

            if ($userDetails) {

                if ($userDetails->status != 'active') {
                    Auth::guard('user')->logout();
                    return response()->json(['result' => false, 'message' => 'Your account is no longer exist.']);
                }
                User::find($userDetails->userid)->update([
                    'status' => 'deleted',
                ]);
                $returnArray = array('result' => true, 'message' => 'Account deleted Successfully');
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }

        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function bulkprescriptionUpload(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $userDetails = User::select('users.*')
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
                ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.api_token', $apiToken)
                ->where('device_tokens.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
                $bulk_prescription_arr = array();

                $validator = Validator::make($request->all(), [
                    'prescription_file.*' => 'required|mimes:jpeg,jpg,png,pdf,doc',
                    // 'order_id'=>'required'
                ]);
                if ($validator->fails()) {
                    $returnArray = array('result' => false, 'message' => $validator->errors()->first());
                } else {
                    if (!empty($request->file('prescription_file'))) {
                        foreach ($request->file('prescription_file') as $key => $file) {

                            $fileName = 'presc_' . time() . '.' . $file->extension();
                            $file->move(public_path('/assets/uploads/prescription/'), $fileName);
                            $prescription = new Prescription();
                            $prescription->user_id = $userID;
                            $prescription->type = 'bulk';
                            $prescription->file = $fileName;
                            // $prescription->order_id = $order->id;
                            $prescription->status = 1;
                            $prescription->save();
                            $bulk_prescription_arr[] = $prescription->id;
                        }
                        $returnArray = array('result' => true, 'message' => 'Prescription uploaded successfully.', 'prescription_ids' => $bulk_prescription_arr);

                    } else {
                        $returnArray = array('result' => false, 'message' => 'Prescription file not found.');
                    }

                }

            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login.');
            }

        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }
}
