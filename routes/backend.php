<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\backend\AuthController;
use App\Http\Controllers\backend\ExamController;
use App\Http\Controllers\backend\RoleController;
use App\Http\Controllers\backend\UserController;
use App\Http\Controllers\backend\AdminController;
use App\Http\Controllers\backend\BlogsController;
use App\Http\Controllers\backend\TeamsController;
use App\Http\Controllers\backend\CommonController;
use App\Http\Controllers\backend\ManagerController;
use App\Http\Controllers\backend\SectionController;
use App\Http\Controllers\backend\SlidersController;
use App\Http\Controllers\backend\CategoryController;
use App\Http\Controllers\backend\CountersController;
use App\Http\Controllers\backend\SettingsController;
use App\Http\Controllers\backend\DashboardController;
use App\Http\Controllers\backend\ReferencesController;
use App\Http\Controllers\backend\CouponCodesController;
use App\Http\Controllers\backend\ExamStartPageController;
use App\Http\Controllers\backend\StandartPagesController;
use App\Http\Controllers\backend\StudentRatingsController;

Route::group([
    'prefix'=>'admin',
    'namespace' => 'App\\Http\\Controllers\\backend',
], function () {

    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AuthController::class, 'login'])->name('admin.login');
        Route::post('/auth', [AuthController::class, 'auth'])->name('admin.auth');
    });

    Route::group(['middleware' => 'admins'], function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::post('/profile/save', [AdminController::class, 'save'])->name('admin.profile.save');
        Route::post('/profile-update-avatar', [AdminController::class, 'updateAvatar'])->name('admin.update.avatar');

        Route::group([
            'prefix' => 'categories',
        ], function () {
            Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
            Route::post('/store', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
            Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('categories.delete');
        });

        Route::group([
            'prefix' => 'sections/{exam_id}',
        ], function () {
            Route::get('/create', [SectionController::class, 'create'])->name('sections.create');
            Route::post('/store', [SectionController::class, 'store'])->name('sections.store');
            Route::get('/edit/{id}', [SectionController::class, 'edit'])->name('sections.edit');
            Route::put('/update/{id}', [SectionController::class, 'update'])->name('sections.update');
            Route::get('/delete/{id}', [SectionController::class, 'delete'])->name('sections.delete');
        });


        Route::group([
            'prefix' => 'users',
        ], function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/subscriptions', [UserController::class, 'subscriptions'])->name('users.subscriptions');
            Route::get('/unsubscribe/{user_id}/{course_id}', [UserController::class, 'unsubscribe'])->name('users.unsubscribe');
            Route::get('/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/store', [UserController::class, 'store'])->name('users.store');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('users.update');
            Route::get('/delete/{id}', [UserController::class, 'delete'])->name('users.delete');
        });

        Route::group([
            'prefix' => 'managers',
        ], function () {
            Route::get('/', [ManagerController::class, 'index'])->name('managers.index');
            Route::get('/create', [ManagerController::class, 'create'])->name('managers.create');
            Route::post('/store', [ManagerController::class, 'store'])->name('managers.store');
            Route::get('/edit/{id}', [ManagerController::class, 'edit'])->name('managers.edit');
            Route::put('/update/{id}', [ManagerController::class, 'update'])->name('managers.update');
            Route::get('/delete/{id}', [ManagerController::class, 'delete'])->name('managers.delete');
        });

        Route::group([
            'prefix' => 'roles',
        ], function () {
            Route::get('/', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/store', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/update/{id}', [RoleController::class, 'update'])->name('roles.update');
            Route::get('/delete/{id}', [RoleController::class, 'delete'])->name('roles.delete');
        });


        Route::group([
            'prefix' => 'exams',
        ], function () {
            Route::get('/', [ExamController::class, 'index'])->name('exams.index');
            Route::any('/analyze', [ExamController::class, 'analyzeview'])->name('exams.analyze');
            Route::get('/create', [ExamController::class, 'create'])->name('exams.create');
            Route::post('/store', [ExamController::class, 'store'])->name('exams.store');
            Route::get('/edit/{id}', [ExamController::class, 'edit'])->name('exams.edit');

            Route::put('/update/{id}', [ExamController::class, 'update'])->name('exams.update');
            Route::get('/delete/{id}', [ExamController::class, 'delete'])->name('exams.delete');

            Route::group([
                'prefix' => 'questions',
            ], function () {
                Route::get('/{exam_id}/{section_id?}', [ExamController::class, 'questions'])->name('exams.questions');
                Route::get('/{exam_id}/{section_id}/create', [ExamController::class, 'createQuestion'])->name('exams.questions.create');
                Route::post('/{exam_id}/{section_id}/store', [ExamController::class, 'storeQuestion'])->name('exams.questions.store');
                Route::get('/{exam_id}/{section_id}/edit/{id}', [ExamController::class, 'editQuestion'])->name('exams.questions.edit');
                Route::put('/{exam_id}/{section_id}/update/{id}', [ExamController::class, 'updateQuestion'])->name('exams.questions.update');
                Route::get('/{exam_id}/{section_id}/delete/{id}', [ExamController::class, 'deleteQuestion'])->name('exams.questions.delete');
            });

            Route::group([
                'prefix' => 'answers',
            ], function () {
                Route::get('/{exam_id}/{section_id}/{question_id}', [ExamController::class, 'answers'])->name('exams.answers');
                Route::get('/{exam_id}/{section_id}/{question_id}/create', [ExamController::class, 'createAnswer'])->name('exams.answers.create');
                Route::post('/{exam_id}/{section_id}/{question_id}/store', [ExamController::class, 'storeAnswer'])->name('exams.answers.store');
                Route::get('/{exam_id}/{section_id}/{question_id}/edit/{id}', [ExamController::class, 'editAnswer'])->name('exams.answers.edit');
                Route::put('/{exam_id}/{section_id}/{question_id}/update/{id}', [ExamController::class, 'updateAnswer'])->name('exams.answers.update');
                Route::get('/{exam_id}/{section_id}/{question_id}/delete/{id}', [ExamController::class, 'deleteAnswer'])->name('exams.answers.delete');
            });
        });

        Route::group([
            'prefix' => 'exam-results',
        ], function () {
            Route::get('/', [CommonController::class, 'examResults'])->name('exam.results');
            Route::get('/show/{result_id}', [CommonController::class, 'examResultShow'])->name('exam.result.show');
            Route::get('/destroy/{result_id}', [CommonController::class, 'examResultDestroy'])->name('exam.result.destroy');
        });

        Route::resource('studentratings', StudentRatingsController::class);
        Route::resource('counters', CountersController::class);
        Route::resource('sliders', SlidersController::class);
        Route::resource('standartpages', StandartPagesController::class);
        Route::resource('blogs', BlogsController::class);
        Route::post('delete_image', [BlogsController::class, 'deleteimage'])->name("delete.image");
        Route::resource('teams', TeamsController::class);
        Route::resource('settings', SettingsController::class);
        Route::resource('exam_start_page', ExamStartPageController::class);
        Route::resource('coupon_codes', CouponCodesController::class);
        Route::resource('references', ReferencesController::class);
    });
});
