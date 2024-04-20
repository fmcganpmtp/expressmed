<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use Validator;
use App\Models\News;
use App\Models\Newsgallery;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
    	$news = News::latest()->paginate(10);

        return view('admin.news.index',compact('news'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' =>  'required|unique:news,title',
            'description' =>  'required',
        ]);

        $file = $request->file('image');
        $fileName = '';

        if($file){
            $this->validate($request, [
                'image' =>  'required|mimes:jpeg,jpg,png,svg|max:5048',
            ]);
            $fileName = time().'.'.$request->image->extension();

            $request->image->move(public_path('/assets/uploads/news/'), $fileName);
        }

        $id = News::create([
            'image'=>$fileName,
            'title'=>$request->title,
            'description'=>$request->description,
        ])->id;

        $counter = 0;
        if(!empty(array_filter($request->type))){
            foreach($request->type as $type){
                $fileName = '';
                if($type == 'youtube'){
                    $val= Validator::make($request->all(), [
                        'txt_url' =>  'nullable|array',
                        'txt_url.'.$counter =>  'nullable|url',
                    ]);
                    ($val->fails())?News::find($id)->delete():'';
                    $this->validate($request, [
                        'txt_url' =>  'nullable|array',
                        'txt_url.'.$counter =>  'nullable|url',
                    ]);
                    if($request->txt_url[$counter] != ''){
                        $youtubeEmbed = $this->YoutubeEmbed($request->txt_url[$counter]);
                        Newsgallery::create([
                            'type'=>$type,
                            'news_id'=>$id,
                            'url'=>$youtubeEmbed,
                        ]);
                    }
                } else {
                    if($type == 'video'){
                        $val= Validator::make($request->all(), [
                            'file_gallery' =>  'nullable|array',
                            'file_gallery.'.$counter =>  'nullable|mimes:mp4|max:50240',
                        ]);
                        ($val->fails())?News::find($id)->delete():'';

                       $this->validate($request, [
                            'file_gallery' =>  'nullable|array',
                            'file_gallery.'.$counter =>  'nullable|mimes:mp4|max:50240',
                        ]);
                    } elseif($type == 'image'){
                        $val=Validator::make($request->all(), [
                            'file_gallery' =>  'nullable|array',
                            'file_gallery.'.$counter =>  'nullable|mimes:jpeg,jpg,png,svg|max:2048',
                        ]);
                        ($val->fails())?News::find($id)->delete():'';
                       $this->validate($request, [
                            'file_gallery' =>  'nullable|array',
                            'file_gallery.'.$counter =>  'nullable|mimes:jpeg,jpg,png,svg|max:2048',
                        ]);
                    }
                    ($val->fails())?News::find($id)->delete():'';

                    if(isset($request->file('file_gallery')[$counter])){
                        $file = $request->file('file_gallery')[$counter];
                        if($file){
                            $fileName = time().$counter.'.'.$request->file_gallery[$counter]->extension();
                            $request->file_gallery[$counter]->move(public_path('/assets/uploads/news/gallery'), $fileName);
                            Newsgallery::create([
                                'type'=>$type,
                                'news_id'=>$id,
                                'url'=>$fileName,
                            ]);
                        }
                    }
                }
                $counter++;
            }
        }
        return redirect()->route('admin.news')->with('success','News added successfully');
    }

    private function YoutubeEmbed($url)
    {
        if(strlen($url) > 11){
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)){
                return $match[1];
            } else {
                return false;
            }
        } else {
            return $url;
        }
    }

    public function show( $id = null)
    {
        $news= News::find($id);
        $media = Newsgallery::where('news_id', '=' , $id)->get()->all();
        return view('admin.news.show',compact('news','media'));
    }

    public function edit($id = null)
    {
        $news= News::find($id);
        $media = Newsgallery::where('news_id', '=' , $id)->get()->all();
        return view('admin.news.edit',compact('news','media'));
    }

    public function update(Request $request, $id = null)
    {
        $file = $request->file('image');
        if($file) {
            // Delete existing Image coDe--
            $news = News::find($id);
            if(!empty($news) && $news->image != ''){
                $file_path = public_path('/assets/uploads/news/').$news->image;
                File::delete($file_path);
            }

            $this->validate($request, [
                'image' =>  'required|mimes:jpeg,jpg,png,svg|max:2048',
                'title' =>  'required|unique:news,title,'.$id,
	            'description' =>  'required',
            ]);

            $fileName = time().'.'.$request->image->extension();

            $request->image->move(public_path('/assets/uploads/news/'), $fileName);

            News::find($id)->update([
            	'title'=>$request->title,
                'image'=>$fileName,
				'description'=>$request->description,
            ]);
        } else {
        	$this->validate($request, [
	            'title' =>  'required|unique:news,title,'.$id,
	            'description' =>  'required',
            ]);
            News::find($id)->update([
                'title'=>$request->title,
                'description'=>$request->description,
            ]);
        }
        $counter = 0;
        if(!empty($request->type)) {
            foreach($request->type as $type) {
                $fileName = '';
                if($type == 'youtube'){
                    $this->validate($request, [
                        'txt_url' =>  'nullable|array',
                        'txt_url.'.$counter =>  'nullable|url',
                    ]);
                    if($request->txt_url[$counter]!=''){
                        $youtubeEmbed = $this->YoutubeEmbed($request->txt_url[$counter]);
                        Newsgallery::create([
                            'type'=>$type,
                            'news_id'=>$id,
                            'url'=>$youtubeEmbed,
                        ]);
                    }
                } else {
                    if($type == 'video'){
                        $this->validate($request, [
                            'file_gallery' =>  'nullable|array',
                            'file_gallery.'.$counter =>  'nullable|mimes:mp4|max:50240',
                        ]);
                    } elseif($type == 'image'){
                        $this->validate($request, [
                            'file_gallery' =>  'nullable|array',
                            'file_gallery.'.$counter =>  'nullable|mimes:jpeg,jpg,png,svg|max:2048',
                        ]);
                    }

                    if(isset($request->file('file_gallery')[$counter])){
                        $file = $request->file('file_gallery')[$counter];
                        if($file){
                            $fileName = time().$counter.'.'.$request->file_gallery[$counter]->extension();
                            $request->file_gallery[$counter]->move(public_path('/assets/uploads/news/gallery'), $fileName);
                            Newsgallery::create([
                                'type'=>$type,
                                'news_id'=>$id,
                                'url'=>$fileName,
                            ]);
                        }
                    }
                }
                $counter++;
            }
        }

        return redirect()->route('admin.news')->with('success','Data updated successfully');
    }

    public function destroy($id = null)
    {
        $news= News::find($id);
        if(!empty($news)&&$news->image!=''){
            $file_path = public_path('/assets/uploads/news/').$news->image;
            File::delete($file_path);
        }
        $gallery = Newsgallery::where('news_id', '=' , $id)->get()->all();
        foreach($gallery as $type){
           if($type->type != 'youtube'){
                $file_path = public_path('/assets/uploads/news/gallery/').$type->url;
                File::delete($file_path);
           }
        }
        News::find($id)->delete();
        Newsgallery::where('news_id', $id)->delete();

        return redirect()->route('admin.news')->with('success','News deleted successfully');
    }

    public function ajaxtiny(Request $request)
    {
        $file_path = app_path().'/images/news/';

        $file = $request->file('file');

        $fileName = time().'.'.$request->file->extension();

        $request->file->move(public_path('/assets/uploads/tiny/'), $fileName);
        $data['location'] = '../../assets/uploads/tiny/'.$fileName;
        echo json_encode($data);
    }

    public function removeMedia(Request $request)
    {
        $gallery = Newsgallery::find($request->id);
        if(!empty($gallery) && $gallery->type != 'youtube'){
            $file_path = public_path('/assets/uploads/news/gallery/').$gallery->url;
            File::delete($file_path);
        }
        Newsgallery::find($request->id)->delete();
        $message = "Successfully deleted";
        $ajax_status = 'success';
        $return_array = array('ajax_status'=>$ajax_status,'message' =>$message );
        return response()->json($return_array);
    }

}
