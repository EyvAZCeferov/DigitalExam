<input type="hidden" name="current_section_id" id="current_section_id">

<div class="row classjustifstart sections_area" id="sections_area">
    <div id="section_elements" class="element">
        @if (!empty($data->sections) && count($data->sections) > 0)
            @foreach ($data->sections as $key => $value)
                @if ($key == 0)
                    <script defer>
                        document.getElementById('current_section').value = {{ $value->id }};
                        window.addEventListener('load', function() {
                            get_section({{ $value->id }});
                        });
                    </script>
                @endif
                <div class="section_el">
                    <div class="section_element" onclick="get_section({{ $value->id }})"
                        id="section_element_{{ $value->id }}">
                        {{ $value->name }}

                    </div>
                    <div class="d-flex mt-1">
                        <button class="btn btn-warning btn-sm mt-1"
                            type="button"onclick="create_edit_section(event,{{ $value->id }})"><i
                                class="fa fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm mt-1" type="button"
                            onclick="deletequestion({{ $value->id }},'section')"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="d-inline-block element">
        <button class="btn btn-sm btn-primary create_section_button" onclick="toggleModalnow('create_sections', 'open')"
            type="button">@lang('additional.pages.exams.sections') @lang('additional.buttons.add') </button>
    </div>
</div>
<div class="row section_and_questions hide" id="section_and_questions">
    <div class="left" id="section_and_questions_left">
        <button type="button" class="btn btn-primary create_question" onclick="create_question()">@lang('additional.buttons.createquestion') <i
                class="fa fa-plus"></i></button>
        <div class="questions_list">

        </div>
    </div>
    <div class="right" id="section_and_questions_right">

    </div>
</div>
