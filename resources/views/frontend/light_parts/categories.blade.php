@if (!empty(categories(null, 'onlyparent')))
    @include('frontend.light_parts.section_title', [
        'title' => trans('additional.footer.categories'),
        'url' => session()->has('subdomain') ? route('category_exam.subdomain',['subdomain'=>session()->get("subdomain")]) : route('category_exam'),
    ])
    <section class="categories_light_part row">
        @foreach (categories(null,null) as $key => $value)
            <a class="col-sm-6 col-md-4 col-lg-3 categories_light_part_one"
                href="{{ session()->has('subdomain') ? route('category_exam.subdomain', ['category' => $value->id,'subdomain'=>session()->get("subdomain")]) : route('category_exam', ['category' => $value->id]) }}">
                @if (isset($value->image) && !empty($value->image))
                    <div class="image">
                        <img src="{{ getImageUrl($value->image, 'category') }}" class="img-fluid img-responsive"
                            alt="{{ $value->name[app()->getLocale() . '_name'] }}">
                    </div>
                @endif

                <div class="more_section">{{ $value->name[app()->getLocale() . '_name'] }}</div>
            </a>
        @endforeach
    </section>
@endif
