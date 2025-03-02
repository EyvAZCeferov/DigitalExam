<?php

use App\Models\Exam;
use App\Models\User;
use App\Models\Blogs;
use App\Models\Teams;
use App\Models\Section;
use App\Models\Sliders;
use App\Models\Category;
use App\Models\Counters;
use App\Models\Settings;
use App\Models\ExamResult;
use App\Models\References;
use App\Models\CouponCodes;
use App\Models\ExamQuestion;
use App\Models\Payments;
use App\Models\ExamStartPage;
use App\Models\MarkQuestions;
use App\Models\StandartPages;
use App\Models\ExamReferences;
use App\Models\StudentRatings;
use App\Models\ExamResultAnswer;
use App\Models\ExamStartPageIds;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

if (!function_exists('answerChoice')) {
    function answerChoice($key): string
    {
        $choices = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        return $choices[$key] ?? $key;
    }
}

if (!function_exists('getImageUrl')) {
    function getImageUrl($image, $clasore)
    {
        $url = public_path('uploads/' . $clasore . '/' . $image);
        try {
            if (in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $tempurl = 'temp/' . $image;
                if (!File::exists(public_path($tempurl))) {
                    Image::cache(function ($image) use ($url, $tempurl) {
                        return $image->make($url)->save(public_path($tempurl));
                    });
                }
            } else {
                $tempurl = '/uploads/' . $clasore . '/' . $image;
            }

            return url($tempurl);
        } catch (\Exception $e) {
            Log::info([
                '----------------GET IMAGE ERROR-----------------',
                $e->getMessage(),
                $e->getLine()
            ]);
            return url($tempurl);
        }
    }
}

if (!function_exists('strip_tags_with_whitespace')) {
    function strip_tags_with_whitespace($string, $allowable_tags = null)
    {
        $string = str_replace('<', ' <', $string);
        $string = str_replace('&nbsp; ', ' ', $string);
        $string = str_replace('&nbsp;', ' ', $string);
        $string = strip_tags($string, $allowable_tags);
        $string = str_replace('  ', ' ', $string);
        $string = str_replace('\'', ' ', $string);
        $string = str_replace('"', ' ', $string);
        $string = trim($string);
        $string = preg_replace('/\\\\u\{[0-9A-Fa-f]{1,6}\}/', '', $string);
        $string = str_replace(array("\xC2\xA0", '&nbsp;'), '', $string);
        return trim($string);
    }
}

if (!function_exists('createRandomCode')) {
    function createRandomCode($type = "int", $length = 4)
    {
        if ($type == "int") {
            if ($length == 4) {
                return random_int(1000, 9999);
            }
        } elseif ($type == "string") {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
    }
}

if (!function_exists('dbdeactive')) {
    function dbdeactive()
    {
        DB::connection()->disconnect();
        Cache::flush();
    }
}

if (!function_exists('image_upload')) {
    function image_upload($image, $clasor, $imagename = null)
    {
        try {
            $filename = $imagename ?? time() . '.' . $image->extension();
            $image->storeAs($clasor, $filename, 'uploads');
            return $filename;
        } catch (\Exception $e) {
            Log::info([
                '------------------IMAGE UPLOAD ERROR-----------------',
                $e->getMessage(),
                $e->getLine(),
            ]);
        }
    }
}

if (!function_exists('image_upload_compressed')) {
    function image_upload_compressed($image, $clasor, $imagename = null)
    {
        try {
            $filename = $imagename ?? time() . '.' . $image->extension();

            $tempPath = sys_get_temp_dir() . '/' . $filename;
            Image::make($image)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($tempPath, 75);

            $filePath = $clasor . '/' . $filename;
            Storage::disk('uploads')->put($filePath, file_get_contents($tempPath));

            unlink($tempPath);

            return $filename;
        } catch (\Exception $e) {
            Log::info([
                '------------------IMAGE UPLOAD ERROR-----------------',
                $e->getMessage(),
                $e->getLine(),
            ]);

            return null; // Hata durumunda null döndür
        }
    }
}

if (!function_exists('file_upload')) {
    function file_upload($file, $clasor, $name = null)
    {
        $filename = $name ?? time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs($clasor, $filename, 'uploads');
        return $filename;
    }
}

if (!function_exists('delete_image')) {

    function delete_image($image, $clasor)
    {
        if (Storage::disk('uploads')->exists($clasor . '/' . $image)) {
            Storage::disk('uploads')->delete($clasor . '/' . $image);
            return true;
        }
        return false;
    }
}

if (!function_exists('queuework')) {
    function queuework()
    {
        while (true) {
            try {
                Artisan::call('queue:work');
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            sleep(1);
        }
    }
}

if (!function_exists('count_endirim_faiz')) {
    function count_endirim_faiz($price, $endirim_price)
    {
        $model = 0;
        if ($price > 0 && $endirim_price > 0) {
            $discount_percentage = (($price - $endirim_price) / $price) * 100;
            $formatted_discount = number_format($discount_percentage, 2);
            $model = $formatted_discount;
        }
        return Cache::rememberForever("count_endirim_faiz" . $price . $endirim_price, fn() => $model);
    }
}

if (!function_exists('settings')) {
    function settings($key = null)
    {
        $mdsettings = Settings::latest()->first();
        if (isset($key) && !empty($key)) {
            $subdomain = session()->has("subdomain") ? session()->get("subdomain") : null;
            if (!empty($subdomain)) {
                $mds = users($subdomain, 'subdomain');
                if (!empty($mds) && isset($mds->id)) {
                    if ($key == "name") {
                        $model = isset($mds->name) && !empty($mds->name) ? $mds->name : $mdsettings->name[app()->getLocale() . '_name'];
                    } else if ($key == "description") {
                        $model = isset($mds->name) && !empty($mds->name) ? $mds->name . '-' . $mds->subdomain : $mdsettings->description[app()->getLocale() . '_description'];
                    } else if ($key == "logo") {
                        $model = isset($mds->picture) && !empty($mds->picture) ? getImageUrl($mds->picture, 'users') : getImageUrl($key, 'settings');
                        // $model = getImageUrl($mdsettings->logo, 'settings');
                    } else if ($key == "logo_white") {
                        $model = getImageUrl($mdsettings->logo_white, 'settings');
                    }
                } else {
                    if ($key == "name") {
                        $model = $mdsettings->name[app()->getLocale() . '_name'];
                    } else if ($key == "description") {
                        $model = $mdsettings->description[app()->getLocale() . '_description'];
                    } else if ($key == "logo") {
                        $model = getImageUrl($mdsettings->logo, 'settings');
                    } else if ($key == "logo_white") {
                        $model = getImageUrl($mdsettings->logo_white, 'settings');
                    }
                }
            } else {
                if ($key == "name") {
                    $model = $mdsettings->name[app()->getLocale() . '_name'];
                } else if ($key == "description") {
                    $model = $mdsettings->description[app()->getLocale() . '_description'];
                } else if ($key == "logo") {
                    $model = getImageUrl($mdsettings->logo, 'settings');
                } else if ($key == "logo_white") {
                    $model = getImageUrl($mdsettings->logo_white, 'settings');
                }
            }
        } else {
            $model = $mdsettings;
        }
        return Cache::rememberForever("settings" . $key . session()->getId(), fn() => $model);
    }
}

if (!function_exists('standartpages')) {
    function standartpages($key = null, $type = "slug")
    {
        if (isset($key) && !empty($key) && $type == "type") {
            $model = StandartPages::where('type', $key)->first();
        } else if (isset($key) && !empty($key) && $type == "slug") {
            $model = StandartPages::where('slugs->az_slug', $key)->orWhere('slugs->ru_slug', $key)->orWhere('slugs->en_slug', $key)->first();
        } else {
            $model = StandartPages::orderBy('id', 'ASC')->get();
        }
        return Cache::rememberForever("standartpages" . $key . $type, fn() => $model);
    }
}

if (!function_exists('categories')) {
    function categories($key = null, $type = "slug")
    {
        if ($type == "slug") {
            $model = Category::where('slugs->az_slug', $key)->orWhere('slugs->ru_slug', $key)->orWhere('slugs->en_slug', $key)->first();
        } else if ($type == "onlyparent") {
            $model = Category::whereNull('parent_id')->orderBy('order_number', 'DESC')->get();
        } else if ($type == "exammedcats") {
            $model = Category::whereHas('exams')->orderBy('order_number', 'DESC')->get();
        } else if ($type == "id") {
            $model = Category::where('id', $key)->first();
        } else {
            $model = Category::orderBy('order_number', 'DESC')->get();
        }
        return Cache::rememberForever("categories" . $key . $type, fn() => $model);
    }
}

if (!function_exists('sections')) {
    function sections($key = null, $type = "exammed")
    {
        if ($type == "exammed") {
            $model = Section::whereHas('questions')->select('name', DB::raw('MAX(id) as id'))
                ->groupBy('name')->get();
        } else if ($type == "id") {
            $model = Section::where('id', $key)->first();
        } else {
            $model = Section::select('name', DB::raw('MAX(id) as id'))
                ->groupBy('name')
                ->orderBy('id', 'DESC')->get();
        }
        return Cache::rememberForever("sections" . $key . $type, fn() => $model);
    }
}

if (!function_exists('users')) {
    function users($key = null, $type = "exammed")
    {
        if ($type == "exammed") {
            $model = User::where('user_type', 2)->orderBy("id", "DESC")->whereHas('exams')->get();
        } else if ($type == "company") {
            $model = User::where('user_type', 2)->orderBy("id", "DESC")->get();
        } else if ($type == "subdomain") {
            $model = User::where('subdomain', $key)->where('user_type', 2)->orderBy("id", "DESC")->first();
        } else if ($type == "id") {
            $model = User::where('id', $key)->first();
        } else {
            $model = User::orderBy("id", "DESC")->whereHas('exams')->get();
        }
        return Cache::rememberForever("users" . $key . $type, fn() => $model);
    }
}

if (!function_exists('counters')) {
    function counters()
    {
        $model = Counters::orderBy('order_number', 'ASC')->where('status', true)->get();
        return Cache::rememberForever("counters", fn() => $model);
    }
}

if (!function_exists('exams')) {
    function exams($key = null, $type = "id")
    {
        if (isset($key) && $type == "id") {
            $model = Exam::where('id', $key)->first();
        } else if (isset($key) && $type == "slug") {
            $model = Exam::where("slug", $key)->first();
        } else if (isset($key) && $type == "subdomain") {
            $model = Exam::where("user_id", $key)->orderBy("id", 'DESC');
            if (session()->has("subdomain")) {
                $user = users(session()->get("subdomain"), 'subdomain');
                if (!empty($user))
                    $model = $model->where("user_id", $user->id);
            }
            $model = $model->get();
        } else if (isset($key) && $type == "search") {
            $model = Exam::whereRaw('LOWER(JSON_EXTRACT(`name`, "$.az_name")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.ru_name")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.en_name")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`description`, "$.az_description")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`description`, "$.ru_description")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`description`, "$.en_description")) like ?', ['%' . $key . '%'])
                ->orderBy("order_number", 'ASC');
            if (session()->has("subdomain")) {
                $user = users(session()->get("subdomain"), 'subdomain');
                if (!empty($user))
                    $model = $model->where("user_id", $user->id);
            }
            $model = $model->orderBy("id", 'DESC')->get();
        } else if (empty($key) && $type == "most_used_tests") {
            $model = Exam::with([
                'results' => function ($query) {
                    $query->orderBy('point', 'DESC');
                }
            ])->orderByDesc('id');
            if (session()->has("subdomain")) {
                $user = users(session()->get("subdomain"), 'subdomain');
                if (!empty($user))
                    $model = $model->where("user_id", $user->id);
            }
            $model = $model->orderBy("id", 'DESC')->get();
        } else {
            $model = Exam::where('status', true)->orderBy("id", 'DESC')->orderBy("order_number", 'ASC');
        }

        return Cache::rememberForever("exams" . $key . $type, fn() => $model);
    }
}

if (!function_exists('sliders')) {
    function sliders()
    {
        $model = Sliders::where('status', true)->orderBy('id', 'DESC')->get();
        return Cache::rememberForever("sliders", fn() => $model);
    }
}

if (!function_exists('student_ratings')) {
    function student_ratings()
    {
        $model = StudentRatings::where('status', true)->orderBy('order_number', 'ASC')->get();
        return Cache::rememberForever("student_ratings", fn() => $model);
    }
}

if (!function_exists('blogs')) {
    function blogs($key = null)
    {
        if (isset($key) && !empty($key)) {
            $model = Blogs::where('status', true)
                ->where('slugs->az_slug', $key)->orWhere('slugs->ru_slug', $key)->orWhere('slugs->en_slug', $key)
                ->first();
        } else {
            $model = Blogs::where('status', true)->orderBy('id', 'DESC')->get();
        }
        return Cache::rememberForever("blogs" . $key, fn() => $model);
    }
}

if (!function_exists('teams')) {
    function teams($key = null)
    {
        if (isset($key) && !empty($key)) {
            $model = Teams::where('slugs->az_slug', $key)->orWhere('slugs->ru_slug', $key)->orWhere('slugs->en_slug', $key)
                ->first();
        } else {
            $model = Teams::orderBy('order_number', 'ASC')->get();
        }
        return Cache::rememberForever("teams" . $key, fn() => $model);
    }
}

if (!function_exists('exam_answered')) {
    function exam_answered($auth_id, $exam_id)
    {
        $model = ExamResult::where('user_id', $auth_id)->where('exam_id', $exam_id)->first();
        return Cache::rememberForever("exam_answered" . $auth_id . $exam_id, fn() => $model);
    }
}

if (!function_exists('exam_start_page')) {
    function exam_start_page($key = null, $type = "default")
    {
        if ($type == "default") {
            $model = ExamStartPage::orderBy("order_number", 'ASC')->where('default', true)->first();
        } else if ($type == "expectdefault") {
            $model = ExamStartPage::orderBy("order_number", 'ASC')->where('default', false)->get();
        } else {
            $model = ExamStartPage::orderBy("order_number", 'ASC')->get();
        }
        return Cache::rememberForever("exam_start_page" . $key . $type, fn() => $model);
    }
}

if (!function_exists('coupon_codes')) {
    function coupon_codes($key = null, $type = "default")
    {
        if ($type == "default") {
            $model = CouponCodes::where("status", 'ASC')->orderBy('id', 'DESC')->first();
        } else if ($type == "code") {
            $model = CouponCodes::where("code", $key)->first();
        } else if ($type == "id") {
            $model = CouponCodes::where("id", $key)->first();
        } else {
            $model = CouponCodes::orderBy('id', 'DESC')->get();
        }
        return Cache::rememberForever("coupon_codes" . $key . $type, fn() => $model);
    }
}

if (!function_exists('payments')) {
    function payments($auth_id = null, $exam_id = null, $exam_result_id = null, $transaction_id = null, $coupon_id = null, $id = null)
    {
        $model = Payments::orderBy('id', 'DESC');
        if (isset($auth_id) && !empty($auth_id)) {
            $model = $model->where("user_id", $auth_id);
        }

        if (isset($exam_id) && !empty($exam_id)) {
            $model = $model->where("exam_id", $exam_id);
        }

        if (isset($exam_result_id) && !empty($exam_result_id)) {
            $model = $model->where("exam_result_id", $exam_result_id);
        }

        if (isset($transaction_id) && !empty($transaction_id)) {
            $model = $model->where("transaction_id", $transaction_id);
        }

        if (isset($coupon_id) && !empty($coupon_id)) {
            $model = $model->where("coupon_id", $coupon_id);
        }

        if (isset($id) && !empty($id)) {
            $model = $model->where("id", $id);
        }

        $model = $model->where('payment_status', 0);
        $model = $model->get();
        if (count($model) == 1) {
            $model = $model[0];
        }
        return Cache::rememberForever("payments" . $auth_id . $exam_id . $exam_result_id . $transaction_id . $coupon_id . $id, fn() => $model);
    }
}

if (!function_exists('references')) {
    function references($key = null, $type = "asc")
    {
        if ($type == "asc") {
            $model = References::orderBy('order_number', 'ASC')->get();
        } else {
            $model = References::orderBy('id', 'DESC')->get();
        }
        return Cache::rememberForever("references" . $key . $type, fn() => $model);
    }
}

if (!function_exists('exist_on_model')) {
    function exist_on_model($key = null, $data_id = null, $type = "references")
    {
        if ($type == "references") {
            $model = ExamReferences::where("exam_id", $data_id)->where("reference_id", $key)->first();
        } elseif ($type == "start_page") {
            $model = ExamStartPageIds::where("exam_id", $data_id)->where("start_page_id", $key)->first();;
        }
        return Cache::rememberForever("exist_on_model" . $key . $data_id . $type, fn() => $model);
    }
}

if (!function_exists('question_is_marked')) {
    function question_is_marked($question_id, $exam_id, $exam_result_id, $user_id)
    {
        $model = MarkQuestions::where("exam_id", $exam_id)
            ->where("exam_result_id", $exam_result_id)
            ->where("question_id", $question_id)
            ->where("user_id", $user_id)->first();
        return Cache::rememberForever("question_is_marked" . $question_id . $exam_id . $exam_result_id . $user_id, fn() => $model);
    }
}

if (!function_exists('int_to_abcd_value')) {
    function int_to_abcd_value($key)
    {
        $model = '';
        if ($key == 0) {
            $model = "A";
        } else if ($key == 1) {
            $model = "B";
        } else if ($key == 2) {
            $model = "C";
        } else if ($key == 3) {
            $model = "D";
        } else if ($key == 4) {
            $model = "E";
        } else if ($key == 5) {
            $model = "F";
        } else if ($key == 6) {
            $model = "G";
        } else if ($key == 7) {
            $model = "H";
        }
        return Cache::rememberForever("int_to_abcd_value" . $key, fn() => $model);
    }
}

if (!function_exists('answer_result_true_or_false')) {
    function answer_result_true_or_false($question_id, $value)
    {
        $model = null;
        if ($value != null) {
            $question = ExamQuestion::where("id", $question_id)->first();
            if (!empty($question->correctAnswer())) {
                if ($question->type == 1 || $question->type == 5) {
                    if ($question->correctAnswer()->id == $value) {
                        $model = true;
                    } else {
                        $model = false;
                    }
                } else if ($question->type == 2) {
                    if (!empty($question->correctAnswer()->where('id', $value)->first())) {
                        $model = true;
                    } else {
                        $model = false;
                    }
                } else if ($question->type == 3) {
                    if (strip_tags_with_whitespace($question->correctAnswer()->answer) == strip_tags_with_whitespace($value)) {
                        $model = true;
                    } else {
                        $model = false;
                    }
                } else if ($question->type == 4) {
                    $questions = [];
                    $answers = [];
                    foreach ($question->correctAnswer() as $key => $val) {
                        $json_answers = json_decode($val, true);
                        $questions[] = ['content' => $json_answers['question_content']];
                        $answers[] = ['content' => $json_answers['answer_content']];
                    }
                    $newArray = array_combine(
                        array_column($questions, 'content'),
                        array_column($answers, 'content')
                    );
                    $newArrayEncoded = [];
                    foreach ($newArray as $key => $val) {
                        $newArrayEncoded[strip_tags_with_whitespace($key)] = strip_tags_with_whitespace($val);
                    }
                    $newArray2 = [];
                    foreach (json_decode($value, true) as $key => $value) {
                        $newArray2[strip_tags_with_whitespace($key)] = strip_tags_with_whitespace($value);
                    }
                    $model = ($newArrayEncoded === $newArray2) ? true : false;
                }
            }
        }
        return Cache::rememberForever("answer_result_true_or_false" . $question_id . $value, fn() => $model);
    }
}

if (!function_exists('your_answer_result_true_or_false')) {
    function your_answer_result_true_or_false($question_id, $value, $result_id)
    {
        // $cacheKey = "your_answer_result_true_or_false" . $question_id . $result_id . $value;

        // return Cache::rememberForever($cacheKey, function () use ($question_id, $value, $result_id) {
            $question_result = ExamResultAnswer::where("question_id", $question_id)
                ->where('result_id', $result_id)
                ->first();

            if (empty($question_result)) {
                return null;
            }

            if ($question_result->question->type == 1 || $question_result->question->type == 5) {
                return $question_result->answer_id == $value;
            } elseif ($question_result->question->type == 2) {
                return in_array($value, $question_result->answers);
            } elseif ($question_result->question->type == 3) {
                return $question_result->value !== null ? $question_result->value : null;
            }

            return null;
        // });
    }
}
if (!function_exists('exam_result_answer_true_or_false')) {
    function exam_result_answer_true_or_false($question_id, $result_id)
    {
        $result = 'null';
        $model = ExamResultAnswer::where('question_id', $question_id)->where('result_id', $result_id)->first();
        if (!empty($model) && isset($model->id)) {
            if ($model->result == true) {
                $result = 'true';
            } else {
                $result = 'false';
            }
        }

        return Cache::rememberForever("exam_result_answer_true_or_false"  . $question_id . $result_id, fn() => $result);
    }
}

if (!function_exists('exam_for_profile')) {
    function exam_for_profile($type, $auth_id)
    {
        $model = Exam::orderBy('id', 'DESC');
        $user = users($auth_id, 'id');
        if ($user->user_type == 2)
            $model = $model->where("user_id", $auth_id);

        if ($type == "active") {
            $model = $model->whereHas('results', function ($query) {
                $query->orderBy("id", 'DESC');
                $query->whereBetween('created_at', [Carbon::now()->subDays(10), Carbon::now()]);
            });
        } else {
            $model = $model->whereHas('results', function ($query) {
                $query->orderBy("id", 'DESC');
                $query->whereBetween('created_at', [Carbon::now()->subDays(50), Carbon::now()->subDays(10)]);
            });
        }

        $model = $model->get();
        return Cache::rememberForever("exam_for_profile"  . $type . $auth_id, fn() => $model);
    }
}

if (!function_exists('create_dns_record')) {
    function create_dns_record($domain)
    {
        try {
            $recordType = 'A';
            $recordContent = env('CL_ZN_IP');
            $cl_ZN_ID = env('CL_ZN_ID');
            $url = "https://api.cloudflare.com/client/v4/zones/" . $cl_ZN_ID . "/dns_records";

            $client = new Client();
            $response = $client->request('POST', $url, [
                'headers' => [
                    'X-Auth-Email' => env('CL_AC_MAIL'),
                    'X-Auth-Key' => env('CL_API_TOKEN'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'type' => $recordType,
                    'name' => $domain,
                    'content' => $recordContent,
                    'proxied' => true,
                    'ttl' => 300
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                $responseData = json_decode($response->getBody(), true);
                create_apache_vhost($domain);
                purge_cache();
                // return response()->json(['message' => 'DNS kaydı oluşturuldu.', 'data' => $responseData]);
            } else {
                Log::info([
                    'CREATE DNS RECORD ERROR',
                    $statusCode
                ]);
                return response()->json(['error' => 'API isteği başarısız oldu. Durum Kodu:' . $statusCode]);
            }
        } catch (\Exception $e) {
            return [
                "CREATEDNSRECORD ERROR",
                $e->getMessage(),
                $e->getLine()
            ];
            Log::info([
                'CREATE DNS RECORD ERROR',
                $e->getMessage(),
                $e->getLine()
            ]);
        }
    }
}

if (!function_exists('purge_cache')) {
    function purge_cache()
    {
        try {
            $url = "https://api.cloudflare.com/client/v4/zones/" . env('CL_ZN_ID') . "/purge_cache";
            $client = new Client();
            $response = $client->request('DELETE', $url, [
                'headers' => [
                    'X-Auth-Email' => env('CL_AC_MAIL'),
                    'X-Auth-Key' => env('CL_API_TOKEN'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'purge_everything' => true,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                return response()->json(['message' => 'Önbellek temizlendi.']);
            } else {
                Log::info([
                    'PURGE CACHE ERROR',
                    $statusCode
                ]);
                return response()->json(['error' => 'API isteği başarısız oldu. Durum Kodu:' . $statusCode]);
            }
        } catch (\Exception $e) {
            Log::info([
                'PURGE CACHE ERROR',
                $e->getMessage(),
                $e->getLine()
            ]);
        }
    }
}

if (!function_exists('create_apache_vhost')) {
    function create_apache_vhost($domain, $port = 8080)
    {
        $ip = env('CL_ZN_IP');
        // Define key parameters
        $domainWithoutWWW = preg_replace('/^www\./', '', $domain);
        $serverAlias = "www." . $domain;
        $docRoot = "/home/digital/sites/digitalexam.az/public";
        $confFilePath = "/etc/httpd/vhost.d/" . $domainWithoutWWW . ".conf";
        $accessLog = "/etc/httpd/vhost_logs/" . $domainWithoutWWW . "_access";
        $errorLog = "/etc/httpd/vhost_logs/" . $domainWithoutWWW . "_error";
        $phpSocketPath = "/var/run/php-fpm/php82w-digital.sock";

        $vhostConfig = "
<VirtualHost $ip:$port >
    ServerName $domainWithoutWWW
    ServerAlias $serverAlias
    DocumentRoot $docRoot

    SetEnvIf X-Forwarded-Proto https HTTPS=on
    <IFModule proxy_fcgi_module>
        ProxyFCGISetEnvIf \"true\" SCRIPT_FILENAME \"$docRoot%{reqenv:SCRIPT_NAME}\"
        <FilesMatch \.php$>
            SetHandler \"proxy:unix:$phpSocketPath|fcgi://localhost/\"
        </FilesMatch>
    </IFModule>

    <Directory \"$docRoot\">
        AllowOverride All
        Require all granted
    </Directory>

    CustomLog $accessLog combined
    ErrorLog $errorLog
    DirectoryIndex index.php index.html index.htm
</VirtualHost>
    ";

        if (!file_exists($docRoot)) {
            mkdir($docRoot, 0755, true);
            file_put_contents($docRoot . "/index.html", "<h1>Welcome to $domain</h1>");
        }

        try {
            file_put_contents($confFilePath, $vhostConfig);

            shell_exec("systemctl reload httpd");

            return response()->json(['message' => 'Apache VirtualHost created successfully for ' . $domain]);
        } catch (\Exception $e) {
            // Log any error
            Log::error([
                'CREATE VHOST ERROR',
                $e->getMessage(),
                $e->getLine()
            ]);
            return [
                "CREATEVHOST ERROR",
                $e->getMessage(),
                $e->getLine()
            ];
        }
    }
}

if (!function_exists('modifyRelativeUrlsToAbsolute')) {
    function modifyRelativeUrlsToAbsolute($content)
    {
        $domain = 'https://digitalexam.az';
        $pattern = '/<img.*?src=[\"\'](.*?)\.\.\/(.*?)["\']/';
        $replacement = '<img src="' . $domain . '/$2"';
        $modifiedContent = preg_replace($pattern, $replacement, $content);
        return $modifiedContent;
    }
}

if (!function_exists('calculate_exam_result')) {
    function calculate_exam_result($exam_result_id)
    {
        try {
            $examResult = ExamResult::where("id", $exam_result_id)->first();
            if (!empty($examResult) && isset($examResult->id)) {
                $exam = Exam::where('id', $examResult->exam_id)->first();
                if (!empty($exam) && isset($exam->id)) {
                    $examsections = Section::where("exam_id", $exam->id);
                    if (session()->has('selected_section')) {
                        $examsections = $examsections->where('id', session()->get('selected_section'));
                    }

                    $examsections = $examsections->pluck('id');
                    $examquestions = ExamQuestion::orderBy("id", 'DESC')->whereIn("exam_section_id", $examsections)->get();
                    $exampoint = $exam->point;
                    $correctAnswers = $examResult->correctAnswers();
                    $examquestionscount = count($examquestions);
                    $model = 0;
                    if ($exam->layout_type == "sat") {
                        $wrongpoint = 0;
                        $wrongAnswers = $examResult->wrongAnswers(false);
                        if (!empty($wrongAnswers)) {
                            foreach ($wrongAnswers as $wronganswer) {
                                $wrongpoint += floatval($wronganswer->section->wrong_point);
                            }
                        }
                        $model = $exam->point - $wrongpoint;
                    } else {
                        $model = $examquestionscount > 0 ? ($correctAnswers / $examquestionscount) * $exampoint : $correctAnswers * $exampoint;
                    }
                    return $model ?? 200;
                }
            }
            return 200;
        } catch (\Exception $e) {
            return [$e->getMessage(), $e->getLine()];
        }
    }
}

if (!function_exists('calculateforsection')) {
    function calculateforsection($result_id, $section_number)
    {
        try {
            $result = ExamResult::where('id', $result_id)->first();
            $point = 200;
            $sections = collect();
            if ($section_number == 1) {
                $sections = Section::where('exam_id', $result->exam_id)->orderBy("id", "ASC")->take(2)->pluck('id');
            } else {
                $sections = Section::where('exam_id', $result->exam_id)->orderBy("id", "DESC")->take(2)->pluck('id');
            }

            $wrongpoint = 0;
            $answers = ExamResultAnswer::whereIn("section_id", $sections)
                ->where('result_id', $result->id)
                ->where("result", false)
                ->with('section')
                ->whereNull("deleted_at")
                ->orderBy('id', 'DESC')
                ->distinct('question_id')
                ->get();

            if (!empty($answers)) {
                foreach ($answers as $wronganswer) {
                    $wrongpoint += floatval($wronganswer->section->wrong_point);
                }
            }

            $point = 800 - $wrongpoint;
            $point = customRound($point);

            return $point;
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }
    }
}

if (!function_exists('customRound')) {
    function customRound($number)
    {
        if (is_string($number)) {
            $number = str_replace(',', '', $number);
        }
        $number = floatval($number);


        if (is_float($number)) {
            $number = round($number, -1);
        }

        return $number;
    }
}

if (!function_exists('exam_result')) {
    function exam_result($exam_id, $auth_id)
    {
        $model = ExamResult::orderBy('id', 'DESC');

        if (isset($auth_id) && !empty($auth_id)) {
            $user = users($auth_id, 'id');
        }

        if ($user->user_type == 2) {
            $model = Exam::orderBy('id', 'DESC');
        } else {
            $model = $model->where('user_id', $auth_id);
            $model = $model->whereNotNull("point");
            $model = $model->whereNotNull("time_reply");
            $model = $model->where("payed", 1);
        }

        if (isset($exam_id) && !empty($exam_id)) {
            if ($user->user_type == 2) {
                $model = $model->where("id", $exam_id);
            } else {
                $model = $model->where('exam_id', $exam_id);
            }
        }

        $model = $model->first();

        if ($user->user_type == 2 && $model->user_id == $user->id) {
            $model = !empty($model) && isset($model->slug) ? $model->slug : null;
        } else {
            $model = !empty($model) && isset($model->id) ? $model->id : null;
            // $model = null; // heleki
        }

        return Cache::rememberForever("exam_result"  . $exam_id . $auth_id, fn() => $model);
    }
}

if (!function_exists('get_answer_choised')) {
    function get_answer_choised($exam_results_ids, $question_id, $question_type, $value_id = null)
    {
        $model = ExamResultAnswer::orderBy('id', 'DESC')
            ->whereIn('result_id', $exam_results_ids)
            ->where('question_id', $question_id);

        if ($question_type == 1 || $question_type == 5) {
            $model = $model->where('answer_id', $value_id)
                ->whereNotNull("answer_id")
                ->whereNull('value');
        } else if ($question_type == 2) {
            $model = $model->whereJsonContains('answers', $value_id)
                ->whereNotNull("answers")
                ->whereNull("answer_id")
                ->whereNull('value');
        } else if ($question_type == 3) {
            $model = $model->whereNull("answers")
                ->whereNull("answer_id")
                ->whereNotNull('value');
        } else if ($question_type == 4) {
            $model = $model->whereNull("answers")
                ->whereNull("answer_id")
                ->whereNotNull('value');
        }

        $model = $model->with('result_model')->get();

        return Cache::rememberForever("get_answer_choised"  . $exam_results_ids . $question_id . $question_type . $value_id, fn() => $model);
    }
}

if (!function_exists('formattedTime')) {
    function formattedTime($seconds, $type = 'minute')
    {
        $model = null;
        if ($type == 'minute') {
            $model = str_pad(floor($seconds / 60), 2, '0', STR_PAD_LEFT);
        } else {
            $model = str_pad($seconds % 60, 2, '0', STR_PAD_LEFT);
        }
        return Cache::rememberForever("formattedTime"  . $seconds . $type, fn() => $model);
    }
}

if (!function_exists('exam_finish_and_calc')) {
    function exam_finish_and_calc($exam_id, $auth_id)
    {
        $model = null;
        $examresult = ExamResult::where('exam_id', $exam_id)->where('user_id', $auth_id)->whereNull('point')->orderBy('id', 'DESC')->first();
        if (!empty($examresult) && isset($examresult->id)) {
            $lastpoint = session()->has("point") ? session()->get('point') : 0;
            $point = $lastpoint + calculate_exam_result($examresult->id);
            $examresult->update(['point' => $point ?? 0]);

            $exam = Exam::find($exam_id);
            if ($exam->show_result_user == true) {
                $model = route("user.exam.resultpage", $examresult->id);
            } else {
                $model = route("page.welcome");
            }
        } else {
            $model = null;
        }

        return Cache::rememberForever("exam_finish_and_calc"  . $exam_id . $auth_id, fn() => $model);
    }
}

if (!function_exists('calc_this_exam')) {
    function calc_this_exam($result_id)
    {
        $model = null;
        $examresult = ExamResult::where('id', $result_id)->orderBy('id', 'DESC')->first();
        if (!empty($examresult) && isset($examresult->id)) {
            $point = calculate_exam_result($result_id);
            $examresult->update(['point' => $point ?? 0]);
            $model = $examresult;
        } else {
            $model = null;
        }

        return Cache::rememberForever("calc_this_exam"  . $result_id, fn() => $model);
    }
}

if (!function_exists('remove_repeated_result_answers')) {
    function remove_repeated_result_answers($result_id)
    {
        DB::transaction(function () use ($result_id) {
            $examresult = ExamResult::where('id', $result_id)->whereNotNull('point')->orderBy('id', 'DESC')->first();
            if (!empty($examresult) && isset($examresult->id)) {
                $old_question_ids = [];
                foreach ($examresult->answers as $answer) {
                    if (in_array($answer->question_id, $old_question_ids)) {
                        $answer->delete();
                    } else {
                        array_push($old_question_ids, $answer->question_id);
                    }
                }
            }
        });

        return true;
    }
}

if (!function_exists('filter_number')) {
    function filter_number($phone)
    {
        $phonenumber = str_replace('(', '', $phone);
        $phonenumber = str_replace(')', '', $phone);
        $phonenumber = str_replace('-', '', $phone);
        $phonenumber = str_replace(' ', '', $phone);
        $phonenumber = preg_replace('/\D/', '', $phone);
        $phonenumber = str_replace(['(', ')', '-', ' '], '', $phone);
        $phonenumber = trim($phonenumber);
        return $phonenumber;
    }
}

if (!function_exists('is_valid_phone_format')) {
    function is_valid_phone_format($phone)
    {
        $pattern = '/^\(0\d{2}\) \d{3}-\d{2}-\d{2}$/';
        return preg_match($pattern, $phone) === 1;
    }
}

if (!function_exists('exam_result_answer_true_or_false_new')) {
    function exam_result_answer_true_or_false_new($question_id, $result_id)
    {
        $model = ExamResultAnswer::where('question_id', $question_id)
            ->where('result_id', $result_id)
            ->orderByDesc('id')
            ->whereNotNull("result")
            ->first();

        if (!is_null($model) && ($model->result == 0 || $model->result == 1)) {
            return strval($model->result);
        } else {
            return null;
        }
    }
}

if (!function_exists('exam_section')) {
    function exam_section($id)
    {
        try {
            $model = Section::find($id);

            return Cache::rememberForever("exam_section" . $id, fn() => $model);
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }
    }
}

if (!function_exists('convertTimeToMinutes')) {
    function convertTimeToMinutes($time)
    {
        $timeParts = explode(":", $time);
        return (int)$timeParts[0] * 60 + (int)$timeParts[1];
    }
}

if (!function_exists('convertToMinutesAndSeconds')) {
    function convertToMinutesAndSeconds($seconds)
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }
}

if (!function_exists('exams_buyed_or_not')) {
    function exams_buyed_or_not($exam_id, $user_id)
    {
        $exam = Exam::where("id", $exam_id)->first();
        if (!$exam) {
            return null;
        }
        $payment = Payments::where("exam_id", $exam_id)->where("user_id", $user_id)->orderByDesc('id')->where('payment_status', 1);
        $model = null;
        if ($exam->rebuy) {
            $payment = $payment->whereHas("exam_result",function($q){
                $q->whereNull("point");
            })->first();
        } else {
            $payment = $payment->first();
        }

        return $payment;
    }
}

if (!function_exists('exam_islenildi')) {
    function exam_islenildi($result_id)
    {
        try {
            $exam_result = ExamResult::where("id", $result_id)->first();
            $answered_question_ids = $exam_result->answers->pluck('question_id')->unique();
            $examsections = Section::where("exam_id", $exam_result->exam_id)->with("questions")->get();
            $required_questions = collect();

            foreach ($examsections as $section) {
                if ($section->question_count > 0 && $section->question_count <= $section->questions->count()) {
                    $required_questions = $required_questions->merge($section->questions->take($section->question_count));
                } else {
                    $required_questions = $required_questions->merge($section->questions);
                }
            }

            $required_question_ids = $required_questions->pluck('id')->unique();

            $unique_answered_ids = $answered_question_ids->intersect($required_question_ids);
            $missing_question_ids = $required_question_ids->diff($answered_question_ids);

            if (($unique_answered_ids->count() === $required_question_ids->count() || count($missing_question_ids) < 3) && !empty($exam_result) && $exam_result->point>0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
