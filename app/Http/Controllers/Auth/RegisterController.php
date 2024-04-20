<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Generalsetting;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use App\Rules\IsValidPassword;
use Illuminate\Validation\Rule;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);
    // }

    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }

    // Register new user coDe--
    public function view_registerUser()
    {
        $countries = Country::all();
        return view('customer_register', compact('countries'));
    }

    public function registerUser(Request $request)
    {
        if($request->phone!=''){
            $str = ltrim($request->phone, "0");
            $request->merge(['phone' => $str]);
        }
        $validation = $this->validatorUser($request->all());
        $request->merge(['email' => str_replace(' ', '', $request->email)]);
        $message = '';
        if ($validation->fails()) {
            foreach ($validation->errors()->toArray() as $value) {
                $message .= $value[0] . '<br />';
            }
            if ($request->country != '101') {
                $message .= 'Service not available in your country';
            }
            $msg = array(
                'status' => 'error',
                'message' => $message,
            );

        } elseif ($request->country != '101') {

            $message .= 'Service not available in your country';

            $msg = array(
                'status' => 'error',
                'message' => $message,
            );
        } else {

            $userID = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'country_id' => $request->country,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ])->id;
            if (($request->email) != '') {
                $sendmail = $this->sentCustomerWelcomMail($request->email);
            }
            if ($userID) {
                $msg = array(
                    'status' => 'success',
                    'message' => 'Your account created successfull.',
                );

                $settings = Generalsetting::where('item', '=', 'notification_email')->first();
                if ($settings) {
                    // Account Verification mail to customer --
                    $verification_code = $userID;
                    // Mail::send('email.mail_verfication',
                    //     array(
                    //         'name' => $request->name,
                    //         'email' => $request->email,
                    //         'type' => 'customer',
                    //         'verification_code' =>  $verification_code,
                    //     ), function($message) use ($request, $settings) {
                    //         $message->from($settings->value,'ShopeOn');
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
                    //     'subject' => 'ShopeOn Notification mail - New customer '.$request->name.' registered',
                    // ), function($message) use ($request, $settings) {
                    //     $message->from($settings->value,'ShopeOn');
                    //     $message->to($settings->value);
                    //     $message->subject('Notification Mail: New Customer Registered ');
                    // });
                    //Notification mail to admin /--
                }

            } else {
                $msg = array(
                    'status' => 'error',
                    'message' => 'Error: User creation failed.',
                );
            }
        }

        return response()->json($msg);
    }

    protected function validatorUser(array $data)
    {

        return Validator::make($data, [
            'name' => 'required',
            'email' => [
                'required','regex:/(.+)@(.+)\.(.+)/i','email',Rule::unique('users')->where(function($query) {
                  $query->where('status', '!=', 'deleted');
              }),
            ],
            'country' => 'required',
            // 'phone' => 'required|unique:users,phone|numeric',
            'phone' => [
                'required','numeric',Rule::unique('users')->where(function($query) {
                  $query->where('status', '!=', 'deleted');
              }),
            ],
            'password' => ['required', new IsValidPassword()],
            'confirm_password' => ['same:password'],
        ]);
    }

    public function sentCustomerWelcomMail($email)
    {
        $customer = User::where('email', '=', $email)->get()->first();
        //Check if the user exists
        if (!$customer) {
            return false;
        }
        //Create Password Reset Token

        $settings = Generalsetting::where('item', '=', 'notification_email')->first();

        if ($settings) {
            Mail::send('email.customerwelcomeMail',

                array(
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'subject' => 'Welcome - Expressmed',

                ), function ($message) use ($customer, $settings) {
                    $message->from($settings->value, 'Expressmed');
                    $message->to($customer->email);
                    $message->subject('Welcome - Expressmed');
                });
        }
        return true;
    }
}
