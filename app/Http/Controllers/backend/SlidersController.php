<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Sliders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class SlidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorizeForUser(auth('admins')->user(), 'sliders-list');

        $data = Sliders::orderBy('order_number','DESC')->get();
        return view('backend.pages.sliders.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorizeForUser(auth('admins')->user(), 'sliders-create');

        return view('backend.pages.sliders.create_edit');
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
            $description = [];

            DB::transaction(function () use (&$name, &$description, $request) {
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

                $image=null;
                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"),'sliders');
                }

                $data = new Sliders();
                $data->name = $name;
                $data->description = $description;
                $data->url = $request->url??null;
                $data->order_number = $request->order_number??1;
                $data->status = $request->input('status') ? 1 : 0;
                $data->image = $image??null;
                $data->save();
            });
            return redirect(route('sliders.index'))->with('success', 'Uğurlu');
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
        $this->authorizeForUser(auth('admins')->user(), 'sliders-update');
        $data = Sliders::where('id', $id)->first();

        return view('backend.pages.sliders.create_edit', compact('data'));
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
            $description = [];
            $data = Sliders::where('id', $id)->first();

            DB::transaction(function () use (&$name, &$description, $request,&$data) {
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

                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"),'sliders');
                    $data->update(['image'=>$image]);
                }

                $data->name = $name;
                $data->description = $description;
                $data->url = $request->url??null;
                $data->order_number = $request->order_number??1;
                $data->status = $request->input('status') ? 1 : 0;
                $data->update();
            });
            return redirect(route('sliders.index'))->with('success', 'Uğurlu');
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
            $this->authorizeForUser(auth('admins')->user(), 'sliders-delete');

            $data = Sliders::where('id', $id)->first();
            $data->delete();

            return redirect()->route('sliders.index')->with(['success' => 'Uğurla!']);
        } catch (\Exception $e) {
            return redirect()->route('sliders.index')->with(['error' => $e->getMessage()]);
        } finally {
            dbdeactive();
        }
    }
}
