<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;

class RoutesController extends Controller
{
    public function standartpage($slug)
    {
        if ($slug != "contactus") {
            $data = standartpages($slug, 'slug');
            return view("frontend.pages.standartpage", compact("data"));
        } else {
            return view("frontend.pages.contactus");
        }
    }
    public function welcome()
    {
        try {
            return view('frontend.welcome');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function exams(Request $request)
    {
        try {
            $data = collect();
            $categories = collect();
            $category = collect();
            $subdomain = Session::get("subdomain")??null;

            if (isset($request->slug) && !empty($request->slug)) {
                $category = Category::where('slugs->az_slug', $request->slug)
                    ->orWhere('slugs->ru_slug', $request->slug)
                    ->orWhere('slugs->en_slug', $request->slug)
                    ->orWhere('slugs->tr_slug', $request->slug)
                    ->orderBy('id', 'DESC')
                    ->first();

                if (!empty($category->exams)) {
                    $data = $category->exams;
                }

                if (!empty($subdomain)) {
                    $data = $data->where('user_id', settings($subdomain)->id)->get();
                }

                if (!empty($category->sub)) {
                    foreach ($category->sub as $sub) {
                        $categories->push($sub);
                    }
                }
            } else {
                if (!empty($subdomain))
                    $data = exams(settings($subdomain)->id,'subdomain');
                else
                    $data = exams();
            }

            return view('frontend.exams.index', compact('data', 'categories', 'category'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function examinfo(Request $request,$subdomain=null, $slug)
    {
        try {
            $data = collect();
            if (is_numeric($slug) && preg_match('/^\d+$/', $slug)) {
                $data = Exam::where('id',$slug)->first();
            } else {
                $data = Exam::where('slug',$slug)->first();
            }
            return view('frontend.exams.show', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function blogs_front(Request $request,$subdomain=null, $slug)
    {
        try {
            $data = blogs($slug);
            return view('frontend.pages.blogs.show', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function teams_front(Request $request,$subdomain=null, $slug)
    {
        try {
            $data = teams($slug);
            return view('frontend.pages.teams.show', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function search(Request $request,$subdomain=null)
    {
        try {
            $key = $request->input("search");
            $exams = Exam::whereRaw('name like ?', ['%' . $key . '%'])->orWhereRaw('content like ?', ['%' . $key . '%'])->orderBy("order_number", 'ASC')->get();

            return view('frontend.exams.index', compact('exams', 'sub_categories', 'category', 'filters', 'search'));
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect('/notfound')->with("error", $e->getMessage());
        }
    }
    public function sitemap(Request $request)
    {
        try {
            $path = public_path('uploads/sitemaps/sitemap.xml');
            $sitemap=Sitemap::create();
            $sitemap->add(Url::create(route('user.page.welcome'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(1));

            foreach(standartpages() as $page){
                $sitemap->add(Url::create(route('pages.show',$page->slugs[app()->getLocale().'_slug']))
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                ->setPriority(0.9));
            }

            $sitemap->add(Url::create(route('login'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.9));
            $sitemap->add(Url::create(route('user.register'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.9));
            $sitemap->add(Url::create(route('email'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.9));
            $sitemap->add(Url::create(route('user.logout'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.9));
            $sitemap->add(Url::create(route('user.action.search'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.8));
            $sitemap->add(Url::create(route('user.front.exams.index'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.8));
            foreach(exams() as $exam){
                $sitemap->add(Url::create(route('user.front.exams.show',$exam->id))
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.7));
            }
            foreach(blogs(null,'blogs') as $exam){
                $sitemap->add(Url::create(route('user.blogs_front.show',$exam->slugs[app()->getLocale().'_slug']))
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.6));
            }
            foreach(blogs(null,'lessons') as $exam){
                $sitemap->add(Url::create(route('user.lessons_front.show',$exam->slugs[app()->getLocale().'_slug']))
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.7));
            }
            foreach(teams() as $exam){
                $sitemap->add(Url::create(route('user.lessons_front.show',$exam->slugs[app()->getLocale().'_slug']))
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.7));
            }
            $sitemap->add(Url::create(route('user.index'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.8));
            $sitemap->add(Url::create(route('user.lessons_front.index'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.8));
            $sitemap->add(Url::create(route('user.exam.results'))
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.7));
            $sitemap->add(Url::create('/notfound')
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.7));
            $sitemap=$sitemap->writeToFile($path);
            return $sitemap;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage(), 'line' => $e->getLine(), 'status' => 'error'];
        }
    }
}
