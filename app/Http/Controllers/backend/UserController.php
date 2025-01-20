<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorizeForUser(auth('admins')->user(), 'user-list');

        $users = User::orderBy('created_at')->get();
        return view('backend.pages.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeForUser(auth('admins')->user(), 'user-create');

        return view('backend.pages.users.create');
    }

    public function store(Request $request)
    {
        $this->authorizeForUser(auth('admins')->user(), 'user-create');

        $rules = [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'string', 'unique:users,email'],
            'phone' => ['required', 'string'],
            'username' => ['required', 'string', 'unique:admins,username', 'unique:users,username'],
            'password' => ['required', 'string', 'min: 6'],
        ];

        $request->validate($rules);

        $model = new User();

        $model->name = $request->input('name');
        $model->email = $request->input('email');
        $model->phone = $request->input('phone');
        $model->password = Hash::make($request->input('password'));

        $model->save();

        return redirect()->route('users.index')->with(['success' => 'Uğurla!']);
    }

    public function edit($id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'user-update');

        $user = User::findOrFail($id);
        return view('backend.pages.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        try {
            $this->authorizeForUser(auth('admins')->user(), 'user-update');

            $rules = [
                'name' => ['required', 'string'],
                'email' => ['required', 'email', 'string', 'unique:users,email,' . $id],
                'phone' => ['required', 'string'],
            ];

            $request->validate($rules);

            $model = User::findOrFail($id);

            if ($request->hasFile('picture')) {
                $image = image_upload($request->file("picture"),'users');
                $model->picture=$image;
            }

            $model->name = $request->input('name');
            $model->email = $request->input('email');
            $model->phone = $request->input('phone');
            
            if ($request->password) {
                $model->password = Hash::make($request->input('password'));
            }

            $model->save();

            return redirect()->route('users.index')->with(['success' => 'Uğurla!']);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function delete($id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'user-delete');

        $model = User::findOrFail($id);
        $model->delete();

        return redirect()->route('users.index')->with(['success' => 'Uğurla!']);
    }
}
