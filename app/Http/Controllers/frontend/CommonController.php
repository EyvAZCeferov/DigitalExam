<?php

namespace App\Http\Controllers\frontend;

use App\Models\Exam;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Section;
use App\Models\ExamResult;
use Illuminate\Support\Str;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use App\Models\ExamResultAnswer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ExamReferences;
use App\Models\ExamStartPageIds;
use App\Models\Payments;
use Illuminate\Support\Facades\Log;

class CommonController extends Controller
{
    public function exam($subdomain = null, $exam_id)
    {
        $exam = Exam::findOrFail($exam_id);
        $sections = Section::where('exam_id', $exam->id)->orderBy('created_at')->get();
        if ($exam->questionCount() === 0) {
            return redirect()->route('user.index');
        }

        return view('frontend.pages.exam.index', compact('exam', 'exam_id', 'sections'));
    }
    public function examFinish(Request $request)
    {
        try {
            // sleep(20);
            $result = collect();
            $nextsection = false;
            $nexturl = '';
            DB::transaction(function () use ($request, &$result, &$nextsection, &$nexturl) {
                $exam = Exam::where('id', $request->input('exam_id'))->first();
                $examsection = Section::where("id", $request->current_section)->first();
                $result = ExamResult::where("id", $request->exam_result_id)->first();
                if (!empty($result) && isset($result->id)) {
                    $result->update([
                        'time_reply' => session()->get("time_reply") ?? 0 + $request->time_exam,
                    ]);
                } else {
                    $result = new ExamResult();
                    $result->user_id = $request->user_id;
                    $result->exam_id = $request->exam_id;
                    $result->payed = 1;
                    $result->continued = 1;
                    $result->time_reply = session()->get("time_reply") ?? 0 + $request->time_exam;
                    $result->save();
                }


                if (empty($result) && !isset($result->id))
                    $result = ExamResult::where("id", $request->exam_result_id ?? session()->get("result_id"))->first();

                $result_id = $result->id ?? $request->exam_result_id;
                if (empty($result_id))
                    $result_id = session()->get('result_id');

                $exam_id = $result->exam_id ?? $exam->id;
                if (empty($result_id))
                    $exam_id = $request->exam_id ?? session()->get('result_id');

                $user_id = $result->user_id ?? $request->user_id;
                if (empty($user_id))
                    $user_id = session()->get('user_id');

                if (!empty($request->answers) && count($request->answers) > 0) {
                    foreach ($request->answers as $section_id => $answers) {
                        foreach ($answers as $question_id => $answer) {
                            $answer = strip_tags_with_whitespace($answer);
                            $answer = str_replace('\'', '', $answer);
                            $answer = str_replace('"', '', $answer);

                            $question_id = strip_tags_with_whitespace($question_id);
                            $question_id = str_replace('\'', '', $question_id);
                            $question_id = str_replace('"', '', $question_id);

                            $question = ExamQuestion::where("id", $question_id)->first();
                            if (!empty($question)) {
                                $time_reply = $request->question_time_replies[$question_id] ?? 0;
                                $resultAnswer = ExamResultAnswer::where("question_id", $question_id)->where("result_id", $result->id ?? $request->exam_result_id)->first();

                                if (empty($resultAnswer) && !isset($resultAnswer->id))
                                    $resultAnswer = new ExamResultAnswer();

                                if ($question->type == 1 || $question->type == 5) {
                                    $resultAnswer->result_id = $result->id ?? $request->exam_result_id;
                                    $resultAnswer->section_id = $section_id;
                                    $resultAnswer->question_id = $question->id;
                                    $resultAnswer->answer_id = $answer;
                                    $resultAnswer->result = $answer == $question->correctAnswer()?->id ? 1 : 0;
                                    $resultAnswer->time_reply = $time_reply == 0 ? null : $time_reply;
                                    $resultAnswer->save();
                                } else if ($question->type == 2) {
                                    $answer = array_map('intval', $answer);
                                    $user_answer = serialize($answer);
                                    $correct_answer = serialize($question->correctAnswer()?->pluck('id')->toArray());
                                    $resultAnswer->result_id = $result->id ?? $request->exam_result_id;
                                    $resultAnswer->section_id = $section_id;
                                    $resultAnswer->question_id = $question->id;
                                    $resultAnswer->answers = $answer;
                                    $resultAnswer->result = $user_answer == $correct_answer ? 1 : 0;
                                    $resultAnswer->time_reply = $time_reply == 0 ? null : $time_reply;
                                    $resultAnswer->save();
                                } else if ($question->type == 3) {
                                    $resultAnswer->result_id = $result->id ?? $request->exam_result_id;
                                    $resultAnswer->section_id = $section_id;
                                    $resultAnswer->question_id = $question->id;
                                    $resultAnswer->value = $answer;
                                    $correctAnswer = $question->correctAnswer()?->answer;
                                    if ($correctAnswer && !empty($answer)) {
                                        $correctAnswersArray = explode(',', strip_tags_with_whitespace($correctAnswer));
                                        $result = in_array(strip_tags_with_whitespace($answer), $correctAnswersArray) ? 1 : 0;
                                        $resultAnswer->result = $result;
                                    } else {
                                        $resultAnswer->result = 0;
                                    }
                                    $resultAnswer->time_reply = $time_reply == 0 ? null : $time_reply;
                                    $resultAnswer->save();
                                } else if ($question->type == 4) {
                                    if ($answer['answered'] == 1) {
                                        if (!empty($answer['questions']) && !empty($answer['answers'])) {
                                            $newArray = array_combine($answer['questions'], $answer['answers']);
                                            $newArrayEncoded = [];
                                            foreach ($newArray as $key => $value) {
                                                // Remove unwanted characters from keys and values
                                                $newArrayEncoded[strip_tags_with_whitespace($key)] = strip_tags_with_whitespace($value);
                                            }

                                            $array2 = $question->answers->pluck('answer')->toArray();
                                            $newArray2 = [];
                                            foreach ($array2 as $value) {
                                                $decodedValue = json_decode($value, true);
                                                // Remove unwanted characters from the decoded question and answer content
                                                $newArray2[strip_tags_with_whitespace($decodedValue['question_content'])] = strip_tags_with_whitespace($decodedValue['answer_content']);
                                            }

                                            // Compare the two arrays
                                            $difference = ($newArrayEncoded === $newArray2) ? true : false;

                                            $resultAnswer->result_id = $result->id ?? $request->exam_result_id;
                                            $resultAnswer->section_id = $section_id;
                                            $resultAnswer->question_id = $question->id;
                                            $resultAnswer->value = json_encode($newArray);
                                            $resultAnswer->result = $difference;
                                            $resultAnswer->time_reply = $time_reply == 0 ? null : $time_reply;
                                            $resultAnswer->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $examquestions = ExamQuestion::whereIn("exam_section_id", $exam->sections->pluck("id"))->get();

                if (!empty($examquestions) && count($examquestions) > 0) {
                    foreach ($examquestions as $question) {
                        if ($question) {
                            $questtype = 'radio';
                            switch ($question->type) {
                                case 1:
                                    $questtype = 'radio';
                                    break;
                                case 2:
                                    $questtype = 'checkbox';
                                    break;
                                case 3:
                                    $questtype = 'textbox';
                                    break;
                                case 4:
                                    $questtype = 'match';
                                    break;
                                case 5:
                                    $questtype = 'audio';
                                    break;
                                default:
                                    $questtype = 'radio';
                            }
                            if (empty($result) && !isset($result->id))
                                $result = ExamResult::where("id", $request->exam_result_id ?? session()->get("result_id"))->first();

                            $result_id = $result->id ?? $request->exam_result_id;
                            if (empty($result_id))
                                $result_id = session()->get('result_id');

                            $exam_id = $result->exam_id ?? $exam->id;
                            if (empty($result_id))
                                $exam_id = $request->exam_id ?? session()->get('result_id');

                            $user_id = $result->user_id ?? $request->user_id;
                            if (empty($user_id))
                                $user_id = session()->get('user_id');

                            $session_key = $result_id . $exam_id . $user_id . $question->id . $questtype;
                            $getresultanswer = ExamResultAnswer::where("result_id", $result->id ?? $request->exam_result_id)
                                ->where('question_id', $question->id)
                                ->first();
                            if (empty($getresultanswer) && !isset($getresultanswer->id)) {

                                $value = session()->has($session_key) ? session()->get($session_key) : null;
                                if (!empty($value)) {
                                    if ($question->type == 1 || $question->type == 5) {
                                        $resultAnswer = new ExamResultAnswer();
                                        $resultAnswer->result_id = $result->id ?? $request->exam_result_id;
                                        $resultAnswer->section_id = $question->section_id;
                                        $resultAnswer->question_id = $question->id;
                                        $resultAnswer->answer_id = $value;
                                        $resultAnswer->result = $value == $question->correctAnswer()?->id ? 1 : 0;
                                        $resultAnswer->time_reply = 5;
                                        $resultAnswer->save();
                                    } else if ($question->type == 2) {
                                        $answer = array_map('intval', $value);
                                        $user_answer = serialize($answer);
                                        $correct_answer = serialize($question->correctAnswer()?->pluck('id')->toArray());
                                        $resultAnswer = new ExamResultAnswer();
                                        $resultAnswer->result_id = $result->id ?? $request->exam_result_id;
                                        $resultAnswer->section_id = $question->section_id;
                                        $resultAnswer->question_id = $question->id;
                                        $resultAnswer->answers = $answer;
                                        $resultAnswer->result = $user_answer == $correct_answer ? 1 : 0;
                                        $resultAnswer->time_reply = 5;
                                        $resultAnswer->save();
                                    } else if ($question->type == 3) {
                                        $resultAnswer = new ExamResultAnswer();
                                        $resultAnswer->result_id = $result->id ?? $request->exam_result_id;
                                        $resultAnswer->section_id = $question->section_id;
                                        $resultAnswer->question_id = $question->id;
                                        $resultAnswer->value = $value;
                                        $correctAnswer = $question->correctAnswer()?->answer;
                                        if ($correctAnswer && !empty($value)) {
                                            $correctAnswersArray = explode(',', strip_tags_with_whitespace($correctAnswer));
                                            $resultAnswer->result = in_array(strip_tags_with_whitespace($value), $correctAnswersArray) ? 1 : 0;
                                        } else {
                                            $resultAnswer->result = 0;
                                        }
                                        $resultAnswer->time_reply = 5;
                                        $resultAnswer->save();
                                    } else if ($question->type == 4) {
                                        if ($value['answered'] == 1) {
                                            if (!empty($value['questions']) && !empty($value['answers'])) {
                                                $newArray = array_combine($value['questions'], $value['answers']);
                                                $newArrayEncoded = [];
                                                foreach ($newArray as $key => $value) {
                                                    $newArrayEncoded[strip_tags_with_whitespace($key)] = strip_tags_with_whitespace($value);
                                                }
                                                $array2 = $question->answers->pluck('answer')->toArray();
                                                $newArray2 = [];
                                                foreach ($array2 as $value) {
                                                    $decodedValue = json_decode($value, true);
                                                    $newArray2[strip_tags_with_whitespace($decodedValue['question_content'])] = strip_tags_with_whitespace($decodedValue['answer_content']);
                                                }

                                                $difference = ($newArrayEncoded === $newArray2) ? true : false;
                                                $resultAnswer = new ExamResultAnswer();
                                                $resultAnswer->result_id = $result->id ?? $request->exam_result_id;
                                                $resultAnswer->section_id = $question->section_id;
                                                $resultAnswer->question_id = $question->id;
                                                $resultAnswer->value = json_encode($newArray);
                                                $resultAnswer->result = $difference;
                                                $resultAnswer->time_reply = 5;
                                                $resultAnswer->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($examsection->time_range_sections > 0) {
                    $point = calculate_exam_result($result_id);
                    session()->put('point', $point);
                    session()->put('result_id', $result_id);
                    session()->put('exam_id', $exam_id);
                    session()->put('user_id', $user_id);
                    session()->put('time_reply', session()->get("time_reply") ?? 0 + $request->time_exam);
                    session()->put('selected_section', $request->selected_section + 1);
                    $nextsection = true;
                } else {
                    $pointlast = session()->has('point') ? session()->get('point') : 0;
                    $point = number_format($pointlast + (!empty($result_id) ?  calculate_exam_result($result_id) : 0), 2);
                    $time_reply = session()->has('time_reply') ? session()->get('time_reply') + $request->time_exam : $request->time_exam;
                    app()->setLocale(session()->get('changedLang'));
                    session()->put('language', session()->get('changedLang'));
                    session()->put('lang', session()->get('changedLang'));
                    $updateData = [
                        'point' => $exam->layout_type == "sat" ? customRound($point) : $point,
                        'counted_point' => $point,
                        'time_reply' => $time_reply,
                    ];

                    if (empty($result) && !isset($result->id)) {
                        $result = ExamResult::where("id", $result_id)->first();
                    }
                    if (!empty($result) && isset($result->id)) {
                        $result->update($updateData);
                    }

                    session()->forget(['point', 'time_reply', 'selected_section']);
                }

                $nexturl = '';

                if ($nextsection == true) {
                    $nexturl = route("user.exams.redirect_exam", ['exam_id' => $exam_id, 'selected_section' => session()->get('selected_section') ?? 0]);
                } else {
                    if ($exam->show_result_user == true) {
                        $point = calculate_exam_result($result_id);
                        ExamResult::find($result_id)->update(['point' => $point]);
                        $nexturl = route("user.exam.resultpage", $result_id);
                        remove_repeated_result_answers($result_id);
                    } else {
                        $nexturl = route("page.welcome");
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => trans("additional.messages.exam_finished", [], $request->language ?? 'az'),
                'url' => $nexturl,
                'nextsection' => $nextsection
            ]);
        } catch (\Exception $e) {
            Log::info([
                'status' => '--------ERRROR finishing----------',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
        } finally {
            dbdeactive();
        }
    }
    public function examResults()
    {
        $results = ExamResult::where('user_id', auth('users')->user()->id)
            ->orderByDesc('id')
            ->get();

        return view('frontend.pages.exam.results', compact('results'));
    }
    public function examResultPage_nosubdomain($result_id)
    {
        $exam_result = ExamResult::where('user_id', auth('users')->user()->id)
            ->with('answers.answer')
            ->orderByDesc('id')
            ->findOrFail($result_id);

        return view('frontend.exams.resultpage', compact('exam_result'));
    }
    public function examResultPage($subdomain = null, $result_id)
    {
        if (Auth::guard("users")->check() && Auth::guard("users")->user()->user_type == 2) {
            $exam = Exam::where('slug', $result_id)->where("user_id", Auth::guard("users")->id())->first();
            $exam_results = ExamResult::where('exam_id', $exam->id)
                ->with('answers.answer')
                ->orderByDesc('id')->get();


            if (!empty($exam_results) && count($exam_results) > 0) {
                return view('frontend.exams.results.resultoncompany', compact('exam_results', 'exam'));
            } else {
                return redirect()->back()->with('info', trans("additional.pages.exams.notfound"));
            }
        } else {
            $exam_result = ExamResult::where('user_id', auth('users')->user()->id)
                ->with('answers.answer')
                ->orderByDesc('id')
                ->findOrFail($result_id);

            if ($exam_result->point == 0) {
                $point = customRound($exam_result->counted_point);
                $exam_result->update(['point' => $point]);
            }

            return view('frontend.exams.resultpage', compact('exam_result'));
        }
    }
    public function examResult_nosubdomain($result_id)
    {
        $exam_result = ExamResult::with('answers.answer')
            ->orderByDesc('id')
            ->findOrFail($result_id);

        calc_this_exam($exam_result->exam_id, $exam_result->user_id);
        $exam = $exam_result->exam;

        return view('frontend.exams.result', compact('exam_result', 'exam'));
    }
    public function examResult($subdomain = null, $result_id)
    {
        $exam_result = ExamResult::with('answers.answer')
            ->orderByDesc('id')
            ->findOrFail($result_id);
        calc_this_exam($exam_result->exam_id, $exam_result->user_id);
        $exam = $exam_result->exam;
        return view('frontend.exams.result', compact('exam_result', 'exam'));
    }
    public function notfound()
    {
        return view("frontend.pages.notfound");
    }
    public function redirect_exam(Request $request)
    {
        try {
            if (Auth::guard('users')->check()) {
                $exam = Exam::where("id", $request->exam_id)
                    ->with(['sections', 'references'])
                    ->first();
                $exam_start_pages = collect();

                session()->put('selected_section', $request->selected_section ?? 0);
                session()->put('changedLang', app()->getLocale() ?? 'az');
                if ($exam->layout_type == "sat") {
                    app()->setLocale('en');
                    session()->put('language', 'en');
                    session()->put('lang', 'en');
                }

                if (!empty($exam)) {
                    $exam_result = ExamResult::where("exam_id", $request->exam_id)
                        ->where('user_id', Auth::guard('users')->id())
                        ->whereNull("point")
                        ->first();
                    if (empty($exam_result) && !isset($exam_result->id)) {
                        $exam_result2 = ExamResult::where("exam_id", $request->exam_id)
                            ->where('user_id', Auth::guard('users')->id())
                            ->whereNotNull("point")
                            ->where('payed', true)
                            ->first();

                        if (!empty($exam_result2)) {
                            return $this->examResult_nosubdomain($exam_result2->id);
                        } else {
                            if (!empty($exam->start_pages)) {
                                $default = exam_start_page();
                                foreach ($exam->start_pages as $page) {
                                    if (!empty($page->start_page)) {
                                        $exam_start_pages->push($page->start_page);
                                    }
                                }
                                $exam_start_pages->push($default);
                                $exam_start_pages = $exam_start_pages->sortBy('order_number')->values();
                            } else {
                                $exam_start_pages = exam_start_page();
                            }
                            if (empty($exam_start_pages)) {
                                session()->put('result_id', $exam_result->id);
                                session()->put('exam_id', $exam_result->exam_id);
                                session()->put('user_id', $exam_result->user_id);

                                $questions = collect();

                                if (session()->has('selected_section')) {
                                    if (!empty($exam->sections) && count($exam->sections) > 0) {
                                        $selectedsection = $exam->sections[session()->get('selected_section')];
                                        $qesutions = $selectedsection->questions;

                                        if ($selectedsection->question_count > 0) {
                                            $availableQuestionCount = $qesutions->count();

                                            if ($selectedsection->random == true) {
                                                $questions = $qesutions->random(min($selectedsection->question_count, $availableQuestionCount));
                                            } else {
                                                $questions = $qesutions->take($selectedsection->question_count);
                                            }
                                        } else {
                                            foreach ($qesutions as $qesution) {
                                                $questions[] = $qesution;
                                            }
                                        }
                                    } else {
                                        $qesutions = $exam->sections->pluck('questions');
                                        foreach ($qesutions as $qesution) {
                                            foreach ($qesution as $qest) {
                                                $questions[] = $qest;
                                            }
                                        }
                                    }
                                } else {
                                    return abort(403, 'Administrator ilə əlaqə saxlayın');
                                }

                                return view("frontend.exams.exam_main_process.index", compact("exam", 'exam_result', 'questions'));
                            } else {
                                return view("frontend.exams.exam_main_process.start_page", compact("exam", 'exam_start_pages'));
                            }
                        }
                    } else {

                        if ($exam->questionCount() > 0) {
                            session()->put('result_id', $exam_result->id);
                            session()->put('exam_id', $exam_result->exam_id);
                            session()->put('user_id', $exam_result->user_id);


                            $questions = collect();

                            if (session()->has('selected_section')) {
                                if (!empty($exam->sections) && count($exam->sections) > 0) {
                                    $selectedsection = $exam->sections[session()->get('selected_section')];
                                    $qesutions = $selectedsection->questions;

                                    if ($selectedsection->question_count > 0) {
                                        $availableQuestionCount = $qesutions->count();

                                        if ($selectedsection->random == true) {
                                            $questions = $qesutions->random(min($selectedsection->question_count, $availableQuestionCount));
                                        } else {
                                            $questions = $qesutions->take($selectedsection->question_count);
                                        }
                                    } else {
                                        foreach ($qesutions as $qesution) {
                                            $questions[] = $qesution;
                                        }
                                    }
                                } else {
                                    $qesutions = $exam->sections->pluck('questions');
                                    foreach ($qesutions as $qesution) {
                                        foreach ($qesution as $qest) {
                                            $questions[] = $qest;
                                        }
                                    }
                                }
                            } else {
                                return abort(403, 'Administrator ilə əlaqə saxlayın');
                            }

                            return view("frontend.exams.exam_main_process.index", compact('exam', 'exam_result', 'questions'));
                        } else {
                            $exam_result->delete();
                            return redirect()->back()->with('info', trans('additional.messages.examnotfound'));
                        }
                    }
                } else {
                    return $this->notfound();
                }
            } else {
                return redirect(route('login'))->with('error', trans("additional.headers.login"));
            }
        } catch (\Exception $e) {
            Log::info([
                'status' => '--------ERRROR redirecting----------',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            dd([$e->getMessage(), $e->getLine()]);

            return redirect()->back()->with("error", $e->getMessage(), $e->getLine());
        } finally {
            dbdeactive();
        }
    }
    public function set_exam(Request $request)
    {
        try {
            session()->forget('point');
            session()->forget('time_reply');
            session()->forget('selected_section');
            $exam_result = ExamResult::where("exam_id", $request->get("exam_id"))
                ->where('user_id', Auth::guard('users')->id())
                ->whereNull("point")
                ->first();
            $exam = Exam::where("id", $request->get("exam_id"))
                ->with(['sections', 'references'])
                ->first();
            $coupon_code = collect();
            if (!empty($request->get("coupon_code"))) {
                $coupon_code = coupon_codes($request->get('coupon_code'), 'code');
            }
            $exam_price = 0;
            if ($exam->price > 0) {
                $payment = !empty($exam_result) && isset($exam_result->id) ? payments($exam_result->user_id, $exam->id, $exam_result->id, null, null, null) : null;
                if (empty($payment)) {
                    if ($exam->endirim_price != null && $exam->endirim_price != $exam->price) {
                        $exam_price = $exam->endirim_price;
                    } else {
                        $exam_price = $exam->price;
                    }

                    if (!empty($coupon_code) && isset($coupon_code->discount) && $coupon_code->discount > 0 && $exam_price > 0) {
                        if ($coupon_code->type == "percent") {
                            $exam_price -= $exam_price * $coupon_code->discount / 100;
                        } else {
                            if ($coupon_code->discount > $exam_price) {
                                $exam_price = 0;
                            } else {
                                $exam_price -= $coupon_code->discount;
                            }
                        }
                    }
                }
            }
            if (empty($exam_result) && !isset($exam_result->id)) {
                $exam_result = new ExamResult();
                $exam_result->user_id = Auth::guard('users')->id();
                $exam_result->exam_id = $request->exam_id;
                $exam_result->payed = $exam_price == 0 ? true : false;
                $exam_result->save();
            }

            if ($exam_result->payed == true) {
                return $this->redirect_exam($request);
            } else {
                $payment_link = $this->payment_start($request, $exam, $exam_result, $coupon_code, $exam_price);
                if (!empty($payment_link))
                    return redirect($payment_link);
                else
                    return $this->notfound();
            }
        } catch (\Exception $e) {
            Log::info([
                'status' => '--------Set Exam----------',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            dd([$e->getMessage(), $e->getLine()]);
            return redirect()->back()->with("error", $e->getMessage());
        } finally {
            dbdeactive();
        }
    }
    protected function payment_start(Request $request, $exam, $exam_result, $coupon_code, $exam_price)
    {
        try {
            $payment_dat = [
                'exam_id' => $request->exam_id,
                'exam_name' => $exam->name[app()->getLocale() . '_name'],
                'exam_image' => getImageUrl($exam->image, 'exams'),
                'exam_result_id' => $exam_result->id,
                'user_id' => Auth::guard('users')->id(),
                'user_name' => Auth::guard('users')->user()->name,
                'user_email' => Auth::guard('users')->user()->email ?? null,
                'user_phone' => Auth::guard('users')->user()->phone ?? null,
                'token' => createRandomCode("string", 20),
                'price' => $exam->price,
                'endirim_price' => $exam->endirim_price,
                'amount' => $exam_price,
                'coupon_id' => !empty($coupon_code) && isset($coupon_code->id) ? $coupon_code->id ?? null : null,
                'coupon_name' => !empty($coupon_code) && !empty($coupon_code->name) && isset($coupon_code->name[app()->getLocale() . '_name']) ? $coupon_code->name[app()->getLocale() . '_name'] ?? null : null,
                'coupon_code' => !empty($coupon_code) && isset($coupon_code->code) ? $coupon_code->code ?? null : null,
                'coupon_discount' => !empty($coupon_code) && isset($coupon_code->discount) ? $coupon_code->discount ?? null : null,
                'coupon_type' => !empty($coupon_code) && isset($coupon_code->type) ? $coupon_code->type ?? null : null,
            ];
            $apiscontroller = new ApisController();
            $data = $apiscontroller->create_payment($payment_dat);
            if (!empty($data) && isset($data)) {
                return $data;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }
    public function add_edit_exam(Request $request)
    {
        try {
            $data = collect();
            // DB::transaction(function () use (&$data, $request) {
            if (isset($request->top_id) && !empty($request->top_id)) {
                $data = Exam::where("id", $request->top_id)->first();
            } else {
                $data = new Exam();
            }

            if (!(isset($request->exam_name) && !empty($request->exam_name)))
                return redirect()->back()->with('error', 'Məlumatları tam doldurun');

            if (!((isset($request->description) && !empty($request->description)) || (isset($request->mce_0) && !empty($request->mce_0))))
                return redirect()->back()->with('error', 'Məlumatları tam doldurun');

            if ($request->hasFile('image')) {
                $image = image_upload($request->file("image"), 'exams');
            }

            if (Auth::guard('users')->user()->user_type == 1) {
                return redirect('/logout')->with("error", "Hesabınıza şirkət olaraq daxil olmalısınız");
            }

            $name = [
                'az_name' => trim($request->exam_name, 'az'),
                'ru_name' => trim(GoogleTranslate::trans($request->exam_name, 'ru')),
                'en_name' => trim(GoogleTranslate::trans($request->exam_name, 'en')),
            ];
            $description = [
                'az_description' => trim(modifyRelativeUrlsToAbsolute($request->description ?? $request->mce_0, 'az')),
                'ru_description' => trim(modifyRelativeUrlsToAbsolute(GoogleTranslate::trans($request->description ?? $request->mce_0, 'ru'))),
                'en_description' => trim(modifyRelativeUrlsToAbsolute(GoogleTranslate::trans($request->description ?? $request->mce_0, 'en'))),
            ];
            $start_time = null;
            if ($request->input('start_time') != null)
                $start_time = Carbon::parse($request->input('start_time'));

            $data->category_id = intval($request->input('category_id'));
            $data->name = $name;
            $data->content = $description;
            $data->slug = Str::slug($name['az_name']) . '-' . Str::uuid();
            $data->point = $request->input('point') ?? 0;
            $data->status = $request->input('exam_status') == "on" ? 1 : 0;
            $data->order_number = 1;
            $data->price = $request->input('price') ?? 0;
            $data->endirim_price = $request->input('endirim_price') ?? 0;
            $data->user_id = intval($request->auth_id) ?? auth('users')->id();
            $data->user_type = "users";
            $data->repeat_sound = $request->input('repeat_sound') == "on" ? 1 : 0;
            $data->show_result_user = $request->input('exam_show_result_answer') == "on" ? 1 : 0;
            $data->show_calc = $request->input('show_calculator') == "on" ? 1 : 0;
            $data->start_time = $start_time ?? null;
            if (!empty($image))
                $data->image = $image;
            $data->layout_type = $request->input('layout_type') ?? 'standart';
            $data->save();

            $exam_start_pages = ExamStartPageIds::where("exam_id", $data->id)->get();
            foreach ($exam_start_pages as $val) {
                $val->delete();
            }

            if (!empty($request->exam_start_page_id)) {
                foreach ($request->exam_start_page_id as $id) {
                    $page = new ExamStartPageIds();
                    $page->exam_id = $data->id;
                    $page->start_page_id = $id;
                    $page->save();
                }
            }

            $references = ExamReferences::where("exam_id", $data->id)->get();
            foreach ($references as $val) {
                $val->delete();
            }

            if (!empty($request->exam_references)) {
                foreach ($request->exam_references as $id) {
                    $page = new ExamReferences();
                    $page->exam_id = $data->id;
                    $page->reference_id = $id;
                    $page->save();
                }
            }

            // });
            dbdeactive();
            return redirect(route('exams_front.createoredit', ['slug' => $data->slug]))->with('success', "Əlavə edildi");
        } catch (\Exception $e) {
            dd([$e->getMessage(), $e->getLine()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function add_edit_exam_subdomain(Request $request, $subdomain = null)
    {
        try {
            $data = collect();
            // DB::transaction(function () use (&$data, $request) {

            if (!(isset($request->exam_name) && !empty($request->exam_name)))
                return redirect()->back()->with('error', 'Məlumatları tam doldurun');

            if (!((isset($request->description) && !empty($request->description)) || (isset($request->mce_0) && !empty($request->mce_0))))
                return redirect()->back()->with('error', 'Məlumatları tam doldurun');


            if (isset($request->top_id) && !empty($request->top_id)) {
                $data = Exam::where("id", $request->top_id)->first();
            } else {
                $data = new Exam();
            }

            if (Auth::guard('users')->user()->user_type == 1) {
                return redirect('/logout')->with("error", "Hesabınıza şirkət olaraq daxil olmalısınız");
            }

            if ($request->hasFile('image')) {
                $image = image_upload($request->file("image"), 'exams');
            }

            $name = [
                'az_name' => trim($request->exam_name, 'az'),
                'ru_name' => trim(GoogleTranslate::trans($request->exam_name, 'ru')),
                'en_name' => trim(GoogleTranslate::trans($request->exam_name, 'en')),
            ];
            $description = [
                'az_description' => trim(modifyRelativeUrlsToAbsolute($request->description ?? $request->mce_0, 'az')),
                'ru_description' => trim(modifyRelativeUrlsToAbsolute(GoogleTranslate::trans($request->description ?? $request->mce_0, 'ru'))),
                'en_description' => trim(modifyRelativeUrlsToAbsolute(GoogleTranslate::trans($request->description ?? $request->mce_0, 'en'))),
            ];
            $start_time = null;
            if ($request->input('start_time') != null)
                $start_time = Carbon::parse($request->input('start_time'));

            $data->category_id = intval($request->input('category_id'));
            $data->name = $name;
            $data->content = $description;
            $data->slug = Str::slug($name['az_name']) . '-' . Str::uuid();
            $data->point = $request->input('point') ?? 0;
            $data->status = $request->input('exam_status') == "on" ? 1 : 0;
            $data->order_number = 1;
            $data->price = $request->input('price') ?? 0;
            $data->endirim_price = $request->input('endirim_price') ?? 0;
            $data->user_id = intval($request->auth_id) ?? auth('users')->id();
            $data->user_type = "users";
            $data->repeat_sound = $request->input('repeat_sound') == "on" ? 1 : 0;
            $data->show_result_user = $request->input('exam_show_result_answer') == "on" ? 1 : 0;
            $data->show_calc = $request->input('show_calculator') == "on" ? 1 : 0;
            $data->start_time = $start_time ?? null;
            if (!empty($image))
                $data->image = $image;
            $data->layout_type = $request->input('layout_type') ?? 'standart';
            $data->save();

            $exam_start_pages = ExamStartPageIds::where("exam_id", $data->id)->get();
            foreach ($exam_start_pages as $val) {
                $val->delete();
            }

            if (!empty($request->exam_start_page_id)) {
                foreach ($request->exam_start_page_id as $id) {
                    $page = new ExamStartPageIds();
                    $page->exam_id = $data->id;
                    $page->start_page_id = $id;
                    $page->save();
                }
            }

            $references = ExamReferences::where("exam_id", $data->id)->get();
            foreach ($references as $val) {
                $val->delete();
            }

            if (!empty($request->exam_references)) {
                foreach ($request->exam_references as $id) {
                    $page = new ExamReferences();
                    $page->exam_id = $data->id;
                    $page->reference_id = $id;
                    $page->save();
                }
            }

            // });
            dbdeactive();
            return redirect(route('exams_front.createoredit', ['slug' => $data->slug]))->with('success', "Əlavə edildi");
        } catch (\Exception $e) {
            dd([$e->getMessage(), $e->getLine()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function examResultPageStudentsWithSubdomain($subdomain = null, $result_id, Request $request)
    {
        if (Auth::guard("users")->check() && Auth::guard("users")->user()->user_type == 2) {
            $exam = Exam::where('id', $result_id)->where("user_id", Auth::guard("users")->id())->first();
            if ($request->has('responseType') && !empty($request->input("responseType")) && $request->input("responseType") == 'json') {
                $exam_results = ExamResult::where('exam_id', $exam->id)
                    ->with('answers.answer')
                    ->orderByDesc('id')->get();

                return response()->json(['status' => 'success', 'data' => $exam_results]);
            }

            return view('frontend.exams.results.resultoncompanywithdesign', compact("exam"));
        }
    }
    public function getresultsusers(Request $request)
    {
        try {
            if ($request->has('responseType') && !empty($request->input("responseType")) && $request->input("responseType") == 'json') {
                $exam_results = ExamResult::where('exam_id', $request->input("exam_id"))
                    ->with(['answers.answer', 'user', 'exam'])
                    ->orderByDesc('id')->get();
                $true_false_questions = [];
                $wrong_and_truecounts = [];
                if (!empty($exam_results) && count($exam_results) > 0) {
                    foreach ($exam_results as $result) {
                        remove_repeated_result_answers($result->id);
                        $true_false_questions[] = $result->id;
                        $wrong_and_truecounts[] = $result->id;
                    }
                }


                if (count($true_false_questions) > 0 && !empty($true_false_questions)) {
                    foreach ($true_false_questions as $true_false_question) {
                        $array = [];
                        $model = ExamResultAnswer::where('result_id', $true_false_question)->whereNotNull("result")->get();
                        if (!empty($model) && count($model) > 0) {
                            $sections = Section::where("exam_id", $model[0]->result_model->exam_id)->pluck('id');
                            $questions = ExamQuestion::whereIn("exam_section_id", $sections)->get();
                            foreach ($questions as $question) {
                                $array[$question->id] = 'null';
                            }

                            foreach ($model as $mod) {
                                if (isset($array[$mod->question_id])) {
                                    if ($mod->result == 1) {
                                        $array[$mod->question_id] = 'true';
                                    } elseif ($mod->result == 0) {
                                        $array[$mod->question_id] = 'false';
                                    } else {
                                        $array[$mod->question_id] = null;
                                    }
                                } else {
                                    $array[$mod->question_id] = 'null';
                                }
                            }

                            $true_false_questions[$true_false_question] = $array;
                        }
                    }
                }

                if (!empty($wrong_and_truecounts) && count($wrong_and_truecounts)) {
                    foreach ($wrong_and_truecounts as $wrgtru) {
                        $array = [];
                        $model = ExamResult::where('id', $wrgtru)->first();
                        if (!empty($model)) {
                            $wrong_and_truecounts[$wrgtru] = ['correct' => $model->correctAnswers() ?? 0, 'wrong' => $model->wrongAnswers() ?? 0];
                        }
                    }
                }

                return response()->json(['status' => 'success', 'data' => $exam_results, 'true_false_questions' => $true_false_questions, 'wrong_and_truecounts' => $wrong_and_truecounts]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function get_exam_result_answer_true_or_false(Request $request)
    {
        try {
            $array = [];
            $model = ExamResultAnswer::where('result_id', $request->input("result_id"))->get();
            $sections = Section::where("exam_id", $model[0]->result_model->exam_id)->pluck('id');
            $questions = ExamQuestion::whereIn("section_id", $sections)->get();
            foreach ($questions as $question) {
                $array[$question->id] = 'null';
            }

            foreach ($model as $mod) {
                if (isset($array[$mod->question_id])) {
                    $array[$mod->question_id] = $mod->result == true ? 'true' : 'false';
                } else {
                    $array[$mod->question_id] = 'null';
                }
            }

            return $array;
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }
    public function checkpayment(Request $request)
    {
        try {
            $payment = Payments::where("transaction_id", $request->input("ID"))->first();
            if ($payment) {
                $payment->update(['from_capital' => $request->all(), 'payment_status' => $request->input("STATUS") == 'FullyPaid' ? 1 : 2]);
            }
            return redirect(route("exams.show", $payment->exam->slug));
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }
}
