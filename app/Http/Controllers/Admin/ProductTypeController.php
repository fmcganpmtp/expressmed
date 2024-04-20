<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Producttype;
use App\Models\Category;
use App\Models\Product;

class ProductTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        // $ProductTypes = Producttype::latest()->paginate(30);
        $ProductTypes = Category::where('parent_id',0)->latest()->paginate(30);

        return view('admin.products.producttype',compact('ProductTypes'))->with('i', ($request->input('page', 1) - 1) * 30);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'producttype' =>  'required|unique:producttypes,producttype',
        ],['producttype.unique' => 'The product type has already been taken. Try new.']);

        $file = $request->file('image');
        $fileName = '';
        if($file) {
            $this->validate($request, [
                'image' =>  'required|mimes:jpeg,jpg,png,svg|max:1048|dimensions:max_width=50,max_height=50',
            ]);

            $fileName = time().'.'.$request->image->extension();
            $request->image->move(public_path('/assets/uploads/category/'), $fileName);
        }

        $ProducttypeArray['name'] = $request->producttype;
        $ProducttypeArray['description'] = $request->productdescription;
        $ProducttypeArray['status'] ='active';
        $ProducttypeArray['image'] =$fileName;
        $ProducttypeArray['parent_id']=0;
        // $ProducttypeArray['type'] = 'user';

        $InsertID = Category::create($ProducttypeArray)->id;
        if($InsertID){
            return redirect()->route('admin.producttype')->with('success', 'Product type entered successfully');
        } else {
            return redirect()->back()->withErrors('Something went wrong. Product type not uploaded.');
        }
    }

    public function update(Request $request)
    {
        if($request->producttype_id != ''){
            $this->validate($request, [
                'producttype_update' =>  'required|unique:producttypes,producttype,'.$request->producttype_id,
            ],[
                'producttype_update.required' => 'Product type field is required.',
                'producttype_update.unique' => 'The product type has already been taken. Try new.'
            ]);
            $file = $request->file('productimage_update');
        $fileName = '';
        if($file) {
            $this->validate($request, [
                'productimage_update' =>  'required|mimes:jpeg,jpg,png,svg|max:1048|dimensions:max_width=50,max_height=50',
            ],
            [
                'productimage_update.mimes' => 'The product image must be a file of type: jpeg, jpg, png, svg.',
                'productimage_update.dimensions' => 'The product image has invalid image dimensions.'
            ]
        );

            $fileName = time().'.'.$request->productimage_update->extension();
            $request->productimage_update->move(public_path('/assets/uploads/category/'), $fileName);
            $ProducttypeupdateArray['image'] = $fileName;


        }
        $ProducttypeupdateArray['name'] = $request->producttype_update;
        $ProducttypeupdateArray['description'] = $request->productdescription_update;
        $ProducttypeupdateArray['status'] ='active';
        $ProducttypeupdateArray['parent_id']=0;
            Category::find($request->producttype_id)->update($ProducttypeupdateArray);
            return redirect()->route('admin.producttype')->with('success', 'Product type successfully updated.');
        }
    }

    public function destroy($id)
    {
        $Producttype = Category::find($id);

        if($Producttype){
            $NotDeletable = Product::where('producttypeid', $id)->where('status','active')->exists();

            if($NotDeletable){
                return redirect()->back()->withErrors('Something went wrong. You cannot delete this product type. Products created in this type.');
            } else {
                Category::find($id)->delete();
                return redirect()->route('admin.producttype')->with('success', 'Product type deleted successfully.');
            }
        } else {
            return redirect()->back()->withErrors('Delete failed. Product type not found.');
        }
    }
}
