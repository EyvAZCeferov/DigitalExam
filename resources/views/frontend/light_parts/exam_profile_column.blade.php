<div class="my-4 py-2">
    <div class="row section_title">
        <h3>{{ trans('additional.pages.exams.' . $title) }}</h3>
        @if(auth('users')->check() && auth('users')->user()->user_type==2)
            <a href="{{ route("exams_front.createoredit") }}" class="btn btn-primary">@lang('additional.buttons.add') <i
                    class="fa-solid fa-circle-plus"></i>
            </a>
        @endif
    </div>
    <ul class="nav nav-pills custom_nav-pills mb-3" id="{{ $nav_id }}_tab" role="tablist">
        @foreach ($tab_datas as $key => $value)
            <li class="nav-item" role="presentation">
                <button class="btn-sm nav-link @if ($key == 0) active @endif"
                    onclick="change_tabs_elements('{{ $nav_id }}',{{ $key }})"
                    id="{{ $nav_id }}-{{ $key }}_button" data-bs-toggle="pill"
                    data-bs-target="#{{ $nav_id }}-{{ $key }}" type="button" role="tab"
                    aria-controls="{{ $nav_id }}-{{ $key }}-tab"
                    aria-selected="true">@lang('additional.pages.exams.' . $key)</button>
            </li>
        @endforeach

    </ul>
    <div class="tab-content custom_tab-content" id="{{ $nav_id }}_tabContent">
        @foreach ($tab_datas as $key => $value)
            <div class="tab-pane fade @if ($key == 0) show active @endif"
                id="{{ $nav_id }}-{{ $key }}_tab" role="tabpanel"
                aria-labelledby="{{ $nav_id }}-{{ $key }}">
                @if (!empty($value) && count($value) > 0)
                    @include('frontend.light_parts.products.products_grid', [
                        'products' => $value,
                    ])
                @else
                    <p class="not_found_text text-center">@lang('additional.pages.exams.notfound') </p>
                @endif
            </div>
        @endforeach

    </div>
</div>
