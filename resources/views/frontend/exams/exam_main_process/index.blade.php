@extends('frontend.layouts.exam_layout')
@section('title', $exam->name[app()->getLocale() . '_name'])
@section('description', $exam->description[app()->getLocale() . '_description'] ?? '')
@push('css')
    <style type="text/css" media="print">
        * {
            display: none;
        }

        #print_error {
            display: block;
        }
    </style>
    <style>
        #desmoscalculator {
            z-index: 9999999999;
        }
    </style>
@endpush
@section('content')
    @php
        $questions = collect();

        if (session()->has('selected_section')) {
            if (!empty($exam->sections) && count($exam->sections) > 0) {
                $selectedsection = $exam->sections[session()->get('selected_section')];
                $qesutions = $selectedsection->questions;

                // Check if there are enough questions available
                if ($selectedsection->question_count > 0) {
                    $availableQuestionCount = $qesutions->count();

                    if ($selectedsection->random == true) {
                        // Ensure we don't request more than available
                $questions = $qesutions->random(min($selectedsection->question_count, $availableQuestionCount));
            } else {
                $questions = $qesutions->take($selectedsection->question_count);
            }
        } else {
            foreach ($qesutions as $qesution) {
                $questions[] = $qesution;
            }
        }
    } else {
        $qesutions = $exam->sections->pluck('questions');
        foreach ($qesutions as $qesution) {
            foreach ($qesution as $qest) {
                $questions[] = $qest;
            }
        }
    }
} else {
    return abort(403, 'Administrator ilə əlaqə saxlayın');
        }
    @endphp

    <form action="" id="exam" class="d-block" method="POST">
        @csrf
        <input type="hidden" name="first_question" id="first_question" value="{{ $questions[0]->id }}">
        <input type="hidden" name="current_question" id="current_question" value="{{ $questions[0]->id }}">
        <input type="hidden" name="current_section" id="current_section" value="{{ $questions[0]->exam_section_id }}">
        <input type="hidden" name="current_section_name" id="current_section_name"
            value="{{ $questions[0]->section->name }}">
        <input type="hidden" name="selected_section" id="selected_section"
            value="{{ session()->get('selected_section') }}">
        <input type="hidden" name="time_range_sections" id="time_range_sections"
            value="{{ $questions[0]->section->time_range_sections }}">
        <input type="hidden" name="next_section" id="next_section"
            value="{{ !empty($exam->sections[session()->get('selected_section') + 1]) ? true : false }}">
        <input type="hidden" name="all_questions" id="all_questions" value="{{ count($questions) }}">
        <input type="hidden" name="show_time" id="show_time" value="true">
        <input type="hidden" name="time_exam" id="time_exam" value="0">
        <input type="hidden" name="time_end_exam" id="time_end_exam"
            value="{{ $exam->sections[session()->get('selected_section')]->duration }}">
        <input type="hidden" name="section_start_time" id="section_start_time" value="0">
        <input type="hidden" name="marked_questions[]" id="marked_questions"
            value="{{ $exam_result->marked->pluck('question_id') }}">
        <input type="hidden" name="answered_questions[]" id="answered_questions">
        <input type="hidden" name="notanswered_questions[]" id="notanswered_questions">
        <input type="hidden" name="user_id" id="user_id" value="{{ auth('users')->id() }}">
        <input type="hidden" name="exam_id" id="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="exam_result_id" id="exam_result_id" value="{{ $exam_result->id }}">
        <input type="hidden" name="language" id="language" value="{{ app()->getLocale() }}">

        {{-- Question Time replies --}}
        @foreach ($questions as $key => $value)
            <input type="hidden" name="question_time_replies[{{ $value->id }}]"
                id="question_time_replies_{{ $value->id }}" value="0">

            <input type="hidden" name="question_answers_values[{{ $value->id }}]"
                id="question_answers_values_{{ $value->id }}" />
        @endforeach
        {{-- Question Time replies --}}

        <section class="exam_page {{ $exam->layout_type }}">
            @include('frontend.exams.exam_main_process.parts.header', [
                'exam' => $exam,
                'questions' => $questions,
            ])
            @include('frontend.exams.exam_main_process.parts.content', [
                'exam' => $exam,
                'questions' => $questions,
                'exam_result' => $exam_result,
            ])

            @include('frontend.exams.exam_main_process.parts.footer', [
                'exam' => $exam,
                'questions' => $questions,
            ])

            {{-- Desmos Calculator --}}
            <div id="desmoscalculator" class="modal custom-modal show" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-dark">
                            <button type="button" class="close" onclick="toggleModalnow('desmoscalculator', 'hide')"
                                data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <button class="btn btn-dark btn-sm desmos_expandable" id='desmos_expandable' type="button"
                            onclick="toggledesmosmodal('desmoscalculator')"><i class="fa fa-expand-alt"></i></button>
                        <div class="modal-body">
                            <iframe src="https://www.desmos.com/calculator/c2hvdjfpmi"
                                style="border:0px #ffffff none;width:100%;height:100%;" name="myiFrame" scrolling="no"
                                frameborder="0" marginheight="0px" marginwidth="0px" allowfullscreen></iframe>
                        </div>

                    </div>
                </div>
                <br>
            </div>
            {{-- Desmos Calculator --}}

            {{-- References --}}
            <div id="references" class="modal custom-modal modal-lg show" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-dark">
                            <h3 class="text-white">@lang('additional.pages.exams.referances')</h3>
                            <button type="button" class="close text-white"
                                onclick="toggleModalnow('references', 'hide')" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            @if (count($exam->references) > 1)
                                <div class="references_top_buttons">
                                    <div></div>
                                    <div>
                                        <a href="javascript:void(0)"
                                            onclick="toggle_references_modal_content('open')">@lang('additional.pages.exams.references_open')</a>
                                        <a href="javascript:void(0)"
                                            onclick="toggle_references_modal_content('hide')">@lang('additional.pages.exams.references_hide')</a>
                                    </div>
                                </div>

                                @foreach ($exam->references as $key => $value)
                                    <div class="reference" id="reference_{{ $key }}">
                                        <div class="referance_title">
                                            <h4>{{ $value->reference->name[app()->getLocale() . '_name'] }}</h4>
                                            <a href="javascript:void(0)"
                                                id="toggler_button_reference_{{ $key }}"
                                                class="referance_toggle_button"
                                                onclick="toggle_references_modal_content_element({{ $key }})"><i
                                                    class="fa fa-plus"></i></a>
                                        </div>
                                        <div class="referance_body hide" id="body_reference_{{ $key }}">
                                            @if (isset($value->reference->image) && !empty($value->reference->image))
                                                <div class="col-sm-12 col-md-6 col-lg-8 img_area">
                                                    <img src="{{ getImageUrl($value->reference->image, 'exams') }}"
                                                        class="img-fluid img-responsive"
                                                        alt="{{ $value->reference->image }}">
                                                </div>
                                            @endif
                                            <div
                                                class="@if (isset($value->reference->image) && !empty($value->reference->image)) col-sm-12 col-md-6 col-lg-4 @else col-sm-12 col-md-12 col-lg-12 @endif">
                                                {!! $value->reference->description[app()->getLocale() . '_description'] !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif(count($exam->references) == 1)
                                <div class="reference" style="border:none;">
                                    <div class="referance_body d-block">
                                        @if (isset($exam->references[0]->reference->image) && !empty($exam->references[0]->reference->image))
                                            <div class="col-sm-12 col-md-12 col-lg-12 img_area">
                                                <img src="{{ getImageUrl($exam->references[0]->reference->image, 'exams') }}"
                                                    class="img-fluid img-responsive"
                                                    alt="{{ $exam->references[0]->reference->image }}">
                                            </div>
                                        @endif
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            {!! $exam->references[0]->reference->description[app()->getLocale() . '_description'] !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
                <br>
            </div>
            {{-- References --}}
        </section>
    </form>

    <div id="print_error" class="text text-danger w-100 text-center">
        @lang('additional.messages.noprint')
    </div>

    <div id="loader_for_sections" class="loader_for_sections">
        <div class="timer_section">
            <div class="hour_area">
                <span id="minutes_start_time">
                </span>:<span id="seconds_start_time"></span>
            </div>
        </div>

        <div class="time_section_arasi">
            @lang('additional.pages.exams.section_arasi_vaxd_gozle', ['time' => $exam->time_range_sections])
        </div>

        <div class="time_wait_please">
            <img src="{{ asset('front/assets/img/bg_images/time_wait_please.png') }}" alt="Time_wait_please">
        </div>

    </div>

@endsection
@push('js')
    {{-- Header Buttons --}}
    <script>
        let intervalTimerID;
        let question_time_reply = 0;
        let sec = 0;
        let qalan_vaxt;
        const time_end_exam = document.getElementById('time_end_exam').value;
        let finishmodalshowed = false;
        let redirect_url = null;
        let allowReload = false;
        const countDownDate = new Date(Date.now() + (time_end_exam * 60 * 1000)).getTime();
        const time_range_sections = document.getElementById("time_range_sections");

        function togglehours() {
            var clock_area = document.getElementById("timer_section");
            var clock_toggle_button = document.getElementById("timer_button");
            if (clock_area.classList.contains("hide")) {
                clock_area.classList.remove("hide");
                clock_toggle_button.text = '@lang('additional.buttons.hide')';
            } else {
                clock_area.classList.add("hide");

                clock_toggle_button.text = '@lang('additional.buttons.show')';
            }
        }

        function togglecalculator() {
            $('#desmoscalculator').toggle();
            //jquery Draggable
            $('#desmoscalculator').draggable({
                drag: function(event, ui) {
                    $(this).css({
                        'top': ui.position.top + 'px',
                        'left': ui.position.left + 'px'
                    });
                },
                touchStart: function(event, ui) {
                    var offsetX = event.originalEvent.touches[0].pageX - $(this).offset().left;
                    var offsetY = event.originalEvent.touches[0].pageY - $(this).offset().top;
                    $(this).data('offset', {
                        x: offsetX,
                        y: offsetY
                    });
                },
                touchMove: function(event, ui) {
                    var offset = $(this).data('offset');
                    var x = event.originalEvent.touches[0].pageX - offset.x;
                    var y = event.originalEvent.touches[0].pageY - offset.y;
                    $(this).css({
                        'top': y + 'px',
                        'left': x + 'px'
                    });
                }
            });

        }

        function touchHandler(event) {
            var touch = event.changedTouches[0];

            var simulatedEvent = document.createEvent("MouseEvent");
            simulatedEvent.initMouseEvent({
                    touchstart: "mousedown",
                    touchmove: "mousemove",
                    touchend: "mouseup"
                } [event.type], true, true, window, 1,
                touch.screenX, touch.screenY,
                touch.clientX, touch.clientY, false,
                false, false, false, 0, null);

            touch.target.dispatchEvent(simulatedEvent);
        }

        function init() {
            document.addEventListener("touchstart", touchHandler, true);
            document.addEventListener("touchmove", touchHandler, true);
            document.addEventListener("touchend", touchHandler, true);
            document.addEventListener("touchcancel", touchHandler, true);
        }

        window.addEventListener('load',function(){
            init();
        })

        function togglereferances() {
            $('#references').toggle();
            $('#references').draggable();
        }
    </script>
    {{-- Header Buttons --}}
    {{-- Footer Buttons --}}
    <script>
        
        function toback() {
            try {
                showLoader();
                var current_question = document.getElementById("current_question").value;
                var first_question = document.getElementsByClassName('content_exam')[0];
                var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
                showfinishmodal('hide');
                if (current_question == first_question.dataset.id) {
                    hideLoader();
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

                    stopaudios();
                    hideLoader();
                }

                question_time_reply = 0;
            } catch (error) {
                hideLoader();
                toast(error, 'error');
            }
        }

        function togglequestions(modal = false) {
            var footer_questions = document.getElementById('footer_questions');
            showfinishmodal('hide');
            if (footer_questions.classList.contains('active')) {
                footer_questions.classList.remove("active");
            } else {
                footer_questions.classList.add("active");
            }

            if (modal == true) {
                showfinishmodal('open');
            }
        }

        function tonext(tolast = false, type = null) {
            try {
                showLoader();
                var current_question = document.getElementById("current_question").value;
                var all_questions = document.getElementById("all_questions").value;
                var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
                var time_range_sections = document.getElementById("time_range_sections").value;
                var next_section = document.getElementById("next_section").value;
                var section_start_time = document.getElementById("section_start_time");
                var loader_for_sections = document.getElementById("loader_for_sections");
                var form = document.getElementById("exam");
                question_time_reply = 0;
                stopaudios();
                if (all_questions == currentDivQuestion.dataset.key || tolast == true) {
                    if (finishmodalshowed == false && tolast == false) {
                        hideLoader();
                        showfinishmodal('open');
                    } else {
                        if (type == "imtahanzamanibitdi" && next_section == true && time_range_sections > 0) {
                            document.getElementById("time_range_sections").value = time_range_sections - 1;
                            if (time_range_sections > 0) {
                                hideLoader();
                                if (next_section == 1) {
                                    section_start_time.value = document.getElementById("time_exam").value;
                                    form.classList.remove('d-block');
                                    form.style.display = "none";
                                    loader_for_sections.classList.add("active");
                                }
                            } else {
                                allowReload = true;
                                tonext(true, 'imtahanzamanibitdi');
                            }
                        } else {
                            var forum = document.getElementById("exam");
                            var formData = new FormData(forum);

                            fetch("{{ route('finish_exam') }}", {
                                    method: "POST",
                                    body: formData
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error("Network response was not ok.");
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    toast(data.message, data.status);
                                    hideLoader();

                                    if (data.status == "success") {

                                        if (data.url != null && data.url != '' && data.url != ' ') {
                                            redirect_url = data.url;
                                        }

                                        if (data.nextsection == false) {
                                            allowReload = true;
                                            window.location.href = data.url;
                                        }

                                        if (type == "imtahanzamanibitdi") {
                                            allowReload = true;
                                            window.location.href = data.url;
                                        }
                                    } else {
                                        toast("Şəbəkədə problem var. 3 saniyə ərzində yenidən yoxlanılacaq...", 'info');
                                        setTimeout(() => tonext(true), 3000);
                                    }
                                })
                                .catch(error => {
                                    hideLoader();
                                    toast(error.message, "error");
                                });

                            if (time_range_sections > 0) {
                                hideLoader();
                                if (next_section == 1) {
                                    section_start_time.value = document.getElementById("time_exam").value;
                                    form.classList.remove('d-block');
                                    form.style.display = "none";
                                    loader_for_sections.classList.add("active");
                                }
                            } else {
                                clearInterval(intervalTimerID);
                            }
                        }
                    }
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
                    hideLoader();
                }
            } catch (error) {
                hideLoader();
                toast(error, 'error');
            }
        }

        function showfinishmodal(action) {
            var showfinishmodal = document.getElementById('showfinishmodal');
            var content_area_exam = document.getElementById('content_area_exam');
            var footer_questions = document.getElementById("footer_questions");
            var footer_questions_top = document.getElementById('footer_questions_top');
            if (action == "open") {
                finishmodalshowed = true;
                showfinishmodal.classList.remove('hide');
                footer_questions_top.innerHTML = footer_questions.innerHTML;
                content_area_exam.classList.add('hide');
            } else {
                finishmodalshowed = false;
                showfinishmodal.classList.add('hide');
                footer_questions_top.innerHTML = '';
                content_area_exam.classList.remove('hide');
            }
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

        function updatepad() {
            var current_question = document.getElementById("current_question").value;
            var first_question = document.getElementById("first_question").value;

            var footer_question_buttons = document.getElementsByClassName('footer_question_buttons');
            var marked_questions = document.getElementById('marked_questions').value;

            for (var i = 0; i < footer_question_buttons.length; i++) {
                const element_for_saved = footer_question_buttons[i];
                element_for_saved.classList.remove("current");

                if (element_for_saved.classList.contains("saved")) {
                    element_for_saved.classList.remove("saved")
                }

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

        function getquestion(id) {
            try {
                showLoader();
                var activecontentquestions = document.getElementsByClassName("content_exam");
                showfinishmodal('hide');
                for (var i = 0; i < activecontentquestions.length; i++) {
                    activecontentquestions[i].classList.remove("show");
                }

                var selected = document.getElementById(`content_exam_${id}`);
                selected.classList.add("show");
                document.getElementById("current_question").value = id;
                question_time_reply = 0;
                togglequestions();
                hideLoader();
            } catch (error) {
                hideLoader();
                toast(error, 'error');
            }
        }

        // window.addEventListener('load',function(){
            setInterval(updatepad, 500);
        // })
    </script>
    {{-- Footer Buttons --}}

    {{-- Create Timer --}}
    <script>
        
        function pad(val) {
            return val.toString().padStart(2, '0');
        }

        
        function pad_new(val) {
            return val > 9 ? val : "0" + val;
        }

        intervalTimerID = setInterval(updateClock, 1000);

        function updateClock() {
            sec++;
            question_time_reply++;
            var inputtimeinput = document.getElementById("time_exam");
            var section_start_time = document.getElementById("section_start_time");
            var time_exam = document.getElementById("time_exam");
            var loader_for_sections = document.getElementById("loader_for_sections");
            var form = document.getElementById("exam");
            const now = new Date();
            const difference = countDownDate - now.getTime();
            const minutesDifference = Math.floor(difference / (1000 * 60));
            const secondsDifference = Math.floor((difference % (1000 * 60)) / 1000);
            inputtimeinput.value = sec;
            if (loader_for_sections.c ssList.contains('active') && section_start_time.value > 0) {
                qalan_vaxt = (parseInt(section_start_time.value) + parseInt(time_range_sections.value)) - parseInt(time_exam
                    .value);
                const minutesQalanVaxtDifference = Math.floor(qalan_vaxt / 60);
                const secondsQalanVaxtDifference = qalan_vaxt % 60;
                if (document.getElementById('seconds_start_time')) {
                    document.getElementById('seconds_start_time').innerHTML = pad_new(secondsQalanVaxtDifference);
                }

                if (document.getElementById('minutes_start_time')) {
                    document.getElementById('minutes_start_time').innerHTML = pad_new(minutesQalanVaxtDifference);
                }

                if (qalan_vaxt == 0) {
                    clearInterval(intervalTimerID);
                    if (onchangecountdown != null) {
                        clearInterval(onchangecountdown);
                    }

                    allowReload = true;

                    if (redirect_url != null) {
                        window.location.href = redirect_url;
                    } else {
                        tonext(true, 'imtahanzamanibitdi');
                    }

                }
            } else {
                section_start_time.value = 0;
                allowReload = true;
            }

            if (difference <= 0) {
                allowReload = true;
                document.getElementById('minutes').innerHTML = "00";
                document.getElementById('seconds').innerHTML = "00"
                tonext(true, "imtahanzamanibitdi");
                return;
            }

            if (document.getElementById('seconds')) {
                document.getElementById('seconds').innerHTML = pad_new(secondsDifference);
            }

            if (document.getElementById('minutes')) {
                document.getElementById('minutes').innerHTML = pad_new(minutesDifference);
            }

            settimerforcurrentquestion();
        }

        function settimerforcurrentquestion() {
            var current_question = document.getElementById('current_question').value;
            var question_time_replies_currentval = document.getElementById(`question_time_replies_${current_question}`)
                .value ?? 0;
            document.getElementById(`question_time_replies_${current_question}`).value = parseInt(
                question_time_replies_currentval) + 1;
        }

    </script>
    {{-- Create Timer --}}

    {{-- onchange tab --}}
    <script>
        let onchangecountdown;
        let loaderVisibleonchange = false;
        let secondsLeftcountdown = 5;
        let show1time = true;

        function onchangeShowLoader() {
            loaderVisibleonchange = true;
            if (show1time == true) {
                alert('@lang('additional.messages.ifchangewindowtab')');
                show1time = false;
                allowReload = true;
            } else {
                var modalshowcountdown = `<div id="modalshowcountdown" class="modal custom-modal show" tabindex="-1" role="dialog"
                    aria-labelledby="myModalLabel">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body text-muted text-large my-1">
                                <div class='my-1 d-flex '>
                                    <span class='d-inline-block' id='minutescountdown'>00</span><span class='d-inline-block'>:</span><span
                                        id='secondscountdown' class='d-inline-block'>05</span>
                                </div>
                                <div class='text-center text-danger'>
                                    @lang('additional.messages.ifchangewindowtab')
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>`;
                document.body.innerHTML += modalshowcountdown;
                toggleModalnow('modalshowcountdown', 'open');
            }

            onchangeStartCountdown();
        }

        function onchangeStartCountdown() {
            onchangecountdown = setInterval(function() {
                if (secondsLeftcountdown == 0) {

                    loaderVisibleonchange = false;
                    var modalshowcountdown = document.getElementById('modalshowcountdown');

                    if (modalshowcountdown != null) {
                        setTimeout(() => {
                            toggleModalnow('modalshowcountdown', 'hide');
                            modalshowcountdown.remove();
                        }, 1000);
                    } else {
                        clearInterval(onchangecountdown);
                        clearInterval(intervalTimerID);
                    }

                    tonext(true);
                }

                if (document.getElementById('secondscountdown') != null) {
                    document.getElementById('secondscountdown').innerHTML = `0${secondsLeftcountdown}`;
                }

                secondsLeftcountdown--;
            }, 1000);
        }

        // function checkPageFocus() {
        //     if (document.hasFocus() || !document.hidden) {
        //         clearInterval(onchangecountdown);
        //         setTimeout(() => {
        //             loaderVisibleonchange = !loaderVisibleonchange;
        //             if (loaderVisibleonchange == false) {
        //                 var modalshowcountdown = document.getElementById('modalshowcountdown');
        //                 setTimeout(() => {
        //                     if (modalshowcountdown != null) {
        //                         modalshowcountdown.remove();
        //                     }
        //                 }, 1000);
        //             }
        //         }, 400);
        //     } else {
        //         onchangeShowLoader();
        //     }
        // }

        // document.addEventListener("visibilitychange", function() {
        //     checkPageFocus();
        // });

        // checkPageFocus();
    </script>
    {{-- onchange tab --}}

    {{-- Content Functions --}}
    {{-- References Functions --}}
    <script defer>
        function stopaudios() {
            var oneTimeAudios = document.querySelectorAll('.only1time');
            if (oneTimeAudios.length > 0) {
                oneTimeAudios.forEach(function(audio) {
                    if (!audio.paused && !audio.ended) {
                        audio.pause();
                    }
                });
            }
        }

        function toggle_references_modal_content_element(key) {
            var toggler_button_reference = document.getElementById(`toggler_button_reference_${key}`);
            var body_reference = document.getElementById(`body_reference_${key}`);

            if (body_reference.classList.contains('hide')) {
                body_reference.classList.remove('hide');
                toggler_button_reference.innerHTML = '<i class="fa fa-minus"></i>';
            } else {
                body_reference.classList.add('hide');
                toggler_button_reference.innerHTML = '<i class="fa fa-plus"></i>';
            }
        }

        function toggle_references_modal_content(type) {
            var referance_toggle_buttons = document.getElementsByClassName('referance_toggle_button');
            var referance_bodyes = document.getElementsByClassName('referance_body');
            for (var i = 0; i < referance_toggle_buttons.length; i++) {
                if (type == "open") {
                    referance_toggle_buttons[i].innerHTML = '<i class="fa fa-minus"></i>';
                } else {
                    referance_toggle_buttons[i].innerHTML = '<i class="fa fa-plus"></i>';
                }
            }

            for (var i = 0; i < referance_bodyes.length; i++) {
                if (type == "open") {
                    referance_bodyes[i].classList.remove('hide');
                } else {
                    referance_bodyes[i].classList.add('hide');
                }
            }
        }

        function toggledesmosmodal(desmosid) {
            showLoader();
            var desmoscalc = document.getElementById(desmosid);
            var desmos_expandable = document.getElementById('desmos_expandable');
            if (desmoscalc.classList.contains('modal-lg')) {
                desmoscalc.classList.remove("modal-lg");
                desmos_expandable.innerHTML = '<i class="fa fa-expand-alt"></i>';
            } else {
                desmoscalc.classList.add("modal-lg");
                desmos_expandable.innerHTML = '<i class="fa fa-compress-alt"></i>';
            }
            hideLoader();
        }
    </script>
    {{-- References Functions --}}

    {{-- Exam Functions --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            renderMathInElement(document.body, {
                delimiters: [{
                        left: '$$',
                        right: '$$',
                        display: true
                    },
                    {
                        left: '$',
                        right: '$',
                        display: false
                    },
                    {
                        left: '\\(',
                        right: '\\)',
                        display: true
                    },
                    {
                        left: '\\[',
                        right: '\\]',
                        display: true
                    }
                ],
                throwOnError: false
            });
        });

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

        function remove_button_toggler() {
            var elements = document.getElementsByClassName('remove_button');
            var btn_question_container_undo_or_redo = document.getElementsByClassName(
                'btn-question_container_undo_or_redo');
            var question_answer_one_element_container = document.getElementsByClassName(`question_answer_one`);
            var question_container_undo_or_redo = document.getElementsByClassName(`btn-question_container_undo_or_redo`);

            for (let index = 0; index < btn_question_container_undo_or_redo.length; index++) {
                const element = btn_question_container_undo_or_redo[index];
                if (element.classList.contains("show")) {
                    element.classList.remove("show");
                    for (let index2 = 0; index2 < question_answer_one_element_container.length; index2++) {
                        const element2 = question_answer_one_element_container[index];
                        element2.classList.remove('removable');
                    }
                    for (let index3 = 0; index3 < question_container_undo_or_redo.length; index3++) {
                        const element3 = question_container_undo_or_redo[index];
                        element3.innerHTML =
                            `<img src="{{ asset('front/assets/img/bg_images/a+icon.png') }}" class="img-fluid img-responsive" />`;
                    }

                } else {
                    element.classList.add("show");
                }
            }

            for (var i = 0; i < elements.length; i++) {
                if (elements[i].classList.contains('active')) {
                    elements[i].classList.remove("active");
                } else {
                    elements[i].classList.add("active");
                }
            }
        }

        function select_answer(question_id, answer_id, type) {
            var all_questions = document.getElementById("all_questions").value;
            var currentDivQuestion = document.getElementById(`content_exam_${question_id}`);
            var answers_selected = document.getElementsByClassName(`answers_${question_id}`);
            var clicked_el = document.getElementById(`question_answer_one_${question_id}_${answer_id}_${type}`);
            var radio_input = clicked_el.querySelector('input[type="radio"]');
            var checkbox_input = clicked_el.querySelector('input[type="checkbox"]');

            set_question_value_on_session(question_id, answer_id, type);

            if (clicked_el.classList.contains('removable')) {
                return;
            }

            if (type == "radio") {
                for (var i = 0; i < answers_selected.length; i++) {
                    answers_selected[i].classList.remove('selected');
                    var input = answers_selected[i].querySelector('input[type="radio"]');
                    if (input) input.checked = false;
                }
            }

            clicked_el.classList.toggle('selected');

            if (radio_input) {
                radio_input.checked = clicked_el.classList.contains('selected');
            }

            if (checkbox_input) {
                checkbox_input.checked = clicked_el.classList.contains('selected');
            }

            if (all_questions != currentDivQuestion.dataset.key && type == "radio") {
                // tonext();
            }

            document.getElementById(`question_row_button_${question_id}`).classList.add('answered');
            updatepad();
        }

        function changeTextBox(question_id, type) {
            var text_box = document.getElementById(`question_answer_one_${question_id}_${type}`).value;
            // document.getElementById(`question_answer_one_${question_id}_${type}`).value = text_box.replace(/[^0-9/\\]/g,
            //     '');
            if (text_box.length > 5) {
                document.getElementById(`question_answer_one_${question_id}_${type}`).value = text_box.substring(0, 5);
            }
            var answer_footer_buttons = document.getElementById(`question_row_button_${question_id}`);
            var question_textbox_text_span = document.getElementById(`question_textbox_text_span_${question_id}`);

            if (question_textbox_text_span !== null) {
                question_textbox_text_span.innerHTML = '';
            }
            text_box = document.getElementById(`question_answer_one_${question_id}_${type}`).value;
            if (text_box.length > 0 && text_box != null && $.trim(text_box) != '' && $.trim(text_box) != null && $.trim(
                    text_box) != ' ') {
                var parts = text_box.split('/');
                answer_footer_buttons.classList.add('answered');
                if (parts.length === 2) {
                    var x = parts[0];
                    var y = parts[1];
                    var rendered = katex.renderToString(`\\frac{${x}}{${y}}`, {
                        throwOnError: false,
                        displayMode: true
                    });
                    question_textbox_text_span.innerHTML = `<span>${rendered}</span>`;
                    set_question_value_on_session(question_id, rendered, type);
                } else {
                    question_textbox_text_span.innerHTML = `${text_box}`;
                    set_question_value_on_session(question_id, text_box, type);
                }
            } else {
                answer_footer_buttons.classList.remove('answered');
                question_textbox_text_span.innerHTML = '';
            }
        }

        function mark_unmark_question(id) {
            var exam_id = document.getElementById('exam_id').value;
            var exam_result_id = document.getElementById('exam_result_id').value;
            var user_id = document.getElementById("user_id").value;
            var language = document.getElementById('language').value;
            sendAjaxRequestOLD("{{ route('api.mark_unmark_question') }}", "post", {
                question_id: id,
                exam_id: exam_id,
                exam_result_id: exam_result_id,
                language: language,
                user_id: user_id,
            }, function(e, t) {
                if (e) toast(e, "error");
                else {
                    let n = JSON.parse(t);
                    toast(n.message, n.status);
                    var marked_questions = document.getElementById('marked_questions');
                    marked_questions.value = JSON.stringify(n.data);
                    var element = document.getElementById(`mark_question_button_${id}`);
                    if (element.classList.contains('active')) {
                        element.classList.remove("active");
                        element.innerHTML = '<i class="far fa-bookmark"></i>';
                    } else {
                        element.classList.add("active");
                        element.innerHTML = '<i class="fa fa-bookmark"></i>';
                    }

                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            var oneTimeAudios = document.querySelectorAll('.only1time');

            oneTimeAudios.forEach(function(audio) {
                audio.addEventListener('play', function(event) {
                    audio.controls = false;
                    var audio_tag_text = document.querySelector(".audio_tag_text");
                    audio_tag_text.innerHTML += `<span class="text-info">@lang('additional.pages.exams.audiofile_played')</span>`;
                    audio.removeEventListener('play', arguments.callee);

                }, {
                    once: true
                });
            });
        });

        const leftCol = document.getElementsByClassName('left_col');
        const resizer = document.getElementsByClassName('resizer');
        let isResizing = false;
        let offsetX = 0;

        function draggingleftandrightcolumns() {
            for (let index = 0; index < resizer.length; index++) {
                const element = resizer[index];
                element.addEventListener("mousedown", startResize);
                element.addEventListener("touchstart", startResize);
                element.addEventListener("mouseover", handleMouseOver);
                element.addEventListener("mouseleave", handleMouseLeave);
            }
        }

        function startResize(e) {
            isResizing = true;
            offsetX = (e.type === "mousedown") ? e.clientX : e.touches[0].clientX;
            document.addEventListener("mousemove", resize);
            document.addEventListener("touchmove", resize);
            document.addEventListener("mouseup", stopResize);
            document.addEventListener("touchend", stopResize);
        }

        function resize(e) {
            if (!isResizing) return;
            var minusable = 0;

            if (e.type === "touchstart") {
                minusable = e.touches[0].clientX - (e.srcElement.getBoundingClientRect().left || e.srcElement.offsetLeft);
            } else {
                if (e.layerX > 0) {
                    minusable = e.clientX - (e.layerX + e.srcElement.offsetLeft);
                } else {
                    minusable = e.clientX - e.srcElement.offsetLeft;
                }
            }

            const size = `${e.touches ? e.touches[0].clientX : e.clientX - minusable}px`;
            for (let index = 0; index < leftCol.length; index++) {
                const element = leftCol[index];
                element.style.width = size;
            }
        }

        function stopResize() {
            isResizing = false;
            document.removeEventListener("mousemove", resize);
            document.removeEventListener("touchmove", resize);
            document.removeEventListener("mouseup", stopResize);
            document.removeEventListener("touchend", stopResize);
        }

        function handleMouseOver(e) {
            if (!isResizing) {
                e.currentTarget.style.opacity = 1;
            }
        }

        function handleMouseLeave(e) {
            if (!isResizing) {
                e.currentTarget.style.opacity = 0.5;
            }
        }

        window.addEventListener('load', function() {
            draggingleftandrightcolumns();
            sortanswerarea();
        });

        function sortanswerarea() {
            let answerareas = document.getElementsByClassName('answers_match_area');
            if (answerareas != null && answerareas.length > 0) {
                for (let index = 0; index < answerareas.length; index++) {
                    const element = answerareas[index];
                    $(`#${element.id}`).sortable({
                        start: function(event, ui) {
                            var questionelem = ui.item[0];
                            var questionId = questionelem.dataset.question_id;
                            var sectionId = questionelem.dataset.section_id;
                            var answer_footer_buttons = document.getElementById(
                                `question_row_button_${questionId}`);
                            answer_footer_buttons.classList.add('answered');
                            var question_match_element = document.getElementById(
                                `question_match_element_${sectionId}_${questionId}`);
                            question_match_element.value = 1;
                        }
                    });
                    $(`#${element.id}`).disableSelection();
                }
            }
        }

        function toggleabcline(question_id, value_id) {
            var question_answer_one_element_container_radio = document.getElementById(
                `question_answer_one_${question_id}_${value_id}_radio`);
            var question_answer_one_element_container_checkbox = document.getElementById(
                `question_answer_one_${question_id}_${value_id}_checkbox`);
            var question_container_undo_or_redo = document.getElementById(
                `question_container_undo_or_redo_${question_id}_${value_id}`);
            if (question_answer_one_element_container_radio != null) {
                if (question_answer_one_element_container_radio.classList.contains("removable")) {
                    question_answer_one_element_container_radio.classList.remove('removable');
                    question_container_undo_or_redo.innerHTML =
                        `<img src="{{ asset('front/assets/img/bg_images/a+icon.png') }}" class="img-fluid img-responsive" />`;
                } else {
                    question_answer_one_element_container_radio.classList.add('removable');
                    question_container_undo_or_redo.innerHTML = '<span>@lang('additional.buttons.undo')</span>';
                }
            }

            if (question_answer_one_element_container_checkbox != null) {
                if (question_answer_one_element_container_checkbox.classList.contains("removable")) {
                    question_answer_one_element_container_checkbox.classList.remove('removable');
                    question_container_undo_or_redo.innerHTML =
                        `<img src="{{ asset('front/assets/img/bg_images/a+icon.png') }}" class="img-fluid img-responsive" />`;
                } else {
                    question_answer_one_element_container_checkbox.classList.add('removable');
                    question_container_undo_or_redo.innerHTML = '<span>@lang('additional.buttons.undo')</span>';
                }
            }
        }

        function set_question_value_on_session(question_id, answer_id = null, type) {
            try {
                var exam_id = document.getElementById('exam_id').value;
                var exam_result_id = document.getElementById('exam_result_id').value;
                var user_id = document.getElementById("user_id").value;
                var language = document.getElementById('language').value;
                var _token = document.querySelector('meta[name="_token"]').getAttribute('content');
                sendAjaxRequestOLD("{{ route('exams_set_question_value_on_session') }}", "post", {
                    exam_id,
                    result_id: exam_result_id,
                    language,
                    user_id,
                    question_id,
                    answer_id: answer_id,
                    type_value: type,
                    _token
                }, function(e, t) {
                    let n = JSON.parse(t);
                });
            } catch (err) {
                console.log(err);
            }
        }
    </script>
    {{-- Exam Functions --}}
    {{-- Content Functions --}}

    {{-- Page Functions --}}
    {{-- Disable Prtscr --}}
    <script defer>
        document.addEventListener("keyup", function(e) {
            var keyCode = e.keyCode ? e.keyCode : e.which;
            if (keyCode == 44) {
                stopPrntScr();
            }
        });

        function stopPrntScr() {
            var inpFld = document.createElement("input");
            inpFld.setAttribute("value", ".");
            inpFld.setAttribute("width", "0");
            inpFld.style.height = "0px";
            inpFld.style.width = "0px";
            inpFld.style.border = "0px";
            document.body.appendChild(inpFld);
            inpFld.select();
            document.execCommand("copy");
            inpFld.remove(inpFld);
        }

        function AccessClipboardData() {
            try {
                window.clipboardData.setData('text', "Access   Restricted");
            } catch (err) {}
        }

        setInterval(AccessClipboardData(), 300);

        function copyToClipboard() {
            var aux = document.createElement("input");
            aux.setAttribute("value", "print screen disabled!");
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            document.body.removeChild(aux);
            toast("@lang('additional.messages.noprint')", 'error')
        }

        $(window).keyup(function(e) {
            if (e.keyCode == 44) {
                copyToClipboard();
            }
        });
    </script>
    {{-- Disable Prtscr --}}

    {{-- Disable F5 --}}
    {{-- <script type="text/javascript">
        function disableF5(e) {
            if ((e.which || e.keyCode) == 116 || (e.which || e.keyCode) == 82) {
                e.preventDefault();
                if (allowReload) {
                    window.location.href = redirect_url;
                } else {
                    tonext(true);
                }
            }
        }

        $(document).ready(function() {
            $(document).on("keydown", disableF5);
            document.addEventListener('contextmenu', event => event.preventDefault());
        });

        window.addEventListener('beforeunload', function(e) {
            if (!allowReload) {
                e.preventDefault();
                tonext(true);
                e.returnValue = '';
            }
        });

        window.onbeforeunload = function(e) {
            e.preventDefault(); // Prevent refresh
        };

    </script> --}}
    {{-- Disable F5 --}}

    {{-- Page Functions --}}

    {{-- Exam Refresh Function --}}
    {{-- <script defer>
        function changeurlpage() {
            var currentUrl = window.location.href;
            var setExamIndex = currentUrl.indexOf('/set_exam');

            if (currentUrl.indexOf('selected_section') === -1) {
                if (setExamIndex !== -1) {
                    // '/set_exam' kısmından önceki kısmı al
                    var cururl = currentUrl.substring(0, setExamIndex);

                    var exam_id = document.getElementById("exam_id").value;
                    var updatedUrl = cururl + "/redirect_exam" + (cururl.indexOf('?') !== -1 ? '&' : '?') +
                        'selected_section={{ session()->get('selected_section') }}&exam_id=' + exam_id;
                    history.replaceState(null, null, updatedUrl);
                }
            }
        }


        changeurlpage();
    </script> --}}
    {{-- Exam Refresh Function --}}

    <script>
        window.addEventListener('load', function() {
            @foreach ($questions as $question)
                set_question_value_on_session({{ $question->id }}, null,
                    '{{ App\Models\ExamQuestion::TYS[$question->type] }}');
            @endforeach
        });
    </script>
@endpush
