<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;


class TeamsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request){
    	$teams = Team::latest()->paginate(30);

        return view('admin.teams.index',compact('teams'))

            ->with('i', ($request->input('page', 1) - 1) * 30);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('admin.teams.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'profile_pic' =>  'nullable|mimes:jpeg,png,svg|max:2048',
            'name' =>  'required',
            'position' =>  'required',

        ]);
        $fileName = '';
        $file = $request->file('profile_pic');
        if($file){
            $fileName = time().'.'.$request->profile_pic->extension();
            $request->profile_pic->move(public_path('/assets/uploads/teams/'), $fileName);
        }
        Team::create([

            'image'=>$fileName,
            'name'=>$request->name,
            'position'=>$request->position,
            'description'=>$request->description,
            'instagram'=>$request->instagram,
            'facebook'=>$request->facebook,
            'twitter'=>$request->twitter,
            'linkedin'=>$request->linkedin,

            ]);

        return redirect()->route('teams.index')

                        ->with('success','Team member added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show( $id = null)
    {
         //return view('admin.index');

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        $teams= Team::find($id);

        return view('admin.teams.edit',compact('teams'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {   //dd($request->all());
         $file = $request->file('profile_pic');
        	if($file){
            $this->validate($request, [

                'profile_pic' =>  'nullable|mimes:jpeg,png,svg|max:2048',
                'name' =>  'required',
	            'position' =>  'required',

            ]);
            $file = $request->file('profile_pic');

            $fileName = time().'.'.$request->profile_pic->extension();

            $request->profile_pic->move(public_path('/assets/uploads/teams/'), $fileName);

            Team::find($id)->update([

                'image'=>$fileName,
                'name'=>$request->name,
                'position'=>$request->position,
                'description'=>$request->description,
                'instagram'=>$request->instagram,
                'facebook'=>$request->facebook,
                'twitter'=>$request->twitter,
                'linkedin'=>$request->linkedIn,
                ]);
        }else{

        	$this->validate($request, [

                'name' =>  'required',
	            'position' =>  'required',

            ]);
            Team::find($id)->update([

                'name'=>$request->name,
                'position'=>$request->position,
                'description'=>$request->description,
                'instagram'=>$request->instagram,
                'facebook'=>$request->facebook,
                'twitter'=>$request->twitter,
                'linkedin'=>$request->linkedIn,


                ]);
        }

        return redirect()->route('teams.index')

                        ->with('success','Teams updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = null)
    {
          Team::find($id)->delete();

        return redirect()->route('teams.index')
                        ->with('success','Team member deleted successfully');
    }
	public function orderchange(Request $request){
		$id = $request->team_id;
		$order = $request->order_value;
		if($id>0){
			Team::find($id)->update([
                'displayorder'=>$order 
           ]);
		   return ['result' => true, 'errorMsg' => ''];
		}else{
			return ['result' => false, 'errorMsg' => 'Team not found'];
		}			
	}
}
