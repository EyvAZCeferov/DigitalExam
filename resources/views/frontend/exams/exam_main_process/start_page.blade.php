@extends('frontend.layouts.app')
@section('title')
    {{ @$exam_start_pages[0]->name[app()->getLocale() . '_name'] }}
@endsection
@section('content')
    @foreach ($exam_start_pages as $key => $page)
        <section class="exam_start_page @if ($key == 0) show @else hide @endif "
            id="{{ $key }}_start_page">
            <h1 class="exam_start_page_title">{{ $page->name[app()->getLocale() . '_name'] }}</h1>
            <div class="exam_start_page_section">
                <div class="row">
                    <div
                        class="col-sm-12 @if (isset($page->image) && !empty($page->image)) col-md-6 col-lg-6 @else col-md-12 col-lg-12 @endif">
                        {!! $page->description[app()->getLocale() . '_description'] !!}

                        @if ($page->type == 'coupon')
                            <div class="row my-4">
                                <input class="form-control coupon_code_input" onkeyup="searchcoupon_code(event)"
                                    name="coupon_code" id="coupon_code" placeholder="@lang('additional.pages.payments.coupon_code')" />
                            </div>

                            <div class="row my-4" id="coupon_code_used">
                            </div>
                        @endif


                        <div class="mt-4 font-weight-bold agree_with_you">
                            <input type="checkbox" name="agree_with_you" id="agree_with_you">
                            <label for="agree_with_you">&nbsp; @lang('additional.pages.exams.iamagreewithyou')</label>
                        </div>
                    </div>
                    @if (isset($page->image) && !empty($page->image))
                        <div
                            class="col-sm-12 @if (isset($page->image) && !empty($page->image)) col-md-6 col-lg-6 @else col-md-12 col-lg-12 @endif image_area ">
                            <img src="{{ getImageUrl($page->image, 'exam_start_page') }}"
                                alt="{{ $page->name[app()->getLocale() . '_name'] }}">
                        </div>
                    @endif
                </div>

            </div>
            <div class="exam_buttons">
                <div></div>
                <div>
                    <a class="btn btn-primary"
                        onclick="backpage({{ $key }},@if (count($exam_start_pages) > $key + 1) 'more' @else null @endif)">
                        @lang('additional.buttons.back')
                    </a>
                    <button class="btn btn-secondary disabled next_button"
                        onclick="tonextpage({{ $key }},@if (count($exam_start_pages) > $key + 1) 'next' @else null @endif)">
                        @lang('additional.buttons.next')
                    </button>
                </div>
            </div>
        </section>
    @endforeach
@endsection

@push('js')
    <script defer>
        $("input#agree_with_you").on('change', function() {
            if (this.value == "on") {
                this.value = "off";
                $(".next_button").removeClass("disabled");
            } else {
                this.value = "on";
                $(".next_button").addClass("disabled");
            }
        });

        function tonextpage(nowid, process = null) {
            var nowelement = $(`#${nowid}_start_page`);
            var coupon_code_inp=$("#coupon_code").val()??'';
            if (process == 'next') {
                var new_id = nowid + 1;
                var nextelement = $(`#${new_id}_start_page`);
                $("input#agree_with_you").val();
                $(".next_button").addClass("disabled");
                nowelement.removeClass("show");
                nowelement.addClass("hide");
                nextelement.addClass("show");
                nextelement.removeClass("hide");
            } else {
                window.location.href = `/exam/exams/set_exam?exam_id={{ $exam->id }}&coupon_code=${coupon_code_inp}`;
            }
        }

        function backpage(nowid, process = null) {
            var nowelement = $(`#${nowid}_start_page`);
            if (process == 'more') {
                window.location.href = '{{ url()->previous() }}';
            } else {
                var new_id = nowid - 1;
                var nextelement = $(`#${new_id}_start_page`);
                $("input#agree_with_you").val();
                nowelement.removeClass("show");
                nowelement.addClass("hide");
                nextelement.addClass("show");
                nextelement.removeClass("hide");
            }
        }

        function searchcoupon_code(event) {
            $(event.target).removeClass("success");
            $(event.target).removeClass("error");
            var coupon_code_used = document.getElementById('coupon_code_used');
            coupon_code_used.innerHTML = '';
            var value = event.target.value;
            if (value !== null && value !== '' && value !== ' ' && value.length > 3) {
                sendAjaxRequest("{{ route('api.check_coupon_code') }}", "post", {
                    code: value,
                    exam: {{ $exam->id }},
                    language: '{{ app()->getLocale() }}'
                }, function(e, t) {
                    if (e) toast("error", e);
                    else {
                        let n = JSON.parse(t);
                        if (n.status == "success") {
                            $(event.target).addClass("success");
                            coupon_code_used.innerHTML = n.data;
                        }
                    }
                });
            } else {
                $(event.target).addClass("error");
            }
        }
    </script>
@endpush
