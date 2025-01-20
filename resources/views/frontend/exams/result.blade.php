@extends('frontend.layouts.app')
@section('title', $exam_result->exam->name[app()->getLocale() . '_name'])
@section('content')
    @php
        $exam = $exam_result->exam;
        $questions = collect();
        $qesutions = $exam->sections->pluck('questions');
        foreach ($qesutions as $qesution) {
            foreach ($qesution as $key => $qest) {
                $questions[] = $qest;
            }
        }
    @endphp
    <section class="exam_page">
        <input type="hidden" name="first_question" id="first_question" value="{{ $questions[0]->id }}">
        <input type="hidden" name="current_question" id="current_question" value="{{ $questions[0]->id }}">
        <input type="hidden" name="current_section" id="current_section" value="{{ $questions[0]->exam_section_id }}">
        <input type="hidden" name="current_section_name" id="current_section_name"
            value="{{ $questions[0]->section->name }}">
        <input type="hidden" name="selected_section" id="selected_section"
            value="{{ session()->get('selected_section') }}">
        <input type="hidden" name="time_range_sections" id="time_range_sections" value="{{ $exam->time_range_sections }}">
        <input type="hidden" name="next_section" id="next_section"
            value="{{ !empty($exam->sections[session()->get('selected_section')]) ? true : false }}">
        <input type="hidden" name="all_questions" id="all_questions" value="{{ count($questions) }}">
        <input type="hidden" name="show_time" id="show_time" value="true">
        <input type="hidden" name="time_exam" id="time_exam" value="0">
        <input type="hidden" name="section_start_time" id="section_start_time" value="0">
        <input type="hidden" name="marked_questions[]" id="marked_questions"
            value="{{ $exam_result->marked->pluck('question_id') }}">
        <input type="hidden" name="answered_questions[]" id="answered_questions">
        <input type="hidden" name="notanswered_questions[]" id="notanswered_questions">
        <input type="hidden" name="user_id" id="user_id" value="{{ auth('users')->id() }}">
        <input type="hidden" name="exam_id" id="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="exam_result_id" id="exam_result_id" value="{{ $exam_result->id }}">
        <input type="hidden" name="language" id="language" value="{{ app()->getLocale() }}">
        @include('frontend.exams.exam_main_process.parts.header_result', [
            'exam_result' => $exam_result,
        ])
        @include('frontend.exams.exam_main_process.parts.content', [
            'exam' => $exam,
            'questions' => $questions,
            'exam_result' => $exam_result,
            'hide_abc' => 'hide',
        ])
        @include('frontend.exams.exam_main_process.parts.footer', [
            'exam' => $exam,
            'questions' => $questions,
            'exam_result' => $exam_result,
        ])
    </section>
@endsection


@push('js')
    {{-- Footer Buttons --}}
    <script defer>
        function toback() {
            var current_question = document.getElementById("current_question").value;
            var first_question = document.getElementsByClassName('content_exam')[0];
            var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
            if (current_question == first_question.dataset.id) {
                return;
            } else {
                currentDivQuestion.classList.remove("show");
                var new_key = parseInt(currentDivQuestion.dataset.key) - 1;
                var nextDivQuestion = document.querySelectorAll(`.content_exam[data-key="${new_key}"]`);
                nextDivQuestion.forEach(function(element) {
                    element.classList.add("show");
                    document.getElementById("current_question").value = element.dataset.id;
                    document.getElementById("current_section_name").value = element.dataset.section_name;
                    document.getElementById("current_section").value = element.dataset.section_id;
                });
            }
        }

        function togglequestions() {
            var footer_questions = document.getElementById('footer_questions');
            if (footer_questions.classList.contains('active')) {
                footer_questions.classList.remove("active");
            } else {
                footer_questions.classList.add("active");
            }
        }

        function tonext(tolast=false) {
            var current_question = document.getElementById("current_question").value;
            var all_questions = document.getElementById("all_questions").value;
            var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
            var time_range_sections = document.getElementById("time_range_sections").value;
            var next_section = document.getElementById("next_section").value;
            var section_start_time = document.getElementById("section_start_time");
            var loader_for_sections = document.getElementById("loader_for_sections");
            var form = document.getElementById("exam");
            if (all_questions == currentDivQuestion.dataset.key) {
                window.location.href="/";
            } else {
                currentDivQuestion.classList.remove("show");
                var new_key = parseInt(currentDivQuestion.dataset.key) + 1;
                var nextDivQuestion = document.querySelectorAll(`.content_exam[data-key="${new_key}"]`);
                nextDivQuestion.forEach(function(element) {
                    element.classList.add("show");
                    document.getElementById("current_question").value = element.dataset.id;
                    document.getElementById("current_section_name").value = element.dataset.section_name;
                    document.getElementById("current_section").value = element.dataset.section_id;
                });
            }
        }

        function updatepad() {
            var current_question = document.getElementById("current_question").value;
            var first_question = document.getElementById("first_question").value;

            var footer_question_buttons = document.getElementsByClassName('footer_question_buttons');
            var marked_questions = document.getElementById('marked_questions').value;

            for (var i = 0; i < footer_question_buttons.length; i++) {
                const element_for_saved = footer_question_buttons[i];
                element_for_saved.classList.remove("current");
                if (marked_questions != null && marked_questions.length > 0) {
                    marked_questions_jsoned = JSON.parse(marked_questions);
                    var buttonDataKey = element_for_saved.getAttribute('data-key');
                    if (searchinarray(marked_questions_jsoned, buttonDataKey) == true) {
                        element_for_saved.classList.add("saved");
                    }
                }
            }

            if (current_question == first_question) {
                document.getElementById("to_back").classList.add('hide');
            } else {
                if (document.getElementById("to_back").classList.contains('hide'))
                    document.getElementById("to_back").classList.remove('hide');
            }
            var all_questions = document.getElementById("all_questions").value;
            var footer_active_button = document.getElementById(`question_row_button_${current_question}`);
            var buttons = document.getElementsByClassName("btn-question");
            var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
            var next_button = document.getElementById("next_button");
            var time_range_sections = document.getElementById("time_range_sections").value;
            var next_section = document.getElementById("next_section").value;
            if (all_questions == currentDivQuestion.dataset.key) {
                next_button.classList.remove("btn-secondary");
                next_button.classList.add("active");

                if (time_range_sections > 0) {
                    if (next_section == 1) {
                        next_button.innerHTML =
                            `@lang('additional.buttons.nextsection') @if ($exam->layout_type == 'standart')<i class="fa fa-angle-right"></i>@endif`;
                    } else {
                        next_button.innerHTML =
                            `@lang('additional.buttons.finish') @if ($exam->layout_type == 'standart')<i class="fa fa-check"></i>@endif`;
                    }
                } else {
                    next_button.innerHTML =
                        `@lang('additional.buttons.finish') @if ($exam->layout_type == 'standart')<i class="fa fa-check"></i>@endif`;
                }
            } else {
                next_button.classList.add("btn-secondary");
                next_button.classList.remove("active");
                next_button.innerHTML =
                    `@lang('additional.buttons.next')@if ($exam->layout_type == 'standart') <i class="fa fa-angle-right"></i> @endif`;
            }

            var current_question_text = document.getElementById("current_question_text");
            current_question_text.innerText = currentDivQuestion.dataset.key;

            for (var i = 0; i < buttons.length; i++) {
                if (buttons[i].id == `question_row_button_${current_question}`)
                    buttons[i].classList.add("current");

            }

            var section_name_area = document.getElementById("section_name");
            var current_section_name = document.getElementById("current_section_name").value;
            section_name_area.innerText = current_section_name;

        }

        function searchinarray(obj, searchVal) {
            var result = false;
            for (var i = 0; i < obj.length; i++) {
                if (obj[i] == searchVal) {
                    result = true;
                    break;
                }
            }

            return result;
        }

        function getquestion(id) {
            var activecontentquestions = document.getElementsByClassName("content_exam");
            for (var i = 0; i < activecontentquestions.length; i++) {
                activecontentquestions[i].classList.remove("show");
            }

            var selected = document.getElementById(`content_exam_${id}`);
            selected.classList.add("show");
            document.getElementById("current_question").value = id;
            togglequestions();
        }

        setInterval(updatepad, 500);
    </script>

    <script defer>
        const leftCol = document.getElementsByClassName('left_col');
        const resizer = document.getElementsByClassName('resizer');
        let isResizing = false;
        let offsetX = 0;

        function draggingleftandrightcolumns() {
            for (let index = 0; index < resizer.length; index++) {
                const element = resizer[index];
                element.addEventListener("mousedown", (e) => {
                    e.preventDefault();
                    isResizing = true;
                    offsetX = element.clientWidth / 2;
                    document.addEventListener("mousemove", resize);
                    document.addEventListener("mouseup", () => {
                        isResizing = false;
                        document.removeEventListener("mousemove", resize);
                    });
                });
                element.addEventListener("mouseover", (e) => {
                    if (!isResizing) {
                        element.style.opacity = 1;
                    }
                });
                element.addEventListener("mouseleave", (e) => {
                    if (!isResizing) {
                        element.style.opacity = 0.5;
                    }
                });
            }
        }

        function resize(e) {
            if (!isResizing) return;
            var minusable=0;
            if(e.layerX>0){
                minusable=e.clientX - (e.layerX+e.srcElement.offsetLeft);
            }else{
                minusable=e.clientX-e.srcElement.offsetLeft;
            }
            const size = `${e.clientX - minusable}px`;
            for (let index = 0; index < leftCol.length; index++) {
                const element = leftCol[index];
                element.style.width = size;
            }
        }

        window.addEventListener('load', function() {
            draggingleftandrightcolumns();
        });
    </script>
    {{-- Footer Buttons --}}
    <script defer>
        function increase_decrease_font(type) {
            var elements = document.getElementsByClassName('content_exam_info');

            for (var i = 0; i < elements.length; i++) {
                var fontSize = parseInt(window.getComputedStyle(elements[i]).fontSize); // Mevcut font boyutunu al

                if (type === "increase") {
                    elements[i].style.fontSize = (fontSize + 1) + 'px'; // Font boyutunu artır
                } else if (type === "decrease") {
                    elements[i].style.fontSize = (fontSize - 1) + 'px'; // Font boyutunu azalt
                }

                // p ve span elementlerini bul
                var pElements = elements[i].getElementsByTagName('p');
                var spanElements = elements[i].getElementsByTagName('span');

                // p elementlerinin font boyutunu değiştir
                for (var j = 0; j < pElements.length; j++) {
                    var pFontSize = parseInt(window.getComputedStyle(pElements[j]).fontSize);
                    if (type === "increase") {
                        pElements[j].style.fontSize = (pFontSize + 1) + 'px';
                    } else if (type === "decrease") {
                        pElements[j].style.fontSize = (pFontSize - 1) + 'px';
                    }
                }

                // span elementlerinin font boyutunu değiştir
                for (var k = 0; k < spanElements.length; k++) {
                    var spanFontSize = parseInt(window.getComputedStyle(spanElements[k]).fontSize);
                    if (type === "increase") {
                        spanElements[k].style.fontSize = (spanFontSize + 1) + 'px';
                    } else if (type === "decrease") {
                        spanElements[k].style.fontSize = (spanFontSize - 1) + 'px';
                    }
                }
            }
        }
    </script>
@endpush
