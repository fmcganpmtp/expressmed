<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\Career_application;
use App\Models\Category;
use App\Models\Contentpage;
use App\Models\Generalsetting;
use App\Models\News;
use App\Models\Newsletter;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Productbrand;
use App\Models\Promotionbanner;
use App\Models\PromotionBannerImage;
use App\Models\Sliderimage;
use App\Models\State;
use App\Models\Testimonial;
use App\Models\ProductManufacturer;
use App\Models\Team;
use App\Models\Doctor;


use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;

class HomeController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::select('name', 'comments', 'company_name', 'title', 'profile_pic')->limit(3)->latest()->get();
        $productbrands = Productbrand::select('id', 'name', 'image')->limit(6)->latest()->get();

        $category = Category::select('categories.*')->join('home_categories', 'home_categories.category_id', 'categories.id')->get();
        $homepageProductsArray = array();
        $child_categoryIds = [];
        $all_categoryIds = [];
        // if ($category) {
        //     foreach ($category as $category_data) {
        //         $products = Product::select('products.*', 'product_images.product_image')
        //             ->leftJoin('product_images', 'product_images.id', 'products.thumbnail');
        //             if($category_data->parent_id==0){
        //             $products->where('products.producttypeid', $category_data->id);
        //             }else{
        //                     //

        //             array_push($all_categoryIds, $category_data->id);
        //             array_push($child_categoryIds, $category_data->id);
        //             $obj_category = new Category();
        //             $child_category = $obj_category->getCategories($all_categoryIds);
        //             $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);

        //                $products->whereIn('products.category_id', $child_categoryIds);
        //             }
        //             $products=$products->where('products.hide_from_site','!=' ,'1')->where('products.status', 'active')
        //             ->limit(6)->latest()->get();

        //         if ($products->isNotEmpty()) {
        //             $homepageProductsArray[] = array(
        //                 'category_name' => $category_data->name,
        //                 'category_image' => $category_data->image,
        //                 'category_id' => $category_data->id,
        //                 'products' => $products,
        //             );
        //         }
        //     }
        // }
        // dd($homepageProductsArray[4]);
        $top_selling = OrderDetails::join('products', 'products.id', 'order_details.product_id')
            ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
            ->leftJoin('categories', 'categories.id', 'products.producttypeid')
            ->where('categories.name', '!=', 'All Medicines')
            ->where('products.hide_from_site', '!=', '1')
            ->where('products.status', 'active')
            ->select('products.id', 'products.product_name', 'products.price', 'products.offer_price', 'products.flag', 'products.not_for_sale', 'products.product_url', 'product_images.product_image',
                DB::raw('COUNT(products.id) AS product_cnt'))
            ->groupBy('order_details.product_id')->orderBy('product_cnt', 'DESC')->limit(6)->get();

        $top_selling_brands = OrderDetails::join('products', 'products.id', 'order_details.product_id')
            ->Join('productbrands', 'productbrands.id', 'products.brands')
            ->leftJoin('categories', 'categories.id', 'products.producttypeid')
            ->where('categories.name', '!=', 'All Medicines')
            ->select(DB::raw('COUNT(products.id) AS product_cnt'), 'products.brands', 'productbrands.name as brand_name', 'productbrands.image as image')
            ->groupBy('products.brands')->orderBy('product_cnt', 'DESC')->limit(6)->get();

        // $top_selling_manufactures = OrderDetails::join('products', 'products.id', 'order_details.product_id')
        //     ->join('product_manufacturers', 'product_manufacturers.id', 'products.manufacturer')
        //     ->leftJoin('categories', 'categories.id', 'products.producttypeid')
        //     ->where('categories.name', '!=', 'All Medicines')
        //     ->select(DB::raw('COUNT(products.id) AS product_cnt'), 'products.manufacturer', 'product_manufacturers.name as manufact')
        //     ->groupBy('products.manufacturer')->orderBy('product_cnt', 'DESC')->limit(15)->get();

         $top_selling_manufactures = ProductManufacturer::select('product_manufacturers.name as manufact')
            ->where('product_manufacturers.add_to_home',1)->get();


        $new_arrivals = Product::select('products.*', 'product_images.product_image')
            ->leftJoin('product_images', 'product_images.id', 'products.thumbnail')
            ->leftJoin('categories', 'categories.id', 'products.producttypeid')
            ->where('categories.name', '!=', 'All Medicines')
            ->where('products.hide_from_site', '!=', '1')
            ->where('products.status', 'active')
            ->limit(12)->latest()->get();

        return view('home', compact('testimonials', 'productbrands', 'top_selling', 'new_arrivals', 'top_selling_manufactures', 'top_selling_brands', 'category'));
    }

    public function get_homebanners(Request $request)
    {
        //Main Body Slider--
        $mainSlider = [];
        $MainSliderRow = Promotionbanner::where('section', 'mainbody')->where('position', 'maintop')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($MainSliderRow) {
            $MainSliderImages = PromotionBannerImage::where('promotionbanner_id', $MainSliderRow->id)->get();
            $mainSlider['Details'] = $MainSliderRow;
            $mainSlider['Images'] = $MainSliderImages;

            $returnarray['mainSlider']['details'] = $MainSliderRow;
            $returnarray['mainSlider']['Images'] = $MainSliderImages;
        }

        //Main Body Banner Middle--
        $middleBanner = [];
        $middleBannerRow = Promotionbanner::where('section', 'mainbody')->where('position', 'middle')->where('type', 'plain')->where('status', 'active')->limit(2)->latest()->get(['id', 'title'])->toArray();
        if ($middleBannerRow) {
            $middleBannerImages = PromotionBannerImage::whereIn('promotionbanner_id', array_column($middleBannerRow, 'id'))->get();
            $middleBanner['Images'] = $middleBannerImages;

            $returnarray['middleBanner']['Images'] = $middleBannerImages;
        }

        //Main Body Banner Bottom--
        // $bottomBanner = [];
        // $bottomBannerRow = Promotionbanner::where('section', 'mainbody')->where('position', 'footer')->where('type', 'plain')->where('status', 'active')->limit(4)->latest()->get(['id', 'title'])->toArray();
        // if ($bottomBannerRow) {
        //     $bottomBannerImages = PromotionBannerImage::whereIn('promotionbanner_id', array_column($bottomBannerRow, 'id'))->get();
        //     // $bottomBanner['Details'] = $bottomBannerRow;
        //     $bottomBanner['Images'] = $bottomBannerImages;

        //     $returnarray['bottomBanner']['Images'] = $bottomBannerImages;
        // }

        $bottomBanner = [];
        $bottomBannerRow = Promotionbanner::where('section', 'mainbody')->where('position', 'footer')->where('type', 'plain')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($bottomBannerRow) {
            $bottomBannerImages = PromotionBannerImage::where('promotionbanner_id', $bottomBannerRow->id)->get();

            $bottomBanner['Images'] = $bottomBannerImages;

            $returnarray['bottomBanner']['Images'] = $bottomBannerImages;
        }

        $bottomBanner2 = [];
        $bottomBannerRow2 = Promotionbanner::where('section', 'mainbody')->where('position', 'footer2')->where('type', 'plain')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($bottomBannerRow2) {
            $bottomBannerImages2 = PromotionBannerImage::where('promotionbanner_id', $bottomBannerRow2->id)->get();

            $bottomBanner2['Images'] = $bottomBannerImages2;

            $returnarray['bottomBanner2']['Images'] = $bottomBannerImages2;
        }
        $bottomBanner3 = [];
        $bottomBannerRow3 = Promotionbanner::where('section', 'mainbody')->where('position', 'footer3')->where('type', 'plain')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($bottomBannerRow3) {
            $bottomBannerImages3 = PromotionBannerImage::where('promotionbanner_id', $bottomBannerRow3->id)->get();

            $bottomBanner3['Images'] = $bottomBannerImages3;

            $returnarray['bottomBanner3']['Images'] = $bottomBannerImages3;
        }

        $bottomBanner4 = [];
        $bottomBannerRow4 = Promotionbanner::where('section', 'mainbody')->where('position', 'footer4')->where('type', 'plain')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($bottomBannerRow4) {
            $bottomBannerImages4 = PromotionBannerImage::where('promotionbanner_id', $bottomBannerRow4->id)->get();

            $bottomBanner4['Images'] = $bottomBannerImages4;

            $returnarray['bottomBanner4']['Images'] = $bottomBannerImages4;
        }

        //Sidebar Slider Top1--
        $sidebarSl_top1 = [];
        $sidebarSl_top1Row = Promotionbanner::where('section', 'sidebar')->where('position', 'top')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($sidebarSl_top1Row) {
            $sidebarSl_top1Images = PromotionBannerImage::where('promotionbanner_id', $sidebarSl_top1Row->id)->get();
            $sidebarSl_top1['Images'] = $sidebarSl_top1Images;

            $returnarray['sidebarSl_top1']['Images'] = $sidebarSl_top1Images;
        }

        //Sidebar Plain Image Top2--
        // $sidebarPlain_top2 = [];
        // $sidebarPlain_top2Row = Promotionbanner::where('section', 'sidebar')->where('position', 'top2')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        // if ($sidebarPlain_top2Row) {
        //     $sidebarPlain_top2Images = PromotionBannerImage::where('promotionbanner_id', $sidebarPlain_top2Row->id)->first();
        //     $sidebarPlain_top2['Images'] = $sidebarPlain_top2Images;

        //     $returnarray['sidebarPlain_top2']['Images'] = $sidebarPlain_top2Images;
        // }

        $sidebarSl_top2 = [];
        $sidebarSl_top2Row = Promotionbanner::where('section', 'sidebar')->where('position', 'top2')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        // dd($sidebarSl_top3Row);
        if ($sidebarSl_top2Row) {
            $sidebarSl_top2Images = PromotionBannerImage::where('promotionbanner_id', $sidebarSl_top2Row->id)->get();
            $sidebarSl_top2['Images'] = $sidebarSl_top2Images;

            $returnarray['sidebarSl_top2']['Images'] = $sidebarSl_top2Images;
        }

        $sidebarSl_top3 = [];
        $sidebarSl_top3Row = Promotionbanner::where('section', 'sidebar')->where('position', 'top3')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        // dd($sidebarSl_top3Row);
        if ($sidebarSl_top3Row) {
            $sidebarSl_top3Images = PromotionBannerImage::where('promotionbanner_id', $sidebarSl_top3Row->id)->get();
            $sidebarSl_top3['Images'] = $sidebarSl_top3Images;

            $returnarray['sidebarSl_top3']['Images'] = $sidebarSl_top3Images;
        }

        $sidebarSl_top4 = [];
        $sidebarSl_top4Row = Promotionbanner::where('section', 'sidebar')->where('position', 'top4')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        // dd($sidebarSl_top3Row);
        if ($sidebarSl_top4Row) {
            $sidebarSl_top4Images = PromotionBannerImage::where('promotionbanner_id', $sidebarSl_top4Row->id)->get();
            $sidebarSl_top4['Images'] = $sidebarSl_top4Images;

            $returnarray['sidebarSl_top4']['Images'] = $sidebarSl_top4Images;
        }

        //Sidebar Slider Bottom1--
        $sidebarSl_bottom1 = [];
        $sidebarSl_bottom1Row = Promotionbanner::where('section', 'sidebar')->where('position', 'bottom')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($sidebarSl_bottom1Row) {
            $sidebarSl_bottom1Images = PromotionBannerImage::where('promotionbanner_id', $sidebarSl_bottom1Row->id)->get();
            $sidebarSl_bottom1['Images'] = $sidebarSl_bottom1Images;

            $returnarray['sidebarSl_bottom1']['Images'] = $sidebarSl_bottom1Images;
        }

        //Sidebar Slider Bottom2--
        $sidebarSl_bottom2 = [];
        $sidebarSl_bottom2Row = Promotionbanner::where('section', 'sidebar')->where('position', 'bottom2')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($sidebarSl_bottom2Row) {
            $sidebarSl_bottom2Images = PromotionBannerImage::where('promotionbanner_id', $sidebarSl_bottom2Row->id)->get();
            $sidebarSl_bottom2['Images'] = $sidebarSl_bottom2Images;

            $returnarray['sidebarSl_bottom2']['Images'] = $sidebarSl_bottom2Images;
        }

        return response()->json($returnarray);
    }
    public function Homecategories(Request $request)
    {

        $category = Category::select('categories.*')->join('home_categories', 'home_categories.category_id', 'categories.id')->get();
        $homepageProductsArray = array();

        if ($category) {
            foreach ($category as $category_data) {
                $child_categoryIds = [];
                $all_categoryIds = [];
                $products = Product::select('products.*', 'product_images.product_image')
                    ->leftJoin('product_images', 'product_images.id', 'products.thumbnail');
                if ($category_data->parent_id == 0) {
                    $products->where('products.producttypeid', $category_data->id);
                } else {
                    //    $products->where('products.category_id', $category_data->id);
                    array_push($all_categoryIds, $category_data->id);
                    array_push($child_categoryIds, $category_data->id);
                    $obj_category = new Category();
                    $child_category = $obj_category->getCategories($all_categoryIds);
                    $child_categoryIds = $this->getCategoryIds($child_category, $child_categoryIds);

                    $products->whereIn('products.category_id', $child_categoryIds);

                }
                $products = $products->where('products.hide_from_site', '!=', '1')->where('products.status', 'active')
                    ->limit(6)->latest()->get();

                if ($products->isNotEmpty()) {
                    $homepageProductsArray[] = array(
                        'category_name' => $category_data->name,
                        'category_image' => $category_data->image,
                        'category_id' => $category_data->id,
                        'products' => $products,
                    );
                }
            }
        }
        $returnarray['homepageProductsArray'] = $homepageProductsArray;
        // dd($homepageProductsArray);
        return response()->json($returnarray);
        // return view('home', compact('homepageProductsArray'));
    }

    public function get_pagebanners(Request $request)
    {
        //Sidebar Slider Top1--
        $sidebarSl_top1 = [];
        $sidebarSl_top1Row = Promotionbanner::where('section', 'sidebar')->where('position', 'bottom')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($sidebarSl_top1Row) {
            $sidebarSl_top1Images = PromotionBannerImage::where('promotionbanner_id', $sidebarSl_top1Row->id)->get();
            $sidebarSl_top1['Images'] = $sidebarSl_top1Images;

            $returnarray['sidebarSl_top1']['Images'] = $sidebarSl_top1Images;
        }

        //Sidebar Slider Bottom2--
        $sidebarSl_bottom2 = [];
        $sidebarSl_bottom2Row = Promotionbanner::where('section', 'sidebar')->where('position', 'bottom2')->where('type', 'slider')->where('status', 'active')->orderBy('created_at', 'desc')->first();
        if ($sidebarSl_bottom2Row) {
            $sidebarSl_bottom2Images = PromotionBannerImage::where('promotionbanner_id', $sidebarSl_bottom2Row->id)->get();
            $sidebarSl_bottom2['Images'] = $sidebarSl_bottom2Images;

            $returnarray['sidebarSl_bottom2']['Images'] = $sidebarSl_bottom2Images;
        }

        return response()->json($returnarray);
    }

    public function profile()
    { // need to remove this function-- User profile define in MyaccountController--
        if (Auth::guard('user')->user()) {
            echo 'welcome to the profile for ' . Auth::guard('user')->user()->name;

            return view('home');
        } else {
            echo 'not logged in';
        }
    }

    public function viewcontentpage($seo_url)
    {
        if ($seo_url) {
            $contentpage = Contentpage::where('seo_url', $seo_url)->first();
            $testimonials = Testimonial::select('name', 'comments', 'company_name', 'title', 'profile_pic')->latest()->get();
            $teams = Team::latest()->get();
            $doctors = Doctor::latest()->get();

            if ($contentpage) {
                $sliders = array();
                if ($contentpage->slider != 0) {
                    $sliders = Sliderimage::where('slider_id', $contentpage->slider)->get()->all();
                }

                return view('viewcontentpage', compact('contentpage', 'sliders', 'testimonials','teams','doctors'));
            } else {
                return view('notfound_frontview')->withErrors('Sorry.. page details is not found.');
            }
        } else {
            return view('notfound_frontview')->withErrors('requested url is wrong. go to back and load again.');
        }
    }
	public function OurTeam()
    {
        
		$teams = Team::orderBy('displayorder', 'ASC')->get();
		$doctors = Doctor::orderBy('displayorder', 'ASC')->get();
		return view('ourteams', compact( 'teams','doctors'));
		        
    }
    public function viewcontentpage_api($seo_url)
    {
        if ($seo_url) {
            $contentpage = Contentpage::where('seo_url', $seo_url)->first();

            if ($contentpage) {
                $sliders = array();
                if ($contentpage->slider != 0) {
                    $sliders = Sliderimage::where('slider_id', $contentpage->slider)->get()->all();
                }

                return view('viewcontentpage_api', compact('contentpage', 'sliders'));
            } else {
                return view('notfound_frontview')->withErrors('Sorry.. page details is not found.');
            }
        } else {
            return view('notfound_frontview')->withErrors('requested url is wrong. go to back and load again.');
        }
    }

    public function view_careerjobs()
    {
        $careers = Career::where('status', 'active')->latest()->paginate(20);
        // dd($careers);

        return view('view_careerjobs', compact('careers'));
    }
    public function view_contact_page()
    {
        // $careers = Career::where('status', 'active')->latest()->paginate(20);
        // dd($careers);

        return view('contactus');
    }

    public function apply_careerjobs($id = null)
    {
        if ($id != null) {
            $job_details = Career::find($id);
            if ($job_details) {
                $data['page_title'] = $job_details->job_title;
                return view('apply_careerjobs', compact('job_details'), $data);
            } else {
                return view('notfound_frontview')->withErrors('sorry... career details not found!');
            }
        } else {
            return view('notfound_frontview')->withErrors('requested url is wrong. go to back and load again.');
        }
    }

    public function store_careerjobs(Request $request, $id)
    {
        if ($id != null) {
            $job_details = Career::find($id);
            if ($job_details) {
                $this->validate($request, [
                    'applicant_name' => 'required',
                    'phone' => 'required|numeric|regex:/[0-9]{9}/',
                    'applicant_email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
                    'birthdate' => 'required|before:today|after:1980-01-01',
                    'resume_file' => 'required|mimes:pdf,doc,docx,dot|max:2048'],
                    [
                        'birthdate.before' => 'Please provide your valid DOB',
                        'birthdate.after' => 'Please provide your valid DOB']
                );

                if ($request->file('resume_file')) {
                    $fileName = time() . '.' . $request->resume_file->extension();

                    $request->resume_file->move(public_path('/assets/uploads/careerjobs_resume/'), $fileName);

                    Career_application::create([
                        'career_id' => $id,
                        'applicant_name' => $request->applicant_name,
                        'phone' => $request->phone,
                        'applicant_email' => $request->applicant_email,
                        'birthdate' => date('Y-m-d', strtotime($request->birthdate)),
                        'address' => $request->address,
                        'pin' => $request->pin,
                        'resume' => $fileName,
                    ]);

                    return redirect()->route('view.career.jobs')->with('success', 'You applied successfully for job ' . $job_details->job_title . '.');
                } else {
                    return view('notfound_frontview')->withErrors('Resume not found. Please upload your resume.');
                }
            } else {
                return view('notfound_frontview')->withErrors('sorry... career details not found!');
            }
        } else {
            return view('notfound_frontview')->withErrors('sorry... your attempt failed. Something went wrong. please try again.');
        }
    }

    public function Subscribe_newsletter(Request $request)
    {
        $validation = $this->validatorSubscription($request->all());
        if ($validation->fails()) {
            $message = '';
            foreach ($validation->errors()->toArray() as $value) {
                $message .= $value[0];
            }
            $msg = array(
                'status' => 'error',
                'message' => $message,
                'subscribe_status' => '',
            );
        } else {
            if (Newsletter::where('email_id', $request->email)->where('status', 'subscribed')->exists()) {
                $message = 'You are already subscribed our newsletter.';
                $msg = array(
                    'status' => 'error',
                    'message' => $message,
                    'subscribe_status' => 'subscribed',
                );
            } else {
                Newsletter::create([
                    'email_id' => $request->email,
                    'status' => 'subscribed',
                ]);
                $msg = array(
                    'status' => 'success',
                    'message' => 'Successfully subscribed our newsletter features',
                    'subscribe_status' => '',
                );
            }
        }
        return response()->json($msg);
    }

    public function Unsubscribe_newsletter(Request $request)
    {
        $validation = $this->validatorSubscription($request->all());
        if ($validation->fails()) {
            $message = '';
            foreach ($validation->errors()->toArray() as $value) {
                $message .= $value[0];
            }
            $msg = array(
                'status' => 'error',
                'message' => $message,
                'subscribe_status' => '',
            );
        } else {
            if (Newsletter::where('email_id', $request->email)->where('status', 'subscribed')->exists()) {
                Newsletter::where('email_id', $request->email)->where('status', 'subscribed')->update([
                    'status' => 'unsubscribed',
                ]);
                $msg = array(
                    'status' => 'success',
                    'message' => 'Successfully unsubscribed our newsletter features',
                    'subscribe_status' => '',
                );
            } else {
                $message = 'No more active records found with this email';
                $msg = array(
                    'status' => 'error',
                    'message' => $message,
                    'subscribe_status' => 'subscribed',
                );
            }
        }
        return response()->json($msg);
    }

    public function validatorSubscription(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
        ]);
    }

    public function stateLoader(Request $request)
    {
        $country_id = $request->id;
        $states = array();
        $message = '';
        $ajax_status = 'success';
        if ($country_id) {
            $states = State::where('country_id', $country_id)->get()->toArray();
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message, 'states' => $states);
        return response()->json($return_array);
    }

    public function all_medicine_categories(Request $request)
    {

        $medcineCategories = Category::where('name', 'All Medicines')->where('status', 'active')->first();
        if ($medcineCategories) {
            $med_cat_id = $medcineCategories->id;
        } else {
            $med_cat_id = 0;
        }
        $AllmedcinesubCategories = Category::where('parent_id', $med_cat_id)->where('status', 'active')->orderBy('name', 'asc')->get();

        return view('categorylisting_page', compact('AllmedcinesubCategories'));
    }
    public function Subcategories(Request $request)
    {

        $parent_category_id = $request->cat_id;
        $parent_cat = Category::find($parent_category_id);
        $parent_cat_name = $parent_cat->name;
        $ajax_status = '';
        $sub_categories = array();
        if ($parent_category_id) {

            $sub_categories = Category::where('parent_id', $parent_category_id)->where('status', 'active')->orderBy('name', 'asc')->get();
            $html = '';
            if (count($sub_categories) > 0) {

                $html .= '<ul style="max-height: 1000px; overflow-y: scroll; ">';

                foreach ($sub_categories as $val) {

                    $html .= ' <li class="">';
                    $html .= '<a class="main_med_cat"  href="javascript:subcategory(' . $val->id . ')"  value="' . $val->id . '"data-id="' . $val->id . '" data-name="' . $val->name . '" >' . $val->name . '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
                $ajax_status = 'success';
            } else {

                $ajax_status = 'fail';

            }

        }
        $return_array = array('ajax_status' => $ajax_status, 'sub_categories' => $sub_categories, 'table_data' => $html, 'parent_cat_name' => $parent_cat_name);

        return response()->json($return_array);

        // return view('categorylisting_page');
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

    public function sentContact(Request $request)
    {
        $fullname = $request->fullname;
        $email = $request->email;
        $message = $request->message_contact;

        if ($fullname == '') {
            $ajax_status = 'error';
            $message = 'Enter Your  Name';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        } else if ($email == '') {
            $ajax_status = 'error';
            $message = 'Enter Your Email Address';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $ajax_status = 'error';
            $message = 'Enter A Valid Email Address';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        } else {
            $settings = Generalsetting::where('item', 'notification_email')->get()->first();
            if ($settings && $settings->value != '') {
                Mail::send('email.contactformfull',
                    array(
                        'name' => $request->fullname,
                        'email' => $request->email,
                        'comment' => $request->message,
                    ), function ($message) use ($request, $settings) {
                        $message->from($request->email, $request->fullname);
                        $message->to($settings->value);
                        $message->subject('Contact Request From Expressmed');
                    });
                Mail::send('email.contactformReply',
                    array(
                        'name' => $request->fullname,
                        'email' => $request->email,
                        'subject' => 'RE:Contact Request From Expressmed',
                    ), function ($message) use ($request, $settings) {
                        $message->from($settings->value, 'Expressmed');
                        $message->to($request->email);
                        $message->subject('RE:Contact Request From Expressmed');
                    });
            }
            $message = "Thank you for contacting us. Our team will be in touch with you soon.";
            $ajax_status = 'success';
            $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
            return response()->json($return_array);
        }
        return response()->json($return_array);
    }
    public function news_evets(Request $request)
    {
        $news = News::latest()->get();
        return view('news_events', compact('news'));
    }
    public function news_evets_details(Request $request, $id = null)
    {
        $news_details = News::where('title', $id)->first();
        if ($news_details) {
            $news_media = News::select('newsgalleries.*')
                ->join('newsgalleries', 'newsgalleries.news_id', 'news.id')
                ->where('newsgalleries.news_id', $news_details->id)->get();

            // Newsgallery::where('news_id',$news_details->id)->get();

            return view('news_events_details', compact('news_details', 'news_media'));
        } else {
            return view('notfound_frontview')->withErrors('Somthing Went Wrong.');

        }
    }

}
