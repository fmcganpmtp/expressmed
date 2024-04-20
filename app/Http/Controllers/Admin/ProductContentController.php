<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Productcontent;
use App\Models\Product;

class ProductContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $productcontent=Productcontent::select('productcontents.*');
        if($request->has('search_keyword')&&$request->has('search_keyword')!=''){
            $productcontent->where('name','LIKE','%'.$request->search_keyword.'%');
        }
        $productcontents=$productcontent->orderBy('productcontents.name','ASC')->paginate(30)->appends(request()->except('page'));

        return view('admin.productcontent.index', compact('productcontents'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        return view('admin.productcontent.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' =>  'required|unique:productcontents,name',
        ]);

        Productcontent::create([
            'name'             =>   $request->name,
        ]);

        return redirect()->route('admin.productcontent')->with('success', 'Product content Added successfully.');
    }

    public function edit($id)
    {
        $productcontents = Productcontent::find($id);
        if($productcontents){
            return view('admin.productcontent.edit', compact('productcontents'));
        } else {
            return redirect()->back()->withErrors('Sorry.. Product content details not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $productcontents = Productcontent::find($id);

        if($productcontents){
            $request->validate([
                'name'    =>  'required|unique:productcontents,name,'.$id,
            ]);

            Productcontent::find($id)->update([
                'name'  =>  $request->name,
            ]);

            return redirect()->route('admin.productcontent')->with('success', 'Product content details updated successfully.');
        } else {
            return redirect()->back()->withErrors('Sorry... Updation failed. Product content details not found.');
        }

    }

    public function destroy($id)
    {
        $productcontents = Productcontent::find($id);
        if($productcontents){
            $productcontentexist = Product::whereRaw("find_in_set(".$id.", productcontent_id)")->where('status','active')->exists();
            if(!$productcontentexist){
                Productcontent::find($id)->delete();
                return redirect()->route('admin.productcontent')->with('success', 'Product content deleted successfully');
            } else {
                return redirect()->back()->withErrors('Sorry... Delete failed. Product content existing in products.');
            }
        } else {
            return redirect()->back()->withErrors('Sorry... Delete failed. Product content details not found.');
        }
    }
}
