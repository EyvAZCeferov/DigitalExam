<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ExamResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommonController extends Controller
{
    use AuthorizesRequests;

    public function examResults () {
        $this->authorizeForUser(auth('admins')->user(), 'exam-result');

        $query = ExamResult::query()->with('exam');

        if (!empty(request('date'))) {
            $date = explode(' to ', request('date'));
            $from = Carbon::parse($date[0])->startOfDay();
            $to = isset($date[1]) ? Carbon::parse($date[1])->endOfDay() : Carbon::parse($date[0])->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
        }

        if (!empty(request('user'))) {
            $user = User::find(request('user'));
            if ($user) {
                $query->where('user_id', $user->id);
            }
        }

        if (!empty(request('category'))) {
            $category = Category::find(request('category'));
            if ($category) {
                $query->whereHas('exam', function ($item) use ($category) {
                    $item->where('category_id', $category->id);
                });
            }
        }

        $results = $query->orderByDesc('created_at')->get();
        return view('backend.pages.exam_results.index', compact('results'));
    }

    public function examResultShow ($result_id) {
        $this->authorizeForUser(auth('admins')->user(), 'exam-result');

        $result = ExamResult::with('answers.answer')->findOrFail($result_id);

        return view('backend.pages.exam_results.show', compact('result'));
    }

    public function examResultDestroy($result_id){
        $this->authorizeForUser(auth('admins')->user(), 'exam-result');

        $result = ExamResult::with('answers.answer')->findOrFail($result_id);

        $result->delete();
        dbdeactive();
        return redirect()->back()->with("success",'Silindi');
    }

}
