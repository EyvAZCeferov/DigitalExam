<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorizeForUser(auth('admins')->user(), 'category-list');

        $categories = categories(null, null);

        return view('backend.pages.category.index', compact('categories'));
    }

    public function create()
    {
        $this->authorizeForUser(auth('admins')->user(), 'category-create');

        return view('backend.pages.category.create');
    }

    public function store(Request $request)
    {
        try {
            $this->authorizeForUser(auth('admins')->user(), 'category-create');

            $name = [];
            $slugs = [];
            $description = [];
            $image = null;

            // DB::transaction(function () use (&$name, &$slugs, &$description, &$image, $request) {
                $name = [
                    'az_name' => trim($request->az_name) ?? " ",
                    'ru_name' => $request->ru_name ?? trim(GoogleTranslate::trans($request->az_name, 'ru')),
                    'en_name' => $request->en_name ?? trim(GoogleTranslate::trans($request->az_name, 'en')),
                ];
                $description = [
                    'az_description' => trim($request->az_description) ?? " ",
                    'ru_description' => $request->ru_description ?? trim(GoogleTranslate::trans($request->az_description, 'ru')),
                    'en_description' => $request->en_description ?? trim(GoogleTranslate::trans($request->az_description, 'en')),
                ];

                $slugs = [
                    'az_slug' => Str::slug(trim($name['az_name'])),
                    'ru_slug' => Str::slug(trim($name['ru_name'])),
                    'en_slug' => Str::slug(trim($name['en_name'])),
                ];

                $data = new Category();

                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'category');
                }

                $data->name = $name;
                $data->slugs = $slugs;
                $data->description = $description;
                $data->parent_id = $request->input('parent_id') ?? null;
                $data->image = $image;
                $data->icon = $request->input("icon")??null;
                $data->order_number = $request->input('order_number');
                $data->save();

                dbdeactive();
            // });

            return redirect()->route('categories.index')->with(['success' => 'Uğurlu']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'category-update');
        $data = Category::findOrFail($id);
        return view('backend.pages.category.create', compact('data'));
    }

    public function update(Request $request, $id)
    {
        try {
            $this->authorizeForUser(auth('admins')->user(), 'category-update');
            $name = [];
            $slugs = [];
            $description = [];
            $image = null;
            $data = Category::where("id", $id)->first();

            // DB::transaction(function () use (&$name, &$slugs, &$description, &$image, $request, &$data) {
                $name = [
                    'az_name' => trim($request->az_name) ?? " ",
                    'ru_name' => $request->ru_name ?? trim(GoogleTranslate::trans($request->az_name, 'ru')),
                    'en_name' => $request->en_name ?? trim(GoogleTranslate::trans($request->az_name, 'en')),
                ];
                $description = [
                    'az_description' => trim($request->az_description) ?? " ",
                    'ru_description' => $request->ru_description ?? trim(GoogleTranslate::trans($request->az_description, 'ru')),
                    'en_description' => $request->en_description ?? trim(GoogleTranslate::trans($request->az_description, 'en')),
                ];

                $slugs = [
                    'az_slug' => Str::slug(trim($name['az_name'])),
                    'ru_slug' => Str::slug(trim($name['ru_name'])),
                    'en_slug' => Str::slug(trim($name['en_name'])),
                ];


                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'category');
                }


                $data->name = $name;
                $data->slugs = $slugs;
                $data->description = $description;
                $data->parent_id = $request->input('parent_id') ?? null;
                $data->order_number = $request->input('order_number') ?? 1;
                if (isset($image) && !empty($image)) {
                    $data->image = $image;
                }
                $data->icon = $request->input("icon")??null;
                $data->update();

                dbdeactive();
            // });

            return redirect()->route('categories.index')->with(['success' => 'Uğurlu']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'category-delete');

        $model = Category::findOrFail($id);
        $model->delete();

        dbdeactive();

        return redirect()->route('categories.index')->with(['success' => 'Silindi']);
    }
}
