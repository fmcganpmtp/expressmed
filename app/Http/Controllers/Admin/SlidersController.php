<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;

use App\Models\Slider;
use App\Models\Sliderimage;

class SlidersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
    	$sliders = Slider::latest()->paginate(30);
        $slider_image_data = Sliderimage::all();

        return view('admin.sliders.index',compact('sliders','slider_image_data'))->with('i', ($request->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
          'slider_title' =>  'required',
          'image.0' => 'required|mimes:jpeg,jpg,png,svg|max:2048',
        ]);

        $data1=Slider::create([
            'name'=>$request->slider_title,
        ]);

        $last_inserted_id = $data1->id;
        $counter = 0;

        if($request->file('image')) {
            foreach($request->file('image') as $key=>$image_file){
                $fileName = time().$image_file->getClientOriginalName();
                $image_file->move(public_path('/assets/uploads/sliders/'), $fileName);

                Sliderimage::create([
                    'slider_id'=>$last_inserted_id,
                    'image'=>$fileName,
                    'title' =>$request->title_on_image[$counter],
                    'description' =>$request->description[$counter],
                    'target' =>$request->image_target[$counter],
                ]);
                $counter++;
            }
        }

        return redirect()->route('admin.sliders')->with('success','Sliders added successfully');
    }

    public function show( $id = null)
    {
        $slider_data = Slider::find($id);
        $slider_images = Sliderimage::where('slider_id',$id)->get()->all();
        return view('admin.sliders.show',compact('slider_data','slider_images'));
    }

    public function edit($id = null)
    {
        $slider_data= Slider::find($id);
        $slider_images = Sliderimage::where('slider_id',$id)->get()->all();
        return view('admin.sliders.edit',compact('slider_data','slider_images'));
    }

    public function update(Request $request, $id = null)
    {
        $this->validate($request, [
            'slider_title' =>  'required',
        ]);

        $data1=Slider::find($id)->update([
          'name'=>$request->slider_title,
        ]);

        $file = $request->file('image');
        $counter=0;
        if($file){
            foreach($request->file('image') as $image_file){
                $fileName=time().$image_file->getClientOriginalName();
                $image_file->move(public_path('/assets/uploads/sliders/'), $fileName);
                Sliderimage::create([
                    'slider_id'=>$id,
                    'image'=>$fileName,
                    'title' =>$request->title_on_image[$counter],
                    'description' =>$request->description[$counter],
                    'target' =>$request->image_target[$counter],
                ]);
                $counter++;
            }

        }
        $old_image_id = $request->old_image_id;
        $counter=0;
        if($old_image_id){
            foreach($request->old_image_id as $image_id){
                Sliderimage::find($image_id)->update([
                    'title' =>$request->old_title_on_image[$counter],
                    'description' =>$request->old_description[$counter],
                    'target' =>$request->old_image_target[$counter],
                ]);
                $counter++;
            }
        }
        return redirect()->route('admin.sliders')->with('success','Sliders updated successfully');
    }

    public function destroy($id = null)
    {
        $gallery = Sliderimage::where('slider_id', $id)->get()->all();
        foreach($gallery as $type){
            $file_path = public_path('/assets/uploads/sliders/').$type->image;
            File::delete($file_path);
        }
        Slider::find($id)->delete();
        Sliderimage::where('slider_id', $id)->delete();
        return redirect()->route('admin.sliders')->with('success','Slider deleted successfully');
    }

    public function removeMedia(Request $request)
    {
        $gallery= Sliderimage::find($request->id);
        if(!empty($gallery)){
            $file_path = public_path('/assets/uploads/sliders/').$gallery->image;
            File::delete($file_path);
        }
        Sliderimage::find($request->id)->delete();
        $message = "Successfully deleted";
        $ajax_status = 'success';
        $return_array = array('ajax_status'=>$ajax_status,'message' =>$message );
        return response()->json($return_array);
    }

}
