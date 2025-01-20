<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\StudentRatings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class StudentRatingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorizeForUser(auth('admins')->user(), 'studentratings-list');

        $data = StudentRatings::orderBy('order_number', 'DESC')->get();
        return view('backend.pages.studentratings.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorizeForUser(auth('admins')->user(), 'studentratings-create');

        return view('backend.pages.studentratings.create_edit');
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
            $position = [];

            DB::transaction(function () use (&$name, &$description, &$position, $request) {
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

                $description = [
                    'az_description' => trim($request->az_description) ?? " ",
                    'ru_description' => $request->ru_description ?? trim(GoogleTranslate::trans($request->az_description, 'ru')),
                    'en_description' => $request->en_description ?? trim(GoogleTranslate::trans($request->az_description, 'en')),
                ];

                $image = null;
                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'studentratings');
                }

                $data = new StudentRatings();
                $data->name = $name;
                $data->description = $description;
                $data->position = $position;
                $data->order_number = $request->order_number ?? 1;
                $data->status = $request->input('status') ? 1 : 0;
                $data->rating = $request->rating ?? 1;
                $data->image = $image ?? null;
                $data->save();
            });
            return redirect(route('studentratings.index'))->with('success', 'Uğurlu');
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
        $this->authorizeForUser(auth('admins')->user(), 'studentratings-update');
        $data = StudentRatings::where('id', $id)->first();

        return view('backend.pages.studentratings.create_edit', compact('data'));
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
            $position = [];

            $data = StudentRatings::where("id", $id)->first();

            DB::transaction(function () use (&$name, &$description, &$position, $request, &$data) {
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

                $description = [
                    'az_description' => trim($request->az_description) ?? " ",
                    'ru_description' => $request->ru_description ?? trim(GoogleTranslate::trans($request->az_description, 'ru')),
                    'en_description' => $request->en_description ?? trim(GoogleTranslate::trans($request->az_description, 'en')),
                ];

                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'studentratings');
                    $data->update(['image' => $image]);
                }

                $data->name = $name;
                $data->description = $description;
                $data->position = $position;
                $data->order_number = $request->order_number ?? 1;
                $data->status = $request->input('status') ? 1 : 0;
                $data->rating = $request->rating ?? 1;
                $data->update();
            });
            return redirect(route('studentratings.index'))->with('success', 'Uğurlu');
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
        try{
            $data=StudentRatings::where("id",$id)->first();
            $data->delete();
            return redirect()->back()->with("success",'Uğurlu');
        }catch(\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }finally{
            dbdeactive();
        }
    }
}
