<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function profile () {
        return view('backend.pages.profile');
    }

    public function save (Request $request) {
        $this->validate($request, [
            'email' => 'required|email|unique:admins,email,' . auth('admins')->user()->id,
            'password' => 'nullable|sometimes|min:6',
            'name' => 'string|required',
        ]);

        $admin = auth('admins')->user();

        $admin->name = $request->name;
        $admin->email = $request->email;
        if (!empty($request->password)) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->back()->with(['success' => 'Uğurla!']);
    }

    public function updateAvatar (Request $request) {
        $admin = auth('admins')->user();

        if(!$request->file('avatar')){
            return response()->json([
                'error' => 'Xəta!',
            ]);
        }

        $file = $request->file('avatar');
        $filename= time() . '_' . Str::slug($admin->name) . '.' . $file->getExtension();
        $file->move(public_path('avatars'), $filename);
        $admin->avatar = $filename;
        $admin->save();

        return response()->json([
            'success' => 'Uğurla!',
            'url' => asset('avatars/' . $admin->avatar)
        ]);

    }
}
