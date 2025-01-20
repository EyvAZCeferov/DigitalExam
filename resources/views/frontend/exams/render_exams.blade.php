@if (count($data) > 0)
    @include('frontend.light_parts.products.products_grid', [
        'products' => $data,
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
