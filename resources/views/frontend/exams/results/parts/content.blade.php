<div class="conlol hide" id="showfinishmodal">
    <div class="footer_questions" id="footer_questions_top">
    </div>
</div>
<div id="content_area_exam" class="conlol">
    @foreach ($questions as $key => $value)
        <div class="content_exam @if ($key == 0) show @endif {{ $value->layout }}"
            data-key="{{ $key + 1 }}" data-id="{{ $value->id }}" id="content_exam_{{ $value->id }}"
            data-section_id="{{ $value->exam_section_id }}" data-section_name="{{ $value->section->name }}">
            <div class="col left_col" id="left_col">
                @if ($value->layout == 'onepage')
                    <div class="question_header">
                        <div>
                            <span class="question_number">{{ $key + 1 }}</span>
                            <a href="javascript:void(0)" onclick="mark_list_questions({{ $value->id }})"
                                id="mark_question_button_{{ $value->id }}" class="mark_button"> <i
                                    class="far fa-bookmark"></i> </a>
                            <span class="info_text">@lang('additional.pages.exams.question_info_text')</span>
                        </div>
                        <div>
                            @if ($value->type != 4 && $value->type != 3 && !isset($hide_abc))
                                <a href="javascript:void(0)" class="remove_button" onclick="remove_button_toggler()">
                                    ABC
                                </a>
                            @endif
                        </div>

                    </div>
                @endif
                @if ($value->type != 5)
                    <div class="buttons_top_aplusandminus">
                        <div></div>
                        <div class="buttons">
                            <a href="javascript:void(0)" class="button left"
                                onclick="increase_decrease_font('increase')">A+</a>
                            <a href="javascript:void(0)" class="button right"
                                onclick="increase_decrease_font('decrease')">A-</a>
                        </div>
                    </div>
                @endif
                <div class="content_exam_info @if ($value->type == 5) classcenter @endif"
                    id="content_exam_info" style="flex-direction: column">
                    @if ($value->type == 5)
                        <div class="audio_tag_text">
                            {!! $value->question !!}
                        </div>
                        <br />
                        <audio @if ($exam->repeat_sound == false) class="only1time" @endif id="audio_{{ $value->id }}"
                            controlsList="nodownload" controls>
                            <source src="{{ getImageUrl($value->file, 'exam_questions') }}" type="audio/mpeg">Your
                            browser does not support the audio element.
                        </audio>
                    @elseif($value->type==3 && !empty($value->description))
                        {!! $value->description !!}
                    @else
                        {!! $value->question !!}
                    @endif
                </div>
            </div>

            @if ($value->layout != 'onepage')
                <div id="resizer" class="resizer"></div>
            @endif
            <div class="col right_col" id="right_col">
                @if ($value->layout != 'onepage')
                    <div class="question_header">
                        <div>
                            <span class="question_number">{{ $key + 1 }}</span>
                            <a href="javascript:void(0)" onclick="mark_list_questions({{ $value->id }})"
                                id="mark_question_button_{{ $value->id }}" class="mark_button"> <i
                                    class="far fa-bookmark"></i> </a>
                            <span class="info_text">@lang('additional.pages.exams.question_info_text')</span>
                        </div>
                        <div>
                            @if ($value->type != 4 && $value->type != 3 && !isset($hide_abc))
                                <a href="javascript:void(0)" class="remove_button" onclick="remove_button_toggler()">
                                    ABC
                                </a>
                            @endif
                        </div>

                    </div>
                @endif
                @if($value->type==3 && !empty($value->description))
                    <div class="content_exam_info mt-3 @if($value->type==3) classcenter @endif" id="content_exam_info" style="flex-direction: column">
                        {!! $value->question !!}
                    </div>
                @endif
                <div class="question_content">
                    @if ($value->type == 1 || $value->type == 5)
                        @include('frontend.exams.results.parts.question_radio', [
                            'question' => $value,
                            'exam_results' => $exam_results,
                        ])
                    @elseif($value->type == 2)
                        @include('frontend.exams.results.parts.question_checkbox', [
                            'question' => $value,
                            'exam_results' => $exam_results,
                        ])
                    @elseif($value->type == 3)
                        @include('frontend.exams.results.parts.question_textbox', [
                            'question' => $value,
                            'exam_results' => $exam_results,
                        ])
                    @elseif($value->type == 4)
                       @include('frontend.exams.results.parts.question_match', [
                           'question' => $value,
                           'exam_results' => $exam_results,
                       ])
                    @endif
                </div>

            </div>
        </div>
    @endforeach
</div>
