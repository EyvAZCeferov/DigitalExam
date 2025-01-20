<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Admin;
use App\Models\ExamResult;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index () {

        $users = User::query()->count();
        $exams = Exam::query()->count();
        $exam_results = ExamResult::query()->count();
        $admins = Admin::query()->count();


        return view('backend.pages.dashboard', compact('users', 'exams', 'exam_results', 'admins'));
    }

}
