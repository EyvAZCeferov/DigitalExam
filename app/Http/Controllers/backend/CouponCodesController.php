<?php

namespace App\Http\Controllers\backend;

use App\Models\CouponCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stichoza\GoogleTranslate\GoogleTranslate;

class CouponCodesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data=coupon_codes(null,null);
            return view("backend.pages.coupon_codes.index",compact("data"));
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
            return view("backend.pages.coupon_codes.create_edit");
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
                $model = new CouponCodes();

                $name = [
                    'az_name' => trim($request->az_name) ?? " ",
                    'ru_name' => $request->ru_name ?? trim(GoogleTranslate::trans($request->az_name, 'ru')),
                    'en_name' => $request->en_name ?? trim(GoogleTranslate::trans($request->az_name, 'en')),
                ];

                $model->name = $name;
                $model->code = $request->input('code') ?? 'edudistance';
                $model->discount=$request->input('discount')??0;
                $model->status = $request->input("status")=="on"?true:false;
                $model->type = $request->input("type")??"value";
                $model->user_id = Auth::guard('admins')->id();
                $model->user_type = 'admins';
                $model->save();

            });
            return redirect(route("coupon_codes.index"))->with("info",'Əlavə edildi');
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
            $data=CouponCodes::where('id',$id)->first();
            return view("backend.pages.coupon_codes.create_edit",compact('data'));
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
                $model = CouponCodes::where('id',$id)->first();
                $name = [
                    'az_name' => trim($request->az_name) ?? " ",
                    'ru_name' => $request->ru_name ?? trim(GoogleTranslate::trans($request->az_name, 'ru')),
                    'en_name' => $request->en_name ?? trim(GoogleTranslate::trans($request->az_name, 'en')),
                ];
                $model->name = $name;
                $model->code = $request->input('code') ?? 'edudistance';
                $model->discount=$request->input('discount')??0;
                $model->status = $request->input("status")=="on"?true:false;
                $model->type = $request->input("type")??"value";
                $model->update();

            });
            return redirect(route("coupon_codes.index"))->with("info",'Yeniləndi');
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
            $data=CouponCodes::where("id",$id)->first();
            $data->delete();
            return redirect()->back()->with('success',"Silindi");
        }catch(\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }finally{
            dbdeactive();
        }
    }
}
