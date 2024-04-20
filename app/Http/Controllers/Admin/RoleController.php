<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\Admin;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $roles = Role::latest()->paginate(30);
        return view('admin.role.index',compact('roles'))->with('i', ($request->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        $permissions = Permission::get()->all();
        return view('admin.role.create',compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' =>  'required|unique:roles,name',
            'permission' => 'required',
            'permission.*' => 'required'
        ]);

        if($request->permission != ''){
            $role_id = Role::create([
                            'name'=>$request->name
                        ])->id;

            foreach($request->permission as $items){
                RolePermission::create([
                    'role_id'=>$role_id,
                    'permission_id'=>$items,
                ]);
            }
            return redirect()->route('admin.roles')->with('success','Role created successfully');
        } else {
            return redirect()->back()->withErrors('Failed: Please choose any one of permissions.');
        }
    }

    public function edit($id = null)
    {
        if($id != null){
            $role = Role::find($id);
            if($role){
                $gid = $id;

                $permissions = $query = Permission::select('permissions.*','permissions.id as pid', 'role_permissions.permission_id as rid')
                    ->leftJoin('role_permissions', function($leftJoin) use($gid)
                    {
                        $leftJoin->on('role_permissions.permission_id', '=', 'permissions.id')
                            ->where('role_permissions.role_id', '=', $gid);
                    })->distinct()->get()->all();

                return view('admin.role.edit',compact('permissions','role'));
            } else {
                return redirect()->back()->withErrors('Sorry... Role not found.');
            }
        } else {
            return redirect()->back()->withErrors('Something went wrong. Please check your url.');
        }
    }

    public function update(Request $request, $id = null)
    {
        $this->validate($request, [
            'name' =>  'required|unique:roles,name,'.$id,
            'permission' => 'required',
            'permission.*' => 'required'
        ]);

        if($request->permission != ''){
            Role::find($id)->update(['name'=>$request->name]);

            RolePermission::where('role_id', '=', $id)->delete();

            foreach($request->permission  as $items){
                RolePermission::create([
                    'role_id'=>$id,
                    'permission_id'=>$items,
                ]);
            }
            return redirect()->route('admin.roles')->with('success','Role updated successfully');
        } else {
            return redirect()->back()->withErrors('Failed: Please choose any one of permissions.');
        }

    }

    public function destroy($id = null)
    {
        if($id != null){
            if(!Admin::where('role_id', $id)->exists()){
                Role::find($id)->delete();
                RolePermission::where('role_id', $id)->delete();

                return redirect()->route('admin.roles')->with('success','Role deleted successfully');
            } else {
                return redirect()->back()->withErrors('Sorry... You cannot delete this role. This Role is allocated for admin.');
            }
        } else {
            return redirect()->back()->withErrors('Delete failed. Something went wrong.');
        }
    }

}
