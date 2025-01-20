@extends('backend.layouts.main')

@push('js')

@endpush

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Dashboard</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Gösterge Paneli</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Dashboard</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">

                        <div class="ibox-tools">

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="fullscreen-link">
                                <i class="fa fa-expand"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('dashboard.save') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="faq-item">
                                <div class="row">
                                    <div class="col-12">
                                        <a data-toggle="collapse" href="#faq1" class="faq-question">Banner <i class="fa fa-hand-pointer-o"></i></a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="faq1" class="panel-collapse collapse ">
                                            <div class="faq-answer">
                                                <div class="row">
                                                    @foreach(config('app.locales') as $locale)
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="">Title {{ \Illuminate\Support\Str::upper($locale) }}</label>
                                                                <input type="text"
                                                                       value="{{ old('banner_title.' . $locale, $dashboard?->banner_title->$locale) }}"
                                                                       name="banner_title[{{$locale}}]"
                                                                       class="form-control {{ $errors->first('banner_title.' . $locale) ? 'is-invalid' : '' }}">

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                        @foreach(config('app.locales') as $locale)
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="">Description {{ \Illuminate\Support\Str::upper($locale) }}</label>
                                                                    <textarea name="banner_description[{{$locale}}]"
                                                                              class="form-control  {{ $errors->first('banner_description.' . $locale) ? 'is-invalid' : '' }}">{{ old('banner_description.' . $locale, $dashboard?->banner_description->{$locale}) }}</textarea>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">Image (1440px x 600px)</label>
                                                                <input type="file"
                                                                       name="banner_image"
                                                                       class="form-control {{ $errors->first('banner_image') ? 'is-invalid' : '' }}">

                                                            </div>
                                                            <img style="max-width: 200px" src="{{ $dashboard?->getFirstMedia('banner_image')?->getUrl() }}" alt="">
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="row">
                                    <div class="col-12">
                                        <a data-toggle="collapse" href="#faq2" class="faq-question">Section 1 <i
                                                class="fa fa-hand-pointer-o"></i></a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="faq2" class="panel-collapse collapse ">
                                            <div class="faq-answer">
                                                <div class="row">
                                                    @foreach(config('app.locales') as $locale)
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="">Title {{ \Illuminate\Support\Str::upper($locale) }}</label>
                                                                <input type="text"
                                                                       value="{{ old('section_1_title.' . $locale, $dashboard?->section_1_title->$locale) }}"
                                                                       name="section_1_title[{{$locale}}]"
                                                                       class="form-control {{ $errors->first('section_1_title.' . $locale) ? 'is-invalid' : '' }}">

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @foreach(config('app.locales') as $locale)
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="">Description {{ \Illuminate\Support\Str::upper($locale) }}</label>
                                                                <textarea name="section_1_description[{{$locale}}]"
                                                                          class="form-control summernote {{ $errors->first('section_1_description.' . $locale) ? 'is-invalid' : '' }}">{{ old('section_1_description.' . $locale, $dashboard?->section_1_description->{$locale}) }}</textarea>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">Image (614px x 386px)</label>
                                                                <input type="file"
                                                                       name="section_1_image"
                                                                       class="form-control {{ $errors->first('section_1_image') ? 'is-invalid' : '' }}">

                                                            </div>
                                                            <img style="max-width: 200px" src="{{ $dashboard?->getFirstMedia('section_1_image')?->getUrl() }}" alt="">
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="row">
                                    <div class="col-12">
                                        <a data-toggle="collapse" href="#faq3" class="faq-question">Section 2 <i
                                                class="fa fa-hand-pointer-o"></i></a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="faq3" class="panel-collapse collapse ">
                                            <div class="faq-answer">
                                                <div class="row">
                                                    @foreach(config('app.locales') as $locale)
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="">Title {{ \Illuminate\Support\Str::upper($locale) }}</label>
                                                                <input type="text"
                                                                       value="{{ old('section_2_title.' . $locale, $dashboard?->section_2_title->$locale) }}"
                                                                       name="section_2_title[{{$locale}}]"
                                                                       class="form-control {{ $errors->first('section_2_title.' . $locale) ? 'is-invalid' : '' }}">

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @foreach(config('app.locales') as $locale)
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="">Description {{ \Illuminate\Support\Str::upper($locale) }}</label>
                                                                <textarea name="section_2_description[{{$locale}}]"
                                                                          class="form-control summernote {{ $errors->first('section_2_description.' . $locale) ? 'is-invalid' : '' }}">{{ old('section_2_description.' . $locale, $dashboard?->section_2_description->{$locale}) }}</textarea>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">Image (614px x 386px)</label>
                                                                <input type="file"
                                                                       name="section_2_image"
                                                                       class="form-control {{ $errors->first('section_2_image') ? 'is-invalid' : '' }}">

                                                            </div>
                                                            <img style="max-width: 200px" src="{{ $dashboard?->getFirstMedia('section_2_image')?->getUrl() }}" alt="">
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="row">
                                    <div class="col-12">
                                        <a data-toggle="collapse" href="#faq4" class="faq-question">Section 3 <i
                                                class="fa fa-hand-pointer-o"></i></a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="faq4" class="panel-collapse collapse ">
                                            <div class="faq-answer">
                                                <div class="row">
                                                    @foreach(config('app.locales') as $locale)
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="">Title {{ \Illuminate\Support\Str::upper($locale) }}</label>
                                                                <input type="text"
                                                                       value="{{ old('section_3_title.' . $locale, $dashboard?->section_3_title->$locale) }}"
                                                                       name="section_3_title[{{$locale}}]"
                                                                       class="form-control {{ $errors->first('section_3_title.' . $locale) ? 'is-invalid' : '' }}">

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @foreach(config('app.locales') as $locale)
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="">Description {{ \Illuminate\Support\Str::upper($locale) }}</label>
                                                                <textarea name="section_3_description[{{$locale}}]"
                                                                          class="form-control summernote {{ $errors->first('section_3_description.' . $locale) ? 'is-invalid' : '' }}">{{ old('section_3_description.' . $locale, $dashboard?->section_3_description->{$locale}) }}</textarea>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="">Image (614px x 386px)</label>
                                                                <input type="file"
                                                                       name="section_3_image"
                                                                       class="form-control {{ $errors->first('section_3_image') ? 'is-invalid' : '' }}">

                                                            </div>
                                                            <img style="max-width: 200px" src="{{ $dashboard?->getFirstMedia('section_3_image')?->getUrl() }}" alt="">
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <button class="btn btn-primary">Kaydet</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
