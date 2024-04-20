<?php

namespace App\Http\Controllers\Admin;
use App\Models\Store;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Store::latest()->paginate(30);
        //dd($data);
        return view('admin.store.index', compact('data'))
            ->with('i', (request()->input('page', 1) - 1) * 30);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:stores,name',
            'address' => 'required',
            'location' => 'required',
            'contact_number' => 'nullable|regex:/^[-0-9\s\+]+$/',
        ]);

        $form_data = array(
            'name' => $request->name,
            'location' => $request->location,
            'address' => $request->address,
            'contact_number'=>$request->contact_number,
            'map_location_code' => $request->map_location,
        );
        $store_id = Store::create($form_data)->id;

        return redirect()->route('admin.stores')->with('success', 'Store Added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Store::findOrFail($id);
        return view('admin.store.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Store::findOrFail($id);
        return view('admin.store.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|unique:stores,name,'.$id,
            'address' => 'required',
            'location' => 'required',
            'contact_number' => 'nullable|regex:/^[-0-9\s\+]+$/'

        ]);
        $form_data = array(
            'name' => $request->name,
            'location' => $request->location,
            'address' => $request->address,
            'contact_number'=>$request->contact_number,
            'map_location_code' => $request->map_location,
        );

        Store::whereId($id)->update($form_data);

        return redirect()->route('admin.stores')->with('success', 'Store Data is successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Store::findOrFail($id);
        Store::find($id)->delete();
        return redirect()->route('admin.stores')->with('success', 'Store Data is successfully deleted');    }
}
