<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotionbanner;
use App\Models\PromotionBannerImage;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $promotionbanners = Promotionbanner::select('promotionbanners.*');
        if ($request->has('filter_section') && $request->filter_section != '') {
            $promotionbanners->where('promotionbanners.section', $request->filter_section);
        }
        if ($request->has('filter_position') && $request->filter_position != '') {
            $promotionbanners->where('promotionbanners.position', $request->filter_position);
        }
        if ($request->has('filter_status') && $request->filter_status != '') {
            $promotionbanners->where('promotionbanners.status', $request->filter_status);
        }
        $promotionbanners = $promotionbanners->latest()->paginate(20);

        return view('admin.promotionsbanner.index', compact('promotionbanners'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function create()
    {
        return view('admin.promotionsbanner.create');
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'bannertitle' => 'required|unique:promotionbanners,title',
            'bannersection' => 'required',
            'bannerposition' => 'required|not_in:0',
            'bannertype' => 'required',
            'bannerimage.*' => 'image|mimes:jpeg,jpg,png,svg,webp',
            'bannerurl.*' => 'nullable|url',
        ], [
            'bannertitle.required' => 'The banner title field is required.',
            'bannertitle.unique' => 'The banner title is already exist.',
            'bannersection.required' => 'The banner section field is required.',
            'bannerposition.required' => 'The banner position field is required.',
            'bannerposition.not_in' => 'The banner position field is required.',
            'bannertype.required' => 'Please choose any position for the type.',
        ]);

        switch ($request->bannerposition) {
            case 'maintop':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=1040,min_height=230,max_width=2024,max_height=550',
                ]);
                break;
            case 'middle':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=506,min_height=218,max_width=810,max_height=340',
                ]);
                break;
            case 'footer':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=279,max_width=330,max_height=281',
                ]);
                break;
            case 'footer2':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=279,max_width=330,max_height=281',
                ]);
                break;
            case 'footer3':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=279,max_width=330,max_height=281',
                ]);
                break;
            case 'footer4':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=279,max_width=330,max_height=281',
                ]);
                break;
            case 'top':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=384,max_width=330,max_height=387',
                ]);
                break;
            case 'top2':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=418,max_width=330,max_height=420',
                ]);
                break;
            case 'top3':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=418,max_width=330,max_height=420',
                ]);
                break;
            case 'top4':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=418,max_width=330,max_height=420',
                ]);
                break;
            case 'bottom':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=438,max_width=330,max_height=442',
                ]);
                break;
            case 'bottom2':
                $this->validate($request, [
                    'bannerimage.*' => 'dimensions:min_width=328,min_height=306,max_width=330,max_height=310',
                ]);
                break;
        }

        $promotionbanner_id = Promotionbanner::create([
            'title' => $request->bannertitle,
            'section' => $request->bannersection,
            'position' => $request->bannerposition,
            'type' => $request->bannertype,
            'status' => 'active',
        ])->id;

        if (!empty($request->file('bannerimage'))) {
            foreach ($request->file('bannerimage') as $key => $bannerimage) {
                $filename = 'bann' . '_' . $request->bannersection . '_' . $request->bannerposition . time() . '_' . $key . '.' . $bannerimage->extension();
                $bannerimage->move(public_path('/assets/uploads/promotionbanner/'), $filename);

                PromotionBannerImage::create([
                    'promotionbanner_id' => $promotionbanner_id,
                    'image' => $filename,
                    'banner_url' => $request->bannerurl[$key],
                ]);
            }
        }
        return redirect()->route('admin.promotionbanner')->with('success', 'Promotion banner successfully added.');
    }

    public function show($id = null)
    {
        if ($id != null) {
            $promotionbannerdetails = Promotionbanner::find($id);
            if ($promotionbannerdetails) {
                $BannerImages = PromotionBannerImage::where('promotionbanner_id', $promotionbannerdetails->id)->get();

                return view('admin.promotionsbanner.show', compact('promotionbannerdetails', 'BannerImages'));
            } else {
                return redirect()->back()->withErrors('Something went wrong. Promotion banner details not found.');
            }
        } else {
            return redirect()->back()->withErrors('Something wrong with the url. Please try again.');
        }
    }

    public function edit($id = null)
    {
        if ($id != null) {
            $promotionbannerdetails = Promotionbanner::find($id);
            if ($promotionbannerdetails) {
                $BannerImages = PromotionBannerImage::where('promotionbanner_id', $promotionbannerdetails->id)->get();

                return view('admin.promotionsbanner.edit', compact('promotionbannerdetails', 'BannerImages'));
            } else {
                return redirect()->back()->withErrors('Something went wrong. Promotion banner details not found.');
            }
        } else {
            return redirect()->back()->withErrors('Something wrong with the url. Please try again.');
        }
    }

    public function update(Request $request, $id = null)
    {
        if ($id != null) {
            $this->validate($request, [
                'bannertitle' => 'required|unique:promotionbanners,title,' . $id,
            ], [
                'bannertitle.required' => 'The banner title field is required.',
                'bannertitle.unique' => 'The banner title is already exist.',
            ]);
            $promotionbannerdetails = Promotionbanner::find($id);
            if ($promotionbannerdetails) {
                switch ($promotionbannerdetails->position) {
                    case 'maintop':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=1040,min_height=230,max_width=2024,max_height=550',
                        ]);
                        break;
                    case 'middle':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=506,min_height=218,max_width=810,max_height=340',
                        ]);
                        break;
                    case 'footer':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=328,min_height=279,max_width=330,max_height=281',
                        ]);
                        break;
                    case 'footer2':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=328,min_height=279,max_width=330,max_height=281',
                        ]);
                        break;
                    case 'footer3':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=328,min_height=279,max_width=330,max_height=281',
                        ]);
                        break;
                    case 'footer4':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=328,min_height=279,max_width=330,max_height=281',
                        ]);
                        break;
                    case 'top':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=328,min_height=384,max_width=330,max_height=387',
                        ]);
                        break;
                    case 'top2':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=328,min_height=350,max_width=330,max_height=420',
                        ]);
                        break;
                    case 'bottom':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=328,min_height=438,max_width=330,max_height=442',
                        ]);
                        break;
                    case 'bottom2':
                        $this->validate($request, [
                            'bannerimage.*' => 'dimensions:min_width=328,min_height=306,max_width=330,max_height=310',
                        ]);
                        break;
                }

                Promotionbanner::find($promotionbannerdetails->id)->update([
                    'title' => $request->bannertitle,
                    'status' => $request->status,
                ]);

                $BannerImages = PromotionBannerImage::where('promotionbanner_id', $promotionbannerdetails->id)->exists();

                if (($promotionbannerdetails->type == 'plain' && $BannerImages === false) || $promotionbannerdetails->type == 'slider') {
                    if (!empty($request->file('bannerimage'))) {
                        foreach ($request->file('bannerimage') as $key => $bannerimage) {
                            $filename = 'bann' . '_' . $promotionbannerdetails->section . '_' . $promotionbannerdetails->position . time() . '_' . $key . '.' . $bannerimage->extension();
                            $bannerimage->move(public_path('/assets/uploads/promotionbanner/'), $filename);

                            PromotionBannerImage::create([
                                'promotionbanner_id' => $promotionbannerdetails->id,
                                'image' => $filename,
                                'banner_url' => $request->bannerurl[$key],
                            ]);
                        }
                    }
                }

                return redirect()->back()->with('success', 'Promotion banner successfully updated.');
            } else {
                return redirect()->back()->withErrors('Something went wrong. Promotion banner details not found.');
            }
        } else {
            return redirect()->back()->withErrors('Something wrong with the url. Please try again.');
        }
    }

    public function update_bannerurl(Request $request)
    {
        if ($request->id != null && $request->bannerId != null) {
            $validate = Validator::make($request->all(), [
                'urlValue' => 'nullable|url',
            ], [
                'urlValue.url' => 'Please input valid url',
            ]);
            if ($validate->fails()) {
                return response()->json(['result' => false, 'message' => $validate->errors()->first()]);
            } else {
                $promotionbannerdetails = Promotionbanner::find($request->bannerId);
                if ($promotionbannerdetails) {
                    $BannerImage = PromotionBannerImage::where('id', $request->id)->where('promotionbanner_id', $request->bannerId)->first();
                    if ($BannerImage) {
                        PromotionBannerImage::where('id', $request->id)->where('promotionbanner_id', $request->bannerId)->update(['banner_url' => $request->urlValue]);
                        $returnArray['result'] = true;
                        $returnArray['message'] = 'URL updated successfully.';
                    } else {
                        $returnArray['result'] = false;
                        $returnArray['message'] = 'Failed: Promotion banner image not found.';
                    }
                } else {
                    $returnArray['result'] = false;
                    $returnArray['message'] = 'Failed: Promotion details not found.';
                }
            }
        } else {
            $returnArray['result'] = false;
            $returnArray['message'] = 'Failed: Image id or banner id not found.';
        }
        return response()->json($returnArray);
    }

    public function remove_bannerimage(Request $request)
    {
        if ($request->id != null && $request->bannerid != null) {
            $promotionbannerdetails = Promotionbanner::find($request->bannerid);
            if ($promotionbannerdetails) {
                $BannerImage = PromotionBannerImage::where('id', $request->id)->where('promotionbanner_id', $request->bannerid)->first();
                if ($BannerImage && $BannerImage->image != '') {
                    $image_path = public_path('assets/uploads/promotionbanner/') . $BannerImage->image;
                    File::delete($image_path);
                    PromotionBannerImage::where('id', $request->id)->where('promotionbanner_id', $request->bannerid)->delete();
                    $returnArray['result'] = 'success';
                    $returnArray['type'] = $promotionbannerdetails->type;
                    $returnArray['message'] = 'Image has been removed.';
                } else {
                    $returnArray['result'] = 'failed';
                    $returnArray['message'] = 'Failed: Promotion banner image not found.';
                }
            } else {
                $returnArray['result'] = 'failed';
                $returnArray['message'] = 'Failed: Promotion details not found.';
            }
        } else {
            $returnArray['result'] = 'failed';
            $returnArray['message'] = 'Failed: Image id or banner id not found.';
        }
        return response()->json($returnArray);
    }

    public function changestatus(Request $request)
    {
        $returnarray['result'] = 'failed';
        if (!empty($request->id) && !empty($request->status)) {
            $Promotionbannerdetails = Promotionbanner::find($request->id);
            if ($Promotionbannerdetails) {
                Promotionbanner::find($request->id)->update([
                    'status' => $request->status,
                ]);
                $returnarray['result'] = 'success';
                $returnarray['message'] = 'Promotion banner status updated.';
            } else {
                $returnarray['result'] = 'failed';
                $returnarray['message'] = 'Promotion banner details not found.';
            }
        } else {
            $returnarray['result'] = 'failed';
            $returnarray['message'] = 'Status not updated. Promotion banner id or status is missed.';
        }
        return response()->json($returnarray);
    }

    public function destroy($id = null)
    {
        $Promotionbannerdetails = Promotionbanner::find($id);
        if ($Promotionbannerdetails) {
            $bannerImages = PromotionBannerImage::where('promotionbanner_id', $id)->get();
            if ($bannerImages) {
                Promotionbanner::find($id)->delete();

                foreach ($bannerImages as $value) {
                    if ($value->image != '') {
                        $image_path = public_path('/assets/uploads/promotionbanner/') . $value->image;
                        File::delete($image_path);
                    }
                }

                PromotionBannerImage::where('promotionbanner_id', $id)->delete();

                return redirect()->route('admin.promotionbanner')->with('success', 'Promotion banner deleted successfully.');
            } else {
                return redirect()->back()->withErrors('Delete failed 2. Promotion banner details not found.');
            }
        } else {
            return redirect()->back()->withErrors('Delete failed. Promotion banner details not found.');
        }
    }

}
