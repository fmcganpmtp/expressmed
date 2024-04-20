<?php

namespace App\Http\Controllers\CustomerSupport;

use Illuminate\Http\Request;
use App\Rules\MatchOldPasswordcustomersupport;
use Illuminate\Support\Facades\Hash;
use Auth;
use File;
use App\Http\Controllers\Controller;
use App\Models\CustomerSupport;
use App\Models\Customersupport_chat;
use App\Models\Customersupport_chatmessages;

class CustomersupportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:customersupport');
    }

    public function index()
    {
        //--Dashboard of the customer support coDe--
    }

    public function profile()
    {
        $customersupport = Auth::guard('customersupport')->user();
        return view('customersupport.cs_profile',compact('customersupport'));
    }

    public function updateprofile(Request $request)
    {
        $customersupport_id = Auth::guard('customersupport')->user()->id;

        if($customersupport_id){
            $customersupport = CustomerSupport::find($customersupport_id);
            if($customersupport){
                $this->validate($request, [
                    'name' => 'required',
                    'cs_email' => 'required|unique:admins,email,'.$customersupport_id,
                ]);
                $file = $request->file('profile_pic');

                if($file) {
                    $this->validate($request, [
                        'profile_pic' =>  'required|mimes:jpeg,jpg,png,svg|max:2048',
                    ]);

                    if($customersupport->profile_pic != ''){
                        $image_path = public_path('/assets/uploads/customer_support/').'/'.$customersupport->profile_pic;
                        File::delete($image_path);
                    }

                    $file = $request->file('profile_pic');

                    $fileName = 'cs_profile_'.time().'.'.$request->profile_pic->extension();

                    $request->profile_pic->move(public_path('/assets/uploads/customer_support/'), $fileName);

                    CustomerSupport::find($customersupport_id)->update([
                        'name' => $request->name,
                        'email' => $request->cs_email,
                        'phone' => $request->phone,
                        'profile_pic' => $fileName,
                    ]);
                } else {
                    CustomerSupport::find($customersupport_id)->update([
                        'name' => $request->name,
                        'email' => $request->cs_email,
                        'phone' => $request->phone,
                    ]);
                }

                return redirect()->route('customersupport.profile')->with('success','Customer Support details updated successfully.');
            } else {
                return redirect()->back()->with('Sorry... Update failed. Customer support details not found.');
            }
        } else {
            return redirect()->back()->with('Your account not login. Please login your account.');
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPasswordcustomersupport],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        CustomerSupport::find(Auth::guard('customersupport')->user()->id)->update(['password'=>Hash::make($request->new_password), 'password_string'=>$request->new_password]);

        return redirect()->route('customersupport.profile')->with('success','Succesfully updated your password');
    }

    public function manage_chat(Request $request){
        if(Auth::guard('customersupport')->user()){
            $customersupport_id = Auth::guard('customersupport')->user()->id;

            $available_Chats = Customersupport_chat::where('customersupport_chats.status','active')->get();

            $attended_chat = Customersupport_chat::where('customersupport_id', $customersupport_id)->where('customersupport_chats.status','attend')->first();

            $chat_messages = array();
            if($attended_chat){
                $chat_messages = Customersupport_chatmessages::join('customersupport_chats as CSC', 'CSC.id', 'customersupport_chatmessages.chat_id')
                                ->select('customersupport_chatmessages.*', 'CSC.customer_name')
                                ->where('chat_id', $attended_chat->id)->get();
            }

            return view('customersupport.manage_chat', compact('available_Chats', 'attended_chat', 'chat_messages'));
        }
    }

    public function chat_attend(Request $request){
        $returnArray['result'] = 'failed';
        if(Auth::guard('customersupport')->user()){
            $customersupportID = Auth::guard('customersupport')->user()->id;

            $cs_available = Customersupport_chat::where('customersupport_id', $customersupportID)->where('status', 'attend')->exists();
            if($cs_available){
                $returnArray['result'] = 'failed';
                $returnArray['message'] = 'Sorry... cannot attend this chat. You are busy in conversation with other customer.';
            } else {
                $customerchat = Customersupport_chat::find($request->chatID);
                if($customerchat){
                    Customersupport_chat::find($request->chatID)->update([
                        'customersupport_id' => $customersupportID,
                        'status' => 'attend'
                    ]);

                    $chat_messages = Customersupport_chatmessages::join('customersupport_chats as CSC', 'CSC.id', 'customersupport_chatmessages.chat_id')
                                    ->select('customersupport_chatmessages.*', 'CSC.customer_name')
                                    ->where('chat_id', $request->chatID)->get();

                    $returnArray['result'] = 'success';
                    $returnArray['message'] = 'Successfull attended the chat.';
                    $returnArray['customername'] = $customerchat->customer_name;
                    $returnArray['chat_messages'] = $chat_messages;
                } else {
                    $returnArray['result'] = 'failed';
                    $returnArray['message'] = 'Customers Chat not found.';
                }
            }
        } else {
            $returnArray['result'] = 'failed';
            $returnArray['message'] = 'You are not logged. Please login account.';
        }

        return response()->json($returnArray);
    }

    public function load_message(Request $request){
        if(Auth::guard('customersupport')->user()){
            if($request->chatID != '' && $request->chatID != 0){
                $customersupportID = Auth::guard('customersupport')->user()->id;

                $customerchat = Customersupport_chat::where('customersupport_id', $customersupportID)->where('status', 'attend')->first();
                if($customerchat){
                    $chat_messages = Customersupport_chatmessages::join('customersupport_chats as CSC', 'CSC.id', 'customersupport_chatmessages.chat_id')
                                    ->select('customersupport_chatmessages.*', 'CSC.customer_name')
                                    ->where('chat_id', $request->chatID);
                                    if($request->chatmsg_id != ''){
                                        $chat_messages->where('customersupport_chatmessages.id', '>', $request->chatmsg_id);
                                    }
                    $chat_messages = $chat_messages->get();

                    $returnArray['result'] = 'success';
                    $returnArray['chat_id'] = $request->chatID;
                    $returnArray['chat_messages'] = $chat_messages;
                } else {
                    $returnArray['result'] = 'failed';
                    $returnArray['message'] = 'Chat messages not found.';
                }
            } else {
                $returnArray['result'] = 'failed';
                $returnArray['message'] = 'Chat id is not valid';
            }
        } else {
            $returnArray['result'] = 'failed';
            $returnArray['message'] = 'You are not logged. Please login your account.';
        }
        return response()->json($returnArray);
    }

    public function chat_disconnect(Request $request){
        if(Auth::guard('customersupport')->user()){
            if($request->chatID != '' && $request->chatID != 0){
                $customersupportID = Auth::guard('customersupport')->user()->id;

                $customerchat = Customersupport_chat::where('customersupport_id', $customersupportID)->where('status', 'attend')->first();

                if($customerchat){
                    Customersupport_chat::find($customerchat->id)->update(['status' => 'disabled']);

                    $returnArray['result'] = 'success';
                    $returnArray['message'] = 'Chat disconnected successfully.';
                } else {
                    $returnArray['result'] = 'failed';
                    $returnArray['message'] = 'Invalid request. Chat is not attended by you.';
                }
            } else {
                $returnArray['result'] = 'failed';
                $returnArray['message'] = 'Invalid request';
            }
        } else {
            $returnArray['result'] = 'failed';
            $returnArray['message'] = 'You are not logged. Please login your account.';
        }

        return $returnArray;
    }
}
