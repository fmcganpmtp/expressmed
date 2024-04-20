<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;

use App\Models\Contentpage;
use App\Models\Slider;
use App\Models\Sliderimage;
use App\Models\Category;
use App\Models\HomeCategory;
use App\Models\OfferLinkSection;

class ContentpagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
    	$contents = Contentpage::latest()->paginate(30);
        return view('admin.contentpage.index',compact('contents'))->with('i', ($request->input('page', 1) - 1) * 30);
    }

    public function create()
    {
    	$sliders = Slider::all();
        return view('admin.contentpage.create', [ 'sliders' => $sliders]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'page'          => 'required|unique:contentpages,page',
            'page_title'    => 'required',
            'content'       => 'required',
            'seo_title'     => 'required',
            'seo_keywords'  => 'required',
            'chk_position'  => 'required',
        ]);

        $page_position = '';
        $page_title = $request->page_title;
        if(is_array($request->chk_position) && $request->chk_position != '') {
            $page_position = implode(",",$request->chk_position);
        }

        $seo_url = $this->create_slug($page_title);

        $slider = 0;
        $fileName='';
        if($request->choose == "slider"){
            $slider = $request->slider_title;
        } else {
            $file = $request->file('image');
            if($file){
                $this->validate($request, [
                    'image' =>  'mimes:jpeg,jpg,png,svg|max:2048',
                ]);
                $fileName=time().$file->getClientOriginalName();
                $request->image->move(public_path('/assets/uploads/contents/'), $fileName);
            }
        }

        Contentpage::create([
            'page'=>$request->page,
            'title'=>$request->page_title,
            'page_content'=>$request->content,
            'seo_url'=>$seo_url,
            'seo_title'=>$request->seo_title,
            'seo_description'=>$request->seo_description,
            'seo_keywords'=>$request->seo_keywords,
            'banner_type'=>$request->choose,
            'banner'=>$fileName,
            'slider'=>$slider,
            'page_position'=>$page_position,
        ]);

        return redirect()->route('admin.contentpages')->with('success','Content page successfully added.');
    }

    private function create_slug($string)
    {
        $items = array("index", "create_slug", "show", "create", "store", "edit", "update", "destroy");
        $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        if (in_array($slug, $items)){
            $slug = $slug.time();
        }
        $content = Contentpage::where('seo_url', '=', $slug)->first();
        if($content){
            $slug = $slug.time();
        }
        return strtolower($slug);
    }

    public function show($id = null)
    {
        $contents = Contentpage::find($id);
        $sliders = array();
        if($contents->slider != '') {
            $sliders = Sliderimage::where('slider_id', $contents->slider)->get()->all();
        }
        return view('admin.contentpage.show',compact('contents','sliders'));
    }

    public function edit($id = null)
    {
        $contents = Contentpage::find($id);
        $sliders = Slider::all();
        return view('admin.contentpage.edit',compact('contents','sliders'));
    }

    public function update(Request $request, $id = null)
    {
        $contents = Contentpage::find($id);
        if($contents){
            $this->validate($request, [
                'page'          => 'required|unique:contentpages,page,'.$id,
                'page_title'    => 'required',
                'content'       => 'required',
                'seo_title'     => 'required',
                'seo_keywords'  => 'required',
                'chk_position'  => 'required',
            ]);

            $page_position = '';
            $page_title = $request->page_title;
            if(is_array($request->chk_position) && $request->chk_position != '') {
                $page_position = implode(",",$request->chk_position);
            }

            if($contents->title != $page_title){
                $seo_url = $this->create_slug($page_title);
            } else {
                $seo_url = $contents->seo_url;
            }

            $slider = 0;
            $fileName = '';
            if($request->choose=="slider"){
                $slider = $request->slider_title;
            } else {
                $file = $request->file('image');
                if($file){
                    $this->validate($request, [
                        'image' =>  'mimes:jpeg,jpg,png,svg|max:2048',
                    ]);

                    if($contents->banner_type == 'banner' && $contents->banner != ''){
                        $file_path = public_path('/assets/uploads/contents/').$contents->banner;
                        File::delete($file_path);
                    }
                    $fileName = time().$file->getClientOriginalName();
                    $request->image->move(public_path('/assets/uploads/contents/'), $fileName);
                }
            }

            if($request->choose == "banner"){
                $ContentPageArray['page'] = $request->page;
                $ContentPageArray['title'] = $request->page_title;
                $ContentPageArray['page_content'] = $request->content;
                $ContentPageArray['seo_url'] = $seo_url;
                $ContentPageArray['seo_title'] = $request->seo_title;
                $ContentPageArray['seo_description'] = $request->seo_description;
                $ContentPageArray['seo_keywords'] = $request->seo_keywords;
                $ContentPageArray['banner_type'] = $request->choose;
                $ContentPageArray['slider'] = 0;
                $ContentPageArray['page_position'] = $page_position;

                if($fileName != ''){
                    $ContentPageArray['banner'] = $fileName;
                }

                Contentpage::find($id)->update($ContentPageArray);
            } else if($request->choose == "slider"){
                Contentpage::find($id)->update([
                    'slider'=>$slider,
                    'banner_type'=>$request->choose,
                    'banner'=>'',
                    'page'=>$request->page,
                    'title'=>$request->page_title,
                    'page_content'=>$request->content,
                    'seo_url'=>$seo_url,
                    'seo_title'=>$request->seo_title,
                    'seo_description'=>$request->seo_description,
                    'seo_keywords'=>$request->seo_keywords,
                    'page_position'=>$page_position,
                ]);
            } else {
                if($contents->banner_type == 'banner' && $contents->banner != ''){
                    $file_path = public_path('/assets/uploads/contents/').$contents->banner;
                    File::delete($file_path);
                }

                Contentpage::find($id)->update([
                    'slider'=>0,
                    'banner_type'=>$request->choose,
                    'banner'=>'',
                    'page'=>$request->page,
                    'title'=>$request->page_title,
                    'page_content'=>$request->content,
                    'seo_url'=>$seo_url,
                    'seo_title'=>$request->seo_title,
                    'seo_description'=>$request->seo_description,
                    'seo_keywords'=>$request->seo_keywords,
                    'page_position'=>$page_position,
                ]);
            }

            return redirect()->route('admin.contentpages')->with('success','Content pages updated successfully.');
        } else {
            return redirect()->back()->withErrors('Update failed. Content details not found.');
        }
    }

    public function destroy($id = null)
    {
        $contents = Contentpage::find($id);
        if($contents->banner != '') {
            $file_path = public_path('/assets/uploads/contents/').$contents->banner;
            File::delete($file_path);
        }
        Contentpage::find($id)->delete();

        return redirect()->route('admin.contentpages')->with('success','Content page deleted successfully.');
    }

    public function remove_bannerimage(Request $request)
    {
        if($request->id != ''){
            $Contentpage = Contentpage::find($request->id);
            if($Contentpage){
                // dd($Contentpage);
                if($Contentpage->banner_type == 'banner' && $Contentpage->banner != ''){
                    $imagefile = public_path('/assets/uploads/contents/').$Contentpage->banner;
                    File::delete($imagefile);
                    Contentpage::find($request->id)->update(['banner'=>'']);

                    $returnArray['result'] = true;
                    $returnArray['message'] = 'Banner image removed successfully.';
                } else {
                    $returnArray['result'] = false;
                    $returnArray['message'] = 'Failed. Image not found.';
                }
            } else {
                $returnArray['result'] = false;
                $returnArray['message'] = 'Failed. Testimonial details not found.';
            }
        } else {
            $returnArray['result'] = false;
            $returnArray['message'] = 'Failed. Testimonial ID not found.';
        }
        return response()->json($returnArray);
    }

    public function homeCategory()
    {
        $Category_data = Category::get();
        $homecategories = HomeCategory::select('category_id')->get()->toArray();
        $parentCategories = Category::where('status','active')->where('parent_id',0)->get();

        $arr_selected = array();
        if($homecategories){
            foreach($homecategories as $selected){
                $arr_selected[] =  $selected['category_id'];
            }
        }
        return view('admin.contentpage.homecategory',compact('Category_data','arr_selected','parentCategories'));
    }

    public function store_homeCategory(Request $request)
    {
        HomeCategory::truncate();
        if(!empty($request->categories)){
            if(count($request->categories) <= 6){
                foreach($request->categories as $list){
                    HomeCategory::create([
                        'category_id'=>$list,
                    ]);
                }
                return redirect()->route('admin.home.categories')->with('success','Category choosed successfully.');
            } else {
                return redirect()->back()->with('error','You can choose only 4 categories. Exceed your limit.');
            }
        }
        return redirect()->route('admin.home.categories')->with('success','Category choosed successfully.');
    }

    public function offerlinksection(Request $request){
        $offerexist = OfferLinkSection::where('id','1')->exists();
                if($offerexist){
                    $offer_contents=OfferLinkSection::where('id', '1')->first();                   
                     
                }else{
                    OfferLinkSection::create([
                        'offer_content'=>null,
                        'offer_link'=>null,
                        'status'=>'inactive'
                        
                    ]);
                    $offer_contents=OfferLinkSection::where('id', '1')->first();                  
                    
                }
               
               return view('admin.contentpage.offerlinksection', [ 'offer_contents' => $offer_contents]);
    }

    public function offerlinkupdate(Request $request){

        $this->validate($request, [            
            'status'     => 'required',
        ]);

        $id=1;
        OfferLinkSection::find($id)->update([
            'offer_content'=>$request->offer_content,
            'offer_link'=>$request->offer_link,
            'status'=>$request->status
           
        ]);
                return redirect()->route('admin.home.offersection')->with('success','Offer link section updated successfully.');
    }

}
