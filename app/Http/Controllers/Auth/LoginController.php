<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\DeviceToken;
use App\Models\User;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Validator;
use Illuminate\Validation\Rule;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    //protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
        //$this->middleware('guest:admin')->except('logout');
    }

    //-----Show Admin Login page--
    public function showAdminLoginForm()
    {
        return view('auth.login', [
            'url' => Config::get('constants.guards.admin'),
        ]);
    }
    //-----Show Admin customer Login page--
    public function showcustomersupportLoginForm()
    {
        return view('auth.login', [
            'url' => Config::get('constants.guards.customersupport'),
        ]);
    }

    //-----Admin Login coDe--
    public function adminLogin(Request $request)
    {
        if ($this->guardLogin($request, Config::get('constants.guards.admin'))) {
            return redirect()->intended('/admin');
        }
        return back()->withInput($request->only('email', 'remember'))->with('error', 'Email and Password not match. Please check and try with correct.');
    }
    //-----User customersupportLogin coDe--
    public function customersupportLogin(Request $request)
    {
        if ($this->guardLogin($request, Config::get('constants.guards.customersupport'))) {
            return redirect()->intended('/customersupport/profile');
        }
        return back()->withInput($request->only('email', 'remember'))->with('error', 'Email and Password not match. Please check and try with correct.');
    }

    //-----User Login coDe--
    public function userLogin(Request $request)
    {
        $msg = array();
        if ($this->guardLogin($request, Config::get('constants.guards.user'))) {

            if (Auth::guard('user')->user()->status != 'active') {
                Auth::guard('user')->logout();
                $this->guard()->logout();
                return response()->json(['status' => 'error', 'message' => 'Your account is no longer exist.']);
            }

            $user_id = Auth::guard('user')->user()->id;

            // --Guest cart items insert to cart table--
            if (!isset($_SESSION)) {
                session_start();
            }
            $GuestCart = isset($_SESSION['Session_GuestCart']) ? $_SESSION['Session_GuestCart'] : [];

            if ($GuestCart) {
                $ExistCart = Cart::where('user_id', $user_id)->get();

                if ($ExistCart) { //Update Quantity if Item exist main cart--
                    foreach ($ExistCart as $ExistCart_row) {
                        if (array_key_exists($ExistCart_row->product_id, $GuestCart)) {
                            $qty = ($ExistCart_row->quantity + $GuestCart[$ExistCart_row->product_id]['quantity']);

                            Cart::where('user_id', $user_id)
                                ->where('product_id', $GuestCart[$ExistCart_row->product_id]['product_id'])
                                ->update([
                                    'quantity' => $qty,
                                ]);

                            unset($GuestCart[$ExistCart_row->product_id]);
                        }
                    }
                }
                //Update Quantity if Item exist main cart /--

                foreach ($GuestCart as $productID => $value) {
                    Cart::create([
                        'user_id' => $user_id,
                        'product_id' => $productID,
                        'quantity' => $value['quantity'],
                        'ip' => $value['ip'],
                    ]);
                }
                unset($_SESSION['Session_GuestCart']);
            }

            //--Guest cart items insert to cart table/--

            $msg = array(
                'status' => 'success',
                'message' => 'Login successfull',
            );
        } else {
            $msg = array(
                'status' => 'error',
                'message' => 'Email or password is wrong. Please try again..',
            );
        }
        return response()->json($msg);
    }

    //--All Users login functionality coDe--
    protected function guardLogin(Request $request, $guard)
    {
        $validate_Login = $this->validator($request);

        if ($guard == 'admin') {
            return Auth::guard($guard)->attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password,
                ],
                $request->get('remember')
            );
        } elseif ($guard == 'customersupport') {
            return Auth::guard($guard)->attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password,
                    'status' => 'active',
                ],
                $request->get('remember')
            );
        } else {
            return Auth::guard($guard)->attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password,
                    'status' => 'active',
                ],
                $request->get('remember')
            );
        }
    }

    protected function validator(Request $request)
    {
        return $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);
    }

    //--Users Logout functions defines coDe--
    public function admin_logout(Request $request)
    {
        if (Auth::guard('admin')->check()) // this means that the admin was logged in.
        {
            Auth::guard('admin')->logout();
            return redirect()->route('login.admin');
        }

        $this->guard()->logout();
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    public function userLogout(Request $request)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (Auth::guard('user')->check()) // this means that the user was logged in.
        {
            Auth::guard('user')->logout();
            $this->guard()->logout();
            $request->session()->invalidate();
            unset($_SESSION['product_prescriptions']);
            return redirect('/');
        }
        $this->guard()->logout();
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->back();
    }

    public function customersupportlogout(Request $request)
    {
        if (Auth::guard('customersupport')->check()) {
            Auth::guard('customersupport')->logout();
            return redirect()->route('login.customersupport');
        }
        $this->guard()->logout();
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/login/customersupport');
    }

    public function request_otp(Request $request)
    {

        if ($request->country != '') {

            // $country_code = preg_replace('/[^A-Za-z0-9\-]/', '', $request->countrycode); // Removes special chars.
            // $phone_num = preg_replace('/[^A-Za-z0-9\-]/', '', $request->phone); // Removes special chars.
            // $phone = '+' . $country_code . $phone_num;
            $phone = $request->phone;
            $country = $request->country;
            $userdetails = User::where('phone', $phone)->where('country_id', $country)->where('status', 'active')->first();
            if ($userdetails) {
                if ($userdetails->status == 'active') {
                    $otp_number = rand(100000, 999999);
                    $otp_expiry = date('Y-m-d H:i:s', strtotime('+4 minutes'));
                    $this->sendSms($phone, $otp_number);

                    $now = date('Y-m-d H:i:s');
                    $late_expiry_date = date('Y-m-d H:i:s', strtotime('-1 days', strtotime(str_replace('/', '-', $now))));

                    $up = DeviceToken::where('user_id', $userdetails->id)
                        ->where('device_id', $request->header('device-id'))
                        ->update([
                            'otp_expiry' => $late_expiry_date,
                        ]);

                    $token = DeviceToken::create([
                        'user_id' => $userdetails->id,
                        'otp' => $otp_number,
                        'otp_expiry' => $otp_expiry,

                    ]);

                    //--When OTP update success, generated OTP send by sms to the given phone ~ SMS Gateway--
                    //---Send by sms codebe here....;
                    //--SMS Gateway-//-

                    $returnArray = array('result' => true, 'message' => 'Successfully. Enter given OTP.', 'country' => $country, 'phone' => $phone);
                } else {
                    $returnArray = array('result' => false, 'message' => 'Your account is no longer exist.');
                }

            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: Your phone number not found. Please try again.');}
        } else {
            $returnArray = array('result' => false, 'message' => 'Please choose your country.');
        }

        return response()->json($returnArray);
    }
    public function sendSms($mobile, $otp)
    {
        $message = 'OTP for Login Transaction on ExpressMed is  ' . $otp . ' and valid till 2 minutes. Do not share this OTP to anyone for security reasons';
        $url = 'http://sms.mithraitsolutions.com/httpapi/httpapi?token=a6deb2ecca12cb4687126eddf5c18bdd&sender=EXPRMD&number=' . $mobile . '&route=2&type=1&sms=' . $message . '&templateid=1107164602892456673';
        $response = Http::get($url);
        return $response;
    }
    public function userOtpLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',

        ]);
        $phone = $request->phone;

        if ($validator->fails()) {
            $error_msg = '';
            foreach ($validator->errors()->toArray() as $value) {
                $error_msg .= $value[0];
            }
            $returnArray = array('result' => false, 'message' => $error_msg);
        } else {
            $userdetails = User::select('users.*')
                ->where('users.phone', $phone)
                ->join('device_tokens', 'device_tokens.user_id', 'users.id')
            // ->where('device_tokens.device_id', $deviceID)
                ->where('device_tokens.otp', $request->otp)
                ->where('device_tokens.otp_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
        }
        if (isset($userdetails)) {

            Auth::guard(Config::get('constants.guards.user'))->login($userdetails);
            $msg = array();

            if (Auth::guard('user')->check()) {

                $user_id = Auth::guard('user')->user()->id;
                // dd($user_id);
                // $user_id=$userdetails->id;
                // --Guest cart items insert to cart table--
                if (!isset($_SESSION)) {
                    session_start();
                }
                $GuestCart = isset($_SESSION['Session_GuestCart']) ? $_SESSION['Session_GuestCart'] : [];

                if ($GuestCart) {
                    $ExistCart = Cart::where('user_id', $user_id)->get();

                    if ($ExistCart) { //Update Quantity if Item exist main cart--
                        foreach ($ExistCart as $ExistCart_row) {
                            if (array_key_exists($ExistCart_row->product_id, $GuestCart)) {
                                $qty = ($ExistCart_row->quantity + $GuestCart[$ExistCart_row->product_id]['quantity']);

                                Cart::where('user_id', $user_id)
                                    ->where('product_id', $GuestCart[$ExistCart_row->product_id]['product_id'])
                                    ->update([
                                        'quantity' => $qty,
                                    ]);

                                unset($GuestCart[$ExistCart_row->product_id]);
                            }
                        }
                    }
                    //Update Quantity if Item exist main cart /--

                    foreach ($GuestCart as $productID => $value) {
                        Cart::create([
                            'user_id' => $user_id,
                            'product_id' => $productID,
                            'quantity' => $value['quantity'],
                            'ip' => $value['ip'],
                        ]);
                    }
                    unset($_SESSION['Session_GuestCart']);
                }

                //--Guest cart items insert to cart table/--

                $msg = array(
                    'status' => 'success',
                    'message' => 'Login successfull',
                );
            } else {
                $msg = array(
                    'status' => 'error',
                    'message' => 'Email or password is wrong. Please try again..',
                );
            }
        } else {
            $msg = array(
                'status' => 'error',
                'message' => 'Invalid Otp..',
            );
        }

        return response()->json($msg);
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
            $country_code = preg_replace('/[^A-Za-z0-9\-]/', '', $request->countrycode); // Removes special chars.
            $phone_num = preg_replace('/[^A-Za-z0-9\-]/', '', $request->phone); // Removes special chars.
            $phone = '+' . $country_code . $phone_num;

            if ($validator->fails()) {
                $error_msg = '';
                foreach ($validator->errors()->toArray() as $value) {
                    $error_msg .= $value[0];
                }
                $returnArray = array('result' => false, 'message' => $error_msg);
            } else {

                $userdetails = User::select('users.*')
                    ->where('users.phone', $phone)
                    ->join('device_tokens', 'device_tokens.user_id', 'users.id')

                // ->where('device_tokens.device_id', $deviceID)
                    ->where('users.status', 'active')
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
                            ->where('api_token_expiry', '>=', $now)
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
                            ->where('device_tokens.api_token_expiry', '>=', $now)
                            ->first();

                        if ($UserDetails) {
                            $settings = Generalsetting::where('item', '=', 'notification_email')->first();
                            if ($settings) {

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

}
