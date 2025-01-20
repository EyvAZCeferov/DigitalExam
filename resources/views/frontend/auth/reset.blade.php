@extends('frontend.layouts.app')
@section('title', trans('additional.headers.reset_password'))
@section('content')
    <section class="register_or_login py-5">
        <div class="row">
            <div class="col-0 col-sm-0 col-md-6 col-lg-7">
                <img src="{{ asset('front/assets/img/bg_images/register_bg.png') }}" class="img-responsive"
                    alt="{{ trans('additional.headers.login') . ' / ' . trans('additional.headers.reset_password') }}">
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 right_column">
                <h2 class="text-center mt-2 mb-5">@lang('additional.headers.reset_password')</h2>
                <form action="{{ route('change.password') }}" method="post" class="w-100" enctype="multipart/form-data">
                    @csrf
                    <input name="subdomain" value="{{session()->get('subdomain')??null}}" type="hidden">
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $reset->email }}">
                    @csrf

                    <div class="account-form-item mb-3">
                        <div class="account-form-input account-form-input-pass">
                            <input type="password" class="form-control form-control-lg" placeholder="@lang('additional.forms.password')"
                                name="password" value="{{ old('password') }}" id="password">
                            <span id="password_icon" class="input_icon" onclick="toggleInputFunction('password')"><i
                                    class="fa fa-eye-slash"></i></span>
                        </div>
                    </div>

                    <div class="account-form-item mb-3">
                        <div class="account-form-input account-form-input-pass">
                            <input type="password" class="form-control form-control-lg" placeholder="@lang('additional.forms.password_confirmation')"
                                name="password_confirmation" value="{{ old('password_confirmation') }}"
                                id="password_confirmation">
                            <span id="password_confirmation_icon" class="input_icon"
                                onclick="toggleInputFunction('password_confirmation')"><i
                                    class="fa fa-eye-slash"></i></span>
                        </div>
                    </div>

                    <div class="account-form-button mt-4 mb-2">
                        <button type="submit" class="btn btn-primary btn-block">@lang('additional.buttons.sendrequest')</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
