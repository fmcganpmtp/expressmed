<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

use App\Models\Productsupplier;
use App\Models\Product;

class ProductSupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $suppliers = Productsupplier::select('productsuppliers.*');
        if($request->has('search_keyword')&&$request->has('search_keyword')!=''){
            $suppliers->where('name','LIKE','%'.$request->search_keyword.'%');
        }
        $supplier = $suppliers->orderBy('name','ASC')->paginate(30)->appends(request()->except('page'));
        return view('admin.productsupplier.index', compact('supplier'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        return view('admin.productsupplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' =>  'required|unique:productsuppliers,name',
            'email' =>  'nullable|email|unique:productsuppliers,email',
            'phone_number'=>'nullable|regex:/^[-0-9\s\+]+$/|unique:productsuppliers,phone',
        ]);
        Productsupplier::create([
            'name'             => $request->name,
            'address'          => $request->address,
            'email'            => $request->email,
            'phone'            => $request->phone_number,
        ]);

        return redirect()->route('admin.supplier')->with('success', 'Supplier Added successfully.');
    }

    public function edit($id)
    {
        $supplier = Productsupplier::find($id);
        if($supplier){
            return view('admin.productsupplier.edit', compact('supplier'));
        } else {
            return redirect()->back()->withErrors('Sorry.. Supplier not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $supplier = Productsupplier::find($id);

        if($supplier){
            $request->validate([
                'name'    =>  'required|unique:productsuppliers,name,'.$id,
                'email'    =>  'nullable|email|unique:productsuppliers,email,'.$id,
                'phone_number'=>'nullable|regex:/^[-0-9\s\+]+$/|unique:productsuppliers,phone,' . $id,
            ]);

            Productsupplier::find($id)->update([
                'name'  =>  $request->name,
                'address'          => $request->address,
                'email'            => $request->email,
                'phone'            => $request->phone_number,
            ]);

            return redirect()->route('admin.supplier')->with('success', 'Supplier updated successfully.');
        } else {
            return redirect()->back()->withErrors('Sorry... Updation failed. Supplier not found.');
        }
    }

    public function destroy($id)
    {
        $supplier = Productsupplier::find($id);
        if($supplier){
            $supplierexist = Product::whereRaw('FIND_IN_SET(?,supplier)', [$id])->where('status','active')->exists();
            if(!$supplierexist){
                Productsupplier::find($id)->delete();
                return redirect()->route('admin.supplier')->with('success', 'Supplier deleted successfully');
            } else {
                return redirect()->back()->withErrors('Sorry... Delete failed. Supplier existing in products.');
            }
        } else {
            return redirect()->back()->withErrors('Sorry... Delete failed. Supplier not found.');
        }
    }
}
