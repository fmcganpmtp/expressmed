<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;

use App\Models\Testimonial;

class TestimonialsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $testimonials = Testimonial::latest()->paginate(30);

        return view('admin.testimonials.index',compact('testimonials'))->with('i', ($request->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' =>  'required',
            'company' => 'required',
            'title' => 'required'
        ]);

        $fileName = '';
        $file = $request->file('profile_pic');

        if($file) {
            $this->validate($request, [
                'profile_pic' =>  'required|mimes:jpeg,jpg,png,svg|max:2048',
            ]);

            $fileName = time().'.'.$request->profile_pic->extension();
            $request->profile_pic->move(public_path('/assets/uploads/testimonials/'), $fileName);
        }

        Testimonial::create([
            'profile_pic'=>$fileName,
            'name'=>$request->name,
            'company_name'=>$request->company,
            'title'=>$request->title,
            'comments'=>$request->comments,
        ]);

        return redirect()->route('admin.testimonials')->with('success','Testimonial created successfully');
    }

    public function edit($id)
    {
        $testimonials = Testimonial::find($id);
        if($testimonials){
            return view('admin.testimonials.edit',compact('testimonials'));
        } else {
            return redirect()->back()->withErrors('Testimonial not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $Testimonials = Testimonial::find($id);

        if($Testimonials){
            $this->validate($request, [
                'name' =>  'required',
                'company' => 'required',
                'title' => 'required'
            ]);

            $UpdateArray['name'] = $request->name;
            $UpdateArray['company_name'] = $request->company;
            $UpdateArray['title'] = $request->title;
            $UpdateArray['comments'] = $request->comments;

            $file = $request->file('profile_pic');
            if($file){
                $this->validate($request, [
                    'profile_pic' =>  'required|mimes:jpeg,jpg,png,svg|max:2048',
                ]);

                if($Testimonials->profile_pic != ''){
                    $image_path = public_path('/assets/uploads/testimonials/').'/'.$Testimonials->profile_pic;
                    File::delete($image_path);
                }

                $file = $request->file('profile_pic');

                $fileName = time().'.'.$request->profile_pic->extension();

                $request->profile_pic->move(public_path('/assets/uploads/testimonials/'), $fileName);

                $UpdateArray['profile_pic'] = $fileName;
            }

            Testimonial::find($id)->update($UpdateArray);

            return redirect()->route('admin.testimonials')->with('success','Testimonial updated successfully');
        } else {
            return redirect()->back()->withErrors('Error: Not updated. Testimonial details not found.');
        }

    }

    public function destroy($id)
    {
        $Testimonials = Testimonial::find($id);
        if($Testimonials){
            if($Testimonials->profile_pic != ''){
                $image_path = public_path('/assets/uploads/testimonials/').'/'.$Testimonials->profile_pic;
                File::delete($image_path);
            }
            Testimonial::find($id)->delete();

            return redirect()->route('admin.testimonials')->with('success','Testimonial deleted successfully.');
        } else {
            return redirect()->back()->withErrors('Error: Not updated. Testimonial details not found.');
        }
    }

    public function remove_testimonialimage(Request $request)
    {
        if($request->id != ''){
            $Testimonial = Testimonial::find($request->id);
            if($Testimonial){
                if($Testimonial->profile_pic != ''){
                    $imagefile = public_path('/assets/uploads/testimonials/').$Testimonial->profile_pic;
                    File::delete($imagefile);
                    Testimonial::find($request->id)->update(['profile_pic'=>'']);

                    $returnArray['result'] = true;
                    $returnArray['message'] = 'Testimonial image removed successfully.';
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
}
