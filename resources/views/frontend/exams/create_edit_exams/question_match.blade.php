<div class="answers sortable" id="match">
    @php($answerid = createRandomCode('string', 11))
    <div class="answer match" id="{{ $answerid }}">
        <div class="answer_content">
            <div class="sides left_side">
                <div class="content_element matching_element" data-key="0" id="mathing_questions_0" name="mathing_questions[0]" placeholder="@lang('additional.forms.answer')" contenteditable="true"></div>
            </div>
            <div class="sides right_side">
                <div class="content_element matching_element" data-key="0" id="mathing_answers_0" name="mathing_answers[0]" placeholder="@lang('additional.forms.answer')" contenteditable="true"></div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-success add_remove_buttons add_button"
            onclick="addoreditanswer('match','add','{{ $answerid }}')"><i class="fa fa-plus"></i></button>
    </div>
</div>
