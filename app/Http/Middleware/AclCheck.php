<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class AclCheck
{
    public function handle($request, Closure $next, $item = null)
    {
        if(Auth::guard('admin')->user()->id){
            if(Auth::guard('admin')->user()->is_super){
                return $next($request);
            } else {
                $role_id = Auth::guard('admin')->user()->role_id;
                $userRole = \App\Models\Permission::join('role_permissions', 'role_permissions.permission_id', '=', 'permissions.id')->where(['role_permissions.role_id'=> $role_id,'permissions.slug'=> $item])->count();
                if($userRole > 0) {
                    return $next($request);
                } else {
                    return redirect('/access_restricted');
                }
            }
        } else {
            return redirect('/access_restricted');
        }

        return $request->ajax ? response('Unauthorized.', 401) : redirect('/login');
    }
}
