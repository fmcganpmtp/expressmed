<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

use App\Models\User;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request){

        $users = User::select('users.*');

        if($request->has('search_keyword')&&$request->has('search_keyword')!=''){
            $users->where('name','LIKE','%'.$request->search_keyword.'%')
                  ->orwhere('email','LIKE','%'.$request->search_keyword.'%')
                  ->orwhere('phone','LIKE','%'.$request->search_keyword.'%');
        }
        $users=$users->latest()->paginate(10)->appends(request()->except('page'));
        return view('admin.customers.index',compact('users'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function show($id){
        if($id){
            $CustomerDetails = User::where('id', $id)->first();
            return view('admin.customers.show', compact('CustomerDetails'));
        }
    }

    public function changeStatus(Request $request){
        $ajax_status = '';
        $message = '';
        $return_array = [];
        if (Auth::guard('admin')->user()->id){
            $user_id = Auth::guard('admin')->user()->id;
            if(empty($user_id)){
                $message = "Please login into your account and try again";
                $ajax_status = 'failed';
            }else{
                $id = $request->id;
                $status = $request->status;
                if($id != '' && $status != ''){
                    User::find($id)->update([
                        'status'=>$request->status,
                    ]);
                    $message = "Successfully updated";
                    $ajax_status = 'success';
                }else{
                    $message = "Unable to proceed";
                    $ajax_status = 'failed';
                }
            }
        }else{
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status'=>$ajax_status,'message' =>$message );
        return response()->json($return_array);
    }
}
