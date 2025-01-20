<?php

namespace App\Http\Controllers\frontend;

use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')->with('sub')->get();
        return view('frontend.pages.index', compact('categories'));
    }
    public function exams(Request $request)
    {
        try {
            $category = collect();
            $sub_categories = collect();
            $exams = collect();
            $filters = $request->filters;
            $search = $request->search ?? null;
            DB::transaction(function () use (&$category, &$sub_categories, &$exams, $request, $search) {
                if (isset($request->category) && !empty($request->category)) {
                    if (ctype_digit($request->category)) {
                        $category = Category::findOrFail($request->category);
                        $sub_categories = $category->sub;
                    } else {
                        $category = Category::where(function ($query) use ($request) {
                            $query->where('slugs->az_slug', 'like', '%' . $request->category . '%')
                                ->orWhere('slugs->ru_slug', 'like', '%' . $request->category . '%')
                                ->orWhere('slugs->en_slug', 'like', '%' . $request->category . '%');
                        })->first();

                        $sub_categories = $category->sub;
                    }
                }
                $exams = Exam::with('sections.questions');
                if (!empty($category) && isset($category->id) && !empty($category->id)) {
                    $exams = $exams->where('category_id', $category->id);
                }

                if (!empty($search)) {
                    $exams = $exams->whereRaw('LOWER(JSON_EXTRACT(`name`, "$.az_name")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.ru_name")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.en_name")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.az_description")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.ru_description")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.en_description")) like ?', ['%' . $search . '%']);
                }

                if (session()->has("subdomain")) {
                    $user = users(session()->get("subdomain"), 'subdomain');
                    $exams = $exams->where("user_id", $user->id);
                }

                $exams = $exams->orderBy("order_number", 'ASC')
                    ->get();

                if(!empty($category) && isset($category->id) && !empty($category->id)){
                    $category=null;
                }
            });
            return view('frontend.exams.index', compact('exams', 'sub_categories', 'category', 'filters', 'search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function exams_subdomain(Request $request,$subdomain=null)
    {
        try {
            $category = collect();
            $sub_categories = collect();
            $exams = collect();
            $filters = $request->filters;
            $search = $request->search ?? null;
            DB::transaction(function () use (&$category, &$sub_categories, &$exams, $request, $search) {
                if (isset($request->category) && !empty($request->category)) {
                    if (ctype_digit($request->category)) {
                        $category = Category::findOrFail($request->category);
                        $sub_categories = $category->sub;
                    } else {
                        $category = Category::where(function ($query) use ($request) {
                            $query->where('slugs->az_slug', 'like', '%' . $request->category . '%')
                                ->orWhere('slugs->ru_slug', 'like', '%' . $request->category . '%')
                                ->orWhere('slugs->en_slug', 'like', '%' . $request->category . '%');
                        })->first();

                        $sub_categories = $category->sub;
                    }
                }
                
                $exams = Exam::with('sections.questions');
                if (!empty($category) && isset($category->id) && !empty($category->id)) {
                    $exams = $exams->where('category_id', $category->id);
                }

                if (!empty($search)) {
                    $exams = $exams->whereRaw('LOWER(JSON_EXTRACT(`name`, "$.az_name")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.ru_name")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.en_name")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.az_description")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.ru_description")) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.en_description")) like ?', ['%' . $search . '%']);
                }

                if (session()->has("subdomain")) {
                    $user = users(session()->get("subdomain"), 'subdomain');
                    $exams = $exams->where("user_id", $user->id);
                }

                $exams = $exams->orderBy("order_number", 'ASC')
                    ->get();

                if(!empty($category) && isset($category->id) && !empty($category->id)){
                    $category=null;
                }
            });
            return view('frontend.exams.index', compact('exams', 'sub_categories', 'category', 'filters', 'search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function category_exam(Request $request,$category=null)
    {
        try {
            $categories=categories(null,null);
            $search=$request->search??null;
            return view('frontend.exams.category_exam', compact('categories', 'category','search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function category_exam_subdomain(Request $request,$subdomain=null,$category=null)
    {
        try {
            $categories=categories(null,null);
            $search=$request->search??null;
            return view('frontend.exams.category_exam', compact('categories', 'category','search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function showexam($slug)
    {
        try {
            session()->forget('savethisurl');
            $data = Exam::where("slug", $slug)->first();
            if (!empty($data)) {
                return view('frontend.exams.show', compact('data'));
            } else {
                return redirect('/notfound')->with('error', trans("additional.messages.examnotfound"));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function showexam_subdomain($subdomain=null,$slug)
    {
        try {
            session()->forget('savethisurl');
            $data = Exam::where("slug", $slug)->first();
            if (!empty($data)) {
                return view('frontend.exams.show', compact('data'));
            } else {
                return redirect('/notfound')->with('error', trans("additional.messages.examnotfound"));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function createoreditexam(Request $request)
    {
        try {
            $data = collect();
            $slug = strip_tags_with_whitespace($request->get("slug"));
            if (isset($slug) && !empty($slug)) {
                $data = Exam::where('slug', $slug)->where('user_id',Auth::guard("users")->id())->first();
            }
            return view('frontend.exams.create_edit_exams.index', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
