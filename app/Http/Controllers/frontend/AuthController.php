<?php

namespace App\Http\Controllers\frontend;

use App\Helpers\SendSms;
use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\AuthAttempts;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if (isset($request->savethisurl) && !empty($request->savethisurl)) {
                Session::put("savethisurl", $request->savethisurl);
            }
            return view('frontend.auth.login');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function register(Request $request)
    {
        try {
            if (isset($request->savethisurl) && !empty($request->savethisurl))
                Session::put("savethisurl", $request->savethisurl);

            return view('frontend.auth.register');
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function authenticate(Request $request)
    {
        try {
            session()->forget("subdomain");
            session()->forget("user_id");
            session()->forget("user_email");
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required'
            ], [], [
                'email' => trans("additional.forms.email"),
                'password' => trans("additional.forms.password"),
            ]);

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];
            if (Auth::guard('users')->attempt($credentials)) {
                session()->put("user_id", Auth::guard('users')->id());
                session()->put("user_mail", Auth::guard('users')->user()->email);
                $userwith_subdomain = $request->input("subdomain") ?? User::where('id', Auth::guard('users')->id())->whereNotNull("subdomain")->first()->subdomain;

                if (isset($userwith_subdomain) && !empty($userwith_subdomain)) {
                    Session::put('subdomain', $userwith_subdomain ?? null);
                    // create_dns_record($userwith_subdomain);
                }

                if (Session::has("savethisurl") && !empty(Session::get("savethisurl"))) {
                    return redirect(Session::get("savethisurl"));
                } else {
                    if (isset($userwith_subdomain) && !empty($userwith_subdomain)) {
                        return redirect(env('HTTP_OR_HTTPS') . $userwith_subdomain . '.' . env('APP_DOMAIN') . '/az/profile?user_id=' . Auth::guard('users')->id());
                    } else {
                        return redirect()->route('user.profile');
                    }
                }
            } else {
                return redirect()->back()->with(['error' => trans('additional.messages.passwords_incorrect')]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }
    public function registerSave_new(Request $request)
    {
        try {

            session()->forget("subdomain");
            session()->forget("user_id");
            session()->forget("user_email");
            $this->validate($request, [
                'email' => 'email|string|unique:users,email',
                'password' => 'required|string|min:6',
                'name' => 'string|required|min:5',
                'phone' => 'string|required',
            ], [], [
                'email' => trans("additional.forms.email"),
                'password' => trans("additional.forms.password"),
                'name' => trans("additional.forms.name"),
                'phone' => trans("additional.forms.phone"),
            ]);

            $credentials = [];
            $pincode = $request->input("pincode") ?? null;
            $phone = $request->input("phone");

            if (is_valid_phone_format($phone)) {
                $user = User::where("phone", $request->phone)->orWhere("email", $request->email)->first();
                if (empty($user) && !isset($user->id)) {
                    if (empty($pincode)) {
                        $code = createRandomCode('int', 4);
                        $data = new AuthAttempts();
                        $data->phone_number = filter_number($request->input("phone"));
                        $data->code = $code;
                        $data->ipaddress = $request->ip();
                        $data->useragent = $request->header('user-agent');
                        $data->save();

                        $send = new SendSms();
                        return $send->send(filter_number($request->input("phone")), $code);

                        return response()->json(['statis' => 'success', 'authattempt' => true]);
                    } else {
                        $checkauthattempt = AuthAttempts::where("phone_number", filter_number($request->input("phone")))->orderByDesc('id')->first();
                        if ($checkauthattempt && $checkauthattempt->code == $pincode) {

                            $user = new User();
                            $image = null;
                            if ($request->hasFile('picture') && $request->file('picture')->isValid()) {
                                $image = image_upload($request->file("picture"), 'users');
                            }
                            $user->name = $request->name;
                            $user->phone = $request->phone;
                            $user->email = $request->email;
                            $user->password = Hash::make($request->password);
                            $user->user_type = $request->input("user_type") ?? 1;
                            if (isset($image) && !empty($image)) {
                                $user->picture = $image;
                            }
                            $subdomain = Session::has("subdomain") ? Session::get("subdomain") : $request->input("subdomain");
                            if ($user->user_type == 2) {
                                $subdomain = Str::slug($request->name);
                                create_dns_record($subdomain);
                            }
                            $user->subdomain = $subdomain;
                            $user->save();

                            $credentials = [
                                'email' => $request->email,
                                'password' => $request->password,
                            ];

                            if (Auth::guard('users')->attempt($credentials)) {
                                session()->put("user_id", Auth::guard('users')->id());
                                $checkauthattempt->update(['user_id' => Auth::guard('users')->id()]);
                                session()->put("user_mail", Auth::guard('users')->user()->email);
                                if (isset($subdomain) && !empty($subdomain)) {
                                    Session::put("subdomain", $subdomain);
                                }

                                if (!empty(Session::get("savethisurl"))) {
                                    return response()->json(['status' => 'success', 'url' => Session::get("savethisurl")]);
                                } else {
                                    if (!empty($subdomain))
                                        return response()->json(['status' => 'success', 'url' => env('HTTP_OR_HTTPS') . $subdomain . '.' . env('APP_DOMAIN') . '/az/profile?user_id=' . Auth::guard('users')->id()]);
                                    else
                                        return response()->json(['status' => 'success', 'url' => route('user.profile')]);
                                }
                            } else {
                                return response()->json(['status' => 'error', 'message' => trans('additional.messages.passwords_incorrect')]);
                            }
                        } else {
                            $code = createRandomCode('int', 4);
                            $data = new AuthAttempts();
                            $data->phone_number = filter_number($request->input("phone"));
                            $data->code = $code;
                            $data->ipaddress = $request->ip();
                            $data->useragent = $request->header('user-agent');
                            $data->save();

                            $send = new SendSms();
                            return $send->send(filter_number($request->input("phone")), $code);

                            return response()->json(['statis' => 'success', 'authattempt' => true]);
                        }
                    }
                } else {
                    return response()->json(['status' => 'warning', 'message' => trans('additional.messages.email_or_phone_mathced')]);
                }
            } else {
                return response()->json(['status' => 'warning', 'message' => 'Nömrə düzgün formatda daxil edilməlidir.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage() . '-' . $e->getLine()]);
        }
    }
    public function registerSave(Request $request)
    {
        try {
            session()->forget("subdomain");
            session()->forget("user_id");
            session()->forget("user_email");
            $this->validate($request, [
                'email' => 'required|email|string|unique:users,email',
                'password' => 'required|string|min:6',
                'name' => 'string|required|min:5',
                'phone' => 'string',
            ], [], [
                'email' => trans("additional.forms.email"),
                'password' => trans("additional.forms.password"),
                'name' => trans("additional.forms.name"),
                'phone' => trans("additional.forms.phone"),
            ]);

            $credentials = [];

            $user = User::where("phone", $request->phone)->orWhere("email", $request->email)->first();
            if (empty($user) && !isset($user->id)) {
                $user = new User();
                $image = null;
                if ($request->hasFile('picture') && $request->file('picture')->isValid()) {
                    $image = image_upload($request->file("picture"), 'users');
                }
                $user->name = $request->name;
                $user->phone = $request->phone;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->user_type = $request->input("user_type") ?? 1;
                if (isset($image) && !empty($image)) {
                    $user->picture = $image;
                }
                $subdomain = Session::has("subdomain") ? Session::get("subdomain") : $request->input("subdomain");
                if ($user->user_type == 2) {
                    $subdomain = Str::slug($request->name);
                    create_dns_record($subdomain);
                }
                $user->subdomain = $subdomain;
                $user->save();

                $credentials = [
                    'email' => $request->email,
                    'password' => $request->password,
                ];

                if (Auth::guard('users')->attempt($credentials)) {
                    session()->put("user_id", Auth::guard('users')->id());
                    session()->put("user_mail", Auth::guard('users')->user()->email);
                    if (isset($subdomain) && !empty($subdomain)) {
                        Session::put("subdomain", $subdomain);
                    }

                    if (!empty(Session::get("savethisurl"))) {
                        return response()->json(['status' => 'success', 'url' => Session::get("savethisurl")]);
                    } else {
                        if (!empty($subdomain))
                            return response()->json(['status' => 'success', 'url' => env('HTTP_OR_HTTPS') . $subdomain . '.' . env('APP_DOMAIN') . '/az/profile?user_id=' . Auth::guard('users')->id()]);
                        else
                            return response()->json(['status' => 'success', 'url' => route('user.profile')]);
                    }
                } else {
                    return response()->json(['status' => 'error', 'message' => trans('additional.messages.passwords_incorrect')]);
                }
            } else {
                return response()->json(['status' => 'error', 'url' => route("login")]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function email()
    {
        return view('frontend.auth.email');
    }
    public function sendToken(Request $request)
    {
        if ($request->input("user_type") == 1) {
            $user = User::where('email', '=', request('email'))->first();
        } else {
            $user = User::where('phone', '=', request('phone'))->first();
        }

        if (!$user) {
            return redirect()->back()->with(['error' => 'İstifadəçi mövcud deyil']);
        }

        $token = Str::random(60);
        $code = createRandomCode();
        $link = route('reset', $token);

        if ($request->input("user_type") == 1) {
            Mail::to(request('email'))->send(new ResetPassword($link));
            DB::table('password_resets')->insert([
                'email' => request('email'),
                'token' => $token,
                'created_at' => Carbon::now(),
                'value' => $code,
            ]);
        } else {
            DB::table('password_resets')->insert([
                'phone' => request('phone'),
                'token' => $token,
                'created_at' => Carbon::now(),
                'value' => $code,
            ]);
        }
        return redirect()->route('login')->with(['success' => 'İsmarıc göndərildi.']);
    }
    public function reset($subdomain, $token)
    {
        try {
            $reset = DB::table('password_resets')->where('token', $token)->whereBetween('created_at', [Carbon::now()->subHours(3), Carbon::now()])->first();
            if (!$reset) {
                return redirect()->route('login')->with(['error' => 'Link arıq keçərsizdir!']);
            }

            return view('frontend.auth.reset', compact('token', 'reset'));
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }
    }
    public function reset_nosubdomain($token)
    {
        try {
            $reset = DB::table('password_resets')->where('token', $token)->whereBetween('created_at', [Carbon::now()->subHours(3), Carbon::now()])->first();
            if (!$reset) {
                return redirect()->route('login')->with(['error' => 'Link arıq keçərsizdir!']);
            }

            return view('frontend.auth.reset', compact('token', 'reset'));
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6'
        ]);

        $reset = DB::table('password_resets')->where('token', request('token'))->first();

        User::where('email', $reset->email)->update([
            'password' => Hash::make(request('password')),
        ]);

        DB::table('password_resets')->where('token', request('token'))->delete();
        return redirect()->route('login')->with(['success' => 'Şifrəniz yeniləndi']);
    }
    public function logout()
    {
        session()->forget("subdomain");
        session()->forget("user_id");
        session()->forget("user_email");
        Auth::guard('users')->logout();
        dbdeactive();
        return redirect()->route('login');
    }
    public function profile(Request $request)
    {
        try {
            $url = $request->url();
            $urlParts = parse_url($url);
            $mainDomain = env('APP_DOMAIN');
            $host = $urlParts['host'];
            if (strpos($host, $mainDomain) !== false) {
                $subdomain = str_replace('.' . $mainDomain, '', $host);
                if ($subdomain == env('APP_DOMAIN')) {
                    $subdomain = null;
                }
            }
            if (Session::has("subdomain") && empty($subdomain) && Auth::guard('users')->check()) {
                session()->put("user_mail", Auth::guard('users')->user()->email);
                return redirect(env('HTTP_OR_HTTPS') . Session::get("subdomain") . '.' . env('APP_DOMAIN') . '/az/profile?user_id=' . Auth::guard('users')->id());
            }

            return view('frontend.auth.profile');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
