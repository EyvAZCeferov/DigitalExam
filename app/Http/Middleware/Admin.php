<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admins')->check() ) {
            foreach (config('permissions') as $permission) {
                Gate::define($permission, fn() => auth('admins')->check()
                    && auth('admins')->user()->hasPermissionFor($permission));
            }
            return $next($request);
        }else{
            $admin_id=session()->get("admin_id")??$request->admin_id;
            if(isset($admin_id) && !empty($admin_id)){
                Auth::guard('admins')->loginUsingId($admin_id);
                return $next($request);
            }else{
                return redirect()->route('admin.login');
            }
        }
    }
}
