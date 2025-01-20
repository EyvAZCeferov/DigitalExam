<?php

namespace App\Http\Controllers\backend;

use App\Models\Exam;
use App\Models\MarkQuestions;
use App\Models\User;
use App\Models\Section;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use Illuminate\Support\Str;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use App\Models\ExamReferences;
use App\Models\ExamResultAnswer;
use App\Models\ExamStartPageIds;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class ExamController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-list');

        $trashed = false;

        if ($request->has("trashed")) {
            if ($request->trashed == 'trashed') {
                $trashed = true;
            }
        }

        if ($trashed == true)
            $exams = Exam::withoutGlobalScope('active_status')->with(['category'])->onlyTrashed()->orderBy('created_at')->get();
        else
            $exams = Exam::withoutGlobalScope('active_status')->with(['category'])->orderBy('created_at')->get();

        return view('backend.pages.exams.index', compact('exams', 'trashed'));
    }

    public function create()
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-create');
        return view('backend.pages.exams.create');
    }

    public function store(Request $request)
    {
        try {
            $this->authorizeForUser(auth('admins')->user(), 'exam-create');

            $rules = [
                'category_id' => ['required', 'exists:categories,id'],
                'content' => ['nullable', 'string'],
            ];

            $request->validate($rules);

            $model = new Exam();

            $image = null;
            if ($request->hasFile('image')) {
                $image = image_upload($request->file("image"), 'exams');
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

            $start_time = null;
            if ($request->input('start_time') != null)
                $start_time = Carbon::parse($request->input('start_time'));

            $model->category_id = $request->input('category_id');
            $model->name = $name;
            $model->slug = Str::slug($name['az_name']);
            $model->point = $request->input('point') ?? 0;
            $model->content = $description;
            $model->status = $request->input('status') ? 1 : 0;
            $model->order_number = $request->input('order_number') ?? 1;
            $model->price = $request->input('price') ?? 1;
            $model->endirim_price = $request->input('endirim_price') ?? 1;
            $model->user_id = auth('admins')->id();
            $model->user_type = "admins";
            $model->repeat_sound = $request->input('repeat_sound') ? 1 : 0;
            $model->show_result_user = $request->input('show_result_user') ? 1 : 0;
            $model->start_time = $start_time ?? null;
            $model->image = $image;
            $model->layout_type = $request->layout_type ?? 'standart';
            $model->save();
            dbdeactive();
            return redirect()->route('exams.index')->with(['success' => 'Uğurla!']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-update');
        $data = Exam::withoutGlobalScope('active_status')->withTrashed()->findOrFail($id);
        return view('backend.pages.exams.create', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-update');

        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'content' => ['nullable', 'string']
        ];

        $request->validate($rules);

        $model = Exam::withoutGlobalScope('active_status')->withTrashed()->findOrFail($id);

        if ($request->hasFile('image')) {
            $image = image_upload($request->file("image"), 'exams');
            $model->update(['image' => $image]);
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
        $start_time = null;
        if ($request->input('start_time') != null)
            $start_time = Carbon::parse($request->input('start_time'));

        $model->category_id = $request->input('category_id');
        $model->name = $name;
        $model->slug = Str::slug($name['az_name']);
        $model->point = $request->input('point') ?? 0;
        $model->content = $description;
        $model->status = $request->input('status') ? 1 : 0;
        $model->show_calc = $request->input('show_calc') ? 1 : 0;
        $model->order_number = $request->input('order_number') ?? 1;
        $model->price = $request->input('price') ?? 1;
        $model->endirim_price = $request->input('endirim_price') ?? 1;
        $model->start_time = $start_time ?? null;
        $model->layout_type = $request->layout_type ?? 'standart';
        $model->show_result_user = $request->input('show_result_user') ? 1 : 0;
        $model->save();

        $exam_start_pages = ExamStartPageIds::where("exam_id", $model->id)->get();
        foreach ($exam_start_pages as $val) {
            $val->delete();
        }

        if (!empty($request->exam_start_page_id)) {
            foreach ($request->exam_start_page_id as $id) {
                $page = new ExamStartPageIds();
                $page->exam_id = $model->id;
                $page->start_page_id = $id;
                $page->save();
            }
        }

        $references = ExamReferences::where("exam_id", $model->id)->get();
        foreach ($references as $val) {
            $val->delete();
        }

        if (!empty($request->exam_references)) {
            foreach ($request->exam_references as $id) {
                $page = new ExamReferences();
                $page->exam_id = $model->id;
                $page->reference_id = $id;
                $page->save();
            }
        }


        dbdeactive();
        return redirect()->route('exams.index')->with(['success' => 'Uğurla!']);
    }

    public function delete($id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-delete');

        $model = Exam::withoutGlobalScope('active_status')->withTrashed()->findOrFail($id);
        $model->delete();
        dbdeactive();
        return redirect()->route('exams.index')->with(['success' => 'Uğurla!']);
    }
    public function questions($exam_id, $section_id = null)
    {
        if (isset(request()->responseType) && request()->responseType == "json")
            $var = 'a';
        else
            $this->authorizeForUser(auth('admins')->user(), 'exam-question-list');

        $sections = Section::where('exam_id', $exam_id)->orderBy('created_at')->get();
        $section = $sections->first();
        if ($section_id) {
            $section = Section::where('exam_id', $exam_id)->orderBy('created_at')->findOrFail($section_id);
        }
        $questions = $section ? ExamQuestion::where('section_id', $section?->id)->orderBy('created_at')->get() : [];

        if (isset(request()->responseType) && request()->responseType == "json") {
            return response()->json(['status' => 'success', 'data' => $questions]);
        } else {
            return view('backend.pages.exams.questions.index', compact('questions', 'exam_id', 'sections', 'section'));
        }
    }
    public function createQuestion($exam_id, $section_id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-question-create');

        return view('backend.pages.exams.questions.create', compact('exam_id', 'section_id'));
    }
    public function storeQuestion(Request $request, $exam_id, $section_id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-question-create');

        $rules = [
            'type' => ['required', 'in:' . implode(',', ExamQuestion::TYPES)],
            'image' => [
                'file',
                'sometimes',
                'mimetypes:' . implode(',', ExamQuestion::ALLOWED_FILE_MIMES),
                'max:' . ExamQuestion::ALLOWED_FILE_SIZE_KB
            ],
        ];

        $request->validate($rules);

        $model = new ExamQuestion();
        if ($request->input('type') != 5) {
            $model->question = $request->input('question');
        } else {
            if ($request->hasFile('question_audio')) {
                $audio_file = file_upload($request->file("question_audio"), 'exam_questions');
                $model->question = $audio_file;
            }
        }
        $model->type = $request->input('type');
        $model->exam_section_id = $section_id;
        $model->layout = $request->layout;

        if ($request->hasFile('image')) {
            $model->addMedia($request->image)->toMediaCollection("exam_question");
        }

        $model->save();
        dbdeactive();
        return redirect()->route('exams.questions', [$exam_id, $section_id])->with(['success' => 'Uğurla!']);
    }
    public function editQuestion($exam_id, $section_id, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-question-update');

        $question = ExamQuestion::where('section_id', $section_id)->findOrFail($id);
        return view('backend.pages.exams.questions.edit', compact('question', 'exam_id', 'section_id'));
    }

    public function updateQuestion(Request $request, $exam_id, $section_id, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-question-update');

        $rules = [
            'type' => ['required', 'in:' . implode(',', ExamQuestion::TYPES)],
            'image' => [
                'file',
                'sometimes',
                'mimetypes:' . implode(',', ExamQuestion::ALLOWED_FILE_MIMES),
                'max:' . ExamQuestion::ALLOWED_FILE_SIZE_KB
            ],
        ];

        $request->validate($rules);

        $model = ExamQuestion::where('section_id', $section_id)->findOrFail($id);

        if ($request->input('type') != 5) {
            $model->question = $request->input('question');
        } else {
            if ($request->hasFile('question_audio')) {
                $audio_file = file_upload($request->file("question_audio"), 'exam_questions');
                $model->question = $audio_file;
            }
        }
        $model->type = $request->input('type');
        $model->exam_section_id = $section_id;
        $model->layout = $request->layout;

        if ($request->hasFile('image')) {
            $oldImage = $model->getMedia('exam_question')->first();
            if ($oldImage) {
                $oldImage->delete();
            }

            $model->addMedia($request->image)->toMediaCollection("exam_question");
        }

        $model->save();
        dbdeactive();
        return redirect()->route('exams.questions', [$exam_id, $section_id])->with(['success' => 'Uğurla!']);
    }

    public function deleteQuestion($exam_id, $section_id, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-question-delete');

        $model = ExamQuestion::where('section_id', $section_id)->findOrFail($id);
        // $model->clearMediaCollection('exam_question');
        $model->delete();
        dbdeactive();
        return redirect()->route('exams.questions', [$exam_id, $section_id])->with(['success' => 'Uğurla!']);
    }

    public function answers($exam_id, $section_id, $question_id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-answer-list');

        $question = ExamQuestion::findOrFail($question_id);
        $answers = ExamAnswer::where('question_id', $question_id)->orderBy('created_at')->get();
        return view('backend.pages.exams.answers.index', compact('answers', 'question_id', 'question', 'section_id', 'exam_id'));
    }

    public function createAnswer($exam_id, $section_id, $question_id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-answer-create');
        $question = ExamQuestion::findOrFail($question_id);
        if ($question->type == 3 && $question->answers->count() == 1) {
            return redirect()->route('exams.answers', [$exam_id, $section_id, $question_id]);
        }

        return view('backend.pages.exams.answers.create', compact('exam_id', 'section_id', 'question_id'));
    }

    public function storeAnswer(Request $request, $exam_id, $section_id, $question_id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-answer-create');

        $rules = [
            'exam_id' => ['required', 'exists:exams,id'],
            'question_id' => ['required', 'exists:exam_questions,id'],
            'answer' => ['required', 'string']
        ];

        $request->validate($rules);

        $model = new ExamAnswer();

        $model->answer = $request->input('answer');
        $model->correct = $request->input('correct') ? 1 : 0;
        $model->question_id = $question_id;

        $model->save();
        dbdeactive();
        return redirect()->route('exams.answers', [$exam_id, $section_id, $question_id])->with(['success' => 'Uğurla!']);
    }

    public function editAnswer($exam_id, $section_id, $question_id, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-answer-update');

        $answer = ExamAnswer::findOrFail($id);
        return view('backend.pages.exams.answers.edit', compact('answer', 'exam_id', 'section_id', 'question_id'));
    }

    public function updateAnswer(Request $request, $exam_id, $section_id, $question_id, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-answer-update');

        $rules = [
            'exam_id' => ['required', 'exists:exams,id'],
            'question_id' => ['required', 'exists:exam_questions,id'],
            'answer' => ['required', 'string'],
        ];

        $request->validate($rules);

        $model = ExamAnswer::findOrFail($id);

        $model->answer = $request->input('answer');
        $model->correct = $request->input('correct') ? 1 : 0;
        $model->question_id = $question_id;

        $model->save();
        dbdeactive();
        return redirect()->route('exams.answers', [$exam_id, $section_id, $question_id])->with(['success' => 'Uğurla!']);
    }

    public function deleteAnswer($exam_id, $section_id, $question_id, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'exam-answer-delete');

        $model = ExamAnswer::findOrFail($id);
        $model->delete();
        dbdeactive();
        return redirect()->route('exams.answers', [$exam_id, $section_id, $question_id])->with(['success' => 'Uğurla!']);
    }
    public function analyzeview(Request $request)
    {
        try {
            if ($request->method() == "POST") {
                try {
                    $results = ExamResult::orderBy("id", 'DESC');

                    if ($request->has('user_id') && !empty($request->input("user_id"))) {
                        $results = $results->where("user_id", $request->input('user_id'));
                    }

                    if ($request->has('exam_id') && !empty($request->input("exam_id"))) {
                        $results = $results->where("exam_id", $request->input('exam_id'));
                    }

                    $results = $results->with('answers', 'user', 'exam');
                    $results = $results->get();

                    return response()->json(['status' => 'success', 'results' => $results]);
                } catch (\Exception $e) {
                    return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
                }
            } else {
                $users = User::orderBy('id', 'DESC')->get();
                $exams = Exam::orderBy('id', 'DESC')->get();
                return view('backend.pages.exams.analyze', compact('users', 'exams'));
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function mark_unmark_question(Request $request)
    {
        try {
            $data = MarkQuestions::where("exam_id", $request->input('exam_id'))
                ->where("exam_result_id", $request->input('exam_result_id'))
                ->where("question_id", $request->input('question_id'))
                ->where("user_id", $request->input('user_id'))->first();
            $type = "warning";
            $message = trans("additional.messages.yenidenbaxisdancixarildi", [], $request->language ?? 'az');
            if (empty($data)) {
                $data = new MarkQuestions();
                $data->exam_id = $request->input('exam_id');
                $data->exam_result_id = $request->input('exam_result_id');
                $data->question_id = $request->input('question_id');
                $data->user_id = $request->input('user_id');
                $data->save();
                $type = "success";
                $message = trans("additional.messages.yenidenbaxisucunisaretlendi", [], $request->language ?? 'az');
            } else {
                $type = "warning";
                $message = trans("additional.messages.yenidenbaxisdancixarildi", [], $request->language ?? 'az');
                $data->delete();
            }

            $markedquestions = MarkQuestions::where("exam_id", $request->input('exam_id'))
                ->where("exam_result_id", $request->input('exam_result_id'))
                ->where("user_id", $request->input('user_id'))->pluck('question_id')->toArray();

            return response()->json(['status' => $type, 'data' => $markedquestions, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        } finally {
            dbdeactive();
        }
    }
}
