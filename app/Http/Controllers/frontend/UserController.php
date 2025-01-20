<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Coupon;
use App\Models\Exam;
use App\Models\Section;
use App\Models\UsedCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{

    public function profile () {
        return view('frontend.pages.profile');
    }

    public function update (Request $request) {

        $rules = [
            'name' => ['required', 'string', 'min:5'],
            'email' => ['required', 'string', 'email', 'unique:users,email,' . auth('users')->user()->id],
            'password' => ['nullable', 'sometimes', 'min:6', 'string'],
            'city' => ['nullable', 'sometimes', 'string'],
            'country' => ['nullable', 'sometimes', 'string'],
            'address' => ['nullable', 'sometimes', 'string'],
            'zip_code' => ['nullable', 'sometimes', 'string'],
            'birth' => ['nullable', 'sometimes', 'string'],
            'phone' => ['required', 'string'],
            'username' => ['string', 'required', 'unique:users,username,' . auth('users')->user()->id, 'unique:admins,username'],
        ];

        $request->validate($rules);
        $user = auth('users')->user();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->city = $request->city;
        $user->country = $request->country;
        $user->address = $request->address;
        $user->zip_code = $request->zip_code;
        $user->birth = $request->birth;
        $user->phone = $request->phone;
        $user->username = $request->username;
        $user->school = $request->input('school')??null;
        $user->classroom = $request->input('classroom')??null;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with(['success' => setting('user-profile-update-message')]);
    }

    public function updateAvatar (Request $request) {
        $user = auth('users')->user();

        if(!$request->file('avatar')){
            return response()->json([
                'error' => setting('image-upload-error-message'),
            ]);
        }

        $file = $request->file('avatar');
        $filename= time() . '_' . Str::slug($user->name) . '.' . $file->getExtension();
        $file->move(public_path('avatars'), $filename);
        $user->avatar = $filename;
        $user->save();

        return response()->json([
            'success' => setting('image-upload-success-message'),
            'url' => asset('avatars/' . $user->avatar)
        ]);
    }

}
