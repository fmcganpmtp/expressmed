<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use DB;

use File;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $categories = Category::select('categories.*', 'category.name as parent_category')
            ->leftJoin('categories as category', 'category.id', '=', 'categories.parent_id');
        if ($request->has('search_keyword') && $request->has('search_keyword') != '') {
            $categories->where('categories.name', 'LIKE', '%' . $request->search_keyword . '%');
        }
        $category = $categories->orderBy('categories.name', 'ASC')->paginate(30)->appends(request()->except('page'));
        return view('admin.category.index', compact('category'))->with('i', ($request->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        $parentCategories = Category::where('status', 'active')->where('parent_id', 0)->get();
        return view('admin.category.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories',
        ]);
        $file = $request->file('image');
        $fileName = '';
        if ($file) {
            $this->validate($request, [
                'image' => 'required|mimes:jpeg,jpg,png,svg|max:1048|dimensions:max_width=50,max_height=50',
            ]);

            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('/assets/uploads/category/'), $fileName);
        }

        $parent_id = 0;
        if ($request->selected_category) {
            $parent_id = $request->selected_category;
        }

        $data1 = Category::create([
            'name' => $request->name,
            'parent_id' => $parent_id,
            'image' => $fileName,
            'description' => $request->description,
        ]);
        return redirect()->route('admin.categories')->with('success', 'Category entered successfully');
    }

    public function edit($id = null)
    {
        $categories = Category::find($id);
        if ($categories) {
            if ($categories->status == 'active') {
                $parentCategories = Category::where('status', 'active')->where('parent_id', 0)->get();
                return view('admin.category.edit', compact('categories', 'parentCategories'));
            } else {
                return redirect()->back()->withErrors('Cannot edit this category. Not able to edit deleted category.');
            }
        } else {
            return redirect()->back()->withErrors('Cannot edit this category. Category details not found.');
        }
    }

    public function update(Request $request, $id = null)
    {
        $categoryDetails = Category::find($id);

        if ($categoryDetails) {
            $file = $request->file('image');
            if ($file) {
                $this->validate($request, [
                    'image' => 'required|mimes:jpeg,jpg,png,svg|max:1048|dimensions:max_width=50,max_height=50',
                    'name' => 'required|unique:categories,name,' . $id,
                ]);
                if ($categoryDetails->image != '') {
                    $imagefile = public_path('/assets/uploads/category/') . '/' . $categoryDetails->image;
                    File::delete($imagefile);
                }
                $fileName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('/assets/uploads/category/'), $fileName);
                Category::find($id)->update([
                    'image' => $fileName,
                    'parent_id' => $request->selected_category,
                    'name' => $request->name,
                    'description' => $request->description,
                ]);
            } else {
                $this->validate($request, [
                    'name' => 'required|unique:categories,name,' . $id,
                ]);
                Category::find($id)->update([
                    'parent_id' => $request->selected_category,
                    'name' => $request->name,
                    'description' => $request->description,
                ]);
            }
            return redirect()->route('admin.categories')->with('success', 'Category updated successfully');
        } else {
            return redirect()->back()->withErrors('Sorry.. Update failed. Category details not found.');
        }
    }

    public function destroy($id = null)
    {
        $categoryDetails = Category::find($id);

        if ($categoryDetails) {
            $notDeletable = Category::where('parent_id', $categoryDetails->id)->where('status', 'active')->exists();
            if (!$notDeletable) {
                Category::find($id)->update([
                    'status' => 'deleted',
                ]);
                return redirect()->route('admin.categories')->with('success', 'Category deleted successfully');
            } else {
                return redirect()->back()->withErrors('Delete failed. Cannot delete the category. Child category found in this category.');
            }
        } else {
            return redirect()->back()->withErrors('Sorry.. Delete failed. Category details not found.');
        }
    }

    public function remove_image(Request $request)
    {
        if ($request->id != '') {
            $category = Category::find($request->id);
            if ($category) {
                if ($category->image != '') {
                    $imagefile = public_path('/assets/uploads/category/') . $category->image;
                    File::delete($imagefile);
                    Category::find($request->id)->update(['image' => '']);

                    $returnArray['result'] = true;
                    $returnArray['message'] = 'Category image removed successfully.';
                } else {
                    $returnArray['result'] = false;
                    $returnArray['message'] = 'Failed. Image not found in this category.';
                }
            } else {
                $returnArray['result'] = false;
                $returnArray['message'] = 'Failed. Category details not found.';
            }
        } else {
            $returnArray['result'] = false;
            $returnArray['message'] = 'Failed. Category ID not found.';
        }
        return response()->json($returnArray);
    }

    public function categoriesoffer_update(Request $request, $id = null)
    {

        $categoryDetails = Category::find($request->category_id);

        if ($categoryDetails) {
            // $this->validate($request, [
            //     'offer_percentage' =>  'required|numeric|gte:0|lte:100',
            // ]);
            $request->validate([
                'offer_percentage' => 'nullable|integer|gte:0|lte:99',
            ]);
            $offer_percent=$request->offer_percentage;
            Category::find($request->category_id)->update([
                'offer_percentage' => $request->offer_percentage,
            ]);
            $child_categoryIds = [];
            $all_categoryIds = [];
            array_push($all_categoryIds, $request->category_id);
            array_push($child_categoryIds, $request->category_id);
            $obj_category = new Category();
            $child_category = $obj_category->getCategories($all_categoryIds);
            $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);
            if(($offer_percent==0)||($offer_percent=='')){
                $request->offer_percentage=100;
            }
            Product::whereIn('category_id',$child_categoryIds)->update([
             'offer_price'=>DB::raw('price-((price*'.$request->offer_percentage.')/100)')
            ]);

            return response()->json(['success' => 'Category offer updated successfully']);

        } else {
            return response()->json(['error' => 'Sorry.. Update failed. Category details not found.']);


        }

    }
    private function getCategoryIds($child_category, $child_categoryIds)
    {
        foreach ($child_category as $value) {
            array_push($child_categoryIds, $value->id);

            if ($value->child_categories) {
                $child_categoryIds = $this->getCategoryIds($value->child_categories, $child_categoryIds);
            }
        }
        return $child_categoryIds;
    }

}
