<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Permission;
use App\Models\RolePermission;

class PermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $permissions = Permission::latest()->paginate(30);
        return view('admin.permission.index',compact('permissions'))->with('i', ($request->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        return view('admin.permission.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' =>  'required|unique:permissions,name',
            'slug' =>  'required|unique:permissions,slug',
        ]);

        Permission::create([
            'name'=>$request->name,
            'slug'=>$request->slug,
        ]);
        return redirect()->route('admin.permissions')->with('success','Permission created successfully');
    }

    public function edit($id = null)
    {
        if($id != null){
            $permission = Permission::find($id);
            if($permission){
                return view('admin.permission.edit',compact('permission'));
            } else {
                return redirect()->back()->withErrors('Sorry... Permission not found.');
            }
        } else {
            return redirect()->back()->withErrors('Something went wrong. Please check your url.');
        }
    }

    public function update(Request $request, $id = null)
    {
        $this->validate($request, [
            'name' =>  'required|unique:permissions,name,'.$id,
            'slug' =>  'required|unique:permissions,slug,'.$id,
        ]);
        Permission::find($id)->update(['name'=>$request->name,'slug'=>$request->slug]);

        return redirect()->route('admin.permissions')->with('success','Permission updated successfully');
    }

    public function destroy($id = null)
    {
        if($id != null){
            if(!RolePermission::where('permission_id', $id)->exists()){
                Permission::find($id)->delete();
                return redirect()->route('admin.permissions')->with('success','Permission deleted successfully');
            } else {
                return redirect()->back()->withErrors('Sorry... You cannot delete this permission. This Permission is allocated for role.');
            }
        } else {
            return redirect()->back()->withErrors('Delete failed. Something went wrong.');
        }
    }

}
