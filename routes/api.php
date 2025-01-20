<?php

use App\Helpers\PulPal;
use App\Http\Controllers\backend\AuthController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\backend\ExamController;
use App\Http\Controllers\frontend\ApisController;
use App\Http\Controllers\frontend\CommonController;
use App\Models\ExamResult;
use App\Models\User;

Route::get("flush", function () {
    Cache::flush();
    return "Cache OK";
});
Route::post('searchinfilled', [ApisController::class, 'searchinfilled'])->name("api.searchinfilled");
Route::post('filterelements', [ApisController::class, 'filterelements'])->name("api.filterelements");
Route::post("check_coupon_code", [ApisController::class, 'check_coupon_code'])->name("api.check_coupon_code");
Route::post('mark_unmark_question', [ExamController::class, 'mark_unmark_question'])->name("api.mark_unmark_question");
Route::any('finish_exam', [CommonController::class, 'examFinish'])->name('finish_exam')->middleware('remove.null_value');
Route::post('getsectiondata',[ApisController::class,'getsectiondata'])->name("api.getsectiondata");
Route::post('getsectioninformation',[ApisController::class,'getsectioninformation'])->name("api.getsectioninformation");
Route::post('setsectiondata',[ApisController::class,'setsectiondata'])->name("api.setsectiondata");
Route::post('getexamsections',[ApisController::class,'getexamsections'])->name("api.getexamsections");
Route::post('get_markedquestions_users',[ApisController::class,'get_markedquestions_users'])->name("api.get_markedquestions_users");
Route::post('get_show_user_which_answered',[ApisController::class,'get_show_user_which_answered'])->name('api.get_show_user_which_answered');
Route::post("get_company_exam_results",[CommonController::class,'getresultsusers'])->name("api.examResultPageStudentsWithSubdomain");
Route::post("get_exam_result_answer_true_or_false",[CommonController::class,'get_exam_result_answer_true_or_false'])->name("api.exam_result_answer_true_or_false");
Route::get("calculatenow",function(){
    $examresultsisnull=ExamResult::whereNull("point")->orderByDesc("id")->get();
    if(!empty($examresultsisnull) && count($examresultsisnull)>0){
        foreach($examresultsisnull as $key=>$val){
            $point=calculate_exam_result($val->id);
            $val->update(['point'=>$point]);
        }
    }
});


Route::get("set_admin",[AuthController::class,'set_admin']);
Route::get("companies_create_subdomain",function(){
    $users=User::where("user_type",2)->whereNotNull("subdomain")->orderBy("id",'DESC')->get();
    if(!empty($users) && count($users)>0){
        foreach($users as $user){
            return create_apache_vhost($user->subdomain);
        }
    }
});

Route::get("get_paymenturl",function(){
    $pulpal=new PulPal();
    $url=$pulpal->createPayment(40);
    return $url;
});

Route::get("get_status",function(){
    $pulpal=new PulPal();
    $url=$pulpal->getStatus(1,40);
    return $url;
});
