@push('css')
    <link rel="stylesheet" href="{{ asset('front/assets/js/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/js/slick/slick-theme.css') }}">
@endpush

@extends('frontend.layouts.app')
@section('content')
    @include('frontend.light_parts.sliders')
    @include('frontend.light_parts.categories')
    @if (!session()->has('subdomain'))
        @include('frontend.light_parts.about')
    @else
        <section class="profile_page_header my-3">
            <div class="my-4 py-2">
                @include('frontend.light_parts.section_title', [
                    'title' => trans('additional.pages.exams.favoritedexams'),
                    'url' => session()->has('subdomain') ? route('category_exam.subdomain', ['category' => null,'subdomain'=>session()->get("subdomain")]) : route('category_exam', ['category' => null]),
                ])
                @include('frontend.light_parts.products.products_grid', [
                    'products' => exams(null, 'most_used_tests')->take(4),
                ])
            </div>
        </section>
    @endif
@endsection
@push('js')
    <script defer type="text/javascript" src="{{ asset('front/assets/js/slick/slick.min.js') }}"></script>
    <script defer>
        $(function() {
            $(".slick_slider").each(function() {
                $(this).slick({
                    infinite: true,
                    slidesToShow: $(this).data('slick-show'),
                    slidesToScroll: $(this).data('slick-scroll'),
                    autoplay: true,
                    autoplaySpeed: 2000,
                    dots: false,
                    arrows: true,
                });
            });
        });

        function searchnow(event) {
            var parentDiv = event.target.parentNode.parentNode;
            var input = parentDiv.querySelector('input[type="text"]');
            if (input !== null) {
                var value = input.value;
                if (value.trim() !== '') {
                    window.location.href = `/exams?search=${value}`;
                } else {
                    toast('@lang('additional.messages.required_fill')', 'error');
                }
            }
        }
    </script>
@endpush
