<div class="answers">
    @php($answerid = createRandomCode('string', 11))
    <div class="answer textbox" id="{{ $answerid }}">
        <div class="answer_content" 
            {{-- style="border:none;width:100% !important;display:block !important;" --}}
        >
            <div rows="5" name="textbox_0" class="text-input textbox_0" id="txt_{{ $answerid }}" placeholder="@lang('additional.forms.answer')"></div>
        </div>
    </div>
</div>
<p class="text-muted notification_element">Cavabları (əgər birdən çox düzgün cavab varsa) vergül ilə ayıra bilərsiniz</p>
