<?php

namespace App\Http\Controllers\frontend;

use App\Helpers\KapitalbankNew;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\User;
use App\Helpers\Epoint;
use App\Models\Category;
use App\Models\Payments;
use App\Models\ExamQuestion;
use App\Models\ExamAnswer;
use App\Models\CouponCodes;
use App\Models\Section;
use Illuminate\Support\Str;
use App\Models\MarkQuestions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Helpers\KapitalBank;
use App\Models\ExamResultAnswer;
use Illuminate\Support\Facades\Auth;

class ApisController extends Controller
{
    public function searchinfilled(Request $request)
    {
        try {
            if ($request->type == "exams") {
                if (isset($request->action) && !empty($request->action)) {
                    if ($request->action == "category") {
                        if ($request->category == "all") {
                            $data = exams(null, null);
                        } else {
                            $category = Category::where('slugs->az_slug', $request->category)
                                ->orWhere('slugs->ru_slug', $request->category)
                                ->orWhere('slugs->en_slug', $request->category)
                                ->where('status', true)
                                ->orderBy('id', 'DESC')
                                ->first();
                            if (!empty($category)) {
                                $data = Exam::where("category_id", $category->id)->get();
                            } else {
                                $data = [];
                            }
                        }
                    }
                } else {
                    $data = Exam::whereRaw('LOWER(JSON_EXTRACT(`name`, "$.az_name")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.ru_name")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.en_name")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.az_description")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.ru_description")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.en_description")) like ?', ['%' . $request->input('query') . '%'])
                        ->get();
                    return response()->json([
                        'status' => 'success',
                        "view" => view('frontend.' . $request->type . '.render_exams', compact('data'))->render()
                    ]);
                }
            } else {
                $data = User::whereRaw('LOWER(`name`) like ?', ['%' . $request->input('query') . '%'])
                    ->with('exams')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    "view" => view('frontend.' . $request->type . '.render_exams', compact('data'))->render()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function filterelements(Request $request)
    {
        try {
            $ids = $request->ids;
            $orderby = null;
            $data = collect();
            if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "asc") {
                $orderby = 'name->"$.' . $request->language . '_name"' . $request->orderby;
            } else if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "desc") {
                $orderby = 'name->"$.' . $request->language . '_name"' . $request->orderby;
            } else if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "random") {
                $orderby = "inrandomorder";
            } else if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "priceasc") {
                $orderby = 'price asc';
            } else if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "pricedesc") {
                $orderby = 'price desc';
            }

            if ($request->type == "exams") {
                if ($orderby != "inrandomorder") {
                    $data = Exam::whereIn('id', $ids)->orderByRaw($orderby)->get();
                } else {
                    $data = Exam::whereIn('id', $ids)->inRandomOrder()->get();
                }
            }
            return response()->json([
                'status' => 'success',
                "view" => view('frontend.exams.render_exams', compact('data'))->render()
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function callback(Request $request)
    {
        try {
            $epoint = new Epoint([
                "data" => $request->get("data"),
                "signature" => $request->get("signature")
            ]);

            if ($epoint->isSignatureValid()) {

                $json_string = $epoint->getDataAsJson();
                $json = $epoint->getDataAsObject();

                if ($json->status == "success") {
                    if (!empty($json->card_id)) {
                        //payments
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::info(['------------------CallBack Error------------------', $e->getMessage(), $e->getLine()]);
        }
    }
    public function success_error_page_payment(Request $request)
    {
        $url = $request->url();
        $type = "error";

        if (strpos($url, "success") !== false) {
            $type = "success";
        }

        return view("frontend.pages.payment_callback", compact('type'));
    }
    public function create_payment($req)
    {
        try {
            $exam_price = 0;
            $payment = payments($req['user_id'], $req['exam_id'], $req['exam_result_id'], null, $req['coupon_id'] ?? null, null);
            if ($req['amount'] == 0) {
                $exam = exams($req['exam_id'], 'id');
                $coupon_code = coupon_codes($req['coupon_id'], 'id');
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
            if ((empty($payment) || !isset($payment->id) && empty($payment->id)) || (!empty($payment) && isset($payment->id) && $payment->payment_status == 0)) {
                $user = [
                    'name' => $req['user_name'],
                    'email' => $req['user_email'],
                    'phone' => $req['user_phone'],
                    'id' => $req['user_id']
                ];
                $exam = [
                    'name' => $req['exam_name'],
                    'image' => $req['exam_image'],
                    'id' => $req['exam_id']
                ];
                $coupon = [
                    'name' => $req['coupon_name'] ?? null,
                    'discount' => $req['coupon_discount'] ?? null,
                    'code' => $req['coupon_code'] ?? null,
                    'type' => $req['coupon_type'] ?? null,
                    'id' => $req['coupon_id'] ?? null
                ];

                $data = $req;
                $data['nonce'] = createRandomCode('string', 11);
                $data['external_id'] = createRandomCode('string', 11);

                $payment = new Payments();
                $payment->token = $req['token'];
                $payment->amount = $req['amount'] == 0 ? $exam_price : $req['amount'];
                $payment->payment_status = 0;
                $payment->data = $data;
                $payment->user_id = $req['user_id'];
                $payment->exam_id = $req['exam_id'];
                $payment->coupon_id = $req['coupon_id'] ?? null;
                $payment->exam_result_id = $req['exam_result_id'];
                $payment->exam_data = $exam;
                $payment->user_data = $user;
                $payment->coupon_data = $coupon;
                $payment->transaction_id = createRandomCode('string', 11);
                $payment->save();
            }

            if ($payment->amount > 0) {
                $modelkapital = new KapitalBank();
                $bank = $modelkapital->createPayment($payment->transaction_id);
                $data['url'] = $bank;
                $payment->update(['data' => $data]);
                \Log::info(['------------------PulPal creation link-----------', $bank]);
                return $pulpal;
            } else {
                return $payment;
            }
        } catch (\Exception $e) {
            \Log::info(['------------------PulPal- Errorrr----------', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['status' => 'error', 'message' => $e->getMessage(), 'line' => $e->getLine()];
            Log::info(['------------------Payment Create Callback------------------', $e->getMessage(), $e->getLine()]);
        }
    }
    public function create_and_redirect_pay(Request $request)
    {
        try {
            $payment = Payments::where('user_id', Auth::guard('users')->id())
                ->where("exam_id", $request->input("exam_id"))
                ->where("payment_status", 0)->first();
            if (empty($payment) && !isset($payment->id)) {
                $payment = new Payments();
                $payment->user_id = Auth::guard('users')->id();
                $payment->exam_id = $request->input("exam_id");
                $payment->token = createRandomCode('string', 12);
                $exam = exams($request->input("exam_id"), 'id');
                $payment->exam_data = $exam;
                $payment->user_data = Auth::guard('users')->user();
                $amount = 0;
                if ($exam->endirim_price > 0) {
                    $amount = $exam->endirim_price;
                } else {
                    $amount = $exam->price;
                }
                $payment->amount = $amount;
                $payment->save();
            }

            $payment_system = new KapitalbankNew($payment->id);
            $payment_url = $payment_system->handle();

            if (!empty($payment_url)) {
                if (isset($payment_url['payment_url']) && !empty($payment_url['payment_url'])) {
                    return redirect($payment_url['payment_url']);
                } else {
                    return $payment_url['msg'];
                }
            } else {
                return $payment_url['msg'];
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }
    public function check_coupon_code(Request $request)
    {
        try {
            if (isset($request->code) && !empty($request->code)) {
                $code = CouponCodes::where("code", $request->code)
                    ->where("status", true)->first();
                if (!empty($code)) {
                    $exam = Exam::where("id", $request->exam)->first();
                    $new_price = $exam->price;

                    if ($exam->endirim_price) {
                        if ($code->type == "percent") {
                            $new_price = $exam->endirim_price - ($exam->endirim_price * $code->discount);
                        } else {
                            $new_price = $exam->endirim_price - $code->discount;
                        }
                    } else {
                        if ($code->type == "percent") {
                            $new_price = $exam->price - ($exam->price * $code->discount);
                        } else {
                            $new_price = $exam->price - $code->discount;
                        }
                    }

                    $result = '<span class="text text-info">' . trans('additional.pages.payments.coupon_info', [], $request->language) . ': ' . $code->name[$request->language . '_name'] . ': ' . $code->discount . ($code->type == 'percent' ? '%' : '₼') . '<br/>' . trans('additional.pages.payments.new_price', [], $request->language) . ' <span class="font-weight-bold text text-danger">' . $new_price . '₼</span> </span>';

                    return response()->json(['status' => 'success', 'data' => $result]);
                } else {
                    return response()->json(['status' => 'error', 'message' => trans("additional.messages.nocodefound", [], $request->language ?? 'az')]);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => trans("additional.messages.nocodefound", [], $request->language ?? 'az')]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function upload_image_editor(Request $request)
    {
        try {
            if ($request->hasFile('image')) {
                $image = image_upload_compressed($request->file("image"), 'editor_images');
                return response()->json(['location' => getImageUrl($image, 'editor_images')]);
            } else {
                return response()->json(['error' => 'Resim yüklenirken bir hata oluştu.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function questions_store(Request $request)
    {
        try {
            if (isset($request->question_id) && !empty($request->input("question_id")))
                $model = ExamQuestion::where('id', $request->input("question_id"))->first();
            else
                $model = new ExamQuestion();

            $model->question = modifyRelativeUrlsToAbsolute($request->input('question_input'));

            $questions = ExamQuestion::where("exam_section_id", $request->input('section_id'))->pluck('id');
            if ($request->input("question_type") == 3)
                $model->description = modifyRelativeUrlsToAbsolute($request->input('question_input2'));
            if ($request->input('question_type') == 5 || $request->input('question_type') == '5') {
                if ($request->hasFile('question_audio')) {
                    $audio_file = file_upload($request->file("question_audio"), 'exam_questions');
                    $model->file = $audio_file;
                }
            }
            $model->type = $request->input('question_type');
            $model->exam_section_id = $request->input('section_id');
            $model->layout = $request->input("question_layout");
            $model->order_number = $request->input('order_number') != 1 ? $request->input('order_number') : count($questions) + 1;
            $model->save();

            $examanswers = ExamAnswer::where("question_id", $model->id)->get();
            if (count($examanswers) > 0) {
                foreach ($examanswers as $value) {
                    $value->delete();
                }
            }


            if ($request->input("question_type") != 3 && $request->input("question_type") != 4) {
                $answers = array();
                foreach ($request->except(['_token', 'question_id', 'answers_count', 'section_id', 'question_type', 'exam_id', 'question', 'question_audio', 'language']) as $key => $value) {
                    if (strpos($key, 'answerres_') === 0) {
                        $modelAnswer = new ExamAnswer();
                        $modelAnswer->answer = modifyRelativeUrlsToAbsolute($value);
                        $answerNumber = explode('answerres_', $key)[1];
                        $modelAnswer->correct = isset($request->answers[$answerNumber]) ? true : false;
                        $modelAnswer->question_id = $model->id;
                        $modelAnswer->save();
                    }
                }
            } else if ($request->input("question_type") == 3) {
                $modelAnswer = new ExamAnswer();
                $modelAnswer->answer = !empty($request->input("textbox_0")) ? modifyRelativeUrlsToAbsolute($request->input("textbox_0")) : modifyRelativeUrlsToAbsolute($request->input("answerres_0"));
                $modelAnswer->correct = true;
                $modelAnswer->question_id = $model->id;
                $modelAnswer->save();
            } else if ($request->input('question_type') == 4) {
                $matchData = $request->input("match_data");
                $decodedMatchData = json_decode($matchData, true);
                if (!empty($decodedMatchData) && count($decodedMatchData) > 0) {
                    foreach ($decodedMatchData as $match) {
                        $modelAnswer = new ExamAnswer();
                        $modelAnswer->answer = json_encode($match);
                        $modelAnswer->correct = true;
                        $modelAnswer->question_id = $model->id;
                        $modelAnswer->save();
                    }
                }
            }


            dbdeactive();

            return response()->json(['status' => 'success', 'message' => trans("additional.messages.success", [], $request->input('language') ?? 'az'), 'model' => $model]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function get_question_data(Request $request)
    {
        try {
            $data = ExamQuestion::with('answers')->where('id', $request->input("question_id"))->first();
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function getsectiondata(Request $request)
    {
        try {
            $sections = Section::where('exam_id', $request->input("exam_id"))->orderBy('created_at')->get();
            $section = $sections->first();
            if (!empty($request->input('section_id'))) {
                $section = Section::where('exam_id', $request->input("exam_id"))->orderBy('created_at')->findOrFail($request->input('section_id'));
            }
            $data = $section ? ExamQuestion::where('exam_section_id', $section?->id)->orderBy('created_at')->get() : [];

            return response()->json(['status' => 'success', 'data' => $data, 'section_id' => isset($request->section_id) && !empty($request->section_id) ? $request->section_id : null]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function getsectioninformation(Request $request)
    {
        try {
            $data = sections($request->input('section_id'), 'id');
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function remove_questionorsection_data(Request $request)
    {
        try {
            if ($request->input("element_type") == 'question') {
                $data = ExamQuestion::where('id', $request->input("element_id"))->first();
                if (!empty($data) && isset($data->id)) {
                    $data->delete();
                    dbdeactive();
                    return response()->json(['status' => 'success', 'message' => trans('additional.messages.success', [], $request->input('language') ?? 'az')]);
                } else {
                    return response()->json(['status' => 'warning', 'message' => trans('additional.pages.exams.notfound', [], $request->input('language') ?? 'az')]);
                }
            } else if ($request->input("element_type") == 'section') {
                $data = Section::where('id', $request->input("element_id"))->first();
                if (!empty($data) && isset($data->id)) {
                    $data->delete();
                    dbdeactive();
                    return response()->json(['status' => 'success', 'message' => trans('additional.messages.success', [], $request->input('language') ?? 'az')]);
                } else {
                    return response()->json(['status' => 'warning', 'message' => trans('additional.pages.exams.notfound', [], $request->input('language') ?? 'az')]);
                }
            } else if ($request->input('element_type') == "product") {
                $data = Exam::where('id', $request->input("element_id"))->first();
                if (!empty($data) && isset($data->id)) {
                    $data->delete();
                    dbdeactive();
                    return response()->json(['status' => 'success', 'message' => trans('additional.messages.success', [], $request->input('language') ?? 'az')]);
                } else {
                    return response()->json(['status' => 'warning', 'message' => trans('additional.pages.exams.notfound', [], $request->input('language') ?? 'az')]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function setsectiondata(Request $request)
    {
        try {
            // DB::transaction(function () use ($request) {
            if (isset($request->selected_section_id) && !empty($request->input('selected_section_id'))) {
                $model = Section::where('id', $request->input('selected_section_id'))->first();
            } else {
                $model = new Section();
            }
            $model->exam_id = $request->input('exam_id');
            $model->name = $request->input('name');
            $model->time_range_sections = intval($request->input('time_range_sections')) ?? 0;
            $model->duration = intval($request->input('duration')) ?? 0;
            $model->wrong_point = floatval($request->input('wrong_point')) ?? 1;
            $model->random = $request->input('random') == 'on' ? true : false;
            $model->question_count = intval($request->input('question_count')) ?? null;
            $model->save();
            // });
            return response()->json(['status' => 'success', 'message' => 'Yaradıldı!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        } finally {
            dbdeactive();
        }
    }
    public function getexamsections(Request $request)
    {
        try {
            $sections = Section::where("exam_id", $request->input('exam_id'))->orderBy("id", 'ASC')->get();
            return response()->json(['status' => 'success', 'data' => $sections]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function get_markedquestions_users(Request $request)
    {
        try {
            $markedQuestionUsers = MarkQuestions::where('question_id', $request->input('question_id'))
                ->where("exam_id", $request->input('exam_id'))->whereNotNull('user_id')->whereHas('result', function ($query) {
                    $query->whereNotNull('point');
                })->with(['user', 'result', 'exam'])->get();
            return response()->json(['status' => 'success', 'data' => $markedQuestionUsers]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function get_show_user_which_answered(Request $request)
    {
        try {
            $exam_results = ExamResult::where('exam_id', $request->ids)
                ->with('answers.answer')
                ->orderByDesc('id')->get();
            if (!empty($exam_results)) {
                $datas = get_answer_choised($exam_results->pluck('id'), $request->question_id, $request->type_req, $request->value);
                if (!empty($datas) && count($datas) > 0) {
                    $data = '';
                    foreach ($datas as $dat) {
                        if (!empty($dat->result_model->user)) {
                            $data .= "<div class='my-1 mb-2 p-1 row' style='border-bottom:1px solid #000;'> <h6>" . $dat->result_model->user->name . " / " . $dat->result_model->user->email . "</h6>";
                            $data .= "<div class='text text-dark'>" . trans('additional.pages.exams.earned_point') . ": " . number_format($dat->result_model->point, 2) . "/ <span class='text-success'>" . $dat->result_model->exam->point . "</span> &nbsp;&nbsp;" . trans('additional.pages.exams.timespent') . ": <div class='hour_area d-inline-block text text-info'><span id='minutes'>" . formattedTime($dat->time_reply, 'minute') . "</span>:<span id='seconds'>" . formattedTime($dat->time_reply, 'seconds') . "</span></div>";

                            if ($request->type_req == 3) {
                                $data .= "&nbsp;&nbsp;<span class='" . ($dat->result == true ? 'text-success' : 'text-danger') . "'>" . trans('additional.pages.exams.youranswer') . ": " . htmlspecialchars($dat->value) . "</span>";
                            }

                            if ($request->type_req == 4) {
                                $leftside = '';
                                $rightside = '';

                                foreach (json_decode($dat->value, true) as $key => $val) {
                                    $leftside .= "<div class='column_element question_match_element'>" . $key . "</div>";
                                    $rightside .= "<div class='column_element answer_match_element'>" . $val . "</div>";
                                }

                                $data .= "&nbsp;&nbsp;<span class='" . ($dat->result == true ? 'text-success' : 'text-danger') . "'>" . trans('additional.pages.exams.youranswer') . ": <br/> <div class='question_content'>  <div class='match_questions_one'> <div class='column question_match_area' >" . $leftside . "</div><div class='column answer_match_area' >" . $rightside . "</div></span></div></div>";
                            }

                            $data .= "</div></div>";
                        }
                    }
                    return response()->json(['status' => 'success', 'data' => $data]);
                } else {
                    return response()->json(['status' => 'warning', 'message' => trans('additional.pages.exams.notfound')]);
                }
            } else {
                return response()->json(['status' => 'warning', 'message' => trans('additional.pages.exams.notfound')]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function set_question_value_on_session(Request $request)
    {
        try {
            $exam_id = $request->input('exam_id');
            $result_id = $request->input('result_id');
            $user_id = $request->input('user_id');
            $answer_id = $request->input('answer_id');
            $question_id = $request->input('question_id');
            $type_value = $request->input("type_value");
            $session_key = $result_id . $exam_id . $user_id . $question_id . $type_value;

            $examresultanswer = ExamResultAnswer::where("result_id", $result_id)->where("question_id", $question_id)->first();
            if (empty($examresultanswer)) {
                $question = ExamQuestion::find($question_id);
                $examresultanswer = new ExamResultAnswer();
                $examresultanswer->result_id = $result_id;
                $examresultanswer->question_id = $question_id;
                $examresultanswer->section_id = $question->exam_section_id;
                $examresultanswer->answer_id = null;
                $examresultanswer->result = null;
                $examresultanswer->time_reply = 0;
                $examresultanswer->save();
            }

            session()->put($session_key, $answer_id ?? '');
            return response()->json($session_key);
        } catch (\Exception $e) {
            sendBackupFailureEmail($e->getMessage(), $e->getLine());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
