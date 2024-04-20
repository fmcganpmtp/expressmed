<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Auth;

use App\Models\Career;
use App\Models\Career_application;
use App\Models\Generalsetting;

class CareersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
    	$careers = Career::latest()->paginate(10);
        return view('admin.careers.index',compact('careers'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
         return view('admin.careers.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'job_title' =>  'required|unique:careers,job_title',
            'description' =>  'required',
            'skills' =>  'required',
            'vacancies' =>  'required|numeric|gt:0',
        ]);

        Career::create([
            'job_title'=>$request->job_title,
            'description'=>$request->description,
            'skills'=>$request->skills,
            'no_of_vaccancies'=>$request->vacancies,
            'status'=>'active',
        ]);

        return redirect()->route('admin.careers')->with('success','Careers added successfully.');
    }

    public function show($id = null)
    {
        $careers = Career::find($id);
        $Applicants = array();
        if($careers){
            $Applicants = Career_application::where('career_id', $id)->latest()->paginate(20);

            return view('admin.careers.show',compact('careers', 'Applicants'));
        } else {
            return redirect()->back()->withErrors('Sorry... Career details not found.');
        }

    }

    public function edit($id = null)
    {
        $careers = Career::find($id);
        if($careers){
            return view('admin.careers.edit',compact('careers'));
        } else {
            return redirect()->back()->withErrors('Sorry... Career details not found.');
        }
    }

    public function update(Request $request, $id = null)
    {
        $this->validate($request, [
            'job_title' =>  'required|unique:careers,job_title,'.$id,
            'description' =>  'required',
            'skills' =>  'required',
            'vacancies' =>  'required|numeric|gt:0',
        ]);
        Career::find($id)->update([
            'job_title'=>$request->job_title,
            'description'=>$request->description,
            'skills'=>$request->skills,
            'no_of_vaccancies'=>$request->vacancies,
            'status'=>$request->status
        ]);

        return redirect()->route('admin.careers')->with('success','Career data updated successfully.');
    }

    public function destroy($id = null)
    {
        Career::find($id)->delete();
        return redirect()->route('admin.careers')->with('success','Career data deleted successfully.');
    }

    public function update_status(Request $request, $course_id = null)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];

        if (Auth::guard('admin')->user()) {
            $user_id = Auth::guard('admin')->user()->id;

            $id = $request->id;
            $status = $request->status;
            if($id != '' && $status != ''){
                Career::find($id)->update([
                    'status' => $status,
                ]);

                $message = "Successfully updated status";
                $ajax_status = 'success';
            } else {
                $message = "Unable to proceed";
                $ajax_status = 'failed';
            }
        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status'=>$ajax_status, 'message' =>$message );
        return response()->json($return_array);
    }

    public function send_message(Request $request, $id)
    {
        if($id != null){
            $Applicants = Career_application::join('careers', 'careers.id', 'career_applications.career_id')
                            ->select('career_applications.*', 'careers.job_title')
                            ->where('career_applications.id', $id)
                            ->first();

            if($Applicants){
                if($request->subject != '' && $request->message != ''){

                    $settings = Generalsetting::where('item', '=', 'notification_email')->first();
                    if($settings){

                        Mail::send('email.mail_careerApplicants',
                        array(
                            'applicantname' => $Applicants->applicant_name,
                            'subject' => $request->subject,
                            'message_content' => $request->message,
                        ), function($message) use ($request, $settings, $Applicants)
                        {
                            $message->from($settings->value,'MedCliq');
                            $message->to($Applicants->applicant_email);
                            $message->subject('Job Application response of '. $Applicants->job_title);
                        });

                        return redirect()->back()->with('success', 'Your message successfully send.');
                    }
                } else {
                    return redirect()->back()->with('success', 'Please enter any message.');
                }
            } else {
                return redirect()->back()->with('success', 'Something went wrong. Data not found.');
            }
        } else {
            return redirect()->back()->with('success', 'Error found');
        }
    }
}
