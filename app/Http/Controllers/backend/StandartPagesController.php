<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\StandartPages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;

class StandartPagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorizeForUser(auth('admins')->user(), 'standartpages-list');

        $data = standartpages();
        return view('backend.pages.standartpages.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorizeForUser(auth('admins')->user(), 'standartpages-create');

        return view('backend.pages.standartpages.create_edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $name = [];
            $slugs = [];
            $description = [];

            DB::transaction(function () use (&$name, &$slugs, &$description, $request) {
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

                $images = [];
                if (!empty($request->images)) {
                    foreach ($request->images as $key => $image) {
                        $imagename = $key . '-' . time() . '-blogimage' . '.' . $image->extension();
                        $image = image_upload($image, 'blogs', $imagename);
                        array_push($images, $image);
                    }
                }

                $data = new StandartPages();
                $data->name = $name;
                $data->slugs = $slugs;
                $data->description = $description;
                $data->images=$images;
                $data->save();
            });
            return redirect(route('standartpages.index'))->with('success', 'Uğurlu');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } finally {
            dbdeactive();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $this->authorizeForUser(auth('admins')->user(), 'standartpages-update');
        $data = Standartpages::where('id', $id)->first();

        return view('backend.pages.standartpages.create_edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $name = [];
            $slugs = [];
            $description = [];
            $data = StandartPages::findOrFail($id);

            DB::transaction(function () use (&$name, &$slugs, &$description, $request, &$data) {
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

                $images = [];
                if (!empty($request->images)) {
                    foreach ($request->images as $key => $image) {
                        $imagename = $key . '-' . time() . '-standartpages' . '.' . $image->extension();
                        $image = image_upload($image, 'standartpages', $imagename);
                        array_push($images, $image);
                    }
                }

                $data->name = $name;
                $data->slugs = $slugs;
                $data->description = $description;
                $data->images = $images;
                $data->update();
            });
            return redirect(route('standartpages.index'))->with('success', 'Uğurlu');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } finally {
            dbdeactive();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->authorizeForUser(auth('admins')->user(), 'standartpages-delete');

            $data = Standartpages::where('id', $id)->first();
            $data->delete();

            return redirect()->route('standartpages.index')->with(['success' => 'Uğurla!']);
        } catch (\Exception $e) {
            return redirect()->route('standartpages.index')->with(['error' => $e->getMessage()]);
        } finally {
            dbdeactive();
        }
    }
}
