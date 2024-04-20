<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\MedicineUse;
use App\Models\Product;
use App\Models\Productbrand;
use App\Models\Productcontent;
use App\Models\ProductManufacturer;
use App\Models\ProductMedicineuse;
use App\Models\ProductReview;
use App\Models\Productsupplier;
use App\Models\Producttype;
use App\Models\Product_image;
use App\Models\Tax;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Image;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $products = Product::select('products.*', 'product_images.product_image', 'type1.name as producttype', 'type2.name as category', 'productbrands.name as brand', 'product_manufacturers.name as manufacturer')
            ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
            ->leftjoin('producttypes', 'producttypes.id', 'products.producttypeid')
            ->leftJoin('productbrands', 'productbrands.id', '=', 'products.brands')
            ->leftJoin('categories as type1', 'type1.id', '=', 'products.producttypeid')
            ->leftJoin('categories as type2', 'type2.id', '=', 'products.category_id')
            ->leftJoin('medicine_uses', 'medicine_uses.id', '=', 'products.medicine_use')
            ->leftJoin('product_manufacturers', 'product_manufacturers.id', '=', 'products.manufacturer');
        // ->leftJoin('medicine_uses', 'medicine_uses.id', '=', 'products.medicine_use');

        // if ($request->has('filter_brand') && $request->filter_brand != '') {
        //     $products->where('products.brands', '=', $request->filter_brand);
        // }

        if ($request->has('filter_type') && $request->filter_type != '') {
            $products->where('products.producttypeid', '=', $request->filter_type);
        }
        if ($request->has('filter_category') && $request->filter_category != '') {
            $products->where('products.category_id', '=', $request->filter_category);
        }

        if ($request->has('price_filter') && $request->price_filter != '') {
            $price_range = $request->price_filter;
            global $first_amount;
            global $second_amt;
            if ($price_range != '') {
                $price_set = (explode("-", $price_range));
                $first_amount = $price_set[0];
                $second_amt = $price_set[1];
            }
            $products->whereBetween('products.price', [$first_amount, $second_amt]);
        }

        if ($request->has('search_term') && $request->search_term != '') {
            $pc_id = '';
            $content_ids = Productcontent::select('productcontents.id')
                ->where('productcontents.name', 'LIKE', '%' . $request->search_term . '%')->get();
            // foreach ($content_ids as $content_id) {
            // $pc_id=array();

            // $pc_id[]=$content_id->id;
            //     $pc_id = $content_id->id;

            // }
            //    $pc_ids=implode(',', $pc_id);
            $products->where(function ($query) use ($request, $content_ids, $products) {
                $query->where('products.product_name', 'LIKE', '%' . $request->search_term . '%')
                    ->orWhere('medicine_uses.name', 'LIKE', '%' . $request->search_term . '%')
                    ->orWhere('product_manufacturers.name', 'LIKE', '%' . $request->search_term . '%');
                if (count($content_ids) > 0) {
                    foreach ($content_ids as $content_id) {
                        $products->orWhereRaw("find_in_set('" . $content_id->id . "',products.productcontent_id)");
                    }
                }

            });

            //$products->where('products.product_name', 'LIKE', '%' . $request->search_term . '%')
            // ->orWhere('medicine_uses.name', 'LIKE', '%' . $request->search_term . '%');
            //if(count($content_ids)>0){
            ///foreach ($content_ids as $content_id) {
            ////$products->orWhereRaw("find_in_set('" . $content_id->id . "',products.productcontent_id)");
            // }
            //}

        }

// (A) SETTINGS
        // $source = asset('assets/uploads/products/medi_prod_Img_1641564713_0.jpg'); // SOURCE IMAGE
        // // dd($source);
        // $target = asset('assets/uploads/products/medi_prod_Img_1641564713_0.jpg'); // WATERMARKED IMAGE
        // $quality = 60; // WATERMARKED IMAGE QUALITY (0 to 100)
        // $copytxt = "Copyright"; // COPYRIGHT TEXT
        // $font = "C:\Windows\Fonts\arial.ttf"; // MAKE SURE PATH IS CORRECT!
        // $fontsize = 18; // FONT SIZE

// (B) CREATE IMAGE OBJECT
        // $img = imagecreatefromjpeg($source);

// (C) POSITION CALCULATIONS
        // (C1) SOURCE IMAGE DIMENSIONS
        // $widthS = imagesx($img);
        // $heightS = imagesy($img);

// (C2) TEXT BOX DIMENSIONS
        // $sizeT = imagettfbbox($fontsize, 0, $font, $copytxt);
        // $widthT = max([$sizeT[2], $sizeT[4]]) - min([$sizeT[0], $sizeT[6]]);
        // $heightT = max([$sizeT[5], $sizeT[7]]) - min([$sizeT[1], $sizeT[3]]);

// (C3) CENTER POSITION
        // $posX = CEIL(($widthS - $widthT) / 2);
        // $posY = CEIL(($heightS - $heightT) / 2);
        // if ($posX < 0 || $posY < 0) {exit("Text is too long");} // OPTIONAL ERROR HANDLE

// (D) WRITE TEXT TO IMAGE
        // $fontcolor = imagecolorallocatealpha($img, 255, 0, 0, 30);
        // imagettftext($img, $fontsize, 0, $posX, $posY, $fontcolor, $font, $copytxt);

// (E) SAVE TO FILE
        // imagejpeg($img, $target, $quality);
        // echo "Saved to $target";

//    $img->save();

        $products = $products->where('products.status', 'active')->orwhere('products.status', 'review')->orderBy('products.product_name', 'asc')->paginate(30);

        $brand = Productbrand::orderBy('name', 'asc')->get();
        $category = Category::orderBy('name', 'asc')->get();
        // $Producttypes = Producttype::orderBy('type', 'asc')->orderBy('producttype', 'asc')->get();
        $Producttypes = Category::where('parent_id', 0)->orderBy('name', 'asc')->get();

        return view('admin.products.index', compact('products', 'brand', 'category', 'Producttypes'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        $medical_uses = MedicineUse::orderBy('name', 'asc')->get();
        $parentCategories = Category::where('parent_id', 0)->where('status', 'active')->orderBy('name', 'asc')->get();
        $Producttype = Producttype::all();
        $manufacturers = ProductManufacturer::orderBy('name', 'asc')->get();

        $Taxes = Tax::where('status', 'active')->get();

        return view('admin.products.create', compact('parentCategories', 'Producttype', 'Taxes', 'medical_uses', 'manufacturers'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('admin')->user()) {
            $this->validate($request, [
                'product_name' => 'required',
                'producttype' => 'required',
                'productprice' => 'required|numeric|gt:0',
                'productofferprice' => 'numeric|nullable|lt:productprice',
                'image.*' => 'nullable|mimes:jpeg,jpg,png,svg|max:1024',
            ], [
                'product_name.required' => 'The Product Name field is required.',
                'producttype.required' => 'The Product Type field is required.',
                'productprice.required' => 'The Product Price field is required.',
                'productprice.numeric' => 'Please Enter valid price.',
                'productprice.gt' => 'Please Enter valid price. Zero not allowed.',
                'productofferprice.numeric' => 'Please Enter valid offer price.',
                'image.*.mimes' => 'The Product image must be a file of type: jpeg, jpg, png, svg.',
                'image.*.max' => 'The Product image must not be greater than 1024 kilobytes.',

            ]);
            if (Product::where('product_name', $request->product_name)->where('status', 'active')->exists()) {
                $this->validate($request, [
                    'product_name' => 'unique:products,product_name',

                ]);

            }

            //--Medicine Use-
            $medicine_use_id = 0;
            // if ($request->medicine_use != '') {
            //     $medicine_use_details = MedicineUse::where('name', $request->medicine_use)->first();
            //     if ($medicine_use_details) {
            //         $medicine_use_id = $medicine_use_details->id;
            //     } else {
            //         $medicine_use_id = MedicineUse::create([
            //             'name' => $request->medicine_use,
            //         ])->id;
            //     }
            // }

            //--Brand-
            $brand_id = 0;
            if ($request->selected_brand != '') {
                $brand_id = $request->selected_brand;
            } else if ($request->selected_brand == '' && $request->new_brand != '') {
                $image = $request->file('brand_logo');
                $fileName = '';
                if ($image) {
                    $fileName = time() . '.' . $request->brand_logo->extension();
                    $request->brand_logo->move(public_path('/assets/uploads/brands/'), $fileName);
                }
                $brand_id = Productbrand::create([
                    'name' => $request->new_brand,
                    'image' => $fileName,
                ])->id;
            }

            //-------------Supplier-
            $productsupplier_id = null;
            if ($request->supplier) {
                $productsupplier_id = implode(",", array_unique($request->supplier));
            }

            //-------------Tax-
            $taxIDs = null;
            if ($request->taxes) {
                $taxIDs = implode(',', $request->taxes);
            }

            $product_url = $this->create_slug($request->product_name);

            $need_prescription = 0;
            if ($request->need_prescription == 1) {
                $need_prescription = 1;
            }
            $not_for_sale = 0;
            if ($request->not_for_sale == 1) {
                $not_for_sale = 1;
            }
            $hide_from_site = (($request->hide_from_site == 1) ? '1' : '0');

            //-------------Product Content-
            $productcontents_id = '';
            if ($request->product_contents) {
                $productcontents_id = implode(",", array_unique($request->product_contents));
            }

            //-------------Product Variant-
            $variant_products = '';
            if ($request->variants) {
                $variant_products = implode(",", array_unique($request->variants));
            }

            $categoryIds = [];
            if ($request->producttype != '') {

                array_push($categoryIds, $request->producttype);
            }

            $obj_category = new Category();
            $child_category = $obj_category->getCategories($categoryIds);

            $child_categoryIds = [];
            $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);
            array_push($child_categoryIds, $request->producttype);

            $selected_category = null;
            if (($request->selected_category)) {
                $sel_cat = Category::where('id', $request->selected_category)
                    ->whereIn('parent_id', $child_categoryIds)->first();

                if ($sel_cat) {
                    $selected_category = $request->selected_category;

                }

            }
            if ($request->medicine_for != '') {
                $medicine_for = $request->medicine_for;
            } else {
                $medicine_for = 'used in';
            }
            $product_id = Product::create([
                'product_name' => $request->product_name,
                'description' => $request->description,
                'how_to_use' => $request->how_to_use,
                'benefits' => $request->benefits,
                'side_effects' => $request->side_effects,
                'category_id' => $selected_category,
                'productcontent_id' => $productcontents_id,
                'product_pack' => $request->product_pack,
                'medicine_for' => $medicine_for,
                'medicine_use' => $medicine_use_id,
                'producttypeid' => $request->producttype,
                'brands' => $brand_id,
                'supplier' => $productsupplier_id,
                'manufacturer' => $request->manufacturer,
                'variant_products' => $variant_products,
                'storage' => $request->storage,
                'added_by' => Auth::guard('admin')->user()->id,
                'vendor_type' => 'admin',
                'status' => 'review',
                'tax_ids' => $taxIDs,
                'tagline' => $request->tagline,
                'prescription' => $need_prescription,
                'features' => $request->features,
                'product_url' => $product_url,
                'quantity' => $request->productquantity,
                'price' => $request->productprice,
                'offer_price' => ($request->productofferprice != '' ? $request->productofferprice : 0),
                'not_for_sale' => $not_for_sale,
                'hide_from_site' => ($request->hide_from_site != '' ? $request->hide_from_site : 0),
            ])->id;

            if (!empty($request->file('image'))) {

                $counter = 0;
                foreach ($request->file('image') as $key => $image_file) {
                    $fileName = 'medi_prod_Img_' . time() . '_' . $key . '.' . $image_file->extension();
                    // $image_file->move(public_path('/assets/uploads/products/'), $fileName);
                    // $input['file'] = time().'.'.$image->getClientOriginalExtension();
                    $image = Image::make($image_file->getRealPath());
                    $width = $image->width();
                    $height = $image->height();
                    $dim = (($height + $width) / 4);
                    // $image_file->text('Expressmed', $dim, $dim, function ($font) {
                    //     $font->size(60);
                    //     $font->color('#ed1d24');
                    //     $font->align('center');
                    //     $font->valign('bottom');
                    //     $font->angle(90);
                    // })->save(public_path('/assets/uploads/products/') . '/' . $fileName);

                    $image->insert(public_path('img/watermark.png'), 'center', 5, 5);
                    $image->save(public_path('assets/uploads/products/' . $fileName));

                    $image_id = Product_image::create([
                        'product_id' => $product_id,
                        'product_image' => $fileName,
                    ])->id;

                    if (isset($request->thumnailhid[$counter]) && $request->thumnailhid[$counter] == 'yes') {
                        Product::where('id', $product_id)->update(array('thumbnail' => $image_id));
                    }
                    $counter++;
                }
            }
            $medicine_uses_array = array();
            $medicine_uses_array = $request->medicine_uses;
            if (!empty($medicine_uses_array)) {
                foreach ($medicine_uses_array as $medicine_Row) {
                    $med_use = explode('_', $medicine_Row);

                    $image_id = ProductMedicineuse::create([
                        'product_id' => $product_id,
                        'medicine_for' => $med_use[0],
                        'medicine_use' => $med_use[1],
                    ]);

                }
            }

            return redirect()->route('admin.products')->with('success', 'Product entered successfully');
        } else {
            return redirect()->back()->withErrors('Product Added failed: Your Admin account not logged please login your account.');
        }
    }

    public function show($id = null)
    {

        $product = Product::select('products.*', 'producttypes.producttype', 'product_images.product_image', 'medicine_uses.name as medicine_use_name', 'productbrands.name as brand_name', 'categories.name as category', 'product_manufacturers.name as manufacturer', 'admins.name as approved_by')
            ->Where('products.id', $id)
            ->leftJoin('producttypes', 'producttypes.id', 'products.producttypeid')
            ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->leftJoin('medicine_uses', 'medicine_uses.id', 'products.medicine_use')
            ->leftJoin('productbrands', 'productbrands.id', 'products.brands')
            ->leftjoin('admins', 'admins.id', 'products.approved_by')
            ->leftJoin('product_manufacturers', 'product_manufacturers.id', 'products.manufacturer')
            ->first();

        if ($product->count() > 0) {
            $product_images = Product_image::where('product_id', $product->id)->get();

            $Productcontents = array();
            if ($product->productcontent_id != '') {
                $ProductcontentsIDs = explode(',', $product->productcontent_id);
                $Productcontents = Productcontent::whereIn('id', $ProductcontentsIDs)->get(['name']);
            }

            $product_suppliers = $productsuppliers = array();
            if ($product->supplier != '') {
                $ProductsupplierIDs = explode(",", $product->supplier);
                $productsuppliers = Productsupplier::whereIn('id', $ProductsupplierIDs)->get();
            }

            $TaxIDs = explode(',', $product->tax_ids);
            $Taxes = Tax::whereIn('id', $TaxIDs)->get(['tax_name', 'percentage']);

            $variant_products = '';
            $arr_variants = array();
            if ($product->variant_products != '') {
                $arr_variants = explode(",", $product->variant_products);
            }
            if (!empty($arr_variants)) {
                $variant_products = Product::whereIn('id', $arr_variants)->get();
            }

            $reviews = $product->reviews()->with('user')->approved()->notSpam()->orderBy('created_at', 'desc')->paginate(10);

            return view('admin.products.show', compact('product', 'product_images', 'Productcontents', 'productsuppliers', 'reviews', 'Taxes', 'variant_products'));
        } else {
            return redirect()->route('admin.products')->with('errors', 'Product details not available');
        }
    }

    public function edit($id = null)
    {
        $product = Product::select('products.*', 'productbrands.name as brand_name')->Where('products.id', $id)
            ->leftJoin('productbrands', 'productbrands.id', 'products.brands')
            ->first();

        if (!empty($product) && $product->status == 'active') {
            $parentCategories = Category::where('parent_id', 0)->where('status', 'active')->orderBy('name', 'asc')->get();
            if ($product->producttypeid) {
                $parentsubCategories = Category::where('parent_id', $product->producttypeid)->where('status', 'active')->orderBy('name', 'asc')->get();
            } else {
                $parentsubCategories = Category::where('parent_id', $parentCategories[0]->id)->where('status', 'active')->orderBy('name', 'asc')->get();
            }
            $product_thumbnail = Product_image::where('product_id', $product->id)->get();
            $Producttype = Producttype::all();
            $Taxes = Tax::where('status', 'active')->get();

            $product_contents = '';
            $arr_productcontents = array();
            if ($product->productcontent_id != '') {
                $arr_productcontents = explode(",", $product->productcontent_id);
            }
            if (!empty($arr_productcontents)) {
                $product_contents = Productcontent::whereIn('id', $arr_productcontents)->get();
            }

            $product_suppliers = '';
            $arr_productsuppliers = array();
            if ($product->supplier != '') {
                $arr_productsuppliers = explode(",", $product->supplier);
            }
            if (!empty($arr_productsuppliers)) {
                $product_suppliers = Productsupplier::whereIn('id', $arr_productsuppliers)->get();
            }

            // $sel_medicine_uses = '';
            // $arr_medicine_uses = array();
            // if ($product->medicine_use != '') {
            //     $arr_medicine_uses = explode(",", $product->medicine_use);
            // }
            // if (!empty($arr_medicine_uses)) {
            //     $sel_medicine_uses = MedicineUse::whereIn('id', $arr_medicine_uses)->get();
            // }
            $sel_medicine_uses = ProductMedicineuse::select('medicine_uses.*', 'product_medicineuses.*')
                ->leftJoin('medicine_uses', 'medicine_uses.id', 'product_medicineuses.medicine_use')
                ->where('product_medicineuses.product_id', $id)->get();

            $variant_products = '';
            $arr_variants = array();
            if ($product->variant_products != '') {
                $arr_variants = explode(",", $product->variant_products);
            }
            if (!empty($arr_variants)) {
                $variant_products = Product::whereIn('id', $arr_variants)->get();
            }
            $medicine_uses = MedicineUse::orderBy('name', 'asc')->get();
            $manufacturers = ProductManufacturer::orderBy('name', 'asc')->get();

            return view('admin.products.edit', compact('product', 'parentCategories', 'product_thumbnail', 'Producttype', 'Taxes', 'product_contents', 'product_suppliers', 'variant_products', 'parentsubCategories', 'medicine_uses', 'sel_medicine_uses', 'manufacturers'));
        } else {
            return redirect()->route('admin.products')->withErrors('Sorry... You are not able to edit this product. Product details not available.');
        }
    }

    public function update(Request $request, $id = null)
    {
        $product = Product::select('products.*', 'productbrands.name as brand_name')->Where('products.id', $id)
            ->leftJoin('productbrands', 'productbrands.id', '=', 'products.brands')->first();

        if (isset($product)) {

            $this->validate($request, [
                'product_name' => 'required',
                'producttype' => 'required',
                'productprice' => 'required|numeric|gt:0',
                'productofferprice' => 'numeric|nullable|lt:productprice',
                'image.*' => 'nullable|mimes:jpeg,jpg,png,svg|max:1024',
            ], [
                'product_name.required' => 'The Product Name field is required.',
                'producttype.required' => 'The Product Type field is required.',
                'productprice.required' => 'The Product Price field is required.',
                'productprice.numeric' => 'Please Enter valid price.',
                'productprice.gt' => 'Please Enter valid price. Zero not allowed.',
                'productofferprice.numeric' => 'Please Enter valid offer price.',
                'productofferprice.lt' => 'Product offer price must be less than original price.',
                'image.*.mimes' => 'The Product image must be a file of type: jpeg, jpg, png, svg',
                'image.*.max' => 'The Product image must not be greater than 1024 kilobytes.',

            ]);

            //--Medicine Use-
            // $medicine_use_id = 0;
            // if ($request->medicine_use != '') {
            //     $medicine_use_details = MedicineUse::where('name', $request->medicine_use)->first();
            //     if ($medicine_use_details) {
            //         $medicine_use_id = $medicine_use_details->id;
            //     } else {
            //         $medicine_use_id = MedicineUse::create([
            //             'name' => $request->medicine_use,
            //         ])->id;
            //     }
            // }

            $medicine_use_id = 0;
            if ($request->medicine_uses) {
                $medicine_use_id = implode(",", array_unique($request->medicine_uses));
            }

            $brand_id = 0;
            if ($request->selected_brand != '') {
                $brand_id = $request->selected_brand;
            } else if ($request->selected_brand == '' && $request->new_brand != '') {
                $image = $request->file('brand_logo');
                $fileName = '';
                if ($image) {
                    $fileName = time() . '.' . $request->brand_logo->extension();
                    $request->brand_logo->move(public_path('/assets/uploads/brands/'), $fileName);
                }
                $brand_id = Productbrand::create([
                    'name' => $request->new_brand,
                    'image' => $fileName,
                ])->id;
            }

            //-------------Supplier-
            $productsupplier_id = null;
            if ($request->supplier) {
                $productsupplier_id = implode(",", array_unique($request->supplier));

            }

            $product_name = $request->product_name;
            if ($product->product_name != $product_name) {
                $product_url = $this->create_slug($product_name);
            } else {
                $product_url = $product->product_url;
            }

            //-------------Tax-
            $taxIDs = null;
            if ($request->taxes) {
                $taxIDs = implode(',', $request->taxes);
            }

            $need_prescription = 0;
            if ($request->need_prescription == 1) {
                $need_prescription = 1;
            }
            $not_for_sale = 0;
            if ($request->not_for_sale == 1) {
                $not_for_sale = 1;
            }

            //-------------Product Content-
            $productcontents_id = '';
            if ($request->product_contents) {
                $productcontents_id = implode(",", array_unique($request->product_contents));
            }

            //-------------Product Variant-
            $variant_products = '';
            if ($request->variants) {
                $variant_products = implode(",", array_unique($request->variants));
            }
            $categoryIds = [];
            if ($request->producttype != '') {

                array_push($categoryIds, $request->producttype);
            }

            $obj_category = new Category();
            $child_category = $obj_category->getCategories($categoryIds);

            $child_categoryIds = [];
            $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);
            array_push($child_categoryIds, $request->producttype);

            $selected_category = null;
            if (($request->selected_category)) {

                $sel_cat = Category::where('id', $request->selected_category)
                    ->whereIn('parent_id', $child_categoryIds)->first();
                // dd($sel_cat);

                if ($sel_cat) {
                    $selected_category = $request->selected_category;

                }

            }
            if ($request->medicine_for != '') {
                $medicine_for = $request->medicine_for;
            } else {
                $medicine_for = 'used in';
            }
            // dd($selected_category);
            // dd($productsupplier_id);

            Product::find($id)->update([
                'product_name' => $request->product_name,
                'description' => $request->description,
                'how_to_use' => $request->how_to_use,
                'benefits' => $request->benefits,
                'side_effects' => $request->side_effects,
                'category_id' => $selected_category,
                'productcontent_id' => $productcontents_id,
                'product_pack' => $request->product_pack,
                'medicine_for' => $medicine_for,
                'medicine_use' => $medicine_use_id,
                'producttypeid' => $request->producttype,
                'brands' => $brand_id,
                'supplier' => $productsupplier_id,
                'manufacturer' => $request->manufacturer,
                'variant_products' => $variant_products,
                'storage' => $request->storage,
                'tax_ids' => $taxIDs,
                'tagline' => $request->tagline,
                'prescription' => $need_prescription,
                'features' => $request->features,
                'product_url' => $product_url,
                'quantity' => $request->productquantity,
                'price' => $request->productprice,
                'offer_price' => ($request->productofferprice != '' ? $request->productofferprice : 0),
                'not_for_sale' => $not_for_sale,
                'hide_from_site' => ($request->hide_from_site != '' ? $request->hide_from_site : 0),
            ]);

            if (!empty($request->file('image'))) {
                $counter = 0;
                foreach ($request->file('image') as $key => $image_file) {
                    $fileName = 'medi_prod_Img_' . time() . '_' . $key . '.' . $image_file->extension();

                    // $image_file->move(public_path('/assets/uploads/products/'), $fileName);

                    $image = Image::make($image_file->getRealPath());
                    $width = $image->width();
                    $height = $image->height();
                    $dim = (($height + $width) / 4);
                    // $image_file->text('Expressmed', $dim, $dim, function ($font) {
                    //     $font->size(60);
                    //     $font->color('#ed1d24');
                    //     $font->align('center');
                    //     $font->valign('bottom');
                    //     $font->angle(90);
                    // })->save(public_path('/assets/uploads/products/') . '/' . $fileName);
                    $image->insert(public_path('img/watermark.png'), 'center', 5, 5);
                    $image->save(public_path('assets/uploads/products/' . $fileName));

                    $image_id = $product_image_data = Product_image::create([
                        'product_id' => $id,
                        'product_image' => $fileName,
                    ])->id;

                    if (isset($request->thumnailhid[$counter]) && $request->thumnailhid[$counter] == 'yes') {
                        Product::where('id', $id)->update(array('thumbnail' => $image_id));
                    }
                    $counter++;
                }
            }
            $medicine_uses_array = array();
            $medicine_uses_array = $request->medicine_uses;
            ProductMedicineuse::where('product_id', $id)->delete();

            if (!empty($medicine_uses_array)) {
                // dd($medicine_uses_array);
                foreach ($medicine_uses_array as $medicine_Row) {

                    $med_use = explode('_', $medicine_Row);

                    $med_use_id = ProductMedicineuse::create([
                        'product_id' => $id,
                        'medicine_for' => $med_use[0],
                        'medicine_use' => $med_use[1],
                    ]);

                }
            }

            return redirect()->route('admin.products')->with('success', 'Product updated successfully');

        } else {
            return redirect()->back()->withErrors('Sorry... You are not able to edit this product. Product details not available.');
        }

    }

    private function create_slug($string)
    {
        $items = array("index", "create_slug", "show", "create", "store", "edit", "update", "destroy");
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        if (in_array($slug, $items)) {
            $slug = $slug . time();
        }
        $content = Product::where('product_url', '=', $slug)->first();
        if ($content) {
            $slug = $slug . time();
        }
        return $slug;
    }

    public function destroy($id = null)
    {
        Product::find($id)->update([
            'status' => 'deleted',
        ]);

        return redirect()->route('admin.products')->with('success', 'Product deactivated successfully.');
    }

    public function import_bulk(Request $request)
    {
        $productfile = $request->file('bulk_products');
        if ($productfile) {
            $extension = $productfile->extension();
            $allowed_extension = array("xls", "xlsx");

            if (in_array($extension, $allowed_extension)) {
                try {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($productfile);

                    $worksheet = $spreadsheet->getActiveSheet();
                    $worksheetArray = $worksheet->toArray();

                    // array_shift($worksheetArray);

                    $cnt = 0;

                    foreach ($worksheetArray as $productrow => $value) {

                        if ($worksheetArray[0] != $value) {

                            $original_array = $this->array_fill_keys($worksheetArray[0], $value);

                            if ((isset($original_array['NAME']) && ($original_array['NAME'] != null)) && ((isset($original_array['CATEGORY'])) && $original_array['CATEGORY'] != null) && ((isset($original_array['PRICE'])) && $original_array['PRICE'] != null)) {

                                //------Product Content--
                                $productContentIds = $productContentIds_ = '';
                                if (isset($original_array['CONDENT'])) {
                                    $ProductContents = array_map('trim', explode(',', $original_array['CONDENT']));
                                    if (!empty(array_filter($ProductContents))) {
                                        foreach ($ProductContents as $ProductContents_val) {
                                            $productcontent = Productcontent::where('name', $ProductContents_val)->first();
                                            if ($productcontent) {
                                                $productContentIds .= $productcontent->id . ',';
                                            } else {
                                                $ProductcontentID = Productcontent::create([
                                                    'name' => $ProductContents_val,
                                                ])->id;

                                                if ($ProductcontentID) {
                                                    $productContentIds .= $ProductcontentID . ',';
                                                }
                                            }
                                        }
                                        $productContentIds_ = trim($productContentIds, ",");
                                    }
                                }

                                //------Brand--
                                $brandID = 0;
                                if (isset($original_array['BRAND NAME']) && (!empty($original_array['BRAND NAME']))) {
                                    $brandname = ltrim($original_array['BRAND NAME']);
                                    $brand = Productbrand::where('name', $brandname)->first();
                                    if ($brand) {
                                        $brandID = $brand->id;
                                    } else {
                                        $brandID = Productbrand::create([
                                            'name' => $brandname,
                                            'image' => '',
                                        ])->id;
                                    }
                                }

                                //------Category--
                                $categoryID = 0;
                                if (isset($original_array['CATEGORY']) && (!empty($original_array['CATEGORY']))) {
                                    $categoryname = ltrim($original_array['CATEGORY']);
                                    $category = Category::where('name', $categoryname)->first();
                                    if ($category) {
                                        $categoryID = $category->id;
                                    } else {
                                        $categoryID = Category::create([
                                            'name' => $categoryname,
                                            'parent_id' => 0,
                                            'status' => 'active',
                                        ])->id;
                                    }
                                }

                                //------Product Types--
                                $productTypeID = 1;
                                if (isset($original_array['TYPE']) && (!empty($original_array['TYPE']))) {
                                    $typeName = ltrim($original_array['TYPE']);
                                    $productType = Producttype::where('producttype', $typeName)->first();
                                    if ($productType) {
                                        $productTypeID = $productType->id;
                                    } else {
                                        $productTypeID = Producttype::create([
                                            'type' => 'user',
                                            'producttype' => $typeName,
                                        ])->id;
                                    }
                                }

                                //------Medicine Uses--
                                $medicineuseId = 0;

                                if (isset($original_array['USE']) && (!empty($original_array['USE']))) {
                                    $medicineUsename = $original_array['USE'];
                                    $medicineuse_details = MedicineUse::where('name', $medicineUsename)->first();
                                    if ($medicineuse_details) {
                                        $medicineuseId = $medicineuse_details->id;
                                    } else {
                                        $medicineuseId = MedicineUse::create([
                                            'name' => $medicineUsename,
                                        ])->id;
                                    }
                                }

                                //------Suppliers--
                                $supplierIds = $supplierIds_ = '';
                                if (isset($original_array['SUPPLIER'])) {
                                    $ProductSuppliers = explode("\n", $original_array['SUPPLIER']);
                                    if (!empty(array_filter($ProductSuppliers))) {
                                        foreach ($ProductSuppliers as $ProductSuppliers_val) {
                                            $SupplierName = trim($ProductSuppliers_val);
                                            $productsupplier = Productsupplier::where('name', $SupplierName)->first();
                                            if ($productsupplier) {
                                                $supplierIds .= $productsupplier->id . ',';
                                            } else {
                                                $supplierId = Productsupplier::create([
                                                    'name' => $SupplierName,
                                                ])->id;

                                                if ($supplierId) {
                                                    $supplierIds .= $supplierId . ',';
                                                }
                                            }
                                        }
                                        $supplierIds_ = trim($supplierIds, ",");
                                    }
                                }

                                $prescription = (trim(strtoupper(isset($original_array['PRESCRIPYION REQUIRED /NOT REQUIRED']) && $original_array['PRESCRIPYION REQUIRED /NOT REQUIRED'])) == 'REQUIRED' ? 1 : 0);
                                $priceval = preg_replace('/[^0-9\.]/', '', $original_array['PRICE']);

                                $storage = '';

                                if (isset($original_array['STORAGE'])) {
                                    $storage = preg_replace('/[^A-Za-z0-9\-]/', '', $original_array['STORAGE']);
                                }

                                $product_url = $this->create_slug($original_array['NAME']);

                                $productID = Product::create([
                                    'product_name' => $original_array['NAME'],
                                    'description' => null,
                                    'how_to_use' => null,
                                    'benefits' => null,
                                    'side_effects' => (isset($original_array['SIDE EFFECT']) && $original_array['SIDE EFFECT'] != '' ? nl2br($original_array['SIDE EFFECT']) : null),
                                    'category_id' => $categoryID,
                                    'productcontent_id' => ($productContentIds_ != '' ? $productContentIds_ : null),
                                    'product_pack' => (isset($original_array['PACK']) && $original_array['PACK'] != '' ? $original_array['PACK'] : null),
                                    'medicine_use' => $medicineuseId,
                                    'producttypeid' => $productTypeID,
                                    'brands' => $brandID,
                                    'supplier' => ($supplierIds_ != '' ? $supplierIds_ : null),
                                    'manufacturer' => (isset($original_array['MANUFACTURER']) && $original_array['MANUFACTURER'] != '' ? $original_array['MANUFACTURER'] : null),
                                    'variant_products' => null,
                                    'storage' => ($storage != '' ? $storage : null),
                                    'added_by' => Auth::guard('admin')->user()->id,
                                    'vendor_type' => 'admin',
                                    'status' => 'active',
                                    'tax_ids' => null,
                                    'tagline' => null,
                                    'prescription' => $prescription,
                                    'features' => null,
                                    'product_url' => $product_url,
                                    'quantity' => (isset($original_array['QUANTITY']) && $original_array['QUANTITY'] != '' ? $original_array['QUANTITY'] : 1),
                                    'price' => ($priceval != '' ? $priceval : 0),
                                    'offer_price' => 0,
                                ])->id;

                                //Image import from spreadsheet start--
                                if ($productID) {
                                    $draw = (array) $worksheet->getDrawingCollection();
                                    $drawing = $draw;
                                    if ($drawing) {
                                        //----------Get Image coordinates array--
                                        $coordinate_array = [];
                                        foreach ($worksheet->getDrawingCollection() as $drawval) {
                                            $pos = substr($drawval->getCoordinates(), 0, 1);
                                            $coordinate_array[] = $drawval->getCoordinates();
                                        }
                                        $validateCordinate = $pos . ($cnt + 2);

                                        if (array_search($validateCordinate, $coordinate_array) > -1) { ///Check is the image is available in the same product row--
                                            $img_pos = array_search($validateCordinate, $coordinate_array);
                                            $zipReader = fopen($drawing[$img_pos]->getPath(), 'r');

                                            $imageContents = '';
                                            while (!feof($zipReader)) {
                                                $imageContents .= fread($zipReader, 1024);
                                            }
                                            fclose($zipReader);
                                            // dd($imageContents);
                                            $extension = $drawing[$img_pos]->getExtension();

                                            $myFileName = 'prod_img_' . time() . '_' . $cnt . '.' . $extension;

                                            file_put_contents(public_path('/assets/uploads/products/') . $myFileName, $imageContents);

                                            $ImageID = Product_image::create([
                                                'product_id' => $productID,
                                                'product_image' => $myFileName,
                                            ])->id;
                                            if ($ImageID) {
                                                Product::find($productID)->update(['thumbnail' => $ImageID]);
                                            }
                                        }
                                    }
                                }

                            }
                            $cnt++;
                        }
                    }

                    return redirect()->route('admin.products')->with('success', 'Your file completely processed.');
                } catch (\Exception $e) {
                    return redirect()->route('admin.products')->withErrors($e->getMessage());
                }
            } else {
                return redirect()->route('admin.products')->withErrors('xls, xlsx files are allowed to import bulk products.');
            }
        } else {
            return redirect()->route('admin.products')->withErrors('File is missing. Please choose your xls, xlsx files.');
        }
    }

    public function search_productcontent(Request $request)
    {
        if ($request->ajax()) {
            $data = Productcontent::where('name', 'LIKE', $request->keyword . '%')->get();
            $output = '';
            if (count($data) > 0) {
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                foreach ($data as $row) {
                    $output .= '<li class="list-group-item" onclick="select_productcontent(' . $row->id . ',\'' . addslashes($row->name) . '\')">' . $row->name . '</li>';
                }
                $output .= '<li class="list-group-item "><a href="javascript:void(0)" id="new_productcontent">Enter New Product content here</a></li>';
                $output .= '</ul>';
            } else {
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                $output .= '<li class="list-group-item">' . 'No results' . '</li>';
                $output .= '<li class="list-group-item "><a href="javascript:void(0)" id="new_productcontent">Enter New Product content here</a></li>';
                $output .= '</ul>';
            }
            return $output;
        }
    }

    public function add_newproductcontent(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:productcontents,name',
        ]);

        if ($validate->fails()) {
            $message = '';
            foreach ($validate->errors()->toArray() as $error) {
                $message .= $error[0];
            }
            $returnarray['result'] = 'failed';
            $returnarray['message'] = $message;
        } else {
            $ProductcontentID = Productcontent::create([
                'name' => $request->name,
            ])->id;

            if ($ProductcontentID) {
                $returnarray['result'] = 'success';
                $returnarray['ProductcontentID'] = $ProductcontentID;
                $returnarray['name'] = $request->name;
                $returnarray['message'] = 'Product content successfully added.';
            } else {
                $returnarray['result'] = 'failed';
                $returnarray['message'] = 'sorry... Product content added failed.';
            }
        }
        return response()->json($returnarray);
    }

    public function search_supplier(Request $request)
    {
        if ($request->ajax()) {
            $data = Productsupplier::where('name', 'LIKE', $request->keyword . '%')->get();
            $output = '';

            if (count($data) > 0) {
                foreach ($data as $row) {
                    $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                    $output .= '<li class="list-group-item" onclick="select_productSupplier(' . $row->id . ',\'' . addslashes($row->name) . '\')">' . $row->name . '</li>';
                }
                // $output .= '<li class="list-group-item "><a href="javascript:void(0)" id="new_productsupplier">Enter New Supplier here</a></li>';
                $output .= '</ul>';
            } else {
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                $output .= '<li class="list-group-item">' . 'No results. Please add new supplier.' . '</li>';
                // $output .= '<li class="list-group-item "><a href="javascript:void(0)" id="new_productsupplier">Enter New Supplier here</a></li>';
                $output .= '</ul>';
            }
            return $output;
        }
    }

    public function add_newsupplier(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:productsuppliers,name',
        ]);

        if ($validate->fails()) {
            $message = '';
            foreach ($validate->errors()->toArray() as $error) {
                $message .= $error[0];
            }
            $returnarray['result'] = 'failed';
            $returnarray['message'] = $message;
        } else {
            $ProductSupplierID = Productsupplier::create([
                'name' => $request->name,
            ])->id;

            if ($ProductSupplierID) {
                $returnarray['result'] = 'success';
                $returnarray['ProductSupplierID'] = $ProductSupplierID;
                $returnarray['name'] = $request->name;
                $returnarray['message'] = 'Supplier successfully added.';
            } else {
                $returnarray['result'] = 'failed';
                $returnarray['message'] = 'sorry... Supplier added failed.';
            }
        }
        return response()->json($returnarray);
    }

    public function search_brand(Request $request)
    {
        if ($request->ajax()) {
            $data = Productbrand::select('id', 'name', 'image')->where('name', 'LIKE', $request->brand . '%')->get();
            $output = '';
            if (count($data) > 0) {
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                foreach ($data as $row) {

                    $output .= '<li class="list-group-item" onclick="select_brand(' . $row->id . ',\'' . addslashes($row->name) . '\')">' . $row->name . '</li>';
                }
                // $output .= '<li class="list-group-item "><a href="javascript:void(0)" id="newbrand">Enter new Brand Here</a></li>';
                $output .= '</ul>';
            } else {
                $output .= '<li class="list-group-item">' . 'No results. Please add brand.' . '</li>';
                // $output .= '<li class="list-group-item "><a href="javascript:void(0)" id="newbrand">Enter new Brand Here</a></li>';
            }
            return $output;
        }
    }

    public function search_medicine_use(Request $request)
    {
        if ($request->ajax()) {
            $data = MedicineUse::where('name', 'LIKE', $request->keyword . '%')->get();
            $output = '';
            if (count($data) > 0) {
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                foreach ($data as $row) {
                    $output .= '<li class="list-group-item" onclick="select_medicine_use(\'' . addslashes($row->name) . '\')">' . $row->name . '</li>';
                }
                $output .= '</ul>';
            }
            return $output;
        }
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::where('product_name', 'LIKE', $request->variant . '%')->where('status', 'active')->get();
            $output = '';
            if (count($data) > 0) {
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                foreach ($data as $row) {
                    $product_name = preg_replace("/[^ \w]+/", "", $row->product_name);

                    $output .= '<li class="list-group-item" onclick="select_variants(' . $row->id . ',\'' . addslashes($product_name) . '\')">' . $product_name . '</li>';
                }
                $output .= '</ul>';
            } else {
                $output .= '<li class="list-group-item">' . 'No results' . '</li>';
            }
            return $output;
        }
    }

    public function product_subcategory(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::where('id', $request->parent_cat_id)->first();
            $old_sub_category = 0;
            if (isset($request->selected_cat_id)) {
                $old_sub_category = $request->selected_cat_id;
            }
            $output = '';
            if (count($data->subcategory) > 0) {
                $output = '<ul>  <li>';
                $output .= '<a href="javascript:void(0)" id="category" data_item="' . $data->id . '" class="category_items" >' . $data->product_name . '</li>';

                $output .= view('admin.category.subCategoryList', ['subcategories' => $data->subcategory, 'old_sub_category' => $old_sub_category]);

                $output .= '<li></ul>';

            } else {
                $output .= '<li">' . 'No Subcategories' . '</li>';
            }
            $ajax_status = 'success';
            $return_array = array('ajax_status' => $ajax_status, 'html' => $output);
            return response()->json($return_array);
        }
    }

    public function product_editsubcategory(Request $request)
    {

        if ($request->ajax()) {

            $data = Category::where('id', $request->parent_cat_id)->first();
            $product_details = Product::where('id', $request->product_id)->first();
            if (isset($request->old_parent_cat_id) && ($request->old_parent_cat_id > 0)) {
                $data = Category::where('id', $request->old_parent_cat_id)->first();
            }
            // if(isset($request->selected_sub_cat_id)&&($request->selected_sub_cat_id>0)){
            //     $product_details=Product::where('category_id', $request->selected_sub_cat_id)->first();
            // }
            $output = '';
            if (isset($data->subcategory)) {
                $output = '<ul>  <li>';
                $output .= '<a href="javascript:void(0)" id="category" data_item="' . $data->id . '" class="category_items" >' . $data->product_name . '</li>';

                $output .= view('admin.category.subCategoryListProductEdit', ['subcategories' => $data->subcategory, 'product' => $product_details]);

                $output .= '<li></ul>';

            } else {
                $output .= '<li">' . 'No Subcategories' . '</li>';
            }
            $ajax_status = 'success';
            $return_array = array('ajax_status' => $ajax_status, 'html' => $output);
            return response()->json($return_array);
        }
    }

    public function removeMedia(Request $request)
    {
        $gallery = Product_image::find($request->id);
        if (!empty($gallery) && $gallery->product_image != '') {
            $file_path = public_path('/assets/uploads/products/') . $gallery->product_image;
            File::delete($file_path);
        }
        Product_image::find($request->id)->delete();
        $message = "Successfully deleted";
        $ajax_status = 'success';
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

    public function setThumbnail(Request $request)
    {
        if ($request->image_id != '' && $request->id != '') {
            Product::where('id', $request->id)->update(array('thumbnail' => $request->image_id));
            $message = "Successfully updated";
            $ajax_status = 'success';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        } else {
            $message = "Data invalid";
            $ajax_status = 'failed';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        }
    }

    public function update_productreview(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];

        if (!empty($request->userid) && !empty($request->productid)) {
            ProductReview::where('id', $request->product_reviews_id)->where('user_id', $request->userid)->where('product_id', $request->productid)
                ->update([
                    'reviews' => $request->productreview,
                    'rating' => $request->starvalue,
                ]);
            $ajax_status = 'success';
        } else {
            $message = "Invalid product or user.";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

    public function destroy_productreview(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];

        if (!empty($request->userid) && !empty($request->productid)) {
            ProductReview::where('id', $request->product_reviews_id)->where('user_id', $request->userid)->where('product_id', $request->productid)->delete();
            $ajax_status = 'success';
        } else {
            $message = "Invalid product or user.";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }

    public function create_duplicate($id = null)
    {
        if (Auth::guard('admin')->user()) {
            $product_details = Product::select('products.*', 'productbrands.name as brand_name', 'medicine_uses.name as medicine_use_name')->Where('products.id', $id)
                ->leftJoin('productbrands', 'productbrands.id', 'products.brands')
                ->leftJoin('medicine_uses', 'medicine_uses.id', 'products.medicine_use')
                ->first();

            $product_url = $this->create_slug($product_details->product_name);
            $product_images = Product_image::where('product_id', $id)->get();

            $duplicate_product_id = Product::create([
                'product_name' => $product_details->product_name,
                'description' => $product_details->description,
                'how_to_use' => $product_details->how_to_use,
                'benefits' => $product_details->benefits,
                'side_effects' => $product_details->side_effects,
                'category_id' => $product_details->category_id,
                'productcontent_id' => $product_details->productcontent_id,
                'product_pack' => $product_details->product_pack,
                'medicine_use' => $product_details->medicine_use,
                'producttypeid' => $product_details->producttypeid,
                'brands' => $product_details->brands,
                'supplier' => $product_details->supplier,
                'manufacturer' => $product_details->manufacturer,
                'variant_products' => $product_details->variant_products,
                'storage' => $product_details->storage,
                'added_by' => Auth::guard('admin')->user()->id,
                'vendor_type' => 'admin',
                'status' => 'review',
                'tax_ids' => $product_details->tax_ids,
                'tagline' => $product_details->tagline,
                'prescription' => $product_details->prescription,
                'features' => $product_details->features,
                'product_url' => $product_url,
                'quantity' => $product_details->quantity,
                'price' => $product_details->price,
                'offer_price' => $product_details->offer_price,

            ])->id;

            if ($product_details->thumbnail != '') {
                $thumbnail_image = Product::select('product_images.*')
                    ->join('product_images', 'product_images.id', 'products.thumbnail')
                    ->where('products.id', $id)
                    ->first();
            }

            if (isset($product_images)) {
                if (count($product_images) > 0) {
                    foreach ($product_images as $key => $image_file) {

                        $fileName = 'medi_prod_Img_' . time() . '_' . $key . '.' . pathinfo($image_file->product_image, PATHINFO_EXTENSION);
                        $oldPath = public_path('/assets/uploads/products/' . $image_file->product_image);

                        $newPath = public_path('/assets/uploads/products/' . $fileName);
                        if (\File::copy($oldPath, $newPath)) {
                        }
                        $image_id = Product_image::create([
                            'product_id' => $duplicate_product_id,
                            'product_image' => $fileName,
                        ])->id;

                        if (isset($thumbnail_image->product_image)) {
                            if (($thumbnail_image->product_image) == ($image_file->product_image)) {

                                Product::where('id', $duplicate_product_id)->update(array('thumbnail' => $image_id));
                            }

                        }
                    }
                }

            }
            return redirect()->route('admin.products')->with('success', 'Product duplicate entered successfully');
        } else {
            return redirect()->back()->withErrors('Product Added failed: Your Admin account not logged please login your account.');
        }
    }

    public function array_fill_keys($keyArray, $valueArray)
    {
        if (is_array($keyArray)) {
            foreach ($keyArray as $key => $value) {
                $filledArray[$value] = $valueArray[$key];
            }
        }
        return $filledArray;
    }
    public function find_subcategories(Request $request)
    {
        // $state = State::where('country_id',$request->CountryId)->select('id','name')->get();
        $data = Category::where('id', $request->typeId)->first();
        $subcategories = Category::select('categories.*')
            ->where('categories.parent_id', $request->typeId)
            ->get();

        $output = '';

        if (count($data->subcategory) > 0) {
            // $output = '<optgroup label="' . $data->name . '">';
            $output .= '<option value="">All Categories</option>';
            $output .= view('admin.category.subCategorydropdown', ['subcategories' => $data->subcategory, 'selcatId' => $request->selcatId]);

            // $output .= '</optgroup>';

        } else {
            $output .= '<option value="">' . 'No categories' . '</option>';
        }
        $ajax_status = 'success';
        $return_array = array('ajax_status' => $ajax_status, 'html' => $output);

        return response()->json($return_array);
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

    public function removed_products(Request $request)
    {
        $products = Product::select('products.*', 'product_images.product_image', 'type1.name as producttype', 'type2.name as category', 'product_manufacturers.name as manufacturer')
            ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
            ->leftJoin('categories as type1', 'type1.id', '=', 'products.producttypeid')
            ->leftJoin('categories as type2', 'type2.id', '=', 'products.category_id')
            ->leftJoin('medicine_uses', 'medicine_uses.id', '=', 'products.medicine_use')
            ->leftJoin('product_manufacturers', 'product_manufacturers.id', '=', 'products.manufacturer')
            ->where('products.status', 'deleted');

        // if ($request->has('filter_brand') && $request->filter_brand != '') {
        //     $products->where('products.brands', '=', $request->filter_brand);
        // }

        if ($request->has('filter_type') && $request->filter_type != '') {
            $products->where('products.producttypeid', '=', $request->filter_type);
        }
        if ($request->has('filter_category') && $request->filter_category != '') {
            $products->where('products.category_id', '=', $request->filter_category);
        }

        if ($request->has('price_filter') && $request->price_filter != '') {
            $price_range = $request->price_filter;
            global $first_amount;
            global $second_amt;
            if ($price_range != '') {
                $price_set = (explode("-", $price_range));
                $first_amount = $price_set[0];
                $second_amt = $price_set[1];
            }
            $products->whereBetween('products.price', [$first_amount, $second_amt]);
        }

        if ($request->has('search_term') && $request->search_term != '') {
            $pc_id = '';
            $content_ids = Productcontent::select('productcontents.id')
                ->where('productcontents.name', 'LIKE', '%' . $request->search_term . '%')->get();

            $products->where('products.product_name', 'LIKE', '%' . $request->search_term . '%')
                ->orWhere('medicine_uses.name', 'LIKE', '%' . $request->search_term . '%');
            if (count($content_ids) > 0) {
                foreach ($content_ids as $content_id) {
                    $products->orWhereRaw("find_in_set('" . $content_id->id . "',products.productcontent_id)");
                }
            }

        }
        $products = $products->latest()->paginate(30);

        $brand = Productbrand::orderBy('name', 'asc')->get();
        $category = Category::orderBy('name', 'asc')->get();

        $Producttypes = Category::where('parent_id', 0)->orderBy('name', 'asc')->get();

        return view('admin.products.removedproducts', compact('products', 'brand', 'category', 'Producttypes'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function update_sellstatus(Request $request)
    {
        if ($request->product_id != '' && $request->status != '') {
            $status = $request->status;
            $flag = 0;
            if ($status == "sold-out") {

                $flag = 1;
            }
            Product::where('id', $request->product_id)->update(array('flag' => $flag));
            $message = "Successfully updated";
            $ajax_status = 'success';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        } else {
            $message = "Data invalid";
            $ajax_status = 'failed';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        }
    }

    public function update_hideoption(Request $request)
    {
        if ($request->product_id != '' && $request->status != '') {
            $status = $request->status;
            $flag = 0;
            if ($status == "hide") {

                $flag = 1;
            }
            Product::where('id', $request->product_id)->update(array('hide_from_site' => $flag));
            $message = "Successfully updated";
            $ajax_status = 'success';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        } else {
            $message = "Data invalid";
            $ajax_status = 'failed';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        }
    }

    public function product_approval(Request $request)
    {
        if ($request->product_id != '') {
            $status = 'active';
            $user_id = Auth::guard('admin')->user()->id;
            Product::where('id', $request->product_id)->update(array('status' => $status, 'approved_by' => $user_id));
            $message = "Product Successfully Approved";
            $ajax_status = 'success';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        } else {
            $message = "Data invalid";
            $ajax_status = 'failed';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        }
    }
    // public function add_watermark()
    // {
    //     $product_images = Product_image::all();
    //     $arr_ext = array('jpg', 'jpeg', 'png', 'web', 'webp', 'bmp');

    //     foreach ($product_images as $img) {
    //         $ext = pathinfo(public_path('assets/uploads/products/' . $img->product_image), PATHINFO_EXTENSION);
    //         if (is_readable(public_path('assets/uploads/products/' . $img->product_image))) {
    //             if (in_array($ext, $arr_ext)) {
    //                 $image = Image::make(public_path('assets/uploads/products/'.$img->product_image));
    //                 $width=$image->width();
    //                 $height=$image->height();
    //                 $dim=(($height+$width)/4);

    //                 $image->text('Expressmed', $dim, $dim, function ($font) {
    //                     $font->size(30);
    //                     $font->color('#c9c9cb');
    //                     $font->align('center');
    //                     $font->valign('bottom');
    //                     $font->angle(90);
    //                 })->save(public_path('assets/uploads/products/'.$img->product_image));
    //             }

    //         }
    //     }
    //     return redirect()->route('list.allproductlisting')->with('success', 'You applied successfully.');

    // }

}
