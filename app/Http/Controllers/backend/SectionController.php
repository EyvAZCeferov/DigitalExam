<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    use AuthorizesRequests;

    public function create($exam_id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'section-create');

        return view('backend.pages.sections.create', compact('exam_id'));
    }

    public function store(Request $request, $exam_id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'section-create');

        $rules = [
            'name' => ['required', 'string'],
        ];

        $request->validate($rules);

        $model = new Section();

        $model->exam_id = $exam_id;
        $model->name = $request->input('name');
        $model->time_range_sections = $request->input('time_range_sections') ?? 0;
        $model->save();

        if (isset($request->responseType) && $request->responseType == "json") {
            $sections = Section::where("exam_id", $exam_id)->orderBy("id", 'ASC')->get();
            return response()->json(['status' => 'success', 'message' => 'Yaradıldı!', 'data' => $sections]);
        } else {
            return redirect()->route('exams.questions', $exam_id)->with(['success' => 'Uğurla!']);
        }
    }

    public function edit($exam_id, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'section-update');

        $section = Section::where('exam_id', $exam_id)->findOrFail($id);
        return view('backend.pages.sections.edit', compact('section', 'exam_id'));
    }

    public function update(Request $request, $exam_id, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'section-update');

        $rules = [
            'name' => ['required', 'string']
        ];

        $request->validate($rules);

        $model = Section::where('exam_id', $exam_id)->findOrFail($id);
        $model->name = $request->input('name');
        $model->time_range_sections = $request->input('time_range_sections') ?? 0;
        $model->save();

        return redirect()->route('exams.questions', $exam_id)->with(['success' => 'Uğurla!']);
    }

    public function delete($exam_id, $id)
    {
        $this->authorizeForUser(auth('admins')->user(), 'section-delete');

        $model = Section::where('exam_id', $exam_id)->findOrFail($id);
        $model->delete();

        return redirect()->route('exams.questions', $exam_id)->with(['success' => 'Uğurla!']);
    }
}
