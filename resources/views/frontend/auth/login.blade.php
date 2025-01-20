@extends('frontend.layouts.app')
@section('title', trans('additional.headers.login'))
@section('content')
    <section class="register_or_login py-5">
        <div class="row">
            <div class="col-0 col-sm-0 col-md-6 col-lg-7">
                <img src="{{ asset('front/assets/img/bg_images/register_bg.png') }}" class="img-responsive"
                    alt="{{ trans('additional.headers.login') . ' / ' . trans('additional.headers.register') }}">
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 right_column">
                <h2 class="text-center mt-2 mb-5">@lang('additional.headers.login')</h2>
                <form action="{{ route('user.authenticate') }}" method="post" class="w-100">
                    @csrf
                    <input name="subdomain" value="{{session()->get('subdomain')??null}}" type="hidden">
                    <div class="account-form-item mb-3">
                        <div class="account-form-input">
                            <input type="email" placeholder="@lang('additional.forms.email')" name="email" value="{{ old('email') }}"
                                class="form-control form-control-lg">
                        </div>
                    </div>
                    <div class="account-form-item mb-3">
                        <div class="account-form-input account-form-input-pass">
                            <input type="password" class="form-control form-control-lg" placeholder="@lang('additional.forms.password')"
                                id="password" name="password" value="{{ old('password') }}">
                            <span id="password_icon" class="input_icon" onclick="toggleInputFunction('password')" ><i class="fa fa-eye-slash"></i></span>
                        </div>
                    </div>
                    <div class="account-form-label my-2">
                        <a class="register_inline w-100 d-block text-right"
                            href="{{ route('email') }}">@lang('additional.pages.auth.forgetpassword')</a>
                    </div>

                    <div class="account-form-button my-3">
                        <button type="submit" class="btn btn-primary btn-block">@lang('additional.headers.login')</button>
                        <p class="text-center">/</p>
                        <a type="button" href="{{ route("register") }}" class="btn btn-secondary btn-block">@lang('additional.headers.register')</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

