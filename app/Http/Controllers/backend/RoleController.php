<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index () {
        $this->authorizeForUser(auth('admins')->user(), 'role-list');

        $roles = Role::orderBy('created_at')->get();
        return view('backend.pages.roles.index', compact('roles'));
    }

    public function create () {
        $this->authorizeForUser(auth('admins')->user(), 'role-create');

        return view('backend.pages.roles.create');
    }

    public function store (Request $request) {
        $this->authorizeForUser(auth('admins')->user(), 'role-create');

        $rules = [
            'name' => ['required', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'string'],
        ];

        $request->validate($rules);

        $model = new Role();

        $model->name = $request->input('name');
        $model->permissions = collect($request->input('permissions'))->map(fn ($value) => preg_replace(['#<script(.*?)>(.*?)</script>#is', '/\bon\w+=\S+(?=.*>)/'], '', $value));

        $model->save();

        return redirect()->route('roles.index')->with(['success' => 'Uğurla!']);
    }

    public function edit ($id) {
        $this->authorizeForUser(auth('admins')->user(), 'role-update');

        $role = Role::findOrFail($id);
        return view('backend.pages.roles.edit', compact('role'));
    }

    public function update (Request $request, $id) {
        $this->authorizeForUser(auth('admins')->user(), 'role-update');

        $rules = [
            'name' => ['required', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'string'],
        ];

        $request->validate($rules);

        $model = Role::findOrFail($id);

        $model->name = $request->input('name');
        $model->permissions = collect($request->input('permissions'))->map(fn ($value) => preg_replace(['#<script(.*?)>(.*?)</script>#is', '/\bon\w+=\S+(?=.*>)/'], '', $value));

        $model->save();

        return redirect()->route('roles.index')->with(['success' => 'Uğurla!']);
    }

    public function delete ($id) {
        $this->authorizeForUser(auth('admins')->user(), 'role-delete');

        $model = Role::findOrFail($id);
        $model->delete();

        return redirect()->route('roles.index')->with(['success' => 'Uğurla!']);
    }
    
}
