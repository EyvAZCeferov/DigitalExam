<?php

namespace App\Http\Controllers\backend;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = settings();
            return view("backend.pages.settings.create_edit", compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $address = [];
            $description = [];
            $social_media = [];
            $logo = null;
            $logo_white = null;


            DB::transaction(function () use (&$name, &$address, &$address_2, &$description, &$social_media, &$logo, &$logo_white, $request) {
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
                $address = [
                    'az_address' => trim($request->az_address) ?? " ",
                    'ru_address' => $request->ru_address ?? trim(GoogleTranslate::trans($request->az_address, 'ru')),
                    'en_address' => $request->en_address ?? trim(GoogleTranslate::trans($request->az_address, 'en')),
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
                    'maps_google' => isset($request->maps_google) && !empty($request->maps_google) ? $request->maps_google : " ",
                ];

                if ($request->hasFile('logo')) {
                    $logo = image_upload($request->file("logo"), 'settings');
                }
                if ($request->hasFile('logo_white')) {
                    $logo_white = image_upload($request->file("logo_white"), 'settings');
                }

                $data = new Settings();
                $data->name = $name;
                $data->address = $address;
                $data->description = $description;
                $data->social_media = $social_media;
                $data->logo = $logo;
                $data->logo_white = $logo_white;
                $data->save();
            });
            return redirect()->back()->with('success', 'Uğurlu');
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
        //
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
            $address = [];
            $description = [];
            $social_media = [];
            $logo = null;
            $logo_white = null;
            $data = Settings::findOrFail($id);


            DB::transaction(function () use (&$name, &$address, &$address_2, &$description, &$social_media, &$logo, &$logo_white, $request, &$data) {
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
                $address = [
                    'az_address' => trim($request->az_address) ?? " ",
                    'ru_address' => $request->ru_address ?? trim(GoogleTranslate::trans($request->az_address, 'ru')),
                    'en_address' => $request->en_address ?? trim(GoogleTranslate::trans($request->az_address, 'en')),
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
                    'maps_google' => isset($request->maps_google) && !empty($request->maps_google) ? $request->maps_google : " ",
                ];
                if ($request->hasFile('logo')) {
                    $logo = image_upload($request->file("logo"), 'settings');
                    $data->update(['logo' => $logo]);
                }
                if ($request->hasFile('logo_white')) {
                    $logo_white = image_upload($request->file("logo_white"), 'settings');
                    $data->update(['logo_white' => $logo_white]);
                }

                $data->name = $name;
                $data->address = $address;
                $data->description = $description;
                $data->social_media = $social_media;
                $data->update();
            });
            return redirect()->back()->with('success', 'Uğurlu');
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
        //
    }
}
