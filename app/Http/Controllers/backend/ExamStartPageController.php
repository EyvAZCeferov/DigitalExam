<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\ExamStartPage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ExamStartPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data=exam_start_page(null,null);
            return view("backend.pages.exam_start_page.index",compact("data"));
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
            return view("backend.pages.exam_start_page.create_edit");
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
                $model = new ExamStartPage();
                $image = null;
                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'exam_start_page');
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
                $model->default = $request->input('default') ? 1 : 0;
                $model->order_number=$request->input('order_number')??1;
                $model->image = $image;
                $model->type = $request->input("type")??'info';
                /* `->user_id = Auth::guard('admins')->id();` is assigning the currently
                authenticated admin user's ID to the `user_id` attribute of the `ExamStartPage`
                model. This is a way to associate the created or updated exam start page with the
                admin user who performed the action. The `Auth::guard('admins')->id()` method
                retrieves the ID of the authenticated admin user. */
                // $model->user_id = Auth::guard('admins')->id();
                $model->save();

            });
            return redirect(route("exam_start_page.index"))->with("info",'Əlavə edildi');
        }catch(\Exception $e){
            dd($e->getMessage());
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
            $data=ExamStartPage::where("id",$id)->first();
            return view("backend.pages.exam_start_page.create_edit",compact('data'));
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
                $model = ExamStartPage::where('id',$id)->first();
                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'exam_start_page');
                    $model->image = $image;
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
                $model->default = $request->input('default') ? 1 : 0;
                $model->order_number=$request->input('order_number')??1;
                $model->type = $request->input("type")??'info';
                $model->update();

            });
            return redirect(route("exam_start_page.index"))->with("success",'Yeniləndi');
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
            $data=ExamStartPage::where("id",$id)->first();
            $data->delete();
            return redirect()->back()->with('success',"Silindi");
        }catch(\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }finally{
            dbdeactive();
        }
    }
}
