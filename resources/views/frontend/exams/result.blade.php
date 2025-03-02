@extends('frontend.layouts.app')
@section('title', $exam->name[app()->getLocale() . '_name'])
@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css"
    integrity="sha384-n8MVd4RsNIU0tAv4ct0nTaAbDJwPJzDEaqSD1odI+WdtXRGWt2kTvGFasHpSy3SV" crossorigin="anonymous">
@endpush

@section('content')
<form onsubmit="return;" id="exam" class="d-block" method="POST">

    <input type="hidden" name="first_question" id="first_question" value="{{ $exam->getquestions($exam_result)[0]->id }}" />
        <input type="hidden" name="current_question" id="current_question" value="{{ $exam->getquestions($exam_result)[0]->id }}" />
        <input type="hidden" name="current_section" id="current_section" value="{{ $exam->getquestions($exam_result)[0]->exam_section_id }}" />
        <input type="hidden" name="current_section_name" id="current_section_name"
            value="{{ $exam->getquestions($exam_result)[0]->section->name }}" />
        <input type="hidden" name="selected_section" id="selected_section"
            value="{{ session()->get('selected_section') }}" />
        <input type="hidden" name="time_range_sections" id="time_range_sections" value="{{ $exam->time_range_sections }}" />
        <input type="hidden" name="next_section" id="next_section"
            value="{{ !empty($exam->sections[session()->get('selected_section')]) ? true : false }}" />
        <input type="hidden" name="all_questions" id="all_questions" value="{{ count($exam->getquestions($exam_result)) }}" />
        <input type="hidden" name="show_time" id="show_time" value="true">
        <input type="hidden" name="time_exam" id="time_exam" value="0">
        <input type="hidden" name="section_start_time" id="section_start_time" value="0">
        <input type="hidden" id="time_end_exam" value="{{ $exam_result->time_reply??0 }}" />
        <input type="hidden" name="marked_questions[]" id="marked_questions"
            value="" />
        <input type="hidden" name="answered_questions[]" id="answered_questions">
        <input type="hidden" name="notanswered_questions[]" id="notanswered_questions">
        <input type="hidden" name="user_id" id="user_id" value="{{ auth('users')->id() }}" />
        <input type="hidden" name="exam_id" id="exam_id" value="{{ $exam->id }}" />
        <input type="hidden" name="exam_result_id" id="exam_result_id" value="{{ $exam_result->id }}" />
        <input type="hidden" name="language" id="language" value="{{ app()->getLocale() }}" />
        <input type="hidden" id="page_type" value="result" />

    <section class="exam_page">

        @include('frontend.exams.exam_main_process.parts.header_result', [
            'exam_result' => $exam_result,
        ])
        @include('frontend.exams.exam_main_process.parts.content', [
            'exam' => $exam,
            'questions' => $exam->getquestions($exam_result),
            'exam_result' => $exam_result,
            'hide_abc' => 'hide',
            'page'=>"result"
        ])
        @include('frontend.exams.exam_main_process.parts.footer', [
            'exam' => $exam,
            'questions' => $exam->getquestions($exam_result),
            'exam_result' => $exam_result,
            'page'=>'result',
        ])

        <div id="loader_for_sections" class="loader_for_sections">
            <div class="timer_section">
                <div class="hour_area">
                    <span id="minutes_start_time">
                    </span>:<span id="seconds_start_time"></span>
                </div>
            </div>

        </div>

    </section>
</form>
@endsection


@push('js')
    <script  src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"> </script>
    <script  src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js" onload="renderMathInElement(document.body);"></script>
    <script  src="https://cdn.jsdelivr.net/npm/webfontloader@1.6.28/webfontloader.js"></script>
    <script>
        window.WebFontConfig = {
            custom: {
                families: ['KaTeX_AMS', 'KaTeX_Caligraphic:n4,n7', 'KaTeX_Fraktur:n4,n7',
                    'KaTeX_Main:n4,n7,i4,i7', 'KaTeX_Math:i4,i7', 'KaTeX_Script',
                    'KaTeX_SansSerif:n4,n7,i4', 'KaTeX_Size1', 'KaTeX_Size2', 'KaTeX_Size3',
                    'KaTeX_Size4', 'KaTeX_Typewriter'
                ],
            },
        };
    </script>
    
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
   <script src="{{ asset("front/assets/js/exam.js?v=".time()) }}" ></script>
@endpush
