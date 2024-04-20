<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Country;
use App\Models\Generalsetting;
use App\Models\Invoice;
use App\Models\MedicineUse;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PaymentDetail;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\Productbrand;
use App\Models\Productcontent;
use App\Models\ProductMedicineuse;
use App\Models\ProductReview;
use App\Models\Productsupplier;
use App\Models\Producttype;
use App\Models\Product_image;
use App\Models\State;
use App\Models\Store;
use App\Models\Tax;
use App\Models\UserAddress;
use App\Models\Wishlist;
use Config;
use DB;
use File;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Image;
use Mail;
use PDF;
use Razorpay\Api\Api;
use Seshac\Shiprocket\Shiprocket;
use Session;

class ShoppingController extends Controller
{
    public function index(Request $request, $product_url = null)
    {
        if (!empty($product_url)) {
            $product = Product::select('products.*', 'producttypes.producttype', 'product_images.product_image', 'medicine_uses.name as medicine_use_name', 'productbrands.name as brand_name', 'productbrands.image as brand_image', 'categories.name as category', 'product_manufacturers.name as manufacturer', 'product_manufacturers.image as manufacturer_image')
                ->Where('products.product_url', $product_url)
                ->where('products.hide_from_site', '!=', '1')
                ->where('products.status', 'active')
                ->leftJoin('producttypes', 'producttypes.id', 'products.producttypeid')
                ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                ->leftJoin('categories', 'categories.id', 'products.category_id')
                ->leftJoin('medicine_uses', 'medicine_uses.id', 'products.medicine_use')
                ->leftJoin('productbrands', 'productbrands.id', 'products.brands')
                ->leftJoin('product_manufacturers', 'product_manufacturers.id', 'products.manufacturer')
                ->first();
            if ($product) {

                $type = $categories = Category::find($product->producttypeid)->toArray();

                // $medicine_uses_ids=explode(',',$product->medicine_use);
                // $medicine_uses = MedicineUse::whereIn('id', $medicine_uses_ids)->get()->toArray();
                $medicine_uses = ProductMedicineuse::select('medicine_uses.id as usesid', 'medicine_uses.name', 'product_medicineuses.*')
                    ->leftJoin('medicine_uses', 'medicine_uses.id', 'product_medicineuses.medicine_use')
                    ->where('product_medicineuses.product_id', $product->id)->get();

                $product_variant_ids = explode(',', $product->variant_products);
                // dd( $product->variant_products);

                $product_variants = Product::select('products.*', 'product_images.product_image as product_image')
                    ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
                    ->where('products.hide_from_site', '!=', '1')
                    ->where('products.status', 'active');
                if ($product->variant_products) {
                    $product_variants = $product_variants->whereIn('products.id', $product_variant_ids);
                } else {
                    $product_variants = $product_variants->whereRaw("FIND_IN_SET(?, products.variant_products)", [$product->id]);
                }
                $product_variants = $product_variants->get();

                $product_images = Product_image::leftjoin('products', 'products.thumbnail', 'product_images.id')->where('product_id', $product->id)->get();

                $categories = Category::find($product->category_id);

                $productcontentIds = explode(',', $product->productcontent_id);
                $ProductContents = Productcontent::whereIn('id', $productcontentIds)->get()->toArray();

                $productsupplierIds = explode(',', $product->supplier);
                $ProductSuppliers = Productsupplier::whereIn('id', $productsupplierIds)->get()->toArray();

                //---------------Get product review--
                $reviews = $product->reviews()->with('user')->approved()->notSpam()->orderBy('created_at', 'desc')->paginate(10);
                $total_reviews = $product->reviews()->with('user')->approved()->notSpam()->Onlyreview()->orderBy('created_at', 'desc')->count();
                $total_ratings = $product->reviews()->with('user')->approved()->notSpam()->OnlyRate()->orderBy('created_at', 'desc')->count();

                $ReviewAllow = false;
                if (Auth::guard('user')->user()) {
                    $ReviewExist = ProductReview::where('user_id', Auth::guard('user')->user()->id)->where('product_id', $product->id)->exists();

                    $orders = OrderDetails::join('orders', 'orders.id', 'order_details.order_id')
                        ->where('orders.user_id', Auth::guard('user')->user()->id)
                        ->where('order_details.product_id', $product->id)
                        ->where('order_details.status', 'delivered')
                        ->count();
                    if (!$ReviewExist && $orders > 0) {
                        $ReviewAllow = true;
                    }
                }

                // $similarUse = Product::select('products.*', 'product_images.product_image as product_image')
                //     ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
                //     ->where('products.hide_from_site', '!=', '1')
                //     ->where('products.status', 'active')
                //     ->where('products.medicine_use', $product->medicine_use)
                //     ->where('products.medicine_use', '<>', null)
                //     ->where('products.medicine_use', '<>', 0)
                //     ->where('products.id', '<>', $product->id);
                // $similarUse = $similarUse->limit(6)->get();
                $medicineusesIds = array();
                $medicineuses_name = array();
                // dd($medicine_uses);
                foreach ($medicine_uses as $row) {
                    $medicineusesIds[] = $row->usesid;
                    $medicineuses_name[] = $row->medicine_for . ' ' . $row->name;
                }

                $similarUse = Product::select('products.*', 'product_images.product_image as product_image')
                    ->Join('product_medicineuses', 'product_medicineuses.product_id', 'products.id')
                    ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
                    ->where('products.hide_from_site', '!=', '1')
                    ->where('products.status', 'active')
                    ->whereIn('product_medicineuses.medicine_use', $medicineusesIds)
                    ->where('products.id', '<>', $product->id);
                $similarUse = $similarUse->groupBy('products.id')->limit(6)->get();

                $similarcontentproducts = array();
                if ($product->productcontent_id != '') {
                    $contentId = explode(',', $product->productcontent_id);
                    $similarcontentproducts = Product::select('products.*', 'product_images.product_image as product_image')
                        ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
                        ->leftJoin('productcontents', DB::raw('FIND_IN_SET(productcontents.id, products.productcontent_id)'), '>', DB::raw("'0'"))
                        ->where('products.hide_from_site', '!=', '1')
                        ->where('products.status', 'active')
                        ->whereIn('productcontents.id', $contentId)
                        ->where('products.id', '<>', $product->id)->limit(6)->distinct()->get();
                }

                $relatedProducts = Product::select('products.*', 'product_images.product_image as product_image')
                    ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
                    ->where('products.hide_from_site', '!=', '1')
                    ->where('products.status', 'active')
                    ->where('products.category_id', $product->category_id)
                    ->where('products.category_id', '<>', null)
                    ->where('products.id', '<>', $product->id);
                $relatedProducts = $relatedProducts->limit(6)->get();

                //--Prescription approved product purchase--
                $data['allowPurchase'] = false;
                $data['enablePrescription'] = true;
                $data['prescriptionId'] = 0;
                $data['show_approverdetails'] = false;
                $approverDetails = '';

                $shareComponent = \Share::page(
                    url('/item/' . $product_url),
                    'www.expressmed.in',
                )
                    ->facebook()
                    ->twitter()
                    ->whatsapp();

                if (Auth::guard('user')->user()) {
                    if ($request->has('user_id') && $request->has('prescription_id')) {
                        $userId = key($request->user_id);
                        $data['prescriptionId'] = $prescriptionId = key($request->prescription_id);

                        $prescription = Prescription::where('id', $prescriptionId)->where('user_id', $userId)->where('product_id', $product->id)->where('status', 2)->first();
                        if ($prescription) {
                            if (Auth::guard('user')->user()->id == $prescription->user_id) {
                                $approverDetails = Admin::where('id', $prescription->approved_by)->first();

                                $data['allowPurchase'] = true;
                                $data['enablePrescription'] = false;
                                $data['show_approverdetails'] = true;
                            }
                        }
                    }
                }
                // dd($product);
                return view('productdetails', $data, compact('product', 'product_images', 'categories', 'ProductContents', 'ProductSuppliers', 'reviews', 'total_reviews', 'total_ratings', 'ReviewAllow', 'similarUse', 'similarcontentproducts', 'relatedProducts', 'approverDetails', 'medicine_uses', 'type', 'product_variants', 'medicineusesIds', 'medicineuses_name', 'shareComponent'));
            } else {
                return view('notfound_frontview')->withErrors('Product details not found.');
            }
        } else {
            return view('notfound_frontview')->withErrors('requested url is wrong. go to back and load again.');
        }
    }

    public function productlisting(Request $request)
    {
        $all_brands = Productbrand::select('image', 'id', 'name')->orderBy('name', 'asc')->get();
        $all_categories = Category::where('parent_id', 0)->where('status', 'active')->orderBy('name', 'asc')->get();
        $all_producttypes = Producttype::select('id', 'producttype')->orderBy('type', 'asc')->orderBy('producttype', 'asc')->get();
        $all_medicineuse = MedicineUse::orderBy('name', 'asc')->get();
        $all_med_cat = Category::where('parent_id', 0)->where('name', 'All Medicines')->where('status', 'active')->first();
        $all_med_child_categoryIds = [];
        if ($all_med_cat) {
            $all_med_categoryIds = [];
            array_push($all_med_categoryIds, $all_med_cat->id);
            array_push($all_med_child_categoryIds, $all_med_cat->id);

            $obj_category = new Category();
            $all_med_child_category = $obj_category->getCategories($all_med_categoryIds);

            $all_med_child_categoryIds = $this->getCategoryIds($all_med_child_category, $all_med_child_categoryIds);
        }
        $categoryIds = [];
        if ($request->productcategory != '') {
            foreach ($request->productcategory as $val) {
                array_push($categoryIds, $val);
            }
        }

        if ($request->has('hid_searchCategory') && $request->hid_searchCategory != 0) {
            array_push($categoryIds, $request->hid_searchCategory);
        }

        $obj_category = new Category();
        $child_category = $obj_category->getCategories($categoryIds);

        $child_categoryIds = [];
        $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);

        if ($request->productcategory != '') {
            foreach ($request->productcategory as $categoryvalue) {
                array_push($child_categoryIds, $categoryvalue);
            }
        }

        if ($request->has('hid_searchCategory') && ($request->hid_searchCategory != 0)) {
            array_push($child_categoryIds, $request->hid_searchCategory);
        }
        //    dd($request->hid_searchCategory);
        //--Get product listing under choosed category--
        $products = Product::select('products.*', 'product_images.product_image', DB::raw("GROUP_CONCAT(productcontents.name) as productcontent"))
            ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')

            ->leftjoin("productcontents", DB::raw("FIND_IN_SET(productcontents.id, products.productcontent_id)"), ">", DB::raw("'0'"))
            ->leftJoin('product_medicineuses', 'product_medicineuses.product_id', 'products.id')
            ->leftJoin('medicine_uses', 'medicine_uses.id', 'product_medicineuses.medicine_use')
            ->leftJoin('product_manufacturers', 'product_manufacturers.id', 'products.manufacturer');
            $products->where(function ($query) {
                $query->where('products.hide_from_site', '!=', '1')->where('products.status', 'active');
            });

        if ($request->has('search_keyword') && ($request->search_keyword != '') && (!empty($child_categoryIds))) {
            $products->where('products.product_name', 'LIKE', "%{$request->search_keyword}%");
            //  $products->orWhere('medicine_uses.name', 'LIKE', "%{$request->search_keyword}%");
            //  $products->orWhere('productcontents.name', 'LIKE', "%{$request->search_keyword}%");
        }

        if (!empty($child_categoryIds)) {

            $products->where(function ($query) use ($child_categoryIds) {
                $query->whereIn('products.category_id', $child_categoryIds)
                    ->orWhereIn('products.producttypeid', $child_categoryIds);
            });
        }

        if ($request->has('productbrand') && $request->productbrand != '') {
            $products->whereIn('products.brands', $request->productbrand);
        }

        if ($request->has('producttype') && $request->producttype != '') {
            $products->whereIn('products.producttypeid', $request->producttype);
        }

        if ($request->has('medicineuse') && $request->medicineuse != '') {
            $products->whereIn('product_medicineuses.medicine_use', $request->medicineuse);
            //     $arr_med_use=$request->medicineuse;
            //     foreach($arr_med_use as $key=>$value)
            //     $products->whereRaw("find_in_set('" . $value . "',products.medicine_use)");
        }
        if ($request->has('productcontents') && $request->productcontents != '') {
            $content_product = Product::where('product_url', $request->productcontents)->first();
            $contentId = explode(',', $content_product->productcontent_id);

            $products->whereIn('productcontents.id', $contentId);

        }

        if ($request->has('manufact_') && $request->manufact_ != '') {
            $products->where('product_manufacturers.name', $request->manufact_);
        }

        if ($request->has('search_keyword') && ($request->search_keyword != '') && (empty($child_categoryIds))) {
            $products->where(function ($query)use($request) {
                $query->where('products.product_name', 'LIKE', "%{$request->search_keyword}%");
                $query->orWhere('medicine_uses.name', 'LIKE', "%{$request->search_keyword}%");
                $query->orWhere('productcontents.name', 'LIKE', "%{$request->search_keyword}%");
            });

        }

        // if ($request->has('search_keyword') && ($request->search_keyword != '')&&(!empty($child_categoryIds))) {

        //              $products->where('products.product_name', 'LIKE', "%{$request->search_keyword}%");
        //              $products->orWhere('medicine_uses.name', 'LIKE', "%{$request->search_keyword}%");
        //              $products->orWhere('productcontents.name', 'LIKE', "%{$request->search_keyword}%");
        //          }

         $products=$products->groupBy("products.id")->paginate(40);
        return view('productlisting_page', compact('all_brands', 'all_categories', 'all_producttypes', 'all_medicineuse', 'products', 'all_med_child_categoryIds'))->with('i', ($request->input('page', 1) - 1) * 40);
    }

    public function category_productlisting(Request $request, $categoryname = null)
    {
        if ($categoryname) {
            $selectCategories = Category::where('status', 'active')->where('name', $categoryname)->first();
            if ($selectCategories) {

                $all_brands = Productbrand::select('image', 'id', 'name')->orderBy('name', 'asc')->get();
                $all_categories = Category::where('status', 'active')->orderBy('name', 'asc')->get();
                $all_producttypes = Producttype::select('id', 'producttype')->orderBy('type', 'asc')->orderBy('producttype', 'asc')->get();
                $all_medicineuse = MedicineUse::orderBy('name', 'asc')->get();
                $all_med_cat = Category::where('parent_id', 0)->where('name', 'All Medicines')->where('status', 'active')->first();
                $all_med_child_categoryIds = [];
                if ($all_med_cat) {
                    $all_med_categoryIds = [];
                    array_push($all_med_categoryIds, $all_med_cat->id);
                    array_push($all_med_child_categoryIds, $all_med_cat->id);

                    $obj_category = new Category();
                    $all_med_child_category = $obj_category->getCategories($all_med_categoryIds);

                    $all_med_child_categoryIds = $this->getCategoryIds($all_med_child_category, $all_med_child_categoryIds);
                }

                //Get all child categories id--
                $categoryIds = [];
                if ($request->productcategory != '') {
                    foreach ($request->productcategory as $val) {
                        array_push($categoryIds, $val);
                    }
                } else {
                    array_push($categoryIds, $selectCategories->id);
                }

                $obj_category = new Category();
                $child_category = $obj_category->getCategories($categoryIds);

                $child_categoryIds = [];
                $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);

                if ($request->productcategory != '') {
                    foreach ($request->productcategory as $categoryvalue) {
                        array_push($child_categoryIds, (int) $categoryvalue);
                    }
                } else {
                    array_push($child_categoryIds, $selectCategories->id);
                }

                //--Get product listing under choosed category--
                $products = Product::select('products.*', 'product_images.product_image')
                    ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                    ->where('products.hide_from_site', '!=', '1')
                    ->where('products.status', 'active');

                if (!empty($child_categoryIds)) {
                    // $products->whereIn('products.category_id', $child_categoryIds)
                    //     ->orWhereIn('products.producttypeid', $child_categoryIds);

                    $products->where(function ($query) use ($child_categoryIds) {
                        $query->whereIn('products.category_id', $child_categoryIds)
                            ->orWhereIn('products.producttypeid', $child_categoryIds);
                    });
                }

                if ($request->has('productbrand') && $request->productbrand != '') {
                    $products->whereIn('products.brands', $request->productbrand);
                }

                if ($request->has('producttype') && $request->producttype != '') {
                    $products->whereIn('products.producttypeid', $request->producttype);
                }

                if ($request->has('medicineuse') && $request->medicineuse != '') {
                    $products->whereIn('products.medicine_use', $request->medicineuse);
                }

                $products = $products->paginate(40);

                return view('productlisting_page', compact('selectCategories', 'all_brands', 'all_categories', 'all_producttypes', 'all_medicineuse', 'products', 'all_med_child_categoryIds'))->with('i', ($request->input('page', 1) - 1) * 40);
            } else {
                return view('notfound_frontview')->withErrors('choosed category is not valid.');
            }
        } else {
            return view('notfound_frontview')->withErrors('requested url is wrong. go to back and load again.');
        }
    }

    public function search_item(Request $request)
    {
        $search = $request->term;

        // //Get all child categories id--
        // $categoryIds = [];

        // if ($request->filled('categoryid') && $request->categoryid != 0) {
        //     array_push($categoryIds, $request->categoryid);
        // }
        // $obj_category = new Category();
        // $child_category = $obj_category->getCategories($categoryIds);

        // $child_categoryIds = [];
        // $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);

        // if ($request->filled('categoryid') && $request->categoryid != 0) {
        //     array_push($child_categoryIds, $request->categoryid);
        // }
        $product_ids=Product::Where('product_name', 'LIKE', "%{$search}%")->pluck('id')->toArray();
        $content_ids=ProductContent::Where('name', 'LIKE', "%{$search}%")->pluck('id')->toArray();
        $products = Product::select('products.id', 'products.not_for_sale', 'products.flag', 'products.product_name', 'products.product_url', 'product_images.product_image', 'products.price', 'products.offer_price', DB::raw('COUNT(products.id) as products_count'))
            ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
            // ->leftJoin('medicine_uses', 'medicine_uses.id', 'products.medicine_use')
            ->leftJoin('productcontents', DB::raw('FIND_IN_SET(productcontents.id, products.productcontent_id)'), '>', DB::raw("'0'"))
            ->where('products.hide_from_site', '!=', '1')
            ->where('products.status', 'active')
            ->where(function ($query) use ($search,$product_ids,$content_ids) {
                return $query
                   ->whereIn('products.id',$product_ids)
                    // ->where('products.product_name', 'LIKE', "%{$search}%")
                    // ->orWhere('medicine_uses.name', 'LIKE', "%{$search}%")
                    ->orWhereIn('productcontents.id', $content_ids);
            });
        if ($request->filled('categoryid') && $request->categoryid != 0) {

            $products->where('products.producttypeid', $request->categoryid);

        }
        $products = $products->orderBy('products.created_at', 'DESC')
            ->groupBy('products.id')
            ->limit(6)->get();
        // if (count($products)>0) {
        echo json_encode($products);

        // } else {
        // $products=array('products_count' => 0);
        // echo json_encode($products);
        // return response()->json(array('status' => 'sdsd', 'message' => 'sa', 'mode' => 'dsfs'));

        // echo json_encode(['products_count' => '0']);
        // return response()->json(['products_count' => 0]);
        // }

    }
    public function upload_prescription(Request $request)
    {
        if (Auth::guard('user')->user()) {
            $userID = Auth::guard('user')->user()->id;
            $prscriptionfile = $request->file('file');
            $validate = Validator::make($request->all(), [
                'file' => 'required|mimes:jpeg,jpg,png,pdf,doc',
            ]);

            if ($validate->fails()) {
                return response()->json(['result' => false, 'message' => $validate->errors()->first(), 'type' => 'enable']);
            } else {
                if ($prscriptionfile) {
                    // if (Prescription::where('user_id', $userID)->where('product_id', $request->product_id)->whereIn('status', [1, 2])->exists()) {
                    //     $exist_prescription = Prescription::where('user_id', $userID)->where('product_id', $request->product_id)->whereIn('status', [1, 2])->first();
                    //     $file_path = public_path('/assets/uploads/prescription/') . '/' . $exist_prescription->file;
                    //     File::delete($file_path);
                    //     Prescription::find($exist_prescription->id)->delete();
                    // }
                    $filename = 'presc_' . time() . '.' . $request->file->extension(); // $filename = $request->file->getClientOriginalName(); //Original file name
                    $request->file->move(public_path('/assets/uploads/prescription/'), $filename);

                    if (!isset($_SESSION)) {
                        session_start();
                    }
                    if (isset($_SESSION["product_prescriptions"]) && (is_array($_SESSION["product_prescriptions"]))) {

                        // $_SESSION['product_prescriptions']=array();
                    } else {
                        $_SESSION['product_prescriptions'] = array();
                    }
                    // if (count($_SESSION['product_prescriptions']) > 0) {
                    //     // dd($_SESSION['product_prescriptions']);
                    //     foreach ($_SESSION['product_prescriptions'] as $key1 => $value) {
                    //         // dd($value);
                    //         foreach ($value as $key2 => $value) {
                    //             if ($key2 == $request->product_id) {
                    //                 unset($_SESSION['product_prescriptions'][$key1]);

                    //             }

                    //         }
                    //     }
                    // }
                    // array_push($_SESSION['product_prescriptions'], [$filename]);
                    array_push($_SESSION['product_prescriptions'], $filename);
                    $products = $_SESSION['product_prescriptions'];
                    return response()->json(['result' => true, 'message' => '<span class="text-success"><small>Prescription uploaded successfully. Thank you</small></span>', 'type' => 'disable', 'file_name' => $filename]);
                    // return response()->json(['result' => true, 'message' => '<span class="text-success"><small>Prescription uploaded successfully. Thank you</small></span>', 'type' => 'disable', 'prescription_id' => $prescription_id]);

                    // } else {

                    // $prescription = Prescription::where('user_id', $userID)->where('product_id', $request->product_id)->whereIn('status', [1, 2])->first();

                    // }

                    // $prescription_id = $prescription->id;

                    // return response()->json(['result' => false, 'message' => '<span class="text-danger"><small>Prescription file already uploaded.</small></span>', 'type' => 'enable', 'prescription_id' => $prescription_id]);
                    // }
                } else {
                    return response()->json(['result' => false, 'message' => '<span class="text-danger"><small>Prescription file not found.</small></span>', 'type' => 'enable']);
                }
            }
        } else {
            return response()->json(['result' => false, 'message' => '<span class="text-danger"><small>Please login to continue.</small></span>', 'type' => 'enable']);
        }
    }
    public function delete_prescription(Request $request)
    {
        if (Auth::guard('user')->user()) {
            $userID = Auth::guard('user')->user()->id;
            $prscriptionfile = $request->file_name;

            if ($prscriptionfile) {
                if (!isset($_SESSION)) {
                    session_start();
                }
                if (isset($_SESSION["product_prescriptions"]) && (is_array($_SESSION["product_prescriptions"]))) {

                    // $_SESSION['product_prescriptions']=array();
                } else {
                    $_SESSION['product_prescriptions'] = array();
                }
                if (count($_SESSION['product_prescriptions']) > 0) {
                    foreach ($_SESSION['product_prescriptions'] as $k => $value) {

                        if ($value == $prscriptionfile) {
                            unset($_SESSION['product_prescriptions'][$k]);
                        }
                    }
                }
                return response()->json(['result' => true, 'message' => '<span class="text-success"><small>Prescription uploaded successfully. Thank you</small></span>', 'type' => 'disable']);
            }

        } else {
            return response()->json(['result' => false, 'message' => '<span class="text-danger"><small>Please login to continue.</small></span>', 'type' => 'enable']);
        }
    }

    // public function upload_prescription1(Request $request)
    // {
    //     if (Auth::guard('user')->user()) {
    //         $userID = Auth::guard('user')->user()->id;
    //         $prscriptionfile = $request->file('file');
    //         $validate = Validator::make($request->all(), [
    //             'file' => 'required|mimes:jpeg,jpg,png,pdf,doc',
    //         ]);

    //         if ($validate->fails()) {
    //             return response()->json(['result' => false, 'message' => $validate->errors()->first(), 'type' => 'enable']);
    //         } else {
    //             if ($prscriptionfile) {
    //                 if (Prescription::where('user_id', $userID)->where('product_id', $request->product_id)->whereIn('status', [1, 2])->exists()) {
    //                     $exist_prescription = Prescription::where('user_id', $userID)->where('product_id', $request->product_id)->whereIn('status', [1, 2])->first();
    //                     $file_path = public_path('/assets/uploads/prescription/') . '/' . $exist_prescription->file;
    //                     File::delete($file_path);
    //                     Prescription::find($exist_prescription->id)->delete();
    //                 }
    //                 $filename = 'presc_' . time() . '.' . $request->file->extension(); // $filename = $request->file->getClientOriginalName(); //Original file name
    //                 $request->file->move(public_path('/assets/uploads/prescription/'), $filename);

    //                 if (!isset($_SESSION)) {
    //                     session_start();
    //                 }
    //                 if (isset($_SESSION["product_prescriptions"]) && (is_array($_SESSION["product_prescriptions"]))) {

    //                     // $_SESSION['product_prescriptions']=array();
    //                 } else {
    //                     $_SESSION['product_prescriptions'] = array();
    //                 }
    //                 if (count($_SESSION['product_prescriptions']) > 0) {
    //                     // dd($_SESSION['product_prescriptions']);
    //                     foreach ($_SESSION['product_prescriptions'] as $key1 => $value) {
    //                         // dd($value);
    //                         foreach ($value as $key2 => $value) {
    //                             if ($key2 == $request->product_id) {
    //                                 unset($_SESSION['product_prescriptions'][$key1]);

    //                             }

    //                         }
    //                     }
    //                 }
    //                 array_push($_SESSION['product_prescriptions'], [$request->product_id => $filename]);
    //                 $products = $_SESSION['product_prescriptions'];

    //                 return response()->json(['result' => true, 'message' => '<span class="text-success"><small>Prescription uploaded successfully. Thank you</small></span>', 'type' => 'disable']);
    //                 // return response()->json(['result' => true, 'message' => '<span class="text-success"><small>Prescription uploaded successfully. Thank you</small></span>', 'type' => 'disable', 'prescription_id' => $prescription_id]);

    //                 // } else {

    //                 // $prescription = Prescription::where('user_id', $userID)->where('product_id', $request->product_id)->whereIn('status', [1, 2])->first();

    //                 // }

    //                 // $prescription_id = $prescription->id;

    //                 // return response()->json(['result' => false, 'message' => '<span class="text-danger"><small>Prescription file already uploaded.</small></span>', 'type' => 'enable', 'prescription_id' => $prescription_id]);
    //                 // }
    //             } else {
    //                 return response()->json(['result' => false, 'message' => '<span class="text-danger"><small>Prescription file not found.</small></span>', 'type' => 'enable']);
    //             }
    //         }
    //     } else {
    //         return response()->json(['result' => false, 'message' => '<span class="text-danger"><small>Please login to continue.</small></span>', 'type' => 'enable']);
    //     }
    // }

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

    public function cart(Request $request)
    {
        $user_id = '';
        $carts = array();

        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
        }

        if (empty($user_id)) {

            if (!isset($_SESSION)) {
                session_start();
            }

            $Guest_Cart = (isset($_SESSION['Session_GuestCart'])) ? $_SESSION['Session_GuestCart'] : [];
            if ($Guest_Cart) {
                $GuestCart_Array = [];
                $cnt = 0;
                foreach ($Guest_Cart as $productID => $value) {
                    $GuestCart_Product = Product::leftJoin('product_images', 'products.thumbnail', 'product_images.id')
                        ->where('products.id', $productID)
                        ->select('products.id as product_id', 'products.product_name', 'products.tax_ids', 'product_images.product_image', 'products.offer_price', 'products.price as original_price', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                        ->first();
                    $percent = 0;
                    if ($GuestCart_Product->offer_price != 0) {
                        $percent = number_format((($GuestCart_Product->original_price - $GuestCart_Product->offer_price) * 100) / $GuestCart_Product->original_price);
                    }

                    if ($GuestCart_Product) {
                        if ($GuestCart_Product->tax_ids != null && isset($GuestCart_Product->tax_ids)) {
                            $tax_ids = explode(',', $GuestCart_Product->tax_ids);
                            $GuestCart_Array[$cnt]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                        }

                        $GuestCart_Array[$cnt]['product_id'] = $GuestCart_Product->product_id;
                        $GuestCart_Array[$cnt]['product_name'] = $GuestCart_Product->product_name;
                        $GuestCart_Array[$cnt]['product_image'] = $GuestCart_Product->product_image;
                        $GuestCart_Array[$cnt]['ProductPrice'] = $GuestCart_Product->ProductPrice;
                        $GuestCart_Array[$cnt]['quantity'] = $value['quantity'];
                        $GuestCart_Array[$cnt]['offer_percent'] = $percent;
                    }
                    $cnt++;
                }

                $carts = $GuestCart_Array;
            }
        } else {
            $carts = Cart::join('products', 'carts.product_id', 'products.id')
                ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                ->select('carts.*', 'products.product_name', 'products.tax_ids', 'products.product_url', 'product_images.product_image', 'products.offer_price', 'products.price as original_price', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                ->where('carts.user_id', $user_id)
                ->get();

            if ($carts) {
                foreach ($carts as $key => $value) {
                    if ($value->tax_ids != null && isset($value->tax_ids)) {
                        $tax_ids = explode(',', $value->tax_ids);
                        $carts[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                    }
                    if ($value->offer_price != 0) {
                        $percent = number_format((($value->original_price - $value->offer_price) * 100) / $value->original_price);
                        $carts[$key]['offer_percent'] = $percent;
                    }
                }
            }
        }

        return view('product_cart', compact('carts'));
    }

    public function productaddcart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|gt:0',
        ]);

        if ($validator->fails()) {
            return ['success' => false, "errorMsg" => $validator->errors()->first()];
        } else {
            $user_id = '';
            if (Auth::guard('user')->user()) {
                $user_id = Auth::guard('user')->user()->id;
            }

            if (empty($user_id)) {
                if (!isset($_SESSION)) {
                    session_start();
                }
                // unset($_SESSION['Session_GuestCart']);

                $GuestCart = isset($_SESSION['Session_GuestCart']) ? $_SESSION['Session_GuestCart'] : [];

                // dd($GuestCart);
                if (!$GuestCart) {
                    //If cart is empty first product into cart--

                    $GuestCart[$request->product_id] = array(
                        'product_id' => $request->product_id,
                        'quantity' => $request->quantity,
                        'ip' => $this->getIp(),
                    );
                    $_SESSION['Session_GuestCart'] = $GuestCart;
                } else {
                    if (array_key_exists($request->product_id, $GuestCart)) {
                        //If this product exist in cart then increment quantity--

                        $GuestCart[$request->product_id]['quantity'] += $request->quantity;

                        $_SESSION['Session_GuestCart'] = $GuestCart;
                    } else if ($GuestCart) {
                        //If this product not exist then add this to cart--

                        $GuestCart[$request->product_id] = array(
                            'product_id' => $request->product_id,
                            'quantity' => $request->quantity,
                            'ip' => $this->getIp(),
                        );
                        $_SESSION['Session_GuestCart'] = $GuestCart;
                    }
                }

            } else {
                $carts = Cart::Where('product_id', $request->product_id)->where('user_id', $user_id)->exists();

                if (!$carts) {
                    $cart = Cart::create(['user_id' => $user_id,
                        'product_id' => $request->product_id,
                        'quantity' => $request->quantity,
                        'ip' => $this->getIp(),
                    ]);
                } else {
                    $cart = Cart::where('user_id', $user_id)
                        ->where('product_id', $request->product_id)
                        ->increment('quantity', $request->quantity);
                }
            }

            $cartdata = array();

            if (empty($user_id)) {
                $Guest_Cart = $_SESSION['Session_GuestCart'];

                $GuestCart_Array = [];
                $cnt = 0;
                foreach ($Guest_Cart as $key => $value) {
                    $GuestCart_Product = Product::leftJoin('product_images', 'products.thumbnail', 'product_images.id')
                        ->where('products.id', $key)
                        ->select('products.id', 'products.product_name', 'products.product_url', 'products.tax_ids', 'product_images.product_image', 'products.offer_price', 'products.price as original_price', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                        ->first();
                    $percent = 0;
                    if ($GuestCart_Product->offer_price != 0) {
                        $percent = number_format((($GuestCart_Product->original_price - $GuestCart_Product->offer_price) * 100) / $GuestCart_Product->original_price);
                    }

                    if ($GuestCart_Product) {
                        if ($GuestCart_Product->tax_ids != null && isset($GuestCart_Product->tax_ids)) {
                            $tax_ids = explode(',', $GuestCart_Product->tax_ids);
                            $GuestCart_Array[$cnt]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                        }

                        $GuestCart_Array[$cnt]['product_id'] = $GuestCart_Product->id;
                        $GuestCart_Array[$cnt]['product_name'] = $GuestCart_Product->product_name;
                        $GuestCart_Array[$cnt]['product_url'] = $GuestCart_Product->product_url;
                        $GuestCart_Array[$cnt]['product_image'] = $GuestCart_Product->product_image;
                        $GuestCart_Array[$cnt]['ProductPrice'] = $GuestCart_Product->ProductPrice;
                        $GuestCart_Array[$cnt]['quantity'] = $value['quantity'];
                        $GuestCart_Array[$cnt]['offer_percent'] = $percent;
                    }
                    $cnt++;
                }
                $cartdata = $GuestCart_Array;
            } else {
                $cartdata = Cart::join('products', 'carts.product_id', 'products.id')
                    ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                    ->select('carts.*', 'products.product_name', 'products.product_url', 'products.tax_ids', 'product_images.product_image', 'products.offer_price', 'products.price as original_price', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                    ->where('carts.user_id', $user_id)
                    ->get();

                if ($cartdata) {
                    foreach ($cartdata as $key => $value) {
                        if ($value->tax_ids != null && isset($value->tax_ids)) {
                            $tax_ids = explode(',', $value->tax_ids);
                            $cartdata[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                        }
                        if ($value->offer_price != 0) {
                            $percent = number_format((($value->original_price - $value->offer_price) * 100) / $value->original_price);
                            $cartdata[$key]['offer_percent'] = $percent;
                        }
                    }
                }
            }
            return ['success' => true, 'data' => $cartdata];
        }
    }

    public function productdeletecart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return ['success' => false, "errorMsg" => $validator->errors()->first()];
        } else {
            $user_id = '';
            if (Auth::guard('user')->user()) {
                $user_id = Auth::guard('user')->user()->id;
            }
            $percent = 0;

            switch ($request->quantity) {
                case 'single':
                    if (empty($user_id)) {
                        if (!isset($_SESSION)) {
                            session_start();
                        }
                        $GuestCart = isset($_SESSION['Session_GuestCart']) ? $_SESSION['Session_GuestCart'] : [];

                        if ($GuestCart) {
                            if (array_key_exists($request->product_id, $GuestCart)) {
                                if ($GuestCart[$request->product_id]['quantity'] > 1) {
                                    $GuestCart[$request->product_id]['quantity']--;
                                    $_SESSION['Session_GuestCart'] = $GuestCart;
                                }
                            }
                        }

                    } else {
                        $carts = Cart::where('user_id', $user_id)->Where('product_id', $request->product_id)->first();

                        if ($carts) {
                            if ($carts->quantity > 1) {
                                Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->decrement('quantity', 1);
                            }
                        }
                    }
                    break;
                case 'product':
                    if (empty($user_id)) {
                        if (!isset($_SESSION)) {
                            session_start();
                        }
                        $GuestCart = isset($_SESSION['Session_GuestCart']) ? $_SESSION['Session_GuestCart'] : [];

                        if ($GuestCart) {
                            if (array_key_exists($request->product_id, $GuestCart)) {
                                unset($GuestCart[$request->product_id]);
                                $_SESSION['Session_GuestCart'] = $GuestCart;
                            }
                        }
                    } else {
                        Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->delete();
                    }
                    break;
                case 'all':
                    if (empty($user_id)) {

                        if (!isset($_SESSION)) {
                            session_start();
                        }

                        $GuestCart = isset($_SESSION['Session_GuestCart']) ? $_SESSION['Session_GuestCart'] : [];

                        if ($GuestCart) {
                            unset($_SESSION['Session_GuestCart']);
                        }
                    } else {
                        Cart::where('user_id', $user_id)->delete();
                    }
                    break;
            }

            $cartdata = array();
            if (empty($user_id)) {
                $Guest_Cart = isset($_SESSION['Session_GuestCart']) ? $_SESSION['Session_GuestCart'] : [];

                $GuestCart_Array = [];
                $cnt = 0;
                foreach ($Guest_Cart as $key => $value) {
                    $GuestCart_Product = Product::leftJoin('product_images', 'products.thumbnail', 'product_images.id')
                        ->where('products.id', $key)
                        ->select('products.id as products_id', 'products.product_name', 'products.product_url', 'products.tax_ids', 'product_images.product_image', 'products.offer_price', 'products.price as original_price', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                        ->first();
                    $percent = 0;
                    if ($GuestCart_Product->offer_price != 0) {
                        $percent = number_format((($GuestCart_Product->original_price - $GuestCart_Product->offer_price) * 100) / $GuestCart_Product->original_price);
                    }

                    if ($GuestCart_Product) {
                        if ($GuestCart_Product->tax_ids != null && isset($GuestCart_Product->tax_ids)) {
                            $tax_ids = explode(',', $GuestCart_Product->tax_ids);
                            $GuestCart_Array[$cnt]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                        }

                        $GuestCart_Array[$cnt]['product_id'] = $GuestCart_Product->products_id;
                        $GuestCart_Array[$cnt]['product_url'] = $GuestCart_Product->product_url;
                        $GuestCart_Array[$cnt]['product_name'] = $GuestCart_Product->product_name;
                        $GuestCart_Array[$cnt]['product_image'] = $GuestCart_Product->product_image;
                        $GuestCart_Array[$cnt]['ProductPrice'] = $GuestCart_Product->ProductPrice;
                        $GuestCart_Array[$cnt]['quantity'] = $value['quantity'];
                        $GuestCart_Array[$cnt]['offer_percent'] = $percent;
                    }
                    $cnt++;
                }
                $cartdata = $GuestCart_Array;
            } else {
                $cartdata = Cart::join('products', 'carts.product_id', 'products.id')
                    ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                    ->select('carts.*', 'products.product_name', 'products.product_url', 'products.tax_ids', 'product_images.product_image', 'products.offer_price', 'products.price as original_price', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                    ->where('carts.user_id', $user_id)
                    ->get();

                if ($cartdata) {
                    foreach ($cartdata as $key => $value) {
                        if ($value->tax_ids != null && isset($value->tax_ids)) {
                            $tax_ids = explode(',', $value->tax_ids);
                            $cartdata[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                        }
                        if ($value->offer_price != 0) {
                            $percent = number_format((($value->original_price - $value->offer_price) * 100) / $value->original_price);
                            $cartdata[$key]['offer_percent'] = $percent;
                        }
                    }
                }
            }
            return ['success' => true, 'data' => $cartdata];
        }
    }

    public function checkout(Request $request)
    {
        $address_type = '';
        $user_id = 0;
        $carts = $user_details = $BuyNow = $prescriptiondetails = [];

        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
        }

        if ($request->checkouttype) {
            $checkout_type = 'BuyNow';
        } else {
            $checkout_type = 'Cart';
        }

        $countries = Country::get();

        $user_address = UserAddress::join('users', 'users.id', 'user_addresses.user_id')
            ->where('user_addresses.user_id', $user_id)
            ->select('user_addresses.*', 'users.email')
            ->get();

        foreach ($user_address as $address) {
            $user_details[$address->type] = array(
                "name" => $address->name,
                "email" => $address->email,
                "phone" => $address->phone,
                "pin" => $address->pin,
                "location" => $address->location,
                "address" => $address->address,
                "city" => $address->city,
                "state_id" => $address->state_id,
                "country_id" => $address->country_id,
                "landmark" => $address->landmark,
                "type" => $address->type,
            );
        }

        if ($request->has('checkouttype') && $request->checkouttype != '' & $request->checkouttype == 'direct_buy') {
            //---------Buy Now without cart items--
            if ($request->productid) {
                $ProductDetails = Product::select('products.id as product_id', 'products.product_name', 'products.prescription', 'products.tax_ids', 'products.product_url', 'product_images.product_image', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), 'products.prescription')
                    ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                    ->where('products.id', $request->productid)
                    ->first();

                if ($ProductDetails) {
                    $BuyNow['ProductDetails'] = $ProductDetails;
                    $BuyNow['quantity'] = ($request->quantity != null ? $request->quantity : 1);
                    if ($ProductDetails->tax_ids != '' && isset($ProductDetails->tax_ids)) {
                        $tax_ids = explode(',', $ProductDetails->tax_ids);
                        $BuyNow['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                    }

                    // if($ProductDetails->prescription == 1) {
                    //     $prescriptiondetails = Prescription::where('user_id', $user_id)->where('product_id', $ProductDetails->product_id)->where('status', 2)->first();
                    //     if($prescriptiondetails){
                    //         $BuyNow['quantity'] = $prescriptiondetails->allowed_qty;
                    //     } else {
                    //         return redirect()->route('shopping.productdetail', $ProductDetails->product_url)->withErrors('Sorry...you are not able to buy this product.');
                    //     }
                    // }

                } else {
                    return redirect()->back()->withErrors('Sorry...Something went wrong the product details not found. Cannot buy the item.');
                }
            } else {
                return redirect()->back()->withErrors('Sorry...Something went wrong the product not found. Cannot buy the item.');
            }
        } else {
            if (Auth::guard('user')->user()) {
                $carts = Cart::join('products', 'carts.product_id', 'products.id')
                    ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                    ->select('carts.*', 'products.product_name', 'products.prescription', 'products.tax_ids', 'products.product_url', 'product_images.product_image', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                    ->where('carts.user_id', $user_id)
                    ->get();

                if ($carts->isNotEmpty()) {
                    foreach ($carts as $key => $value) {
                        if ($value->tax_ids != null && isset($value->tax_ids)) {
                            $tax_ids = explode(',', $value->tax_ids);
                            $carts[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                        }
                    }
                } else {
                    return redirect()->back()->withErrors('Sorry...Your cart is empty.');
                }
            } else {
                if (!isset($_SESSION)) {
                    session_start();
                }
                $Guest_Cart = (isset($_SESSION['Session_GuestCart'])) ? $_SESSION['Session_GuestCart'] : [];

                if ($Guest_Cart) {
                    $cnt = 0;
                    foreach ($Guest_Cart as $productID => $value) {
                        $GuestCart_Product = Product::leftJoin('product_images', 'products.thumbnail', 'product_images.id')
                            ->where('products.id', $productID)
                            ->select('products.id as product_id', 'products.product_name', 'products.prescription', 'products.tax_ids', 'product_images.product_image', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                            ->first();

                        if ($GuestCart_Product) {
                            if ($GuestCart_Product->tax_ids != null && isset($GuestCart_Product->tax_ids)) {
                                $tax_ids = explode(',', $GuestCart_Product->tax_ids);
                                $GuestCart_Array[$cnt]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                            }

                            $GuestCart_Array[$cnt]['product_id'] = $GuestCart_Product->product_id;
                            $GuestCart_Array[$cnt]['product_name'] = $GuestCart_Product->product_name;
                            $GuestCart_Array[$cnt]['product_image'] = $GuestCart_Product->product_image;
                            $GuestCart_Array[$cnt]['ProductPrice'] = $GuestCart_Product->ProductPrice;
                            $GuestCart_Array[$cnt]['quantity'] = $value['quantity'];
                        }
                        $cnt++;
                    }
                    $carts = $GuestCart_Array;
                } else {
                    return redirect()->back()->withErrors('Sorry...Your cart is empty.');
                }
            }
        }

        return view('productcheckout', compact('carts', 'user_details', 'countries', 'BuyNow', 'checkout_type', 'prescriptiondetails'));
    }

    public function checkout_UpdateAddress(Request $request)
    {
        $user_id = 0;
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
        }

        if ($request->checkouttype) {
            $checkout_type = 'BuyNow';
        } else {
            $checkout_type = 'Cart';
        }

        if (!Auth::guard('user')->user()) {
            $validator = Validator::make($request->all(), [
                'address_name' => 'required',
                'address_phone' => 'required|regex:/[0-9]{9}/',
                'delivery_address' => 'required',
                'address_address' => 'required',
                'address_location' => 'required',
                'address_city' => 'required',
                'address_pin' => 'required',
                'country' => 'required|numeric',
                'state' => 'required|numeric',
                'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
            ], ['address_address.required' => 'The address field is required.']);
        } else {
            $validator = Validator::make($request->all(), [
                'address_name' => 'required',
                'address_phone' => 'required|regex:/[0-9]{9}/',
                'delivery_address' => 'required',
                'address_address' => 'required',
                'address_location' => 'required',
                'address_city' => 'required',
                'address_pin' => 'required',
                'country' => 'required|numeric',
                'state' => 'required|numeric',
            ], ['address_address.required' => 'The address field is required.']);
        }
        if ($validator->fails()) {
            return ['success' => false, "errorMsg" => $validator->errors()->first()];
        } else {
            $address_exist = false;
            if (Auth::guard('user')->user()) {
                $address_exist = UserAddress::where('user_id', $user_id)->where('type', $request->delivery_address)->first();
            }

            $addressID = null;
            if ($address_exist) {
                $addressID = $address_exist->id;
                UserAddress::where('type', $request->delivery_address)->where('user_id', $user_id)->update([
                    'email' => Auth::guard('user')->user()->email,
                    'name' => $request->address_name,
                    'phone' => $request->address_phone,
                    'pin' => $request->address_pin,
                    'location' => $request->address_location,
                    'address' => $request->address_address,
                    'city' => $request->address_city,
                    'state_id' => $request->state,
                    'country_id' => $request->country,
                    'landmark' => $request->address_landmark,
                ]);
            } else {
                $addressID = UserAddress::create([
                    'user_id' => $user_id,
                    'type' => $request->delivery_address,
                    'name' => $request->address_name,
                    'email' => $request->email,
                    'phone' => $request->address_phone,
                    'pin' => $request->address_pin,
                    'location' => $request->address_location,
                    'address' => $request->address_address,
                    'city' => $request->address_city,
                    'state_id' => $request->state,
                    'country_id' => $request->country,
                    'landmark' => $request->address_landmark,
                ])->id;
            }

            // $CheckOut_ProductIDs = array();
            // if($request->checkout_type == 'cart'){
            //     $CheckOut_ProductIDs = Cart::where('user_id', $user_id)->get('product_id')->toArray();
            // } elseif($request->checkout_type == 'direct_buy') {
            //     $CheckOut_ProductIDs[0]['product_id'] = $request->product_id;
            // }

            //--Check delivery availability coDe--
            $flag = true;
            // $RejectedProducts = [];
            // foreach($CheckOut_ProductIDs as $value){
            //     $Available = Product::join('delivery_locations as DL', 'DL.store_id', 'products.store_id')
            //                 ->where('products.id', $value['product_id'])
            //                 ->where('DL.pincode', $request->address_pin)
            //                 ->exists();

            //     if(!$Available){
            //         array_push($RejectedProducts, $value['product_id']);
            //         $flag = false;
            //     }
            // }

            if ($addressID != null) {
                if ($flag) {
                    return ['result' => true, 'checkout_type' => $checkout_type, 'addressId' => $addressID];
                } else {
                    return ['result' => false, 'errorMsg' => 'Delivery not available to your location.', 'checkout_type' => $checkout_type];
                }
            } else {
                return ['result' => false, 'errorMsg' => 'Something went wrong with your address. Requested address not found.', 'checkout_type' => $checkout_type];
            }
        }
    }

    public function placeOrder(Request $request)
    {
        $user_id = 0;
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
        }
        $OrderMasterArray = array();
        $OrderDetailsArray = array();
        $Product_SellerArray = array();
        $store_id = $request->store_id;

        if (Auth::guard('user')->user()) {

            $user_id = Auth::guard('user')->user()->id;

            if ($user_id) {
                $validator = Validator::make($request->all(), [
                    'address_type' => 'required',
                    'addressId' => 'required',
                    'payment_method' => 'required',
                ]);
                if ($validator->fails()) {
                    return ['result' => false, "errorMsg" => $validator->errors()->first(), "checkout_type" => "Cart"];
                } else {

                    if (!Cart::where('user_id', $user_id)->exists()) {
                        return ['result' => false, "errorMsg" => 'Checkout failed. Your cart is empty. Please add product to cart.', "checkout_type" => "Cart"];
                    } else {
                        if (!isset($_SESSION)) {
                            session_start();
                        }

                        if (!empty($request->cart_ids)) {
                            $cart_ids = explode(',', $request->cart_ids);

                            $address_exist = UserAddress::join('states', 'states.id', 'user_addresses.state_id')
                                ->where('user_addresses.id', $request->addressId)->where('user_addresses.user_id', $user_id)->where('user_addresses.type', $request->address_type)
                                ->select('user_addresses.*', 'states.name as state')->first();
                            if ($address_exist) {

                                $order = new Order();

                                $order->user_id = $user_id;
                                $order->address_id = $address_exist->id;
                                $order->status = 'initiated';
                                $order->payment_method = $request->payment_method;

                                $order->total_amount = $OrderMasterArray['total_amount'] = $request->total_amount;
                                $order->total_tax_amount = $OrderMasterArray['total_tax_amount'] = $request->total_tax_amount;
                                $order->shipping_charge = $OrderMasterArray['shipping_charge'] = $request->shipping_charge;
                                $order->grand_total = $OrderMasterArray['grand_total'] = $request->grandtotal;
                                $order->delivery_type = ($store_id != '' ? 'pickup' : 'direct');
                                $order->store_id = $store_id;
                                $order->date = date('Y-m-d H:i:s');
                                $order->save();

                                $OrderMasterArray['order_id'] = $order->id;

                                foreach ($cart_ids as $key => $cartid) {

                                    $cartproduct = Cart::where('id', $cartid)->first();

                                    if ($cartproduct) {
                                        $order_details = new OrderDetails();
                                        $order_details->order_id = $order->id;
                                        $order_details->product_id = $cartproduct->product_id;
                                        $order_details->quantity = $OrderDetailsArray[$key]['quantity'] = $cartproduct->quantity;

                                        $Productdetails = Product::leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                                            ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
                                            ->where('products.id', $cartproduct->product_id)
                                            ->select(DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), 'products.id as productid', 'products.product_name', 'products.tax_ids', 'GS.value AS email', 'product_images.product_image')
                                            ->first();
                                        $orderDetails['order_items'][$key]['name'] = $Productdetails->product_name;
                                        $orderDetails['order_items'][$key]['sku'] = $Productdetails->productid;
                                        $orderDetails['order_items'][$key]['units'] = $order_details->quantity;
                                        $orderDetails['order_items'][$key]['selling_price'] = $Productdetails->ProductPrice;
                                        $orderDetails['order_items'][$key]['discount'] = "";
                                        $orderDetails['order_items'][$key]['tax'] = "";
                                        $orderDetails['order_items'][$key]['hsn'] = '000';

                                        // $OrderDetailsArray[$key]['product_name'] = $Productdetails->product_name;
                                        // $OrderDetailsArray[$key]['product_image'] = $Productdetails->product_image;

                                        $order_details->tax_ids = $Productdetails->tax_ids;

                                        $total_taxRate = $total_tax_percent = $total_tax = $total_tax_percent = $totalvalue = $total_tax_percent_value = 0;
                                        if ($Productdetails->tax_ids != '') {
                                            $tax_ids = explode(',', $Productdetails->tax_ids);
                                            $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                            foreach ($TaxDetails as $value) {
                                                $total_tax_percent = $total_tax_percent + $value->percentage;

                                                // $total_taxRate += ($Productdetails->ProductPrice * $value->percentage) / 100;
                                            }
                                            $total_tax_percent_value = ($Productdetails->ProductPrice * 100) / ($total_tax_percent + 100);
                                            $totalvalue = $totalvalue + ($Productdetails->ProductPrice - $total_tax_percent_value) * $cartproduct->quantity;

                                        }else{
                                            $total_tax_percent_value = $Productdetails->ProductPrice;

                                        }
                                        $order_details->total_tax = $totalvalue;
                                        $order_details->price = $OrderDetailsArray[$key]['price'] = ($total_tax_percent_value);
                                        $order_details->amount = $OrderDetailsArray[$key]['amount'] = ($cartproduct->quantity * $Productdetails->ProductPrice);

                                        // $Product_SellerArray[$Productdetails->email][$key]['storename'] = $Productdetails->storename;
                                        // $Product_SellerArray[$Productdetails->email][$key]['productname'] = $Productdetails->product_name;
                                        // $Product_SellerArray[$Productdetails->email][$key]['variant_type'] = $Productdetails->variant_type;
                                        // $Product_SellerArray[$Productdetails->email][$key]['variant_unit'] = $Productdetails->variant_unit;
                                        // $Product_SellerArray[$Productdetails->email][$key]['quantity'] = $cartproduct->quantity;
                                        // $Product_SellerArray[$Productdetails->email][$key]['price'] = $OrderDetailsArray[$key]['price'];
                                        // $Product_SellerArray[$Productdetails->email][$key]['amount'] = $OrderDetailsArray[$key]['amount'];

                                        // dd($order_details);
                                        $order_details->save();
                                        $bulk_prescription_arr = array();

                                        if ($request->session()->has('session_data')) {
                                            $session_data = Session::get('session_data');

                                            // dd($session_data['prescription_files']);
                                            foreach ($session_data['prescription_files'] as $key => $file) {
                                                $prescription = new Prescription();
                                                $prescription->user_id = $user_id;
                                                $prescription->type = 'bulk';
                                                $prescription->file = $file;
                                                $prescription->order_id = $order->id;
                                                $prescription->status = 1;
                                                $prescription->save();
                                                $bulk_prescription_arr[] = $prescription->id;
                                            }
                                            $prescription_ids = '';
                                            if (count($bulk_prescription_arr) > 0) {
                                                $prescription_ids = implode(',', $bulk_prescription_arr);
                                            }

                                            Order::where('id', $order->id)->update(array('prescription_ids' => $prescription_ids));
                                            $request->session()->forget('session_data');
                                            // $prescription_id = $prescription->id;

                                        }

                                    }
                                    // dd($cart_ids);

                                }

                                $prescription_arr = array();

                                if (isset($_SESSION["product_prescriptions"]) && count($_SESSION['product_prescriptions']) > 0) {
                                    foreach ($_SESSION['product_prescriptions'] as $key1 => $value) {

                                        // foreach ($value as $key2 => $value) {

                                        $prescription = new Prescription();
                                        $prescription->user_id = $user_id;
                                        // $prescription->product_id = $key2;
                                        $prescription->order_id = $order->id;
                                        $prescription->file = $value;
                                        $prescription->status = 1;
                                        $prescription->save();
                                        $prescription_arr[] = $prescription->id;

                                        // }
                                    }

                                    $prescription_ids = '';
                                    if (count($prescription_arr) > 0) {
                                        $prescription_ids = implode(',', $prescription_arr);
                                    }
                                    Order::where('id', $order->id)->update(array('prescription_ids' => $prescription_ids));

                                    unset($_SESSION['product_prescriptions']);
                                }
                                // $invoice_number = $this->invoiceNumber();
                                // Invoice::create([
                                //     'user_id' => $order->user_id,
                                //     'order_id' => $order->id,
                                //     'invoice_number' => $invoice_number,
                                // ]);
                                // $this->customer_invoice_mail($order->id, $invoice_number);

                                $billing_address = UserAddress::join('countries', 'countries.id', 'user_addresses.country_id')
                                    ->join('states', 'states.id', 'user_addresses.state_id')
                                    ->select('user_addresses.*', 'countries.name as country', 'states.name as state')
                                    ->where('user_addresses.id', $address_exist->id)->first();

                                if ($request->payment_method == 'cod') {

                                    $this->cod_response($order->id, $request->checkout_type);
                                    return ['success' => true, 'order' => $order, 'payment_method' => $order->payment_method];

                                } elseif ($request->payment_method == 'online') {
                                    $razor_order_response = array();
                                    $jwtPayload = array();
                                    if ($request->payment_gateway == 'razorpay') {
                                        $api = new Api(config('constants.RAZORPAY_KEY'), config('constants.RAZORPAY_SECRET'));
                                        $razor_order_response = $api->order->create(array('receipt' => $order->id, 'amount' => (float) round($order->grand_total, 2) * 100, 'currency' => 'INR'));
                                        //  dd($razor_order_response);
                                    } elseif ($request->payment_gateway == 'billdesk') {

                                        $current_time = strtotime("now");
                                        $trace_id = $current_time . 'EXP';
                                        $sub_id = 'sub' . $current_time;
                                        $merchant_id = Config::get('constants.payment_constants.merchant_id');
                                        $client_id = Config::get('constants.payment_constants.client_id');
                                        $secret_key = Config::get('constants.payment_constants.secret_key');

                                        $headers = ["alg" => "HS256", "clientid" => $client_id];

                                        $grand_total = number_format($order->grand_total, 2);
                                        $grand_total = str_replace(",", "", $grand_total);
                                        // $order_date = gmdate(DATE_ATOM, mktime(date('H', strtotime($order->created_at)), date('i', strtotime($order->created_at)), date('s', strtotime($order->created_at)), date('m', strtotime($order->created_at)), date('d', strtotime($order->created_at)), date('Y', strtotime($order->created_at))));
                                        $order_date = date(DATE_ATOM, strtotime($order->date));

                                        $payload = [
                                            "mercid" => $merchant_id,
                                            "orderid" => $order->id,
                                            "amount" => $grand_total,
                                            "order_date" => $order_date,
                                            "currency" => "356",
                                            "ru" => route('payment.response'),
                                            "additional_info" => [
                                                "additional_info1" => $address_exist->name,
                                                "additional_info2" => $address_exist->email,
                                                "additional_info3" => $address_exist->phone,
                                                "additional_info4" => $address_exist->location,
                                                "additional_info5" => $address_exist->city,
                                                "additional_info6" => $address_exist->state,
                                                "additional_info7" => "NA",
                                            ],
                                            "itemcode" => "DIRECT",
                                            "device" => [
                                                "init_channel" => "internet",
                                                "ip" => $this->getIp(),
                                                "user_agent" => $request->userAgent(),
                                            ],
                                        ];
                                        $curl_payload = JWT::encode($payload, $secret_key, "HS256", null, $headers);

                                         $url = "https://api.billdesk.com/payments/ve1_2/orders/create";
                                        //$url = "https://pguat.billdesk.io/payments/ve1_2/orders/create";
                                        $now = now();
                                        $ch = curl_init($url);

                                        $ch_headers = array(
                                            "content-type: application/jose",
                                            "bd-timestamp: $current_time",
                                            "accept: application/jose",
                                            "bd-traceid: $trace_id",
                                        );
                                        curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
                                        curl_setopt($ch, CURLOPT_POST, 1);
                                        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_payload);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                                        $payment_token = curl_exec($ch);
                                        curl_close($ch);

                                        $tokenParts = explode(".", $payment_token);
                                        $tokenHeader = base64_decode($tokenParts[0]);
                                        $tokenPayload = base64_decode($tokenParts[1]);
                                        $jwtHeader = json_decode($tokenHeader);
                                        $jwtPayload = json_decode($tokenPayload);

                                        // foreach ($cart_ids as $key => $cartid) {
                                        //     Cart::where('id', $cartid)->delete();
                                        // }
                                        $razor_order_response['id'] = '';
                                    }
                                    unset($_SESSION['product_prescriptions']);

                                    // return ['success' => true, 'order' => $order, 'jwtPayload' => $jwtPayload, 'payment_method' => $request->payment_method];
                                    return ['success' => true, 'order' => $order, 'jwtPayload' => $jwtPayload, 'payment_method' => $request->payment_method, 'payment_gateway' => $request->payment_gateway, 'razor_order_response_id' => $razor_order_response['id'], 'useraddress' => $billing_address];

                                }

                            } else {
                                return ['result' => false, "errorMsg" => 'Checkout failed. Your address not found.', "checkout_type" => "Cart"];
                            }
                        } else {
                            return ['result' => false, "errorMsg" => 'Checkout failed. Items not found in your cart.', "checkout_type" => "Cart"];
                        }
                    }
                }
            } else {
                return ['result' => false, "errorMsg" => 'Please log in to continue', "checkout_type" => "Cart"];
            }
        } else {
            $validator = Validator::make($request->all(), [
                'address_type' => 'required',
                'addressId' => 'required',
                'payment_method' => 'required',
            ]);
            if ($validator->fails()) {
                return ['result' => false, "errorMsg" => $validator->errors()->first(), "checkout_type" => "Cart"];
            } else {
                $guestReturn = $this->guest_checkout($request->all());
                return $guestReturn;
            }
        }
    }

    private function guest_checkout(array $data)
    {
        $address_exist = UserAddress::join('states', 'states.id', 'user_addresses.state_id')
            ->where('user_addresses.id', $data['addressId'])
            ->where('user_addresses.type', $data['address_type'])->select('user_addresses.*', 'states.name as state')->first();
        if ($address_exist) {
            $order = new Order();
            $order->user_id = 0;
            $order->address_id = $address_exist->id;
            $order->status = 'initiated';
            $order->payment_method = $data['payment_method'];
            $order->delivery_type = ($data['store_id'] != '' ? 'pickup' : 'direct');
            $order->store_id = $data['store_id'];
            $order->total_amount = $data['total_amount'];
            $order->total_tax_amount = $data['total_tax_amount'];
            $order->shipping_charge = $data['shipping_charge'];
            $order->grand_total = $data['grandtotal'];
            $order->save();

            if ($order->id) {

                if (!isset($_SESSION)) {
                    session_start();
                }
                $Guest_Cart = (isset($_SESSION['Session_GuestCart'])) ? $_SESSION['Session_GuestCart'] : [];

                if ($Guest_Cart) {
                    foreach ($Guest_Cart as $productID => $value) {
                        $Productdetails = Product::leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                            ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
                            ->where('products.id', $productID)
                            ->select('products.id as productid', 'products.product_name', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), 'products.tax_ids', 'GS.value AS email', 'product_images.product_image')
                            ->first();

                        if ($Productdetails) {

                            $order_details = new OrderDetails();
                            $order_details->order_id = $order->id;
                            $order_details->product_id = $Productdetails->productid;
                            $order_details->quantity = $value['quantity'];

                            $order_details->tax_ids = $Productdetails->tax_ids;

                            $total_taxRate = $total_tax_percent = $total_tax = $total_tax_percent = $totalvalue = $total_tax_percent_value = 0;

                            if ($Productdetails->tax_ids != '') {
                                $tax_ids = explode(',', $Productdetails->tax_ids);
                                $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                foreach ($TaxDetails as $value) {
                                    $total_tax_percent = $total_tax_percent + $value->percentage;

                                    // $total_taxRate += ($Productdetails->ProductPrice * $value->percentage) / 100;
                                }
                                $total_tax_percent_value = ($Productdetails->ProductPrice * 100) / ($total_tax_percent + 100);
                                $totalvalue = $totalvalue + (($Productdetails->ProductPrice - $total_tax_percent_value) * $order_details->quantity);
                            }else{
                                $total_tax_percent_value = $Productdetails->ProductPrice;

                            }
                            $order_details->total_tax = $totalvalue;
                            $order_details->price = $total_tax_percent_value;
                            $order_details->amount = ($order_details->quantity * $Productdetails->ProductPrice);

                            $order_details->save();

                            // $orderDetails['order_items'][$productID]['name'] = $Productdetails->product_name;
                            // $orderDetails['order_items'][$productID]['sku'] = $Productdetails->productid;
                            // $orderDetails['order_items'][$productID]['units'] = $order_details->quantity;
                            // $orderDetails['order_items'][$productID]['selling_price'] = $Productdetails->ProductPrice;
                            // $orderDetails['order_items'][$productID]['discount'] = "";
                            // $orderDetails['order_items'][$productID]['tax'] = "";
                            // $orderDetails['order_items'][$productID]['hsn'] = '000';

                            unset($Guest_Cart[$Productdetails->productid]);
                        } else {
                            return ['result' => false, "errorMsg" => 'Checkout failed. Product details not found.', "checkout_type" => "Cart"];
                        }
                    }
                    $billing_address = UserAddress::join('countries', 'countries.id', 'user_addresses.country_id')
                        ->join('states', 'states.id', 'user_addresses.state_id')
                        ->select('user_addresses.*', 'countries.name as country', 'states.name as state')
                        ->where('user_addresses.id', $address_exist->id)->first();
                    if ($data['payment_method'] == 'cod') {

                        $this->cod_response($order->id, $data['checkout_type']);
                        return ['success' => true, 'order' => $order, 'payment_method' => $order->payment_method];

                    } elseif ($data['payment_method'] == 'online') {
                        $razor_order_response = array();
                        $jwtPayload = array();
                        if ($data['payment_gateway'] == 'razorpay') {

                            $api = new Api(config('constants.RAZORPAY_KEY'), config('constants.RAZORPAY_SECRET'));
                            $razor_order_response = $api->order->create(array('receipt' => $order->id, 'amount' => (float) round($order->grand_total, 2) * 100, 'currency' => 'INR'));
                        } elseif ($data['payment_gateway'] == 'billdesk') {

                            $current_time = strtotime("now");
                            $trace_id = $current_time . 'EXP';
                            $sub_id = 'sub' . $current_time;
                            $merchant_id = Config::get('constants.payment_constants.merchant_id');
                            $client_id = Config::get('constants.payment_constants.client_id');
                            $secret_key = Config::get('constants.payment_constants.secret_key');

                            $headers = ["alg" => "HS256", "clientid" => $client_id];

                            $grand_total = number_format($order->grand_total, 2);

                            $grand_total = str_replace(",", "", $grand_total);
                            // $order_date = gmdate(DATE_ATOM, mktime(date('H', strtotime($order->created_at)), date('i', strtotime($order->created_at)), date('s', strtotime($order->created_at)), date('m', strtotime($order->created_at)), date('d', strtotime($order->created_at)), date('Y', strtotime($order->created_at))));
                            $order_date = date(DATE_ATOM, strtotime($order->created_at));

                            $payload = [
                                "mercid" => $merchant_id,
                                "orderid" => $order->id,
                                "amount" => $grand_total,
                                "order_date" => $order_date,
                                "currency" => "356",
                                "ru" => route('payment.response'),
                                "additional_info" => [
                                    "additional_info1" => $address_exist->name,
                                    "additional_info2" => $address_exist->email,
                                    "additional_info3" => $address_exist->phone,
                                    "additional_info4" => $address_exist->location,
                                    "additional_info5" => $address_exist->city,
                                    "additional_info6" => $address_exist->state,
                                    "additional_info7" => "NA",
                                ],
                                "itemcode" => "DIRECT",
                                "device" => [
                                    "init_channel" => "internet",
                                    "ip" => $this->getIp(),
                                    "user_agent" => request()->userAgent(),
                                ],
                            ];
                            //dd($payload);
                            $curl_payload = JWT::encode($payload, $secret_key, "HS256", null, $headers);

                             $url = "https://api.billdesk.com/payments/ve1_2/orders/create";
                            //$url = "https://pguat.billdesk.io/payments/ve1_2/orders/create";
                            $now = now();
                            $ch = curl_init($url);

                            $ch_headers = array(
                                "content-type: application/jose",
                                "bd-timestamp: $current_time",
                                "accept: application/jose",
                                "bd-traceid: $trace_id",
                            );
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_payload);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            $payment_token = curl_exec($ch);
                            curl_close($ch);

                            $tokenParts = explode(".", $payment_token);
                            $tokenHeader = base64_decode($tokenParts[0]);
                            $tokenPayload = base64_decode($tokenParts[1]);
                            $jwtHeader = json_decode($tokenHeader);
                            $jwtPayload = json_decode($tokenPayload);
                            $razor_order_response['id'] = '';
                        }
                        // unset($_SESSION['Session_GuestCart']);

                        return ['success' => true, 'order' => $order, 'jwtPayload' => $jwtPayload, 'payment_method' => $data['payment_method'], 'payment_gateway' => $data['payment_gateway'], 'razor_order_response_id' => $razor_order_response['id'], 'useraddress' => $billing_address];

                    }

                } else {
                    return ['result' => false, "errorMsg" => 'Checkout failed. Your cart is empty.', "checkout_type" => "Cart"];
                }
            } else {
                return ['result' => false, "errorMsg" => 'Checkout failed. Your order not placed. Please try again.', "checkout_type" => "Cart"];
            }
        } else {
            return ['result' => false, "errorMsg" => 'Checkout failed. Your address not found.', "checkout_type" => "Cart"];
        }
    }

    public function placeOrder_buynow(Request $request)
    {

        $user_id = 0;
        $OrderMasterArray = array();
        $OrderDetailsArray = array();
        $Product_SellerArray = array();
        $store_id = $request->store_id;
        $merchant_id = Config::get('constants.payment_constants.merchant_id');
        $client_id = Config::get('constants.payment_constants.client_id');
        $secret_key = Config::get('constants.payment_constants.secret_key');

        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
        }
        if ($request->productId) {
            $Productdetails = Product::leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
                ->where('products.id', $request->productId)
                ->select('products.id as productid', 'products.product_name', 'products.tax_ids', 'products.prescription', 'GS.value AS email', 'product_images.product_image', DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'))
                ->first();

            if ($Productdetails) {
                $validator = Validator::make($request->all(), [
                    'address_type' => 'required',
                    'addressId' => 'required',
                    'quantity' => 'required|numeric',
                    'payment_method' => 'required',
                ]);

                if ($validator->fails()) {
                    return ['result' => false, "errorMsg" => $validator->errors()->first(), "checkout_type" => "BuyNow"];
                } else {
                    $prescription = Prescription::where('id', $request->prescriptionID)->where('product_id', $Productdetails->productid)->first();

                    if (!empty($prescription) && $prescription->status != 2 && $Productdetails->prescription == 1) {
                        return ['result' => false, "errorMsg" => 'Sorry... you cannot buy this product. Something wrong with Your prescription. Please upload again.', "checkout_type" => "BuyNow"];
                    }

                    if ($request->addressId != 0) {
                        $address_exist = UserAddress::join('states', 'states.id', 'user_addresses.state_id')
                            ->where('user_addresses.id', $request->addressId)->where('user_addresses.type', $request->address_type)
                            ->select('user_addresses.*', 'states.name as state')->first();

                        if (!isset($_SESSION)) {
                            session_start();
                        }

                        if ($address_exist) {
                            $order = new Order();
                            $order->user_id = $user_id;
                            $order->address_id = $address_exist->id;
                            $order->status = 'initiated';
                            $order->payment_method = $request->payment_method;
                            $order->delivery_type = ($store_id == '' ? 'direct' : 'pickup');
                            $order->store_id = $store_id;
                            // $order->prescription_ids = $prescription_ids;
                            $order->total_amount = $request->total_amount;
                            $order->total_tax_amount = $request->total_tax_amount;
                            $order->shipping_charge = $request->shipping_charge;
                            $order->grand_total = $request->grandtotal;
                            $order->date = date('Y-m-d H:i:s');

                            $order->save();
                            // dd($order);

                            if ($order->id) {
                                $order_details = new OrderDetails();
                                $order_details->order_id = $order->id;
                                $order_details->product_id = $request->productId;
                                $order_details->quantity = $request->quantity;

                                $order_details->tax_ids = $Productdetails->tax_ids;

                                $total_taxRate = $total_tax_percent = $total_tax = $total_tax_percent = $totalvalue = $total_tax_percent_value = 0;
                                if ($Productdetails->tax_ids != '') {
                                    $tax_ids = explode(',', $Productdetails->tax_ids);
                                    $TaxDetails = Tax::whereIn('id', $tax_ids)->get();

                                    foreach ($TaxDetails as $value) {
                                        $total_tax_percent = $total_tax_percent + $value->percentage;

                                        // $total_taxRate += ($Productdetails->ProductPrice * $value->percentage) / 100;
                                    }
                                    $total_tax_percent_value = ($Productdetails->ProductPrice * 100) / ($total_tax_percent + 100);

                                    $totalvalue = $totalvalue + ($Productdetails->ProductPrice - $total_tax_percent_value) * $request->quantity;
                                } else {
                                    $total_tax_percent_value = $Productdetails->ProductPrice;
                                }

                                $total_tax = $total_tax + $totalvalue;

                                ////
                                $order_details->total_tax = $total_tax;
                                $order_details->price = $total_tax_percent_value;
                                $order_details->amount = ($request->quantity * $Productdetails->ProductPrice);
                                $order_details->save();

                                if ($user_id != 0 && $request->prescriptionID != 0) {
                                    Prescription::where('id', $request->prescriptionID)->where('user_id', $user_id)->where('product_id', $Productdetails->productid)->where('status', 2)->update(['status' => 3]);
                                }

                                // $invoice_number = $this->invoiceNumber();
                                // Invoice::create([
                                //     'user_id' => $user_id,
                                //     'order_id' => $order->id,
                                //     'invoice_number' => $invoice_number,
                                // ]);

                                $prescription_arr = array();

                                if (isset($_SESSION["product_prescriptions"]) && count($_SESSION['product_prescriptions']) > 0) {
                                    foreach ($_SESSION['product_prescriptions'] as $key1 => $value) {

                                        // foreach ($value as $key2 => $value) {

                                        $prescription = new Prescription();
                                        $prescription->user_id = $user_id;
                                        // $prescription->product_id = $key2;
                                        $prescription->order_id = $order->id;
                                        $prescription->file = $value;
                                        $prescription->status = 1;
                                        $prescription->save();
                                        $prescription_arr[] = $prescription->id;

                                        // }
                                    }
                                }
                                $prescription_ids = '';
                                if (count($prescription_arr) > 0) {
                                    $prescription_ids = implode(',', $prescription_arr);
                                }
                                Order::where('id', $order->id)->update(array('prescription_ids' => $prescription_ids));
                                $billing_address = UserAddress::join('countries', 'countries.id', 'user_addresses.country_id')
                                    ->join('states', 'states.id', 'user_addresses.state_id')
                                    ->leftjoin('users', 'users.id', 'user_addresses.user_id')
                                    ->select('user_addresses.*', 'countries.name as country', 'states.name as state')
                                    ->where('user_addresses.id', $address_exist->id)->first();
                                // dd($billing_address);
                                if ($request->payment_method == 'cod') {

                                    $this->cod_response($order->id, $request->checkout_type);
                                    return ['success' => true, 'order' => $order, 'payment_method' => $order->payment_method];

                                } elseif ($request->payment_method == 'online') {
                                    $razor_order_response = array();
                                    $jwtPayload = array();
                                    if ($request->payment_gateway == 'razorpay') {
                                        $api = new Api(config('constants.RAZORPAY_KEY'), config('constants.RAZORPAY_SECRET'));
                                        $razor_order_response = $api->order->create(array('receipt' => $order->id, 'amount' => (float) round($order->grand_total, 2) * 100, 'currency' => 'INR'));
                                        //  dd($razor_order_response);
                                    } elseif ($request->payment_gateway == 'billdesk') {
                                        $current_time = strtotime("now");
                                        $trace_id = $current_time . 'EXP';
                                        $sub_id = 'sub' . $current_time;

                                        $headers = ["alg" => "HS256", "clientid" => $client_id];

                                        $grand_total = number_format($order->grand_total, 2);

                                        $grand_total = str_replace(",", "", $grand_total);
                                        // $order_date = gmdate(DATE_ATOM, mktime(date('H', strtotime($order->created_at)), date('i', strtotime($order->created_at)), date('s', strtotime($order->created_at)), date('m', strtotime($order->created_at)), date('d', strtotime($order->created_at)), date('Y', strtotime($order->created_at))));
                                        $order_date = date(DATE_ATOM, strtotime($order->created_at));
                                        $payload = [
                                            "mercid" => $merchant_id,
                                            "orderid" => $order->id,
                                            "amount" => $grand_total,
                                            "order_date" => $order_date,
                                            "currency" => "356",
                                            "ru" => route('payment.response'),
                                            "additional_info" => [
                                                "additional_info1" => $address_exist->name,
                                                "additional_info2" => $address_exist->email,
                                                "additional_info3" => $address_exist->phone,
                                                "additional_info4" => $address_exist->location,
                                                "additional_info5" => $address_exist->city,
                                                "additional_info6" => $address_exist->state,
                                                "additional_info7" => "NA",
                                            ],
                                            "itemcode" => "DIRECT",
                                            "device" => [
                                                "init_channel" => "internet",
                                                "ip" => $this->getIp(),
                                                "user_agent" => $request->userAgent(),
                                            ],
                                        ];
                                        $curl_payload = JWT::encode($payload, $secret_key, "HS256", null, $headers);

                                        // print_r($curl_payload);

                                         $url = "https://api.billdesk.com/payments/ve1_2/orders/create";
                                        //$url = "https://pguat.billdesk.io/payments/ve1_2/orders/create";
                                        $now = now();
                                        $ch = curl_init($url);

                                        $ch_headers = array(
                                            "content-type: application/jose",
                                            "bd-timestamp: $current_time",
                                            "accept: application/jose",
                                            "bd-traceid: $trace_id",
                                        );
                                        // print_r($ch_headers);
                                        curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
                                        curl_setopt($ch, CURLOPT_POST, 1);
                                        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_payload);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                                        $payment_token = curl_exec($ch);
                                        curl_close($ch);

                                        $tokenParts = explode(".", $payment_token);
                                        $tokenHeader = base64_decode($tokenParts[0]);
                                        $tokenPayload = base64_decode($tokenParts[1]);
                                        $jwtHeader = json_decode($tokenHeader);
                                        $jwtPayload = json_decode($tokenPayload);
                                        dd($jwtPayload);

                                        $razor_order_response['id'] = '';
                                        // print_r($jwtPayload);
                                        // dd($payment_token);

                                        // print_r($payment_token);
                                    }
                                    // dd($razor_order_response['id']);
                                    return ['success' => true, 'order' => $order, 'jwtPayload' => $jwtPayload, 'payment_method' => $request->payment_method, 'payment_gateway' => $request->payment_gateway, 'razor_order_response_id' => $razor_order_response['id'], 'useraddress' => $billing_address];
                                }
                            } else {
                                return ['result' => false, "errorMsg" => 'Checkout failed. Your order not placed. Please try again.', "checkout_type" => "BuyNow"];
                            }
                        } else {
                            return ['result' => false, "errorMsg" => 'Checkout failed 2. Your delivery address not found. Please add or choose again.', "checkout_type" => "BuyNow"];
                        }
                    } else {
                        return ['result' => false, "errorMsg" => 'Checkout failed. Your delivery address not found. Please add or choose again.', "checkout_type" => "BuyNow"];
                    }
                }
            } else {
                return ['result' => false, "errorMsg" => 'Checkout failed. Product details not found', "checkout_type" => "BuyNow"];
            }
        } else {
            return ['result' => false, "errorMsg" => 'Checkout failed. Product not found.', "checkout_type" => "BuyNow"];
        }
    }

    public function invoiceOrder($orderID = null)
    {
        if ($orderID) {
            $userID = 0;
            if (Auth::guard('user')->user()) {
                $userID = Auth::guard('user')->user()->id;
            }

            $orders = Order::where('orders.id', $orderID)->first();

            if ($orders) {
                $order_details = OrderDetails::join('products', 'order_details.product_id', 'products.id')
                    ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                    ->where('order_details.order_id', $orderID)
                    ->select('order_details.*', 'products.product_name', 'product_images.product_image')
                    ->get();
                $payment_details = PaymentDetail::where('order_id', $orderID)->first();

                if (count($order_details) > 0) {
                    return view('orderinvoice', compact('orders', 'order_details', 'payment_details'));
                } else {
                    return view('notfound_frontview')->withErrors('Something went wrong order details not found.');
                }
            } else {
                return view('notfound_frontview')->withErrors('Something went wrong order not found.');
            }
        } else {
            return view('notfound_frontview')->withErrors('Invalid url.');
        }
    }

    public function print_invoiceOrder($OrderID = null)
    {
        $orders = array();
        $userType = "customer";
        $invoice_status = ['ordered', 'shipped', 'delivered', 'return'];
        // $products_status = ['ordered', 'shipped', 'delivered', 'return'];
        if ($OrderID) {
            if (Order::where('id', $OrderID)->whereIn('status', $invoice_status)->exists()) {
                $userID = 0;
                if (Auth::guard('user')->user()) {
                    $userID = Auth::guard('user')->user()->id;
                }

                //Get order details coDe--
                $order_Master = Order::join('user_addresses', 'orders.address_id', 'user_addresses.id')
                    ->join('countries', 'user_addresses.country_id', 'countries.id')
                    ->join('states', 'user_addresses.state_id', 'states.id')
                    ->leftjoin('stores', 'stores.id', 'orders.store_id')
                // ->where('orders.id', $OrderID)->where('orders.user_id', $userID)
                    ->where('orders.id', $OrderID)
                    ->select('orders.*', 'user_addresses.name', 'user_addresses.address', 'user_addresses.phone', 'user_addresses.pin', 'user_addresses.location', 'user_addresses.city',
                        'user_addresses.landmark', 'user_addresses.email', 'states.name as state_name', 'countries.name as country_name', 'stores.name as store_name', 'stores.location as store_location', 'stores.address as store_address', 'stores.contact_number as store_contact_number', 'stores.map_location_code as store_location_map')
                    ->first();

                if ($order_Master) {
                    $order_details = OrderDetails::join('products', 'products.id', 'order_details.product_id')
                        ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                        ->where('order_details.order_id', $order_Master->id)
                        ->whereIn('order_details.status', $invoice_status)
                        ->select('order_details.*', 'products.product_name')
                        ->get();
                    //   dd($order_Master);
                    if ($order_details) {
                        $orders = new \stdClass();
                        $orders->order_id = $order_Master->id;
                        $orders->order_date = $order_Master->date;
                        $orders->delivery_type = $order_Master->delivery_type;
                        $orders->store_id = $order_Master->store_id;
                        $orders->store_name = $order_Master->store_name;
                        $orders->store_location = $order_Master->store_location;
                        $orders->store_address = $order_Master->store_address;
                        $orders->store_contact_number = $order_Master->store_contact_number;
                        $orders->store_location_map = $order_Master->store_location_map;
                        $orders->name = $order_Master->name;
                        $orders->address = $order_Master->address;
                        $orders->phone = $order_Master->phone;
                        $orders->pin = $order_Master->pin;
                        $orders->city = $order_Master->city;
                        $orders->location = $order_Master->location;
                        $orders->landmark = $order_Master->landmark;
                        $orders->state_name = $order_Master->state_name;
                        $orders->country_name = $order_Master->country_name;
                        $orders->email = $order_Master->email;

                        $orders->status = $order_Master->status;
                        $orders->total_amount = $order_Master->total_amount;
                        $orders->total_tax_amount = $order_Master->total_tax_amount;
                        $orders->shipping_charge = $order_Master->shipping_charge;
                        $orders->grand_total = $order_Master->grand_total;
                        $orders->order_details = $order_details;

                        return view('invoice_print', compact('orders', 'userType'));
                    } else {
                        return view('invoice_print', compact('orders', 'userType'))->with('error', 'Order details not found.');
                    }
                } else {
                    return view('invoice_print', compact('orders', 'userType'))->with('error', 'Error: Order details not found.');
                }
            } else {
                return view('invoice_print', compact('orders', 'userType'))->with('error', 'Invalid request.');
            }
        } else {
            return view('invoice_print', compact('orders', 'userType'))->with('error', 'Invalid request.');
        }
    }

    public function manage_wishlist(Request $request)
    {
        $status = '';
        $message = '';
        $mode = '';
        if ($request->product_id != '' && $request->product_id != 0) {
            if (Auth::guard('user')->user()) {
                $user_id = Auth::guard('user')->user()->id;
                if (empty($user_id)) {
                    $message = "Please login into your account and try again";
                    $status = 'failed';
                } else {
                    if (Wishlist::where('user_id', $user_id)->where('product_id', $request->product_id)->exists()) {
                        $data = Wishlist::where('user_id', $user_id)->where('product_id', $request->product_id)->delete();
                        $message = "Product removed from wishlist";
                        $status = 'success';
                        $mode = 'removed';
                    } else {
                        $data = Wishlist::create([
                            'user_id' => $user_id,
                            'product_id' => $request->product_id,
                        ]);
                        $message = "Product added to wishlist";
                        $status = 'success';
                        $mode = 'added';
                    }
                }
            } else {
                $message = "Please login into your account and try again";
                $status = 'failed';
            }
        } else {
            $message = "Product not found. Please try again";
            $status = 'failed';
        }
        return response()->json(array('status' => $status, 'message' => $message, 'mode' => $mode));
    }
    public function customer_invoice_mail($OrderID = null, $invoice_number = null)
    {

        $orders = array();
        $userType = "customer";
        if ($OrderID) {
            $userID = 0;
            if (Auth::guard('user')->user()) {
                $userID = Auth::guard('user')->user()->id;
            }

            //Get order details coDe--
            $order_Master = Order::join('user_addresses', 'orders.address_id', 'user_addresses.id')
                ->join('countries', 'user_addresses.country_id', 'countries.id')
                ->join('states', 'user_addresses.state_id', 'states.id')
                ->leftjoin('stores', 'stores.id', 'orders.store_id')
            // ->where('orders.id', $OrderID)->where('orders.user_id', $userID)
                ->where('orders.id', $OrderID)
                ->select('orders.*', 'user_addresses.name', 'user_addresses.address', 'user_addresses.email', 'user_addresses.phone', 'user_addresses.pin', 'user_addresses.location', 'user_addresses.city',
                    'user_addresses.landmark', 'states.name as state_name', 'countries.name as country_name', 'stores.name as store_name', 'stores.location as store_location', 'stores.address as store_address', 'stores.contact_number as store_contact_number', 'stores.map_location_code as store_location_map')
                ->first();

            if ($order_Master) {
                $order_details = OrderDetails::join('products', 'products.id', 'order_details.product_id')
                    ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                    ->where('order_details.order_id', $order_Master->id)
                    ->select('order_details.*', 'products.product_name')
                    ->get();

                if ($order_details) {
                    $orders = new \stdClass();
                    $orders->order_id = $order_Master->id;
                    $orders->order_date = $order_Master->date;
                    $orders->name = $order_Master->name;
                    $orders->phone = $order_Master->phone;
                    $orders->address = $order_Master->address;
                    $orders->pin = $order_Master->pin;
                    $orders->city = $order_Master->city;
                    $orders->location = $order_Master->location;
                    $orders->landmark = $order_Master->landmark;
                    $orders->state_name = $order_Master->state_name;
                    $orders->country_name = $order_Master->country_name;
                    $orders->delivery_type = $order_Master->delivery_type;
                    $orders->store_id = $order_Master->store_id;
                    $orders->store_name = $order_Master->store_name;
                    $orders->store_location = $order_Master->store_location;
                    $orders->store_address = $order_Master->store_address;
                    $orders->store_contact_number = $order_Master->store_contact_number;
                    $orders->store_location_map = $order_Master->store_location_map;
                    $orders->status = $order_Master->status;
                    $orders->total_amount = $order_Master->total_amount;
                    $orders->total_tax_amount = $order_Master->total_tax_amount;
                    $orders->shipping_charge = $order_Master->shipping_charge;
                    $orders->grand_total = $order_Master->grand_total;
                    $orders->order_details = $order_details;
                    $orders->invoice_number = $invoice_number;
                }
                $settings = Generalsetting::where('item', '=', 'notification_email')->first();

                // $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__]);

                $pdf = PDF::loadView('email.pdf-invoice', compact('orders'));
                $path = public_path('assets/uploads/invoice');
                if (!File::exists($path)) {
                    File::makeDirectory($path);
                }
                $fileName = $invoice_number . '.' . 'pdf';
                //  $pdf->save( public_path('assets/uploads/invoice') . '/'. $fileName);
                $pdf->save($path . '/' . $fileName);

                if ($settings) {
                    Mail::send('email.invoice_mail',
                        array(
                            'customername' => $order_Master->name,
                            // 'adminname' => Auth::guard('admin')->user()->name,
                            // 'productname' => $product->product_name,
                            // 'quantity' => $request->quantity,
                            // 'productprice' => $productPrice,
                        ), function ($message) use ($settings, $pdf, $invoice_number, $order_Master) {
                            $message->from($settings->value, 'Expressmed');
                            $message->to($order_Master->email);
                            $message->subject('Expressmed');
                            $message->attachData($pdf->output(), $invoice_number . '.pdf');
                            // $message->subject('Prescription '.$status_msg);
                        });
                }
                return true;
            }

        }

        return false;

    }
    public function invoiceNumber()
    {
        $latest = Invoice::latest()->first();

        if (!$latest) {
            return 'med0001';
        }

        $string = preg_replace("/[^0-9\.]/", '', $latest->invoice_number);

        return 'med' . sprintf('%04d', $string + 1);
    }
    public function all_medicines(Request $request)
    {
        $all_brands = Productbrand::select('image', 'id', 'name')->orderBy('name', 'asc')->get();
        $all_categories = Category::where('parent_id', 0)->where('status', 'active')->orderBy('name', 'asc')->get();
        $all_producttypes = Producttype::select('id', 'producttype')->orderBy('type', 'asc')->orderBy('producttype', 'asc')->get();
        $all_medicineuse = MedicineUse::orderBy('name', 'asc')->get();
        $alphas = range('A', 'Z');

        //Get all child categories id--
        $categoryIds = [];
        if ($request->productcategory != '') {
            foreach ($request->productcategory as $val) {
                array_push($categoryIds, $val);
            }
        }

        if ($request->has('hid_searchCategory') && $request->hid_searchCategory != 0) {
            array_push($categoryIds, $request->hid_searchCategory);
        }

        $obj_category = new Category();
        $child_category = $obj_category->getCategories($categoryIds);

        $child_categoryIds = [];
        $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);

        if ($request->productcategory != '') {
            foreach ($request->productcategory as $categoryvalue) {
                array_push($child_categoryIds, $categoryvalue);
            }
        }

        if ($request->has('hid_searchCategory') && $request->hid_searchCategory != 0) {
            array_push($child_categoryIds, $request->hid_searchCategory);
        }

        //--Get product listing under choosed category--
        $products = Product::select('products.*', 'product_images.product_image', DB::raw("GROUP_CONCAT(productcontents.name) as productcontent"))
            ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')

            ->leftjoin("productcontents", DB::raw("FIND_IN_SET(productcontents.id, products.productcontent_id)"), ">", DB::raw("'0'"))
            ->leftJoin('categories', 'categories.id', 'products.producttypeid')
            ->leftJoin('product_medicineuses', 'product_medicineuses.product_id', 'products.id')
            ->leftJoin('medicine_uses', 'medicine_uses.id', 'product_medicineuses.medicine_use')
            ->where('categories.name', 'All Medicines')
            ->where('products.hide_from_site', '!=', '1')
            ->where('products.status', 'active');

        if ($request->has('label') && $request->label != '') {

            $products->where('products.product_name', 'LIKE', $request->label . '%');
        } else {
            $products->where('products.product_name', 'LIKE', 'A%');

        }

        // if (!empty($child_categoryIds)) {
        //     // dd($child_categoryIds);
        //     $products->whereIn('products.category_id', $child_categoryIds)
        //         ->orWhereIn('products.producttypeid', $child_categoryIds);
        // }

        // if ($request->has('medicineuse') && $request->medicineuse != '') {
        //     $products->whereIn('product_medicineuses.medicine_use', $request->medicineuse);

        // }

        $products->groupBy("products.id")->OrderBy('products.product_name', 'ASC');
        $products = $products->paginate(42);
        return view('allmedicineslisting_page', compact('all_brands', 'all_categories', 'all_producttypes', 'all_medicineuse', 'products', 'alphas'))->with('i', ($request->input('page', 1) - 1) * 40);
    }

    public function all_brands(Request $request)
    {
        $product_brands = Productbrand::orderBy('name', 'ASC')->paginate(30);
        return view('brandlisting_page', compact('product_brands'))->with('i', ($request->input('page', 1) - 1) * 30);

    }

    public function offerproductslist(Request $request)
    {
        $all_brands = Productbrand::select('image', 'id', 'name')->orderBy('name', 'asc')->get();
        $all_categories = Category::where('parent_id', 0)->where('status', 'active')->orderBy('name', 'asc')->get();
        $all_producttypes = Producttype::select('id', 'producttype')->orderBy('type', 'asc')->orderBy('producttype', 'asc')->get();
        $all_medicineuse = MedicineUse::orderBy('name', 'asc')->get();

        //Get all child categories id--
        $categoryIds = [];
        if ($request->productcategory != '') {
            foreach ($request->productcategory as $val) {
                array_push($categoryIds, $val);
            }
        }

        if ($request->has('hid_searchCategory') && $request->hid_searchCategory != 0) {
            array_push($categoryIds, $request->hid_searchCategory);
        }

        $obj_category = new Category();
        $child_category = $obj_category->getCategories($categoryIds);

        $child_categoryIds = [];
        $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);

        if ($request->productcategory != '') {
            foreach ($request->productcategory as $categoryvalue) {
                array_push($child_categoryIds, $categoryvalue);
            }
        }

        if ($request->has('hid_searchCategory') && $request->hid_searchCategory != 0) {
            array_push($child_categoryIds, $request->hid_searchCategory);
        }

        //--Get product listing under choosed category--
        $products = Product::select('products.*', 'product_images.product_image', DB::raw("GROUP_CONCAT(productcontents.name) as productcontent"))
            ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')

            ->leftjoin("productcontents", DB::raw("FIND_IN_SET(productcontents.id, products.productcontent_id)"), ">", DB::raw("'0'"))
            ->leftJoin('categories', 'categories.id', 'products.producttypeid')
            ->leftJoin('product_medicineuses', 'product_medicineuses.product_id', 'products.id')
            ->leftJoin('medicine_uses', 'medicine_uses.id', 'product_medicineuses.medicine_use')
            ->where('products.offer_price', '!=', '0')
            ->where('products.hide_from_site', '!=', '1')
            ->where('products.status', 'active');

        $products->groupBy("products.id");

        if ($request->has('sort') && $request->sort != '') {
            if ($request->sort == 'low-to-high') {
                $products->OrderBy('products.offer_price', 'ASC');
            } elseif ($request->sort = 'high-to-low') {
                $products->OrderBy('products.offer_price', 'DESC');
            }
        }

        $products = $products->paginate(30)->appends(request()->except('page'));

        return view('offerproductslisting_page', compact('all_brands', 'all_categories', 'all_producttypes', 'all_medicineuse', 'products'))->with('i', ($request->input('page', 1) - 1) * 40);
    }
    public function trackorder(Request $request)
    {
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
            $token = Shiprocket::getToken();

            if ($request->has('orderID') && $request->has('orderID') != '') {
                $orders = Order::join('order_details', 'order_details.order_id', 'orders.id')
                    ->select('orders.*')->where('orders.id', '=', $request->orderID)
                    ->where('orders.user_id', $user_id)->first();

                if ($orders) {
                    if ($orders->delivery_type == 'direct') {
                        if ($orders->shipment_id != '') {

                            $track_details = Shiprocket::track($token)->throwShipmentId($orders->shipment_id);
                            return view('track_order', compact('track_details', 'orders'));
                        }
                    } else if ($orders->delivery_type == 'pickup') {
                        if ($orders->store_id != '') {
                            $store_data = Store::find($orders->store_id);
                            return view('track_order', compact('store_data', 'orders'));
                        }

                    } else {
                        return view('track_order')->with('error', 'Error: Shipment details not found.');
                    }
                } else {
                    return view('track_order')->with('error', 'Error: Order details not found.');
                }
            } else {
                return view('track_order');
            }

        } else {
            return view('notfound_frontview')->withErrors('You are not login please login your account.');
        }
    }

    public function create_prescription()
    {
        return view('uploadprescription_page');
    }

    public function store_prescription(Request $request)
    {
        $this->validate($request, [
            'files.*' => 'nullable|mimes:jpeg,jpg,png,pdf|max:1024',
        ], [
            'files.*.mimes' => 'The Prescription file must be a file of type:jpeg,jpg,png,pdf',
            'files.*.max' => 'The Product image must not be greater than 1024 kilobytes.',
        ]);
        $prescriptions_array = array();

        if (!empty($request->file('files'))) {

            foreach ($request->file('files') as $key => $prescription_file) {
                $fileName = 'presc_' . time() . '_' . $key . '.' . $prescription_file->extension();
                $prescription_file->move(public_path('/assets/uploads/prescription/'), $fileName);
                $prescriptions_array['prescription_files'][$key] = $fileName;

            }
        }
        $request->session()->put('session_data', $prescriptions_array);

        $file = $request->all();
        // if ($request->hasFile('files')) {

        //     $images = $request->file('files');
        //     foreach ($images as $image) {
        //     }
        // }
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
            $request->session()->put('user_id', $user_id);
            $session_data = $request->session()->get('session_data');
            $session_data['user_id'] = $user_id;
            return view('addmedicine_page', compact('session_data'));
        } else {
            return view('notfound_frontview')->withErrors('You are not login please login your account.');
        }

    }

    public function add_prescription_medicine()
    {
        return view('addmedicine_page');
    }
    public function add_watermark()
    {
        $products_images = Product_image::paginate(100);

        $arr_ext = array('jpg', 'jpeg', 'png', 'web', 'webp', 'bmp');

        foreach ($products_images as $key => $img) {
            $ext = pathinfo(public_path('assets/uploads/products/' . $img->product_image), PATHINFO_EXTENSION);
            if (is_readable(public_path('assets/uploads/products/' . $img->product_image))) {
                if (in_array($ext, $arr_ext)) {
                    $image = Image::make(public_path('assets/uploads/products/' . $img->product_image));
                    $width = $image->width();
                    $height = $image->height();
                    $dim = (($height + $width) / 4);
                    // $image->text('Expressmed', $dim, $dim, function ($font) {
                    //     $font->size(60);
                    //     $font->color('#ed1d24');
                    //     $font->align('center');
                    //     $font->valign('bottom');
                    //     $font->angle(90);
                    // })->save(public_path('assets/uploads/products/' . $img->product_image));
                    $image->insert(public_path('img/watermark.png'), 'center', 5, 5);
                    $image->save(public_path('assets/uploads/products/' . $img->product_image));

                }

            }

        }

        return view('watermark', compact('products_images'))->with('success', 'Watermark applied successfully.');

    }

    public function payment_response(Request $request)
    {
        $user_id = 0;
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
        }
        $merchant_id = Config::get('constants.payment_constants.merchant_id');
        $client_id = Config::get('constants.payment_constants.client_id');
        $secret_key = Config::get('constants.payment_constants.secret_key');

        $headers = ["alg" => "HS256", "clientid" => $client_id];

        if ($request['transaction_response']) {
            $tokenParts = explode(".", $request['transaction_response']);
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtHeader = json_decode($tokenHeader);
            $jwtresponse = json_decode($tokenPayload);

            if ($jwtresponse) {
                $order_id = $jwtresponse->orderid;
                $current_time = strtotime("now");
                $trace_id = $current_time . 'EXP';

                $ch_headers = array(
                    "content-type: application/jose",
                    "bd-timestamp: $current_time",
                    "accept: application/jose",
                    "bd-traceid: $trace_id",
                );
                $transition_attr = [
                    "mercid" => $merchant_id,
                    "orderid" => $order_id,

                ];
                // print_r($ch_headers);
                $curl_trans = JWT::encode($transition_attr, $secret_key, "HS256", null, $headers);
                // print_r($curl_trans);
                $transition_url = "https://api.billdesk.com/payments/ve1_2/transactions/get";
               // $transition_url = "https://pguat.billdesk.io/payments/ve1_2/transactions/get";
                $ch = curl_init($transition_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_trans);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $transaction = curl_exec($ch);
                curl_close($ch);
                // dd($transaction);
                $tokenParts = explode(".", $transaction);
                $tokenHeader = base64_decode($tokenParts[0]);
                $tokenPayload = base64_decode($tokenParts[1]);
                $jwt_TransitionHeader = json_decode($tokenHeader);
                $jwt_TransitionResponse = json_decode($tokenPayload);

                if ($jwt_TransitionResponse) {
                    if (isset($jwt_TransitionResponse->transaction_error_type)) {
                        if ($jwt_TransitionResponse->transaction_error_type == 'success') {
                            $payment = PaymentDetail::create([
                                'order_id' => $jwt_TransitionResponse->orderid,
                                'transaction_id' => $jwt_TransitionResponse->transactionid,
                                'amount' => $jwt_TransitionResponse->amount,
                                'transaction_date' => $jwt_TransitionResponse->transaction_date,
                                'currency_code' => $jwt_TransitionResponse->currency,
                                'payment_method' => $jwt_TransitionResponse->payment_method_type,
                                'transaction_status' => $jwt_TransitionResponse->transaction_error_type,
                            ]);
                            Order::where('id', $order_id)->update(array('status' => 'ordered'));

                            $order = Order::join('order_details', 'order_details.order_id', 'orders.id')
                                ->where('orders.id', $order_id)->select('orders.*')->first();

                            $Productdetails = Product::join('order_details', 'order_details.product_id', 'products.id')
                                ->join('orders', 'orders.id', 'order_details.order_id')
                                ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
                                ->where('orders.id', $order_id)
                                ->select(DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), 'products.id as productid', 'products.product_name', 'products.tax_ids', 'GS.value AS email', 'order_details.quantity')
                                ->get();

                            $billing_address = Order::join('order_details', 'order_details.order_id', 'orders.id')
                                ->join('user_addresses', 'user_addresses.id', 'orders.address_id')
                                ->join('states', 'states.id', 'user_addresses.state_id')
                                ->join('countries', 'countries.id', 'user_addresses.country_id')
                                ->where('user_addresses.id', $order->address_id)
                                ->select('user_addresses.*', 'countries.name as country', 'states.name as state')->first();

                            foreach ($Productdetails as $key => $product) {
                                $orderDetails['order_items'][$key]['name'] = $product->product_name;
                                $orderDetails['order_items'][$key]['sku'] = $product->productid;
                                $orderDetails['order_items'][$key]['units'] = $product->quantity;
                                $orderDetails['order_items'][$key]['selling_price'] = $product->ProductPrice;
                                $orderDetails['order_items'][$key]['discount'] = "";
                                $orderDetails['order_items'][$key]['tax'] = "";
                                $orderDetails['order_items'][$key]['hsn'] = '000';

                            }
                            if ($order->delivery_type == 'direct') {
                                $token = Shiprocket::getToken();
                                $newLocation['pickup_location'] = $billing_address->address;
                                $newLocation['name'] = $billing_address->name;
                                $newLocation['email'] = $billing_address->email;
                                $newLocation['phone'] = $billing_address->phone;
                                $newLocation['address'] = $billing_address->address . ' 0,' . $billing_address->city;
                                $newLocation['address_2'] = "";
                                $newLocation['city'] = $billing_address->city;
                                $newLocation['state'] = $billing_address->state;
                                $newLocation['country'] = $billing_address->country;
                                $newLocation['pin_code'] = $billing_address->pin;

                                $response = Shiprocket::pickup($token)->addLocation($newLocation);
                                $location = Shiprocket::pickup($token)->getLocations();

                                $orderDetails['order_id'] = $order->id;
                                $orderDetails['order_date'] = $order->created_at;
                                $orderDetails['pickup_location'] = $billing_address->address;
                                $orderDetails['channel_id'] = "";
                                $orderDetails['comment'] = "null";
                                $orderDetails['billing_customer_name'] = $billing_address->name;
                                $orderDetails['billing_last_name'] = "-";
                                $orderDetails['billing_address'] = $billing_address->address;
                                $orderDetails['billing_address_2'] = "-";
                                $orderDetails['billing_city'] = $billing_address->city;
                                $orderDetails['billing_pincode'] = $billing_address->pin;
                                $orderDetails['billing_state'] = $billing_address->state;
                                $orderDetails['billing_country'] = $billing_address->country;
                                $orderDetails['billing_email'] = $billing_address->email;
                                $orderDetails['billing_phone'] = $billing_address->phone;
                                $orderDetails['shipping_is_billing'] = true;
                                $orderDetails['shipping_customer_name'] = "";
                                $orderDetails['shipping_last_name'] = "";
                                $orderDetails['shipping_address'] = "";
                                $orderDetails['shipping_address_2'] = "";
                                $orderDetails['shipping_city'] = "";
                                $orderDetails['shipping_pincode'] = "";
                                $orderDetails['shipping_country'] = "";
                                $orderDetails['shipping_state'] = "";
                                $orderDetails['shipping_email'] = "";
                                $orderDetails['shipping_phone'] = "";
                                $orderDetails['payment_method'] = "Prepaid";
                                $orderDetails['shipping_charges'] = 0;
                                $orderDetails['giftwrap_charges'] = 0;
                                $orderDetails['transaction_charges'] = 0;
                                $orderDetails['total_discount'] = 0;
                                $orderDetails['sub_total'] = $order->grand_total;
                                $orderDetails['length'] = "1";
                                $orderDetails['breadth'] = "1";
                                $orderDetails['height'] = "1";
                                $orderDetails['weight'] = "1";
                                $response = Shiprocket::order($token)->create($orderDetails);
                                // $shipments = Shiprocket::shipment($token)->getSpecific($response['shipment_id']);
                                if (isset($response['shipment_id']) && $response['shipment_id'] != '') {
                                    Order::where('id', $order->id)->update(array('shipment_id' => $response['shipment_id']));
                                }
                            }

                            $invoice_number = $this->invoiceNumber();
                            Invoice::create([
                                'user_id' => $order->user_id,
                                'order_id' => $order->id,
                                'invoice_number' => $invoice_number,
                            ]);
                            $this->customer_invoice_mail($order_id, $invoice_number);
                            $this->Admin_orderConfirmation_mail($order_id, $invoice_number);
                            $status = 'success';
                            $msg = "Payment Successfully Completed";

                        } else {
                            $status = 'failed';
                            $msg = "Payment Failed";
                            Order::where('id', $order_id)->update(array('status' => 'failed'));
                        }
                    } else {
                        $status = 'failed';
                        $msg = "Somthing went wrong Transaction Error";
                    }
                    return view('payment_response', compact('status', 'msg', 'order_id'));

                } else {
                    return view('notfound_frontview')->withErrors('Something went wrong Transaction Error.');
                }
            }
        } else {
            return view('notfound_frontview')->withErrors('Something went wrong Transaction Error.');
        }

    }

    public function payment_return(Request $request, $checkout_type = null)
    {
        if ($request['transaction_response']) {
            $order_id = $request['orderid'];
            $tokenParts = explode(".", $request['transaction_response']);
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtHeader = json_decode($tokenHeader);
            $jwtresponse = json_decode($tokenPayload);
            if (isset($jwtresponse->transaction_error_type)) {
                if ($jwtresponse->transaction_error_type == "success") {

                    if ($checkout_type == 'Cart') {
                        if (Auth::guard('user')->user()) {
                            $user_id = Auth::guard('user')->user()->id;
                            $carts = Cart::join('products', 'carts.product_id', 'products.id')
                                ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                                ->select('carts.*')
                                ->where('carts.user_id', $user_id)
                                ->get();
                            foreach ($carts as $key => $value) {
                                Cart::where('id', $value->id)->delete();
                            }

                        } else {
                            if (!isset($_SESSION)) {
                                session_start();
                            }
                            unset($_SESSION['Session_GuestCart']);
                        }
                    }
                    $payment_status = "success";
                    $msg = "Transaction Successfull, Payment Verified";

                    return redirect()->route('order.invoice', $order_id)->with('success', 'Transaction Successfull, Payment Verified.');
                } else {
                    $this->payment_error_mail($jwtresponse);
                    return view('payment_error_page', compact('jwtresponse'))->with('errors', 'Payment Transaction Failed.');
                }
                return view('payment_error_page', compact('jwtresponse'))->with('errors', 'Somthing Went Wrong.');

            } else {
                return view('notfound_frontview')->withErrors('Something went wrong Transaction Error.');

            }
        }
        return view('notfound_frontview')->withErrors('Something went wrong Transaction Error.');
    }

    public function payment_response_webhook(Request $request)
    {
        $user_id = 0;
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
        }
        $merchant_id = Config::get('constants.payment_constants.merchant_id');
        $client_id = Config::get('constants.payment_constants.client_id');
        $secret_key = Config::get('constants.payment_constants.secret_key');

        $headers = ["alg" => "HS256", "clientid" => $client_id];

        if ($request['transaction_response']) {
            $tokenParts = explode(".", $request['transaction_response']);
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtHeader = json_decode($tokenHeader);
            $jwtresponse = json_decode($tokenPayload);

            if ($jwtresponse) {
                $order_id = $jwtresponse->orderid;
                $current_time = strtotime("now");
                $trace_id = $current_time . 'EXP';

                $ch_headers = array(
                    "content-type: application/jose",
                    "bd-timestamp: $current_time",
                    "accept: application/jose",
                    "bd-traceid: $trace_id",
                );
                $transition_attr = [
                    "mercid" => $merchant_id,
                    "orderid" => $order_id,

                ];
                // print_r($ch_headers);
                $curl_trans = JWT::encode($transition_attr, $secret_key, "HS256", null, $headers);
                // print_r($curl_trans);
                 $transition_url = "https://api.billdesk.com/payments/ve1_2/transactions/get";
                //$transition_url = "https://pguat.billdesk.io/payments/ve1_2/transactions/get";
                $ch = curl_init($transition_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_trans);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $transaction = curl_exec($ch);
                curl_close($ch);
                // dd($transaction);
                $tokenParts = explode(".", $transaction);
                $tokenHeader = base64_decode($tokenParts[0]);
                $tokenPayload = base64_decode($tokenParts[1]);
                $jwt_TransitionHeader = json_decode($tokenHeader);
                $jwt_TransitionResponse = json_decode($tokenPayload);

                if ($jwt_TransitionResponse) {
                    if (isset($jwt_TransitionResponse->transaction_error_type)) {
                        if ($jwt_TransitionResponse->transaction_error_type == 'success') {
                            $payment = PaymentDetail::create([
                                'order_id' => $jwt_TransitionResponse->orderid,
                                'transaction_id' => $jwt_TransitionResponse->transactionid,
                                'amount' => $jwt_TransitionResponse->amount,
                                'transaction_date' => $jwt_TransitionResponse->transaction_date,
                                'currency_code' => $jwt_TransitionResponse->currency,
                                'payment_method' => $jwt_TransitionResponse->payment_method_type,
                                'transaction_status' => $jwt_TransitionResponse->transaction_error_type,
                            ]);
                            Order::where('id', $order_id)->update(array('status' => 'ordered'));

                            $order = Order::join('order_details', 'order_details.order_id', 'orders.id')
                                ->where('orders.id', $order_id)->select('orders.*')->first();

                            $Productdetails = Product::join('order_details', 'order_details.product_id', 'products.id')
                                ->join('orders', 'orders.id', 'order_details.order_id')
                                ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
                                ->where('orders.id', $order_id)
                                ->select(DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), 'products.id as productid', 'products.product_name', 'products.tax_ids', 'GS.value AS email', 'order_details.quantity')
                                ->get();

                            $billing_address = Order::join('order_details', 'order_details.order_id', 'orders.id')
                                ->join('user_addresses', 'user_addresses.id', 'orders.address_id')
                                ->join('states', 'states.id', 'user_addresses.state_id')
                                ->join('countries', 'countries.id', 'user_addresses.country_id')
                                ->where('user_addresses.id', $order->address_id)
                                ->select('user_addresses.*', 'countries.name as country', 'states.name as state')->first();

                            foreach ($Productdetails as $key => $product) {
                                $orderDetails['order_items'][$key]['name'] = $product->product_name;
                                $orderDetails['order_items'][$key]['sku'] = $product->productid;
                                $orderDetails['order_items'][$key]['units'] = $product->quantity;
                                $orderDetails['order_items'][$key]['selling_price'] = $product->ProductPrice;
                                $orderDetails['order_items'][$key]['discount'] = "";
                                $orderDetails['order_items'][$key]['tax'] = "";
                                $orderDetails['order_items'][$key]['hsn'] = '000';

                            }
                            if ($order->delivery_type == 'direct') {
                                $token = Shiprocket::getToken();
                                $newLocation['pickup_location'] = $billing_address->address;
                                $newLocation['name'] = $billing_address->name;
                                $newLocation['email'] = $billing_address->email;
                                $newLocation['phone'] = $billing_address->phone;
                                $newLocation['address'] = $billing_address->address . ' 0,' . $billing_address->city;
                                $newLocation['address_2'] = "";
                                $newLocation['city'] = $billing_address->city;
                                $newLocation['state'] = $billing_address->state;
                                $newLocation['country'] = $billing_address->country;
                                $newLocation['pin_code'] = $billing_address->pin;

                                $response = Shiprocket::pickup($token)->addLocation($newLocation);
                                $location = Shiprocket::pickup($token)->getLocations();

                                $orderDetails['order_id'] = $order->id;
                                $orderDetails['order_date'] = $order->created_at;
                                $orderDetails['pickup_location'] = $billing_address->address;
                                $orderDetails['channel_id'] = "";
                                $orderDetails['comment'] = "null";
                                $orderDetails['billing_customer_name'] = $billing_address->name;
                                $orderDetails['billing_last_name'] = "-";
                                $orderDetails['billing_address'] = $billing_address->address;
                                $orderDetails['billing_address_2'] = "-";
                                $orderDetails['billing_city'] = $billing_address->city;
                                $orderDetails['billing_pincode'] = $billing_address->pin;
                                $orderDetails['billing_state'] = $billing_address->state;
                                $orderDetails['billing_country'] = $billing_address->country;
                                $orderDetails['billing_email'] = $billing_address->email;
                                $orderDetails['billing_phone'] = $billing_address->phone;
                                $orderDetails['shipping_is_billing'] = true;
                                $orderDetails['shipping_customer_name'] = "";
                                $orderDetails['shipping_last_name'] = "";
                                $orderDetails['shipping_address'] = "";
                                $orderDetails['shipping_address_2'] = "";
                                $orderDetails['shipping_city'] = "";
                                $orderDetails['shipping_pincode'] = "";
                                $orderDetails['shipping_country'] = "";
                                $orderDetails['shipping_state'] = "";
                                $orderDetails['shipping_email'] = "";
                                $orderDetails['shipping_phone'] = "";
                                $orderDetails['payment_method'] = "Prepaid";
                                $orderDetails['shipping_charges'] = 0;
                                $orderDetails['giftwrap_charges'] = 0;
                                $orderDetails['transaction_charges'] = 0;
                                $orderDetails['total_discount'] = 0;
                                $orderDetails['sub_total'] = $order->grand_total;
                                $orderDetails['length'] = "1";
                                $orderDetails['breadth'] = "1";
                                $orderDetails['height'] = "1";
                                $orderDetails['weight'] = "1";
                                $response = Shiprocket::order($token)->create($orderDetails);
                                $shipments = Shiprocket::shipment($token)->getSpecific($response['shipment_id']);
                                if (isset($response['shipment_id']) && $response['shipment_id'] != '') {
                                    Order::where('id', $order->id)->update(array('shipment_id' => $response['shipment_id']));
                                }
                            }

                            $invoice_number = $this->invoiceNumber();
                            Invoice::create([
                                'user_id' => $order->user_id,
                                'order_id' => $order->id,
                                'invoice_number' => $invoice_number,
                            ]);
                            $this->customer_invoice_mail($order_id, $invoice_number);
                            $this->Admin_orderConfirmation_mail($order_id, $invoice_number);
                            $status = 'success';
                            $msg = "Payment Successfully Completed";

                        } else {
                            $status = 'failed';
                            $msg = "Payment Failed";
                            Order::where('id', $order_id)->update(array('status' => 'failed'));
                        }
                    } else {
                        $status = 'failed';
                        $msg = "Somthing went wrong Transaction Error";
                    }
                    return view('payment_response', compact('status', 'msg', 'order_id'));

                } else {
                    return view('notfound_frontview')->withErrors('Something went wrong Transaction Error.');
                }
            }
        } else {
            return view('notfound_frontview')->withErrors('Something went wrong Transaction Error.');
        }

    }

    public function payment_error_mail($payment_response = null)
    {
        $settings = Generalsetting::where('item', '=', 'notification_email')->first();

        if ($settings) {
            if ($payment_response != '') {
                $order_details = Order::join('order_details', 'order_details.order_id', 'orders.id')
                    ->join('user_addresses', 'user_addresses.id', 'orders.address_id')
                    ->where('orders.id', $payment_response->orderid)
                    ->select('user_addresses.name', 'user_addresses.email')->first();
                Mail::send('email.payment_error_mail',
                    array(
                        'order_id' => $payment_response->orderid,
                        'date' => $payment_response->transaction_date,
                        'amount' => $payment_response->amount,
                        'transaction_id' => $payment_response->transactionid,
                        'description' => $payment_response->transaction_error_desc,
                        'payment_method' => $payment_response->payment_method_type,
                        'customername' => $order_details->name,

                    ), function ($message) use ($settings, $payment_response, $order_details) {
                        $message->from($settings->value, 'ExpressMed');
                        $message->to($order_details->email);
                        $message->subject('Payment Failed' . ' ' . 'order' . '#' . $payment_response->orderid);
                    });
                return true;
            }
            return false;
        }
    }
    public function getIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip();
    }

    public function Admin_orderConfirmation_mail($OrderID = null, $invoice_number = null)
    {

        $orders = array();
        $userType = "Admin";
        $mode = "Customer_Manageorder";
        if ($OrderID) {
            //Get order details coDe--
            $order_Master = Order::join('user_addresses', 'orders.address_id', 'user_addresses.id')
                ->join('countries', 'user_addresses.country_id', 'countries.id')
                ->join('states', 'user_addresses.state_id', 'states.id')
            // ->where('orders.id', $OrderID)->where('orders.user_id', $userID)
                ->where('orders.id', $OrderID)
                ->select('orders.*', 'user_addresses.name', 'user_addresses.address', 'user_addresses.email', 'user_addresses.phone', 'user_addresses.pin', 'user_addresses.location', 'user_addresses.city',
                    'user_addresses.landmark', 'states.name as state_name', 'countries.name as country_name')
                ->first();

            if ($order_Master) {
                $order_details = OrderDetails::join('products', 'products.id', 'order_details.product_id')
                    ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
                    ->where('order_details.order_id', $order_Master->id)
                    ->select('order_details.*', 'products.product_name')
                    ->get();

                if ($order_details) {
                    $orders = new \stdClass();
                    $orders->order_id = $order_Master->id;
                    $orders->order_date = $order_Master->date;
                    $orders->name = $order_Master->name;
                    $orders->phone = $order_Master->phone;
                    $orders->address = $order_Master->address;
                    $orders->pin = $order_Master->pin;
                    $orders->city = $order_Master->city;
                    $orders->location = $order_Master->location;
                    $orders->landmark = $order_Master->landmark;
                    $orders->state_name = $order_Master->state_name;
                    $orders->country_name = $order_Master->country_name;

                    $orders->status = $order_Master->status;
                    $orders->total_amount = $order_Master->total_amount;
                    $orders->grand_total = $order_Master->grand_total;
                    $orders->order_details = $order_details;
                    $orders->invoice_number = $invoice_number;
                }
                $settings = Generalsetting::where('item', '=', 'notification_email')->first();

                if ($settings) {
                    Mail::send('email.order_cancellationMail',
                        array(
                            'orderid' => $OrderID,
                            'customername' => $order_Master->name,
                            'status' => 'ordered',
                            'usertype' => $userType,
                            'mode' => $mode,
                            'subject' => 'Customer successfully ordered as order from your store.',

                        ), function ($message) use ($settings, $order_Master) {
                            $message->from($settings->value, 'Expressmed');
                            $message->to($settings->value);
                            $message->subject('Customer order Received.');
                        });
                }
                return true;
            }

        }

        return false;

    }

    public function order_checkout($OrderID = null)
    {
        if ($OrderID) {
            $user_id = 0;
            $carts = $user_details = $BuyNow = $prescriptiondetails = [];
            $order = Order::find($OrderID);
            if ($order) {
                $order_details = Order::join('order_details', 'order_details.order_id', 'orders.id')
                    ->join('products', 'products.id', 'order_details.product_id')
                    ->where('orders.id', $OrderID)->get();

                if (count($order_details) > 0) {
                    $user_address = UserAddress::join('users', 'users.id', 'user_addresses.user_id')
                        ->where('user_addresses.user_id', $order->user_id)
                        ->select('user_addresses.*', 'users.email')
                        ->get();
                    // $product_details=Product::find
                    foreach ($user_address as $address) {
                        $user_details[$address->type] = array(
                            "name" => $address->name,
                            "email" => $address->email,
                            "phone" => $address->phone,
                            "pin" => $address->pin,
                            "location" => $address->location,
                            "address" => $address->address,
                            "city" => $address->city,
                            "state_id" => $address->state_id,
                            "country_id" => $address->country_id,
                            "landmark" => $address->landmark,
                            "type" => $address->type,
                        );
                    }
                    return view('ordercheckout', compact('order', 'user_details', 'order_details'));
                } else {
                    return view('notfound_frontview')->withErrors('Something went wrong Order Details not found.');
                }
            } else {
                return view('notfound_frontview')->withErrors('Something went wrong Order not found.');
            }

        } else {
            return view('notfound_frontview')->withErrors('Something went wrong Order ID not found.');
        }

    }

    public function order_payment(Request $request)
    {
        if ($request->order_id != '') {
            $merchant_id = Config::get('constants.payment_constants.merchant_id');
            $client_id = Config::get('constants.payment_constants.client_id');
            $secret_key = Config::get('constants.payment_constants.secret_key');
            $order = Order::find($request->order_id);
            if (Auth::guard('user')->user()) {
                $user_id = Auth::guard('user')->user()->id;
            }

            $address_exist = UserAddress::join('states', 'states.id', 'user_addresses.state_id')
                ->where('user_addresses.id', $order->address_id)
                ->select('user_addresses.*', 'states.name as state')->first();

            if ($address_exist) {

                $current_time = strtotime("now");
                $trace_id = $current_time . 'EXP';
                $sub_id = 'sub' . $current_time;

                $headers = ["alg" => "HS256", "clientid" => $client_id];

                $grand_total = number_format($order->grand_total, 2);

                $grand_total = str_replace(",", "", $grand_total);
                $order_date = date(DATE_ATOM, strtotime($order->created_at));

                $payload = [
                    "mercid" => $merchant_id,
                    "orderid" => $order->id,
                    "amount" => $grand_total,
                    "order_date" => $order_date,
                    "currency" => "356",
                    "ru" => route('payment.response'),
                    "additional_info" => [
                        "additional_info1" => $address_exist->name,
                        "additional_info2" => $address_exist->email,
                        "additional_info3" => $address_exist->phone,
                        "additional_info4" => $address_exist->location,
                        "additional_info5" => $address_exist->city,
                        "additional_info6" => $address_exist->state,
                        "additional_info7" => "NA",
                    ],
                    "itemcode" => "DIRECT",
                    "device" => [
                        "init_channel" => "internet",
                        "ip" => $this->getIp(),
                        "user_agent" => $request->userAgent(),
                    ],
                ];
                $curl_payload = JWT::encode($payload, $secret_key, "HS256", null, $headers);

                // print_r($curl_payload);

                 $url = "https://api.billdesk.com/payments/ve1_2/orders/create";
                //$url = "https://pguat.billdesk.io/payments/ve1_2/orders/create";
                $now = now();
                $ch = curl_init($url);

                $ch_headers = array(
                    "content-type: application/jose",
                    "bd-timestamp: $current_time",
                    "accept: application/jose",
                    "bd-traceid: $trace_id",
                );
                // print_r($ch_headers);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $payment_token = curl_exec($ch);
                curl_close($ch);

                $tokenParts = explode(".", $payment_token);
                $tokenHeader = base64_decode($tokenParts[0]);
                $tokenPayload = base64_decode($tokenParts[1]);
                $jwtHeader = json_decode($tokenHeader);
                $jwtPayload = json_decode($tokenPayload);

                return ['success' => true, 'order' => $order, 'jwtPayload' => $jwtPayload];
            } else {
                return ['result' => false, "errorMsg" => 'Checkout failed. address not found.'];
            }
        } else {
            return view('notfound_frontview')->withErrors('Something went wrong Transaction Error.');
        }
    }
    public function cod_response($order_id, $checkout_type = null)
    {

        $user_id = 0;
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
        }

        $order = Order::join('order_details', 'order_details.order_id', 'orders.id')
            ->where('orders.id', $order_id)->select('orders.*')->first();

        $Productdetails = Product::join('order_details', 'order_details.product_id', 'products.id')
            ->join('orders', 'orders.id', 'order_details.order_id')
            ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
            ->where('orders.id', $order_id)
            ->select(DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), 'products.id as productid', 'products.product_name', 'products.tax_ids', 'GS.value AS email', 'order_details.quantity')
            ->get();

        $billing_address = Order::join('order_details', 'order_details.order_id', 'orders.id')
            ->join('user_addresses', 'user_addresses.id', 'orders.address_id')
            ->join('states', 'states.id', 'user_addresses.state_id')
            ->join('countries', 'countries.id', 'user_addresses.country_id')
            ->where('user_addresses.id', $order->address_id)
            ->select('user_addresses.*', 'countries.name as country', 'states.name as state')->first();
        foreach ($Productdetails as $key => $product) {
            $orderDetails['order_items'][$key]['name'] = $product->product_name;
            $orderDetails['order_items'][$key]['sku'] = $product->productid;
            $orderDetails['order_items'][$key]['units'] = $product->quantity;
            $orderDetails['order_items'][$key]['selling_price'] = $product->ProductPrice;
            $orderDetails['order_items'][$key]['discount'] = "";
            $orderDetails['order_items'][$key]['tax'] = "";
            $orderDetails['order_items'][$key]['hsn'] = '000';

        }
        if ($order->delivery_type == 'direct') {
            $token = Shiprocket::getToken();
            $newLocation['pickup_location'] = $billing_address->address;
            $newLocation['name'] = $billing_address->name;
            $newLocation['email'] = $billing_address->email;
            $newLocation['phone'] = $billing_address->phone;
            $newLocation['address'] = $billing_address->address . ' 0,' . $billing_address->city;
            $newLocation['address_2'] = "";
            $newLocation['city'] = $billing_address->city;
            $newLocation['state'] = $billing_address->state;
            $newLocation['country'] = $billing_address->country;
            $newLocation['pin_code'] = $billing_address->pin;

            $response = Shiprocket::pickup($token)->addLocation($newLocation);
            $location = Shiprocket::pickup($token)->getLocations();

            $orderDetails['order_id'] = $order->id;
            $orderDetails['order_date'] = $order->created_at;
            $orderDetails['pickup_location'] = $billing_address->address;
            $orderDetails['channel_id'] = "";
            $orderDetails['comment'] = "null";
            $orderDetails['billing_customer_name'] = $billing_address->name;
            $orderDetails['billing_last_name'] = "-";
            $orderDetails['billing_address'] = $billing_address->address;
            $orderDetails['billing_address_2'] = "-";
            $orderDetails['billing_city'] = $billing_address->city;
            $orderDetails['billing_pincode'] = $billing_address->pin;
            $orderDetails['billing_state'] = $billing_address->state;
            $orderDetails['billing_country'] = $billing_address->country;
            $orderDetails['billing_email'] = $billing_address->email;
            $orderDetails['billing_phone'] = $billing_address->phone;
            $orderDetails['shipping_is_billing'] = true;
            $orderDetails['shipping_customer_name'] = "";
            $orderDetails['shipping_last_name'] = "";
            $orderDetails['shipping_address'] = "";
            $orderDetails['shipping_address_2'] = "";
            $orderDetails['shipping_city'] = "";
            $orderDetails['shipping_pincode'] = "";
            $orderDetails['shipping_country'] = "";
            $orderDetails['shipping_state'] = "";
            $orderDetails['shipping_email'] = "";
            $orderDetails['shipping_phone'] = "";
            $orderDetails['payment_method'] = "Prepaid";
            $orderDetails['shipping_charges'] = 0;
            $orderDetails['giftwrap_charges'] = 0;
            $orderDetails['transaction_charges'] = 0;
            $orderDetails['total_discount'] = 0;
            $orderDetails['sub_total'] = $order->grand_total;
            $orderDetails['length'] = "1";
            $orderDetails['breadth'] = "1";
            $orderDetails['height'] = "1";
            $orderDetails['weight'] = "1";
            $response = Shiprocket::order($token)->create($orderDetails);
            // $shipments = Shiprocket::shipment($token)->getSpecific($response['shipment_id']);
            if (isset($response['shipment_id']) && $response['shipment_id'] != '') {
                Order::where('id', $order->id)->update(array('shipment_id' => $response['shipment_id']));
            }
        }
        Order::where('id', $order_id)->update(array('status' => 'ordered'));

        $invoice_number = $this->invoiceNumber();
        Invoice::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'invoice_number' => $invoice_number,
        ]);
        $this->customer_invoice_mail($order_id, $invoice_number);
        $this->Admin_orderConfirmation_mail($order_id, $invoice_number);

        if ($checkout_type == 'Cart') {
            if (Auth::guard('user')->user()) {
                $user_id = Auth::guard('user')->user()->id;
                $carts = Cart::join('products', 'carts.product_id', 'products.id')
                    ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                    ->select('carts.*')
                    ->where('carts.user_id', $user_id)
                    ->get();
                foreach ($carts as $key => $value) {
                    Cart::where('id', $value->id)->delete();
                }

            } else {
                if (!isset($_SESSION)) {
                    session_start();
                }
                unset($_SESSION['Session_GuestCart']);
            }
        }

        // return redirect()->route('order.invoice', $order_id)->with('success', 'Transaction Successfull, Payment Verified.');

    }
    public function BilldeskPaymentGateway($order_id = null, $checkout_type = null)
    {

    }

    public function RazorPaymentResponse(Request $request)
    {
        $user_id = 0;
        if (Auth::guard('user')->user()) {
            $user_id = Auth::guard('user')->user()->id;
        }
        $input = $request->all();

        $order_id = $input['order_id'];
        $checkout_type = $input['checkout_type'];
        $api = new Api(config('constants.RAZORPAY_KEY'), config('constants.RAZORPAY_SECRET'));
        $payment = $api->payment->fetch($input['razor_payment_id']);
        if (!empty($payment) && ($payment['status'] == 'captured'||$payment['status'] == 'authorized')) {
            try {
                // Payment detail save in database
                $paymentDetail = new PaymentDetail;
                $paymentDetail->order_id = $order_id;
                $paymentDetail->payment_gateway = 'razorpay';
                $paymentDetail->transaction_id = $payment['id'];
                $paymentDetail->amount = $payment['amount'] / 100;
                $paymentDetail->transaction_date = date('Y-m-d H:i:s', $payment['created_at']);
                $paymentDetail->currency_code = $payment['currency'];
                $paymentDetail->payment_method = $payment['method'];
                $paymentDetail->transaction_status = $payment['status'];
                $saved = $paymentDetail->save();

            } catch (Exception $e) {
                $saved = false;
            }
            if ($saved) {
                Order::where('id', $order_id)->update(array('status' => 'ordered'));

                $order = Order::join('order_details', 'order_details.order_id', 'orders.id')
                    ->where('orders.id', $order_id)->select('orders.*')->first();

                $Productdetails = Product::join('order_details', 'order_details.product_id', 'products.id')
                    ->join('orders', 'orders.id', 'order_details.order_id')
                    ->join('generalsettings as GS', 'GS.item', '=', DB::raw("'notification_email'"))
                    ->where('orders.id', $order_id)
                    ->select(DB::raw('(CASE WHEN products.offer_price != 0 THEN products.offer_price ELSE products.price END) as ProductPrice'), 'products.id as productid', 'products.product_name', 'products.tax_ids', 'GS.value AS email', 'order_details.quantity')
                    ->get();

                $billing_address = Order::join('order_details', 'order_details.order_id', 'orders.id')
                    ->join('user_addresses', 'user_addresses.id', 'orders.address_id')
                    ->join('states', 'states.id', 'user_addresses.state_id')
                    ->join('countries', 'countries.id', 'user_addresses.country_id')
                    ->where('user_addresses.id', $order->address_id)
                    ->select('user_addresses.*', 'countries.name as country', 'states.name as state')->first();
                foreach ($Productdetails as $key => $product) {
                    $orderDetails['order_items'][$key]['name'] = $product->product_name;
                    $orderDetails['order_items'][$key]['sku'] = $product->productid;
                    $orderDetails['order_items'][$key]['units'] = $product->quantity;
                    $orderDetails['order_items'][$key]['selling_price'] = $product->ProductPrice;
                    $orderDetails['order_items'][$key]['discount'] = "";
                    $orderDetails['order_items'][$key]['tax'] = "";
                    $orderDetails['order_items'][$key]['hsn'] = '000';

                }
                if ($order->delivery_type == 'direct') {
                    $token = Shiprocket::getToken();
                    $newLocation['pickup_location'] = $billing_address->address;
                    $newLocation['name'] = $billing_address->name;
                    $newLocation['email'] = $billing_address->email;
                    $newLocation['phone'] = $billing_address->phone;
                    $newLocation['address'] = $billing_address->address . ' 0,' . $billing_address->city;
                    $newLocation['address_2'] = "";
                    $newLocation['city'] = $billing_address->city;
                    $newLocation['state'] = $billing_address->state;
                    $newLocation['country'] = $billing_address->country;
                    $newLocation['pin_code'] = $billing_address->pin;

                    $response = Shiprocket::pickup($token)->addLocation($newLocation);
                    $location = Shiprocket::pickup($token)->getLocations();

                    $orderDetails['order_id'] = $order->id;
                    $orderDetails['order_date'] = $order->created_at;
                    $orderDetails['pickup_location'] = $billing_address->address;
                    $orderDetails['channel_id'] = "";
                    $orderDetails['comment'] = "null";
                    $orderDetails['billing_customer_name'] = $billing_address->name;
                    $orderDetails['billing_last_name'] = "-";
                    $orderDetails['billing_address'] = $billing_address->address;
                    $orderDetails['billing_address_2'] = "-";
                    $orderDetails['billing_city'] = $billing_address->city;
                    $orderDetails['billing_pincode'] = $billing_address->pin;
                    $orderDetails['billing_state'] = $billing_address->state;
                    $orderDetails['billing_country'] = $billing_address->country;
                    $orderDetails['billing_email'] = $billing_address->email;
                    $orderDetails['billing_phone'] = $billing_address->phone;
                    $orderDetails['shipping_is_billing'] = true;
                    $orderDetails['shipping_customer_name'] = "";
                    $orderDetails['shipping_last_name'] = "";
                    $orderDetails['shipping_address'] = "";
                    $orderDetails['shipping_address_2'] = "";
                    $orderDetails['shipping_city'] = "";
                    $orderDetails['shipping_pincode'] = "";
                    $orderDetails['shipping_country'] = "";
                    $orderDetails['shipping_state'] = "";
                    $orderDetails['shipping_email'] = "";
                    $orderDetails['shipping_phone'] = "";
                    $orderDetails['payment_method'] = "Prepaid";
                    $orderDetails['shipping_charges'] = 0;
                    $orderDetails['giftwrap_charges'] = 0;
                    $orderDetails['transaction_charges'] = 0;
                    $orderDetails['total_discount'] = 0;
                    $orderDetails['sub_total'] = $order->grand_total;
                    $orderDetails['length'] = "1";
                    $orderDetails['breadth'] = "1";
                    $orderDetails['height'] = "1";
                    $orderDetails['weight'] = "1";
                    $response = Shiprocket::order($token)->create($orderDetails);
                    // $shipments = Shiprocket::shipment($token)->getSpecific($response['shipment_id']);
                    if (isset($response['shipment_id']) && $response['shipment_id'] != '') {
                        Order::where('id', $order->id)->update(array('shipment_id' => $response['shipment_id']));
                    }
                }
                $invoice_number = $this->invoiceNumber();
                Invoice::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'invoice_number' => $invoice_number,
                ]);
                $this->customer_invoice_mail($order_id, $invoice_number);
                $this->Admin_orderConfirmation_mail($order_id, $invoice_number);
                if ($checkout_type == 'Cart') {
                    if (Auth::guard('user')->user()) {
                        $user_id = Auth::guard('user')->user()->id;
                        $carts = Cart::join('products', 'carts.product_id', 'products.id')
                            ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                            ->select('carts.*')
                            ->where('carts.user_id', $user_id)
                            ->get();
                        foreach ($carts as $key => $value) {
                            Cart::where('id', $value->id)->delete();
                        }

                    } else {
                        if (!isset($_SESSION)) {
                            session_start();
                        }
                        unset($_SESSION['Session_GuestCart']);
                    }
                }
                return redirect()->route('order.invoice', $order_id)->with('success', 'Transaction Successfull, Payment Verified.');
            } else {
                Order::where('id', $order_id)->update(array('status' => 'failed'));
                $this->payment_error_mail($payment);
                $jwtresponse == $payment;
                return view('payment_error_page', compact('jwtresponse'))->with('errors', 'Payment Transaction Failed.');
                // return view('notfound_frontview')->withErrors('Something went wrong Transaction Error.');
            }
        } else {
            return view('notfound_frontview')->withErrors('Something went wrong Transaction Error.');
        }

    }

    public function storePaymentDetails(Request $request)
    {

        $input = $request->all();

        // $uid=$request->razorpay_order_id;

        // $api = new Api(config('constants.RAZORPAY_KEY'), config('constants.RAZORPAY_SECRET'));
        // $payment = $api->payment->fetch($input['razor_payment_id']);
        $amount = (int) $payment['amount'] / 100;
        $payment_data = [
            'payment_id' => $input['razor_payment_id'],
            'payment_status' => $payment['status'],
            'payment_time' => $payment['created_at'],
        ];
        if ($input['checkout_type'] == 'BuyNow') {
            $result = $this->placeOrder_buynow($input);
        } else {
            $result = $this->placeOrder($input, $payment_data);
        }
        $order_id = $result['orders'];
        return redirect()->route('order.invoice', ['id' => $order_id]);
        $arr = array('msg' => 'Payment successfully credited', 'status' => true);
    }
    public function stores_list(Request $request)
    {
        $stores = Store::orderBy('stores.id', 'ASC')->get();
        return response()->json($stores);
    }
}
