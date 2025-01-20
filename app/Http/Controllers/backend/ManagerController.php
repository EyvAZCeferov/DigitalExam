<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    use AuthorizesRequests;

    public function index () {
        $this->authorizeForUser(auth('admins')->user(), 'admin-list');

        $managers = Admin::orderBy('created_at')->get();
        return view('backend.pages.managers.index', compact('managers'));
    }

    public function create () {
        $this->authorizeForUser(auth('admins')->user(), 'admin-create');

        $roles = Role::all();
        return view('backend.pages.managers.create', compact('roles'));
    }

    public function store (Request $request) {
        $this->authorizeForUser(auth('admins')->user(), 'admin-create');

        $rules = [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'string', 'unique:admins,email'],
            'password' => ['required', 'string', 'min: 6'],
            'role_id' => ['required', 'exists:roles,id'],
        ];

        $request->validate($rules);

        $model = new Admin();

        $model->name = $request->input('name');
        $model->email = $request->input('email');
        $model->password = Hash::make($request->input('password'));
        $model->role_id = $request->input('role_id');

        $model->save();

        return redirect()->route('managers.index')->with(['success' => 'Uğurla!']);
    }

    public function edit ($id) {
        $this->authorizeForUser(auth('admins')->user(), 'admin-update');

        $manager = Admin::findOrFail($id);
        $roles = Role::all();
        return view('backend.pages.managers.edit', compact('manager', 'roles'));
    }

    public function update (Request $request, $id) {
        $this->authorizeForUser(auth('admins')->user(), 'admin-update');

        $rules = [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'string', 'unique:admins,email,' . $id],
            'password' => ['sometimes', 'nullable', 'string', 'min: 6'],
            'role_id' => ['required', 'exists:roles,id'],
        ];

        $request->validate($rules);

        $model = Admin::findOrFail($id);

        $model->name = $request->input('name');
        $model->email = $request->input('email');

        $model->role_id = $request->input('role_id');

        if ($request->password) {
            $model->password = Hash::make($request->input('password'));
            $model->password_changed_at = Carbon::now();
        }

        $model->save();

        return redirect()->route('managers.index')->with(['success' => 'Uğurla!']);
    }

    public function delete ($id) {
        $this->authorizeForUser(auth('admins')->user(), 'admin-delete');

        $model = Admin::findOrFail($id);
        $model->delete();

        return redirect()->route('managers.index')->with(['success' => 'Uğurla!']);
    }
}
