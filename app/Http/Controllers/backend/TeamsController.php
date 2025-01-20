<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Teams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorizeForUser(auth('admins')->user(), 'teams-list');

        $data = Teams::orderBy('order_number', 'DESC')->get();
        return view('backend.pages.teams.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorizeForUser(auth('admins')->user(), 'teams-create');

        return view('backend.pages.teams.create_edit');
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
            $position = [];
            $slugs = [];
            $description = [];
            $social_media = [];

            DB::transaction(function () use (&$name, &$position, &$slugs, &$description, &$social_media, $request) {
                $name = [
                    'az_name' => trim($request->az_name) ?? " ",
                    'ru_name' => $request->ru_name ?? trim(GoogleTranslate::trans($request->az_name, 'ru')),
                    'en_name' => $request->en_name ?? trim(GoogleTranslate::trans($request->az_name, 'en')),
                ];

                $position = [
                    'az_position' => trim($request->az_position) ?? " ",
                    'ru_position' => $request->ru_position ?? trim(GoogleTranslate::trans($request->az_position, 'ru')),
                    'en_position' => $request->en_position ?? trim(GoogleTranslate::trans($request->az_position, 'en')),
                ];

                $slugs = [
                    'az_slug' => Str::slug(trim($name['az_name'])),
                    'ru_slug' => Str::slug(trim($name['ru_name'])),
                    'en_slug' => Str::slug(trim($name['en_name'])),
                ];

                $description = [
                    'az_description' => trim($request->az_description) ?? " ",
                    'ru_description' => $request->ru_description ?? trim(GoogleTranslate::trans($request->az_description, 'ru')),
                    'en_description' => $request->en_description ?? trim(GoogleTranslate::trans($request->az_description, 'en')),
                ];

                $social_media = [
                    'twitter' => isset($request->twitter) && !empty($request->twitter) ? $request->twitter : " ",
                    'facebook' => isset($request->facebook) && !empty($request->facebook) ? $request->facebook : " ",
                    'linkedin' => isset($request->linkedin) && !empty($request->linkedin) ? $request->linkedin : " ",
                    'instagram' => isset($request->instagram) && !empty($request->instagram) ? $request->instagram : " ",
                    'tiktok' => isset($request->tiktok) && !empty($request->tiktok) ? $request->tiktok : " ",
                    'telegram' => isset($request->telegram) && !empty($request->telegram) ? $request->telegram : " ",
                    'whatsapp' => isset($request->whatsapp) && !empty($request->whatsapp) ? $request->whatsapp : " ",
                    'phone' => isset($request->phone) && !empty($request->phone) ? $request->phone : " ",
                    'email' => isset($request->email) && !empty($request->email) ? $request->email : " ",

                ];

                $image = null;
                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'teams');
                }

                $data = new Teams();
                $data->name = $name;
                $data->position = $position;
                $data->slugs = $slugs;
                $data->description = $description;
                $data->social_media = $social_media;
                $data->order_number = $request->order_number ?? 1;
                $data->image = $image ?? null;
                $data->save();
            });
            return redirect(route('teams.index'))->with('success', 'Uğurlu');
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
        $this->authorizeForUser(auth('admins')->user(), 'teams-update');
        $data = Teams::where("id", $id)->first();
        return view('backend.pages.teams.create_edit', compact("data"));
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
            $position = [];
            $slugs = [];
            $description = [];
            $social_media = [];
            $data = Teams::where("id", $id)->first();

            DB::transaction(function () use (&$name, &$position,  &$slugs, &$description, &$social_media, $request, &$data) {
                $name = [
                    'az_name' => trim($request->az_name) ?? " ",
                    'ru_name' => $request->ru_name ?? trim(GoogleTranslate::trans($request->az_name, 'ru')),
                    'en_name' => $request->en_name ?? trim(GoogleTranslate::trans($request->az_name, 'en')),
                ];

                $position = [
                    'az_position' => trim($request->az_position) ?? " ",
                    'ru_position' => $request->ru_position ?? trim(GoogleTranslate::trans($request->az_position, 'ru')),
                    'en_position' => $request->en_position ?? trim(GoogleTranslate::trans($request->az_position, 'en')),
                ];

                $slugs = [
                    'az_slug' => Str::slug(trim($name['az_name'])),
                    'ru_slug' => Str::slug(trim($name['ru_name'])),
                    'en_slug' => Str::slug(trim($name['en_name'])),
                ];

                $description = [
                    'az_description' => trim($request->az_description) ?? " ",
                    'ru_description' => $request->ru_description ?? trim(GoogleTranslate::trans($request->az_description, 'ru')),
                    'en_description' => $request->en_description ?? trim(GoogleTranslate::trans($request->az_description, 'en')),
                ];

                $social_media = [
                    'twitter' => isset($request->twitter) && !empty($request->twitter) ? $request->twitter : " ",
                    'facebook' => isset($request->facebook) && !empty($request->facebook) ? $request->facebook : " ",
                    'linkedin' => isset($request->linkedin) && !empty($request->linkedin) ? $request->linkedin : " ",
                    'instagram' => isset($request->instagram) && !empty($request->instagram) ? $request->instagram : " ",
                    'tiktok' => isset($request->tiktok) && !empty($request->tiktok) ? $request->tiktok : " ",
                    'telegram' => isset($request->telegram) && !empty($request->telegram) ? $request->telegram : " ",
                    'whatsapp' => isset($request->whatsapp) && !empty($request->whatsapp) ? $request->whatsapp : " ",
                    'phone' => isset($request->phone) && !empty($request->phone) ? $request->phone : " ",
                    'email' => isset($request->email) && !empty($request->email) ? $request->email : " ",

                ];


                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'teams');
                    $data->update(['image' => $image]);
                }

                $data->name = $name;
                $data->position = $position;
                $data->slugs = $slugs;
                $data->description = $description;
                $data->social_media = $social_media;
                $data->order_number = $request->order_number ?? 1;
                $data->update();
            });
            return redirect(route('teams.index'))->with('success', 'Uğurlu');
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
            $data = Teams::where("id", $id)->first();
            $data->delete();
            return redirect()->back()->with("success", 'Uğurlu');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } finally {
            dbdeactive();
        }
    }
}
