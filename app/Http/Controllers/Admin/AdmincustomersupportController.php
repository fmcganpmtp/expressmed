<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use File;
use Auth;

use App\Models\CustomerSupport;
use App\Models\Generalsetting;

class AdmincustomersupportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $CustomerSupports = CustomerSupport::latest()->paginate(30);

        return view('admin.admin_customersupport.index', compact('CustomerSupports'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        return view('admin.admin_customersupport.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:customer_support,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|min:11|numeric',
        ]);

        $file = $request->file('profile_pic');
        $fileName = '';
        if($file) {
            $this->validate($request, [
                'profile_pic' =>  'mimes:jpeg,jpg,png,svg|max:2048',
            ]);
            $fileName = 'cs_profile_'.time().'.'.$request->profile_pic->extension();
            $request->profile_pic->move(public_path('/assets/uploads/customer_support/'), $fileName);
        }

        CustomerSupport::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_string' => $request->password,
            'phone' => $request->phone,
            'profile_pic' => $fileName,
            'status' => $request->status,
        ]);

        // Login details sent by mail to customer support--
        // $settings = Generalsetting::where('item', '=', 'notification_email')->first();
        // if($settings){
        //     Mail::send('email.mail_notification',
        //         array(
        //             'mode' => 'customersupport_create',
        //             'usertype' => 'customersupport',
        //             'customersupportname' => $request->name,
        //             'username' => $request->email,
        //             'password' => $request->password,
        //             'subject' => 'Your Customer Support account have been created',
        //         ), function($message) use ($request, $settings){
        //             $message->from($settings->value, 'ShopeOn');
        //             $message->to($request->email);
        //             $message->subject('Welcome to ShopeOn. Your Customer Support account have been created');
        //         });
        // }

        return redirect()->route('customersupport.index')->with('success', 'Customer Support added successfully.');
    }

    public function edit($id)
    {
        $customersupport = CustomerSupport::find($id);
        if($customersupport) {
            return view('admin.admin_customersupport.edit', compact('customersupport'));
        } else {
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $customersupport = CustomerSupport::find($id);
        if($customersupport) {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:customer_support,email,'.$id,
                'phone' => 'nullable|min:11|numeric',
            ]);

            $updateArray['name'] = $request->name;
            $updateArray['email'] = $request->email;

            if($request->password != ''){
                $this->validate($request, [
                    'password' => 'required|min:6',
                ]);
                $updateArray['password'] = Hash::make($request->password);
                $updateArray['password_string'] = $request->password;
            }

            $updateArray['phone'] = $request->phone;
            $updateArray['status'] = $request->status;

            $file = $request->file('profile_pic');
            if($file) {
                $this->validate($request, [
                    'profile_pic' =>  'mimes:jpeg,jpg,png,svg,webp|max:2048',
                ]);

                if($customersupport->profile_pic != ''){
                    $image_path = public_path('/assets/uploads/customer_support/').'/'.$customersupport->profile_pic;
                    File::delete($image_path);
                }

                $fileName = 'cs_profile_'.time().'.'.$request->profile_pic->extension();
                $request->profile_pic->move(public_path('/assets/uploads/customer_support/'), $fileName);

                $updateArray['profile_pic'] = $fileName;
            }

            CustomerSupport::find($id)->update($updateArray);

            // Login details sent by mail to customer support--
            // $settings = Generalsetting::where('item', '=', 'notification_email')->first();
            // if($settings){
            //     Mail::send('email.mail_notification',
            //         array(
            //             'mode' => 'customersupport_create',
            //             'usertype' => 'customersupport',
            //             'customersupportname' => $request->name,
            //             'username' => $request->email,
            //             'password' => $request->password,
            //             'subject' => 'Your Customer Support account have been created',
            //         ), function($message) use ($request, $settings){
            //             $message->from($settings->value, 'ShopeOn');
            //             $message->to($request->email);
            //             $message->subject('Welcome to ShopeOn. Your Customer Support account have been created');
            //         });
            // }

            return redirect()->route('customersupport.index')->with('success', 'Customer Support details updated successfully.');
        } else {
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $customersupport = CustomerSupport::find($id);
        if($customersupport) {
            if($customersupport->profile_pic != ''){
                $image_path = public_path('/assets/uploads/customer_support/').'/'.$customersupport->profile_pic;
                File::delete($image_path);
            }
            CustomerSupport::find($id)->delete();
            return redirect()->route('customersupport.index')->with('success', 'Customer Support details deleted successfully.');
        } else {
            return redirect()->back()->withErrors('Sorry... delete failed. Customer support details not found.');
        }
    }

    public function update_status(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];
        if (Auth::guard('admin')->user()->id){
            $user_id = Auth::guard('admin')->user()->id;
            $id = $request->id;
            $status = $request->status;
            if($id != '' && $status != ''){
                CustomerSupport::find($id)->update([
                    'status'=>$request->status,
                ]);
                $message = "Successfully updated";
                $ajax_status = 'success';
            } else {
                $message = "Unable to proceed";
                $ajax_status = 'failed';
            }
        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status'=>$ajax_status,'message'=>$message );
        return response()->json($return_array);
    }
}
