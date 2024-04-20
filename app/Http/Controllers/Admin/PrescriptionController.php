<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use File;

use App\Models\Prescription;
use App\Models\Product;
use App\Models\User;
use App\Models\Generalsetting;

class PrescriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $productsdropdown = Product::orderBy('product_name', 'ASC')->where('status', 'active')->get(['id','product_name']);
        $customerdropdown = User::orderBy('name', 'ASC')->where('status', 'active')->get(['id','name','email']);

        $prescription = Prescription::select('prescriptions.*','users.name as customername','products.product_name','products.product_url','admins.name as approved_by')
            ->join('users','users.id','prescriptions.user_id')->join('products','products.id','prescriptions.product_id')
            ->leftjoin('admins','admins.id','prescriptions.approved_by');

            if($request->has('filter_customer') && $request->filter_customer != ''){
                $prescription->where('prescriptions.user_id', $request->filter_customer);
            }

            if($request->has('filter_product') && $request->filter_product != ''){
                $prescription->where('prescriptions.product_id', $request->filter_product);
            }
            if($request->has('search_keyword') && $request->search_keyword != ''){
                $prescription->where('products.product_name','LIKE','%'.$request->search_keyword.'%')
                ->orWhere('users.name','LIKE','%'.$request->search_keyword.'%');
            }

            if($request->status == 'rejected') {
                $prescription->where('prescriptions.status', 0);
            } elseif($request->status == 'approved') {
                $prescription->where('prescriptions.status', 2);
            } elseif($request->status == 'completed') {
                $prescription->where('prescriptions.status', 3);
            } else {
                $prescription->where('prescriptions.status', 1);
            }

        $prescription = $prescription->latest()->paginate(30);

        return view('admin.prescription.index', compact('prescription','productsdropdown','customerdropdown'))->with('i', (request()->input('page', 1) - 1) * 30 );
    }

    public function destroy(Request $request)
    {
        $prescriptionId = key($request->delete);
        if($prescriptionId){
            $prescription = Prescription::find($prescriptionId);
            if($prescription){
                if($prescription->status == 0){
                    if($prescription->file != ''){
                        $filepath = public_path('/assets/uploads/prescription/').'/'.$prescription->file;
                        File::delete($filepath);
                    }
                    Prescription::find($prescriptionId)->delete();
                    return redirect()->back()->with('success','The prescription deleted successfully.');
                } else {
                    return redirect()->back()->withErrors('Sorry delete failed... Delete allowed only for rejected items.');
                }
            } else {
                return redirect()->back()->withErrors('Sorry delete failed... Prescription details not found.');
            }
        } else {
            return redirect()->back()->withErrors('Sorry delete failed... Prescription id not found.');
        }
    }

    public function manage_prescription(Request $request)
    {

        $validate = Validator::make($request->all(),[
            'prescriptionId' => 'required',
            'quantity' => 'numeric|gte:0',
            'mode' => 'required|in:approve,reject'
        ]);

        if($validate->fails()){
            return response()->json(['result'=>false,'message'=>$validate->errors()->first()]);
        } else {
            $prescription = Prescription::find($request->prescriptionId);
            if($prescription){
                $userDetails = User::find($prescription->user_id);
                if($userDetails){
                    $product = Product::find($prescription->product_id);
                    if($product){
                        $productPrice = (($product->offer_price != 0 ? $product->offer_price : $product->price)) * $request->quantity;

                        $status = ($request->mode == 'approve' ? 2 : 0);
                        $status_msg = ($request->mode == 'approve' ? 'approved' : 'rejected');
                        $approved_by=($request->mode == 'approve' ? Auth::guard('admin')->user()->id : '');

                        Prescription::find($request->prescriptionId)->update(['status'=> $status, 'allowed_qty'=> $request->quantity,'approved_by'=>$approved_by]);


                        $productlink = '';
                        if($status == 2){
                            $productlink = url('/item'.'/'.$product->product_url.'?user_id%5B'.$prescription->user_id.'%5D=&prescription_id%5B'.$prescription->id.'%5D=');
                            // dd($productlink);
                        }

                        //Send the product purchase link by mail to the user--
                        $settings = Generalsetting::where('item', '=', 'notification_email')->first();
                        if($settings){
                            Mail::send('email.prescription_response',
                                array(
                                    'customername' => $userDetails->name,
                                    'adminname' => Auth::guard('admin')->user()->name,
                                    'productname' => $product->product_name,
                                    'quantity' => $request->quantity,
                                    'productprice' => $productPrice,
                                    'link' => $productlink,
                                    'status_mode' => $request->mode,
                                ), function($message) use ($userDetails, $settings, $status_msg) {
                                    $message->from($settings->value, 'Expressmed');
                                    $message->to($userDetails->email);
                                    $message->subject('Prescription '.$status_msg);
                                });
                        }

                        return response()->json(['result'=>true,'message'=>'Successfully '.$status_msg]);
                    } else {
                        return response()->json(['result'=>false,'message'=>'Sorry attempt failed... Product details not found.']);
                    }
                } else {
                    return response()->json(['result'=>false,'message'=>'Sorry attempt failed... User details not found.']);
                }
            } else {
                return response()->json(['result'=>false,'message'=>'Sorry attempt failed... Prescription details not found.']);
            }
        }
    }
}
