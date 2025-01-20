@extends('frontend.layouts.app')
@section('title', trans('additional.headers.profile'))
@section('content')
    <section class="profile_page_header my-3">
        <div class="row classjustifybetween">
            <h2 class="my-3 py-3 d-inline-block" style="width:max-content;">@lang('additional.pages.auth.welcome', ['name_surname' => auth('users')->user()->name])</h2>
            <a href="{{ route("user.logout") }}" style="width:max-content;" class="d-inline-block logout">@lang('additional.headers.logout')</a>
        </div>
        @include('frontend.light_parts.exam_profile_column', [
            'title' => 'yourexams',
            'nav_id' => 'pills',
            'tab_datas' => [exam_for_profile('active',auth('users')->id()), exam_for_profile('passive',auth('users')->id())],
        ])

        @include('frontend.light_parts.categories')
        <div class="my-4 py-2">
            @include('frontend.light_parts.section_title', [
                'title' => trans('additional.pages.exams.most_used_tests'),
                'url' => null,
            ])
            @include('frontend.light_parts.products.products_grid', [
                'products' => exams(null, 'most_used_tests')->take(4),
            ])
        </div>

    </section>
@endsection
