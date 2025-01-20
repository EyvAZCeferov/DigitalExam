@extends('frontend.layouts.app')
@section('title', trans('additional.pages.exams.exams'))
@if(!empty($category) && !empty($category->description) && isset($category->description[app()->getLocale().'_description']) && !empty($category->description[app()->getLocale().'_description']))
    @section('description', $category->description[app()->getLocale().'_description'])
@endif
@section('content')
    <section class="exams">
        @include('frontend.exams.search_filter', ['type' => 'exams','value'=>$search])
        <div class="row mt-1 my-3">
            <div class="col-sm-12 col-md-3 col-lg-2">
                @include('frontend.exams.left_filter', [
                    'data' => $filters,
                    'inproductpage' => 'product_page',
                ])
            </div>
            <div class="col-sm-12 col-md-9 col-lg-10">
                <div class="d-block">

                    @include('frontend.light_parts.section_title', [
                        'title' =>
                            !empty($category) &&
                            !empty($category->name) &&
                            isset($category->name[app()->getLocale() . '_name']) &&
                            !empty($category->name[app()->getLocale() . '_name'])
                                ? $category->name[app()->getLocale() . '_name']
                                : '',
                        'url' => null,
                        'button' => true,
                    ])
                </div>
                <div class="d-block" id="datas">
                    @if (!empty($exams) && count($exams) > 0)
                        @include('frontend.light_parts.products.products_grid', [
                            'products' => $exams,
                        ])
                    @else
                        <p class="text-center text-danger">@lang('additional.messages.examnotfound')</p>
                        <section class="not_found_page">
                            <div class="col-sm-12 col-md-4 col-lg-3 mx-auto text-center">
                                <a class="btn btn-primary notfound_button" href="{{ route('page.welcome') }}">
                                    <i class="fa fa-home"></i> @lang('additional.headers.welcome')
                                </a>
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
