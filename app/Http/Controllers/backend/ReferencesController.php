<?php

namespace App\Http\Controllers\backend;

use App\Models\References;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ReferencesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data=references(null,null);
            return view("backend.pages.exams.references.index",compact("data"));
        }catch(\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            return view("backend.pages.exams.references.create_edit");
        }catch(\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::transaction(function() use($request){
                $model = new References();
                $image = null;
                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'references');
                }
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

                $model->name = $name;
                $model->description = $description;
                $model->order_number=$request->input('order_number')??1;
                $model->image = $image;
                $model->save();

            });
            return redirect(route("references.index"))->with("success",'Əlavə edildi');
        }catch(\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }finally{
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
        try{
            $data=References::where("id",$id)->first();
            return view("backend.pages.exams.references.create_edit",compact("data"));
        }catch(\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
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
        try{
            DB::transaction(function() use($request,$id){
                $model = References::where('id',$id)->first();
                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'references');
                    $model->update(['image'=>$image]);
                }
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

                $model->name = $name;
                $model->description = $description;
                $model->order_number=$request->input('order_number')??1;
                $model->update();

            });
            return redirect(route("references.index"))->with("success",'Yeniləndi');
        }catch(\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }finally{
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
            $data=References::where("id",$id)->first();
            $data->delete();
            return redirect()->back()->with('success',"Silindi");
        }catch(\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }finally{
            dbdeactive();
        }
    }
}
