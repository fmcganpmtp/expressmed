<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

use App\Models\Tax;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $taxes = Tax::latest()->paginate(30);
        return view('admin.taxes.index',compact('taxes'))->with('i', ($request->input('page', 1) - 1) * 30);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tax_name' =>  'required',
            'percentage' =>  'required|numeric|gt:0',
        ]);

        $TaxArray['tax_name'] = $request->tax_name;
        $TaxArray['percentage'] = $request->percentage;

        Tax::create($TaxArray);

        return redirect()->route('admin.taxes')->with('success', 'Tax entered successfully');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'tax_nameUpdate' =>  'required',
            'percentageUpdate' =>  'required|numeric|gt:0',
        ]);

        Tax::find($request->tax_id)->update(['tax_name' => $request->tax_nameUpdate, 'percentage' => $request->percentageUpdate]);

        return redirect()->route('admin.taxes')->with('success', 'Tax updated successfully');
    }

    public function changestatus(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];
        if (Auth::guard('admin')->user()){

            $id = $request->id;
            $status = $request->status;

            if($id != '' && $status != ''){
                Tax::find($id)->update([
                    'status'=>$request->status,
                ]);
                $message = "Successfully ".$status." the status";
                $ajax_status = 'success';
            }else{
                $message = "Unable to proceed";
                $ajax_status = 'failed';
            }
        }else{
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status'=>$ajax_status,'message'=>$message );
        return response()->json($return_array);
    }

}
