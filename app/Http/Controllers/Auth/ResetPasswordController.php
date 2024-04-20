<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Rules\IsValidPassword;
use Mail;

use App\Models\User;
use App\Models\Resetpassword;
use App\Models\Generalsetting;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showuser_resetpassword()
    {
        $data['page_title'] = 'Confirm Your Mail';
        $data['Submit_button'] = 'Confirm Mail';
        $data['type'] = 'mail_confirmation';
        return view('auth.passwords.user_resetpassword', $data);
    }

    public function sentuser_reset(Request $request)
    {
        $this->validate($request, [
            'register_email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
        ]);

        $user = User::where('email', $request->register_email)->first();
        if ($user) {
            //Create Password Reset Token
            Resetpassword::insert([
                'usertype' => 'customer',
                'email' => $request->register_email,
                'token' => Str::random(32),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $tokenData = Resetpassword::where('email', $request->register_email)->latest()->first();
            if($tokenData){
                $settings = Generalsetting::where('item', 'notification_email')->first();
                if($settings){
                    Mail::send('email.passwordresetCustomer',
                        array(
                            'name' => $user->name,
                            'email' => $user->email,
                            'subject' => 'Reset your customer profile password',
                            'token' =>  $tokenData->token,
                        ), function($message) use ($user, $settings)
                        {
                            $message->from($settings->value,'Expressmed');
                            $message->to($user->email);
                            $message->subject('Password reset - Expressmed');
                        });
                }
                return redirect()->back()->with('success','Please check email and follow the instructions. Thank You...');
            } else {
                return redirect()->back()->with('error','Sorry... Your request is failed.');
            }
        } else {
            return redirect()->back()->with('error','You are not registered with us. Please register account. <a href="'. route('register.view').'" >Register</a>');
        }
    }

    public function verifyuser_PasswordReset(Request $request, $email, $key)
    {
        if($email != '' && $key != ''){
            $tokenData = Resetpassword::where('email', $email)->where('token', $key)->first();
            if(!empty($tokenData)){
                $db_time = strtotime($tokenData->created_at);
                $date_exp = strtotime('+1 day', $db_time);
                $date_now = strtotime("now");
                if($date_exp >= $date_now){
                    $data['page_title'] = 'Reset Your Password';
                    $data['Submit_button'] = 'Reset Password';
                    $data['type'] = 'reset_password';
                    return view('auth.passwords.user_resetpassword', $data);
                }else{
                    return redirect()->route('user.reset.password')->withErrors('Token expired. Please try again.');
                }
            }else{
                return redirect()->route('user.reset.password')->withErrors('Token data not matching. Please try again.');
            }
        }else{
            return redirect()->route('user.reset.password')->withErrors('Invalid Credentials. Please try again.');
        }
    }

    public function UserPasswordReset(Request $request, $email, $key)
    {
        if($email != '' && $key != ''){
            $tokenData = Resetpassword::where('email', $email)->where('token', $key)->first();
            if(!empty($tokenData)){
                $db_time = strtotime($tokenData->created_at);
                $date_exp = strtotime('+1 day', $db_time);
                $date_now = strtotime("now");
                if($date_exp >= $date_now){
                    $user = User::where('email', $email)->get()->first();
                    if (!$user) {
                        return redirect()->route('user.reset.password')->with('error','You are not registered with us. Please sign up.');
                    } else {
                        $request->validate([
                            'password' => ['required', new IsValidPassword()],
                            'confirm_password' => 'same:password',
                        ]);
                        User::where('id', $user->id)->update(['password'=> Hash::make($request->password)]);
                        return redirect()->route('user.reset.password')->with('success','Successfully reset your password. You can login with your new password. <a class="login-button" data-toggle="collapse" href="#collapseLogin" role="button" aria-expanded="false" aria-controls="collapseExample">Login</a>');
                    }
                }else{
                    return redirect()->route('user.reset.password')->withErrors('Token expired. Please try again.');
                }
            }else{
                return redirect()->route('user.reset.password')->withErrors('Token data not matching. Please try again.');
            }
        }else{
            return redirect()->route('user.reset.password')->withErrors('Invalid Credentials. Please try again.');
        }
    }

}
