@php
    $exam = $exam_result->exam;
@endphp
<div class="header_columns">
    <h1 id="section_name" class="section_name">{{ $exam->sections[0]->name }}</h1>
    <div class="timer_section" id="timer_section">
        <div class="hour_area">
            <span id="minutes">{{ convertToMinutesAndSeconds($exam_result->timereplyall()) }}</span>
        </div>
    </div>

    <div class="right_section">
        <a class="section calculator d-flex" href="javascript:void(0)">
            @lang('additional.pages.exams.earned_point'): <span class="d-inline-block font-weight-bold">{{ $exam_result->point }}</span>
        </a>
    </div>

</div>
