<div id="question_textbox_{{ $question->id }}">
    <textarea maxlength="5" rows="10" onkeyup="changeTextBox('{{ $question->id }}','textbox')"
        id="question_answer_one_{{ $question->id }}_textbox"
        name="answers[{{ $question->exam_section_id }}]['{{ $question->id }}']"
        class="form-control {{ $exam->layout_type }} @if ($page=='result') @if (answer_result_true_or_false($question->id, $value->id) == true) true @else false @endif @endif ">{{ $page=='result' && !empty($question->correctAnswer()) && isset($question->correctAnswer()->answer) && !empty($question->correctAnswer()->answer) ? strip_tags_with_whitespace($question->correctAnswer()->answer) : null }}</textarea>
    <div class='question_textbox_text' id="question_textbox_text_{{ $question->id }}">@lang('additional.pages.exams.youranswer'): <span
            class='text text-dark' id='question_textbox_text_span_{{ $question->id }}'>
            @if ($page=='result')
                @if (your_answer_result_true_or_false($question->id, $value->id, $exam_result->id) !== null)
                    {{ your_answer_result_true_or_false($question->id, $value->id, $exam_result->id) }} @endif
            @endif
        </span></div>
</div>
