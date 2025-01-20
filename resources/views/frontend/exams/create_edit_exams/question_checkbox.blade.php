<div class="answers">
    @php($answerid = createRandomCode('string', 11))
    <div class="answer multi" id="{{ $answerid }}">
        <div class="answer_content">
            <label class="radio-input" onclick="change_radio('multi', 0)">
                <input type="checkbox" name="answers[0]" value="0" class="input_radios" id="input_radios_0">
                <span class="checkmark" id="label_checkmark_0" ></span>
            </label>

            <span name="answer_reply[0]" id="answer__input_{{ $answerid }}" name="answer_reply[0]"
                class="text-input summernote_element" placeholder="@lang('additional.forms.answer')" contenteditable="true"
                placeholder="@lang('additional.forms.your_question')"></span>
        </div>
        <button type="button" class="btn btn-sm btn-outline-success add_remove_buttons add_button"
            onclick="addoreditanswer('multi','add','{{ $answerid }}')"><i class="fa fa-plus"></i></button>
    </div>
</div>
