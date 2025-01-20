@foreach ($question->answers as $key => $value)
    <div class="question_answer_one_element_container"
        id="question_answer_one_element_container_{{ $question->id }}_{{ $value->id }}">
        <div id="question_answer_one_{{ $question->id }}_{{ $value->id }}_radio"
            class="question_answer_one answers_{{ $question->id }}
        @if (answer_result_true_or_false($question->id, $value->id) == true) true @else false @endif"
            onclick="showuserswhichanswered({{ $exam->id }}, {{ $question->id }}, 1,{{ $value->id }})"
            >
            <input type="radio" name="answers[{{ $value->question->exam_section_id }}][{{ $question->id }}]"
                value="{{ $value->id }}">
            <div class="text text-muted sizing_counters_forquestion_value">
                {{ count(get_answer_choised($exam_results->pluck('id'), $question->id, 1, $value->id)) }}
            </div>
            <div class="rowNumber">{{ int_to_abcd_value($key) }}</div>
            <div class="question">
                {!! $value->answer !!}
            </div>
        </div>
    </div>
@endforeach
