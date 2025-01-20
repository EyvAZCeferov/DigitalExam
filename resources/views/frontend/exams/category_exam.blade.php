@extends('frontend.layouts.app')
@section('title')
    {{ !empty($categoryitem)
        ? $categoryitem->name[app()->getLocale() . '_name']
        : trans('additional.footer.categories') }}
@endsection
@if (!empty($categoryitem) && !empty($categoryitem->description) && isset($categoryitem->description[app()->getLocale().'_description']) && !empty($categoryitem->description[app()->getLocale().'_description']))
    @section('description', $categoryitem->description[app()->getLocale() . '_description'])
@endif

@section('content')
    <section class="category_exams">
        @include('frontend.exams.search_filter', ['type' => 'exams', 'value' => $search])
        <div class="categories_list">
            @foreach ($categories as $key => $value)
                <div class="category_item" id="category_header_{{ $value->id }}" onclick="show_tab({{ $value->id }})">
                    {{ $value->name[app()->getLocale() . '_name'] }}
                </div>
            @endforeach
        </div>
        <div class="category_body">
            @foreach ($categories as $key => $value)
                <div class="category_body_item" id="category_body_{{ $value->id }}">
                    @if(empty($value->parent_id))
                        @php($subcategoryIds = $value->sub->pluck('id')->toArray())
                        @php($exams = \App\Models\Exam::whereIn('category_id', [$value->id, ...$subcategoryIds])->get())
                    @else
                        @php($exams = \App\Models\Exam::where('category_id', $value->id)->get())
                    @endif
                    @if (!empty($exams) && count($exams) > 0)
                        @include('frontend.light_parts.products.products_grid', [
                            'products' => $exams,
                            'addable'=>true
                        ])
                    @else
                        @if (auth('users')->check() && auth('users')->user()->user_type == 2)
                            <div class="products_section">
                                <a class="products_section_element add_product" href="{{ route('exams_front.createoredit',['category'=>$value->id]) }}">
                                    <div class="content">
                                        @lang('additional.buttons.add') <i class="fa fa-plus"></i>
                                    </div>
                                </a>
                            </div>
                        @else
                            <p class="text-center justify-center align-content-center d-block w-100 text-danger">
                                @lang('additional.pages.exams.notfound')</p>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </section>
@endsection


@push('js')
    <script defer>
        function show_tab(id) {
            showLoader();
            var category_item_headers = document.getElementsByClassName('category_item');
            var category_body_items = document.getElementsByClassName('category_body_item');
            for (let index = 0; index < category_item_headers.length; index++) {
                const element = category_item_headers[index];
                element.classList.remove("active");
            }

            for (let index = 0; index < category_body_items.length; index++) {
                const element = category_body_items[index];
                element.classList.remove('active');
            }

            document.getElementById(`category_header_${id}`).classList.add('active');
            document.getElementById(`category_body_${id}`).classList.add('active');
            setTimeout(() => {
                hideLoader();
            }, 500);
        }

        window.addEventListener('load',function(){
            show_tab({{ $category??$categories[0]->id }});
        });
    </script>
@endpush
