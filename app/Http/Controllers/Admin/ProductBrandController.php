<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;

use App\Models\Productbrand;
use App\Models\Product;

class ProductBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $data = Productbrand::select('productbrands.*');

        if($request->has('search_keyword')&&$request->has('search_keyword')!=''){
            $data->where('name','LIKE','%'.$request->search_keyword.'%');
        }
        $data=$data->latest()->paginate(30)->appends(request()->except('page'));
        return view('admin.brands.index', compact('data'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' =>  'required|unique:productbrands,name',
        ],[
            'name.required'=>'The Brand has already been taken.',
            'name.unique'=>'The Brand has already been taken.'
        ]);

        $image = $request->file('image');
        $fileName = '';
        if($image){
            $request->validate([
                'image' =>  'mimes:jpeg,jpg,png,gif,webp|max:1000||dimensions:max_width=150,max_height=100',
            ]);

            $fileName = time().'.'.$request->image->extension();
            $request->image->move(public_path('/assets/uploads/brands/'), $fileName);
        }

        Productbrand::create([
            'name'             =>   $request->name,
            'image'            =>   $fileName
        ]);

        return redirect()->route('admin.brands')->with('success', 'Brand Added successfully.');
    }

    public function edit($id = null)
    {
        if($id){
            $data = Productbrand::find($id);
            if($data){
                return view('admin.brands.edit', compact('data'));
            } else {
                return redirect()->back()->withErrors('Sorry.. Brand details not found.');
            }
        } else {
            return redirect()->route('admin.brands')->withErrors('Sorry... Something went wrong.');
        }
    }

    public function update(Request $request, $id = null)
    {
        if($id){
            $Branddetails = Productbrand::find($id);

            if($Branddetails){
                $image = $request->file('image');
                $fileName = '';

                if($image) {
                    $request->validate([
                        'name'    =>  'required|unique:productbrands,name,'.$id,
                        'image'   =>  'mimes:jpeg,jpg,png,gif,webp|max:1000||dimensions:max_width=150,max_height=100'
                    ],[
                        'name.required'=>'The Brand has already been taken.',
                        'name.unique'=>'The Brand has already been taken.'
                    ]);

                    if($Branddetails->image != ''){
                        $image_path = public_path('/assets/uploads/brands/').'/'.$Branddetails->image;
                        File::delete($image_path);
                    }

                    $fileName = time().'.'.$request->image->extension();
                    $request->image->move(public_path('/assets/uploads/brands/'), $fileName);

                    Productbrand::find($id)->update([
                        'name'  =>  $request->name,
                        'image' =>  $fileName
                    ]);
                } else {
                    $request->validate([
                        'name'  =>  'required|unique:productbrands,name,'.$id,
                    ]);
                    Productbrand::find($id)->update([
                        'name'  =>  $request->name,
                    ]);
                }

                return redirect()->route('admin.brands')->with('success', 'Brand details updated successfully');
            } else {
                return redirect()->back()->withErrors('Sorry... Updation failed. Brand details not found.');
            }
        } else {
            return redirect()->back()->withErrors('Sorry... Something went wrong.');
        }

    }

    public function destroy($id = null)
    {
        if($id){
            $Branddetails = Productbrand::find($id);
            if($Branddetails){
                $Brandexist = Product::where('brands', $Branddetails->id)->where('status','active')->exists();
                if(!$Brandexist){
                    if(!empty($Branddetails) && $Branddetails->image != '') {
                        $file_path = public_path('/assets/uploads/brands/').$Branddetails->image;
                        File::delete($file_path);
                    }
                    Productbrand::find($id)->delete();
                    return redirect()->route('admin.brands')->with('success', 'Brand deleted successfully');
                } else {
                    return redirect()->back()->withErrors('Sorry... Delete failed. Brand exists some products.');
                }
            } else {
                return redirect()->back()->withErrors('Sorry... Delete failed. Brand details not found.');
            }
        } else {
            return redirect()->route('admin.brands')->withErrors('Sorry... Something went wrong.');
        }
    }

    public function remove_brandimage(Request $request)
    {
        if($request->id != ''){
            $Brand = Productbrand::find($request->id);
            if($Brand){
                if($Brand->image != ''){
                    $imagefile = public_path('/assets/uploads/brands/').$Brand->image;
                    File::delete($imagefile);
                    Productbrand::find($request->id)->update(['image'=>'']);

                    $returnArray['result'] = true;
                    $returnArray['message'] = 'Brand image removed successfully.';
                } else {
                    $returnArray['result'] = false;
                    $returnArray['message'] = 'Failed. Image not found.';
                }
            } else {
                $returnArray['result'] = false;
                $returnArray['message'] = 'Failed. Brand details not found.';
            }
        } else {
            $returnArray['result'] = false;
            $returnArray['message'] = 'Failed. Brand ID not found.';
        }
        return response()->json($returnArray);
    }
}
