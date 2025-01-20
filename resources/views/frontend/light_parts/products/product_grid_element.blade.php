@if (empty($product->start_time) || (!empty($product->start_time) && is_int($product->start_time)))
    @php
        $start_time = $product->start_time ? \Carbon\Carbon::createFromTimestamp($product->start_time) : null;
    @endphp
    @if (auth('users')->check() && auth('users')->user()->user_type == 2 && auth('users')->id() == $product->user_id)
        <div class="products_section_element">
            @if ($product->price > 0)
                @if ($product->endirim_price > 0)
                    <div class="products_section_element_price_endirim">@lang('additional.pages.exams.endirim_with_faiz', ['count' => count_endirim_faiz($product->price, $product->endirim_price)])</div>
                @endif
            @endif
            @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time))
            @else
                <span class="products_section_element_price_endirim still_waiting text-white">@lang('additional.pages.exams.stillwait', ['timestamp' => $start_time->format('d.m.Y H:i')])</span>
            @endif

            @if (auth('users')->check() && auth('users')->user()->user_type == 2 && auth('users')->id() == $product->user_id)
                <span class="product_section_element_edit"
                onclick="redirect_tourl('{{ session()->has('subdomain') ? route('exams_front.createoredit.subdomain', ['slug' => $product->slug, 'subdomain' => session()->get('subdomain')]) : route('exams_front.createoredit', ['slug' => $product->slug])  }}')"><i
                        class="fa fa-pencil"></i></span>
            @endif
            @if (
                (auth('users')->check() && auth('users')->user()->user_type == 2) ||
                    (auth('users')->check() &&
                        auth('users')->user()->user_type == 1 &&
                        $product->show_result_user==true &&
                        !empty(exam_result($product->id, auth('users')->id()))))
                <span
                    class="product_section_element_result result_element @if (auth('users')->user()->user_type == 1 ||
                            !(auth('users')->user()->user_type == 2 && auth('users')->id() == $product->user_id)) result_on_left @endif"
                    onclick="redirect_tourl('{{
                        session()->has('subdomain') ?
                            route('user.exam.resultpage', ['subdomain'=>session()->get('subdomain'),'result_id'=>exam_result($product->id, auth('users')->id())]) :
                            route('user.exam.resultpage', exam_result($product->id, auth('users')->id()))
                    }}')"
                    >
                    <i class="fa fa-sticky-note"></i>
                </span>
            @endif

            @if (auth('users')->check() && auth('users')->user()->user_type == 2 && auth('users')->id() == $product->user_id)
                <span class="product_section_element_destroy destroy_elements"
                    onclick="deleteproduct({{ $product->id }},'product')"><i class="fa fa-trash"></i></span>
            @endif

            <div class="products_section_element_header" @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time)) onclick="redirect_tourl('{{ session()->has('subdomain')? route('exams.show.subdomain', ['slug' => $product->slug, 'subdomain' => session()->get('subdomain')]) :  route('exams.show', $product->slug) }}')"
                @endif>
                <img src="{{ getImageUrl($product->image, 'exams') }}"
                    alt="{{ $product->name[app()->getLocale() . '_name'] }}">
            </div>

            <div class="products_section_element_content" @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time)) onclick="redirect_tourl('{{ session()->has('subdomain')? route('exams.show.subdomain', ['slug' => $product->slug, 'subdomain' => session()->get('subdomain')]) :  route('exams.show', $product->slug) }}')"
                @endif>
                <h5 class="products_section_element_content_name text-left minified">
                    {{ mb_substr($product->name[app()->getLocale() . '_name'], 0, 20) }}</h5>
                <h5 class="products_section_element_content_name text-left full d-none">
                        {{ $product->name[app()->getLocale() . '_name'] }}</h5>
                @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time))
                    <button
                        class="products_section_element_content_button @if ($product->price == 0) free_price @endif">
                        @if ($product->price > 0)
                            @if ($product->endirim_price > 0)
                                {{ $product->endirim_price }} <span class="deleted_price">{{ $product->price }}</span>
                            @else
                                {{ $product->price }}
                            @endif

                            AZN
                        @else
                            @lang('additional.buttons.free')
                        @endif
                    </button>
                @endif
            </div>
        </div>
    @else
        <div class="products_section_element">
            @if ($product->price > 0)
                @if ($product->endirim_price > 0)
                    <div class="products_section_element_price_endirim">@lang('additional.pages.exams.endirim_with_faiz', ['count' => count_endirim_faiz($product->price, $product->endirim_price)])</div>
                @endif
            @endif
            @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time))
            @else
                <span class="products_section_element_price_endirim still_waiting text-white">@lang('additional.pages.exams.stillwait', ['timestamp' => $start_time->format('d.m.Y H:i')])</span>
            @endif

            @if (auth('users')->check() && auth('users')->user()->user_type == 2 && auth('users')->id() == $product->user_id)
                <span class="product_section_element_edit"
                    onclick="redirect_tourl('{{ session()->has('subdomain') ? route('exams_front.createoredit.subdomain', ['slug' => $product->slug, 'subdomain' => session()->get('subdomain')]) : route('exams_front.createoredit', ['slug' => $product->slug])  }}')"><i
                        class="fa fa-pencil"></i></span>
            @endif
            @if (
                (auth('users')->check() && auth('users')->user()->user_type == 2) ||
                    (auth('users')->check() &&
                        auth('users')->user()->user_type == 1 &&
                        $product->show_result_user==true &&
                        !empty(exam_result($product->id, auth('users')->id()))
                    ))
                <span
                    class="product_section_element_result result_element @if (auth('users')->user()->user_type == 1 ||
                            !(auth('users')->user()->user_type == 2 && auth('users')->id() == $product->user_id)) result_on_left @endif "
                    onclick="redirect_tourl('{{
                        session()->has('subdomain') ?
                            route('user.exam.resultpage', ['subdomain'=>session()->get('subdomain'),'result_id'=>exam_result($product->id, auth('users')->id())]) :
                            route('user.exam.resultpage', exam_result($product->id, auth('users')->id()))
                    }}')"
                    >
                    <i class="fa fa-sticky-note"></i>
                </span>
            @endif
            @if (auth('users')->check() && auth('users')->user()->user_type == 2 && auth('users')->id() == $product->user_id)
                <span class="product_section_element_destroy destroy_elements"
                    onclick="deleteproduct(${item.id},'product')"><i class="fa fa-trash"></i></span>
            @endif

            <div class="products_section_element_header" @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time)) onclick="redirect_tourl('{{ session()->has('subdomain')? route('exams.show.subdomain', ['slug' => $product->slug, 'subdomain' => session()->get('subdomain')]) :  route('exams.show', $product->slug) }}')"
                @endif>
                <img src="{{ getImageUrl($product->image, 'exams') }}"
                    alt="{{ $product->name[app()->getLocale() . '_name'] }}">
            </div>

            <div class="products_section_element_content" @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time)) onclick="redirect_tourl('{{ session()->has('subdomain')? route('exams.show.subdomain', ['slug' => $product->slug, 'subdomain' => session()->get('subdomain')]) :  route('exams.show', $product->slug) }}')"
                @endif>
                <h5 class="products_section_element_content_name text-left minified">
                    {{ mb_substr($product->name[app()->getLocale() . '_name'], 0, 20) }}</h5>
                <h5 class="products_section_element_content_name text-left full d-none">
                        {{ $product->name[app()->getLocale() . '_name'] }}</h5>
                @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time))
                    <button
                        class="products_section_element_content_button @if ($product->price == 0) free_price @endif">
                        @if ($product->price > 0)
                            @if ($product->endirim_price > 0)
                                {{ $product->endirim_price }} <span class="deleted_price">{{ $product->price }}</span>
                            @else
                                {{ $product->price }}
                            @endif

                            AZN
                        @else
                            @lang('additional.buttons.free')
                        @endif
                    </button>
                @endif
            </div>
        </div>
    @endif
@endif
