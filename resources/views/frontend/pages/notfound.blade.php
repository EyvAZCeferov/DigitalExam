@extends('frontend.layouts.app')
@section('title', trans('additional.pages.notfound.title'))
@section('content')
    <section class="py-5 not_found_page">
        <div class="row">
            <div class="col-0 col-sm-0 col-md-12 col-lg-12 not_found_image">
                <img src="{{ asset('front/assets/img/bg_images/notfound.jpg') }}" class="img-responsive"
                    alt="Səhifə tapılmadı" >
            </div>
        </div>
        <div class="row my-2 justify-center justify-content-center align-center align-items-center align-content-center text-center">
            <div class="col-sm-12 col-md-4 col-lg-3 mx-auto text-center">
                <a class="btn btn-primary notfound_button" href="{{ route("page.welcome") }}">
                    <i class="fa fa-home"></i> @lang('additional.headers.welcome')
                </a>
            </div>
        </div>
    </section>
@endsection
