@extends('frontend.layouts.exam_layout')
@section('title', $exam->name[app()->getLocale() . '_name'])
@section('description', $exam->description[app()->getLocale() . '_description'] ?? '')
@push('css')
    <style type="text/css" media="print">
        * {
            display: none;
        }

        #print_error {
            display: block;
        }
    </style>
    <style>
        #desmoscalculator {
            z-index: 9999999999;
        }

    </style>
    <script>
        const hide_text = "@lang('additional.buttons.hide')";
        const show_text = "@lang('additional.buttons.show')";
        const if_change_window_tab_text = "@lang('additional.messages.ifchangewindowtab')";
        const aplus_icon_path = "{{ asset('front/assets/img/bg_images/a+icon.png') }}";
        const audio_file_played_text = "@lang('additional.pages.exams.audiofile_played')";
        const undo_text = "@lang('additional.buttons.undo')";
        const layout_standart_right ="@lang('additional.buttons.nextsection') @if ($exam->layout_type == 'standart')<i class='fa fa-angle-right'></i>@endif";
        const referances_standart_check ="@lang('additional.buttons.finish') @if ($exam->layout_type == 'standart')<i class='fa fa-check'></i>@endif";
        const rf_open_check ="@lang('additional.buttons.finish') @if ($exam->layout_type == 'standart')<i class='fa fa-check'></i>@endif";
        const rf_aaa ="@lang('additional.buttons.next')@if ($exam->layout_type == 'standart') <i class='fa fa-angle-right'></i> @endif";
    </script>
@endpush
@section('content')

    <form action="" id="exam" class="d-block" method="POST">
        @csrf
        <input type="hidden" name="first_question" id="first_question" value="{{ $questions[0]->id }}">
        <input type="hidden" name="current_question" id="current_question" value="{{ $questions[0]->id }}">
        <input type="hidden" name="current_section" id="current_section" value="{{ $questions[0]->exam_section_id }}">
        <input type="hidden" name="current_section_name" id="current_section_name"
            value="{{ $questions[0]->section->name }}">
        <input type="hidden" name="selected_section" id="selected_section"
            value="{{ session()->get('selected_section') }}">
        <input type="hidden" name="time_range_sections" id="time_range_sections"
            value="{{ $questions[0]->section->time_range_sections??5 }}">
        <input type="hidden" name="next_section" id="next_section"
            value="{{ !empty($exam->sections[session()->get('selected_section') + 1]) ? true : false }}">
        <input type="hidden" name="all_questions" id="all_questions" value="{{ count($questions) }}">
        <input type="hidden" name="show_time" id="show_time" value="true">
        <input type="hidden" name="time_exam" id="time_exam" value="0">
        <input type="hidden" name="time_end_exam" id="time_end_exam"
            value="{{ $exam->sections[session()->get('selected_section')]->duration??300 }}">
        <input type="hidden" name="section_start_time" id="section_start_time" value="0">
        <input type="hidden" name="marked_questions[]" id="marked_questions"
            value="{{ $exam_result->marked->pluck('question_id') }}">
        <input type="hidden" name="answered_questions[]" id="answered_questions">
        <input type="hidden" name="notanswered_questions[]" id="notanswered_questions">
        <input type="hidden" name="user_id" id="user_id" value="{{ auth('users')->id() }}">
        <input type="hidden" name="exam_id" id="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="exam_result_id" id="exam_result_id" value="{{ $exam_result->id }}">
        <input type="hidden" name="language" id="language" value="{{ app()->getLocale() }}">

        {{-- Question Time replies --}}
        @foreach ($questions as $key => $value)
            <input type="hidden" name="question_time_replies[{{ $value->id }}]"
                id="question_time_replies_{{ $value->id }}" value="0">

            <input type="hidden" name="question_answers_values[{{ $value->id }}]"
                id="question_answers_values_{{ $value->id }}" />
        @endforeach
        {{-- Question Time replies --}}

        <section class="exam_page {{ $exam->layout_type }}">
            @include('frontend.exams.exam_main_process.parts.header', [
                'exam' => $exam,
                'questions' => $questions,
            ])
            @include('frontend.exams.exam_main_process.parts.content', [
                'exam' => $exam,
                'questions' => $questions,
                'exam_result' => $exam_result,
            ])

            @include('frontend.exams.exam_main_process.parts.footer', [
                'exam' => $exam,
                'questions' => $questions,
            ])

            {{-- Desmos Calculator --}}
            <div id="desmoscalculator" class="modal custom-modal show" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-dark">
                            <button type="button" class="close" onclick="toggleModalnow('desmoscalculator', 'hide')"
                                data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <button class="btn btn-dark btn-sm desmos_expandable" id='desmos_expandable' type="button"
                            onclick="toggledesmosmodal('desmoscalculator')"><i class="fa fa-expand-alt"></i></button>
                        <div class="modal-body">
                            <iframe src="https://www.desmos.com/calculator/c2hvdjfpmi"
                                sandbox="allow-scripts allow-same-origin allow-popups"
                                style="border:0px #ffffff none;width:100%;height:100%;" name="myiFrame" scrolling="no"
                                frameborder="0" marginheight="0px" marginwidth="0px" allowfullscreen></iframe>
                        </div>

                    </div>
                </div>
                <br>
            </div>
            {{-- Desmos Calculator --}}

            {{-- References --}}
            <div id="references" class="modal custom-modal modal-lg show" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-dark">
                            <h3 class="text-white">@lang('additional.pages.exams.referances')</h3>
                            <button type="button" class="close text-white"
                                onclick="toggleModalnow('references', 'hide')" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            @if (count($exam->references) > 1)
                                <div class="references_top_buttons">
                                    <div></div>
                                    <div>
                                        <a href="javascript:void(0)"
                                            onclick="toggle_references_modal_content('open')">@lang('additional.pages.exams.references_open')</a>
                                        <a href="javascript:void(0)"
                                            onclick="toggle_references_modal_content('hide')">@lang('additional.pages.exams.references_hide')</a>
                                    </div>
                                </div>

                                @foreach ($exam->references as $key => $value)
                                    <div class="reference" id="reference_{{ $key }}">
                                        <div class="referance_title">
                                            <h4>{{ $value->reference->name[app()->getLocale() . '_name'] }}</h4>
                                            <a href="javascript:void(0)"
                                                id="toggler_button_reference_{{ $key }}"
                                                class="referance_toggle_button"
                                                onclick="toggle_references_modal_content_element({{ $key }})"><i
                                                    class="fa fa-plus"></i></a>
                                        </div>
                                        <div class="referance_body hide" id="body_reference_{{ $key }}">
                                            @if (isset($value->reference->image) && !empty($value->reference->image))
                                                <div class="col-sm-12 col-md-6 col-lg-8 img_area">
                                                    <img src="{{ getImageUrl($value->reference->image, 'exams') }}"
                                                        class="img-fluid img-responsive"
                                                        alt="{{ $value->reference->image }}">
                                                </div>
                                            @endif
                                            <div
                                                class="@if (isset($value->reference->image) && !empty($value->reference->image)) col-sm-12 col-md-6 col-lg-4 @else col-sm-12 col-md-12 col-lg-12 @endif">
                                                {!! $value->reference->description[app()->getLocale() . '_description'] !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif(count($exam->references) == 1)
                                <div class="reference" style="border:none;">
                                    <div class="referance_body d-block">
                                        @if (isset($exam->references[0]->reference->image) && !empty($exam->references[0]->reference->image))
                                            <div class="col-sm-12 col-md-12 col-lg-12 img_area">
                                                <img src="{{ getImageUrl($exam->references[0]->reference->image, 'exams') }}"
                                                    class="img-fluid img-responsive"
                                                    alt="{{ $exam->references[0]->reference->image }}">
                                            </div>
                                        @endif
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            {!! $exam->references[0]->reference->description[app()->getLocale() . '_description'] !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
                <br>
            </div>
            {{-- References --}}
        </section>
    </form>

    <div id="print_error" class="text text-danger w-100 text-center">
        @lang('additional.messages.noprint')
    </div>

    <div id="loader_for_sections" class="loader_for_sections">
        <div class="timer_section">
            <div class="hour_area">
                <span id="minutes_start_time">
                </span>:<span id="seconds_start_time"></span>
            </div>
        </div>

        <div class="time_section_arasi">
            @lang('additional.pages.exams.section_arasi_vaxd_gozle', ['time' => $exam->time_range_sections])
        </div>

        <div class="time_wait_please">
            <img src="{{ asset('front/assets/img/bg_images/time_wait_please.png') }}" alt="Time_wait_please">
        </div>

    </div>

@endsection 
@push('js')
    <script src="{{ asset('front/assets/js/exam_classes.js') }}"></script>
    <script src="{{ asset('front/assets/js/exam.js?v='.time()) }}"></script>

    <script>
        window.addEventListener('load', function() {
            setTimeout(() => {
                @foreach ($questions as $question)
                set_question_value_on_session({{ $question->id }}, null,
                    '{{ App\Models\ExamQuestion::TYS[$question->type] }}');
            @endforeach
            }, 1200);
        });
    </script>

@endpush
