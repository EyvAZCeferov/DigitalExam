<div class="products_section">
    <input type="hidden" name="deleting_question_id" id="deleting_question_id">
    <input type="hidden" name="deleting_question_type" id="deleting_question_type">
    @if (isset($addable) && $addable == true)
        @if (auth('users')->check() && auth('users')->user()->user_type == 2)
            <a class="products_section_element add_product"
                href="{{ route('exams_front.createoredit', ['category' => $value->id]) }}">
                <div class="content">
                    @lang('additional.buttons.add') <i class="fa fa-plus"></i>
                </div>
            </a>
        @endif
    @endif
    @foreach ($products as $key => $value)
        @include('frontend.light_parts.products.product_grid_element', ['product' => $value])
    @endforeach
</div>
