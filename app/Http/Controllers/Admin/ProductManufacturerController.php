<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductManufacturer;
use Illuminate\Http\Request;
use File;

class ProductManufacturerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $productmanufacturer = ProductManufacturer::select('product_manufacturers.*');
        if ($request->has('search_keyword') && $request->has('search_keyword') != '') {
            $productmanufacturer->where('name', 'LIKE', '%' . $request->search_keyword . '%');
        }
        $productmanufacturers = $productmanufacturer->orderBy('product_manufacturers.name', 'ASC')->paginate(30);
        return view('admin.productmanufacturer.index', compact('productmanufacturers'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        return view('admin.productmanufacturer.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:product_manufacturers,name',
        ]);
        $image = $request->file('image');
        $fileName = '';
        if ($image) {
            $request->validate([
                'image' => 'mimes:jpeg,jpg,png,gif,webp|max:1000||dimensions:max_width=150,max_height=100',
            ]);
            $fileName = time() . '.' . $image->extension();
            $request->image->move(public_path('/assets/uploads/manufacturers/'), $fileName);

        }

        ProductManufacturer::create([
            'name' => $request->name,
            'image' => $fileName,
            'add_to_home' => $request->add_to_home,

        ]);

        return redirect()->route('admin.manufacturers')->with('success', 'Product manufacturer Added successfully.');
    }

    public function edit($id)
    {
        $productmanufacturer = ProductManufacturer::find($id);
        if ($productmanufacturer) {
            return view('admin.productmanufacturer.edit', compact('productmanufacturer'));
        } else {
            return redirect()->back()->withErrors('Sorry.. Product manufacturer details not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $productmanufacturer = ProductManufacturer::find($id);

        if ($productmanufacturer) {
            $image = $request->file('image');
            $fileName = '';
            if ($image) {
                $request->validate([
                    'name' => 'required|unique:product_manufacturers,name,' . $id,
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|max:1000||dimensions:max_width=150,max_height=100',
                ], [
                    'name.required' => 'The manufacturers name required.',
                    'name.unique' => 'The manufacturers has already been taken.',
                ]);

                if ($productmanufacturer->image != '') {
                    $image_path = public_path('/assets/uploads/manufacturers/') . '/' . $productmanufacturer->image;
                    File::delete($image_path);
                }

                $fileName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('/assets/uploads/manufacturers/'), $fileName);

                ProductManufacturer::find($id)->update([
                    'name' => $request->name,
                    'image' => $fileName,
                    'add_to_home' => $request->add_to_home,
                ]);
            } else {

                $request->validate([
                    'name' => 'required|unique:product_manufacturers,name,' . $id,
                ]);

                ProductManufacturer::find($id)->update([
                    'name' => $request->name,
                    'add_to_home' => $request->add_to_home,

                ]);
            }

            return redirect()->route('admin.manufacturers')->with('success', 'Product manufacturer details updated successfully.');
        } else {
            return redirect()->back()->withErrors('Sorry... Updation failed. Product manufacturer details not found.');
        }

    }

    public function destroy($id)
    {
        $productmanufacturer = ProductManufacturer::find($id);
        if ($productmanufacturer) {
            $productmanufacturerexist = Product::whereRaw("find_in_set(" . $id . ", manufacturer)")->where('status', 'active')->exists();
            if (!$productmanufacturerexist) {
                ProductManufacturer::find($id)->delete();
                return redirect()->route('admin.manufacturers')->with('success', 'Product manufacturer deleted successfully');
            } else {
                return redirect()->back()->withErrors('Sorry... Delete failed. Product manufacturer existing in products.');
            }
        } else {
            return redirect()->back()->withErrors('Sorry... Delete failed. Product manufacturer details not found.');
        }
    }
    public function remove_manufacturersimage(Request $request)
    {
        if ($request->id != '') {
            $manufacturer = ProductManufacturer::find($request->id);
            if ($manufacturer) {
                if ($manufacturer->image != '') {
                    $imagefile = public_path('/assets/uploads/manufacturers/') . $manufacturer->image;
                    File::delete($imagefile);
                    ProductManufacturer::find($request->id)->update(['image' => '']);

                    $returnArray['result'] = true;
                    $returnArray['message'] = 'Manufacturers image removed successfully.';
                } else {
                    $returnArray['result'] = false;
                    $returnArray['message'] = 'Failed. Image not found.';
                }
            } else {
                $returnArray['result'] = false;
                $returnArray['message'] = 'Failed. Manufacturers details not found.';
            }
        } else {
            $returnArray['result'] = false;
            $returnArray['message'] = 'Failed. Manufacturers ID not found.';
        }
        return response()->json($returnArray);
    }
    //
}
