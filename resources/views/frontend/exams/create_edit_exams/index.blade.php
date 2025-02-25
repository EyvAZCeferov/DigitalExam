@extends('frontend.layouts.app')
@section('title', trans('additional.pages.exams.exams') . isset($data) && !empty($data) && isset($data->id) ?
    trans('additional.buttons.edit') : trans('additional.buttons.add'))
@section('content')
    <section class="add_edit_exam">
        <form action="{{ route('user.exam.add_edit_exam') }}" class="d-block" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="language" id="language" value="{{ app()->getLocale() }}">
            <input type="hidden" name="auth_id" id="auth_id" value="{{ auth('users')->id() }}">
            <input type="hidden" name="current_section" id="current_section">
            <input type="hidden" name="deleting_question_id" id="deleting_question_id">
            <input type="hidden" name="deleting_question_type" id="deleting_question_type">
            @if (isset($data) && !empty($data) && isset($data->id)) <input type="hidden" name="top_id"
                    value="{{ $data->id }}"> @endif
            <div class="row my-2">
                @if (isset($data) && !empty($data) && isset($data->id))
                    @include('frontend.light_parts.section_title', [
                        'title' => trans('additional.buttons.edit'),
                    ])
                @else
                    @include('frontend.light_parts.section_title', [
                        'title' => trans('additional.buttons.add'),
                    ])
                @endif
            </div>
            <div class="row mt-1 my-3">
                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <input required type="text" class="form-control w-100" name="exam_name"
                        placeholder="@lang('additional.forms.exam_name')"
                        value="{{ isset($data) && !empty($data) && !empty($data->name) ? $data->name[app()->getLocale() . '_name'] : null }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <select required name="category_id" class="form-control select2" data-placeholder="@lang('additional.pages.exams.category')"
                        placeholder="@lang('additional.pages.exams.category')">
                        <option value="" disabled @if (!(isset($data) && !empty($data) && isset($data->category_id) && !empty($data->category_id))) selected @endif>@lang('additional.pages.exams.category')
                        </option>
                        @foreach (\App\Models\Category::whereNull('parent_id')->with('sub')->get() as $category)
                            <optgroup label="{{ $category->name['az_name'] }}">
                                @foreach ($category->sub as $sub)
                                    @php
                                        $categoryIdFromOldData =
                                            isset($data) &&
                                            !empty($data) &&
                                            isset($data->category_id) &&
                                            !empty($data->category_id)
                                                ? $data->category_id
                                                : null;
                                        $categoryIdFromRequest =
                                            request()->has('category') && !empty(request()->get('category'))
                                                ? request()->get('category')
                                                : null;
                                        $isSelected =
                                            $categoryIdFromOldData == $sub->id || $categoryIdFromRequest == $sub->id;
                                    @endphp

                                    <option {{ $isSelected ? 'selected' : '' }} value="{{ $sub->id }}">
                                        {{ $sub->name['az_name'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    @if (isset($data) && !empty($data) && !empty($data->image))

                        <img data-fancybox data-caption="{{ $data->name[app()->getLocale() . '_name'] }}"
                            href="{{ getImageUrl($data->image, 'exams') }}" src="{{ getImageUrl($data->image, 'exams') }}"
                            class="img-fluid img-responsive" style="height: 150px;margin-bottom:10px;">
                    @endif
                    <input type="file" @if (!(isset($data) && !empty($data) && !empty($data->image))) required @endif name="image" accept="image/*"
                        class="form-control {{ $errors->first('image') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <input type="number" required
                        value="{{ old('point', isset($data) && !empty($data) && isset($data->point) && !empty($data->point) ? $data->point : null) }}"
                        name="point" placeholder="@lang('additional.forms.exam_point')"
                        class="form-control {{ $errors->first('point') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <input type="text"
                        value="{{ old('price', isset($data) && !empty($data) && isset($data->price) && !empty($data->price) ? $data->price : null) }}"
                        name="price" placeholder="@lang('additional.forms.exam_price')"
                        class="form-control {{ $errors->first('price') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <input type="text"
                        value="{{ old('endirim_price', isset($data) && !empty($data) && isset($data->endirim_price) && !empty($data->endirim_price) ? $data->endirim_price : null) }}"
                        name="endirim_price" placeholder="@lang('additional.forms.exam_endirim_price')"
                        class="form-control {{ $errors->first('endirim_price') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    {{-- Status --}}
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                        @if (isset($data) && !empty($data) && !empty($data->status)) @if ($data->status == true) checked @endif @else checked
                            @endif type="checkbox" id="exam_status" name="exam_status">
                        <label class="form-check-label" for="exam_status">@lang('additional.forms.exam_status')</label>
                    </div>
                    {{-- Status --}}

                    {{-- Rebuy --}}
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                        @if (isset($data) && !empty($data) && !empty($data->rebuy)) @if ($data->rebuy == true) checked @endif @else checked
                            @endif type="checkbox" id="exam_rebuy" name="exam_rebuy">
                        <label class="form-check-label" for="exam_rebuy">@lang('additional.forms.exam_rebuy')</label>
                    </div>
                    {{-- Rebuy --}}

                    {{-- show_result_user --}}
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                        @if (isset($data) && !empty($data) && !empty($data->show_result_user)) @if ($data->show_result_user == true) checked @endif @else checked
                            @endif type="checkbox" id="exam_show_result_answer"
                        name="exam_show_result_answer">
                        <label class="form-check-label" for="exam_show_result_answer">@lang('additional.forms.exam_show_result_answer')</label>
                    </div>
                    {{-- show_result_user --}}

                    {{-- show_calc --}}
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                        @if (isset($data) && !empty($data) && !empty($data->show_calc)) @if ($data->show_calc == true) checked @endif @else
                            checked @endif type="checkbox" id="show_calculator" name="show_calculator">
                        <label class="form-check-label" for="show_calculator">@lang('additional.forms.show_calculator')</label>
                    </div>
                    {{-- show_calc --}}

                    {{-- repeat_sound --}}
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                            @if (isset($data) && !empty($data) && !empty($data->repeat_sound)) @if ($data->repeat_sound == true) checked @endif
                            @endif type="checkbox" id="repeat_sound" name="repeat_sound">
                        <label class="form-check-label" for="repeat_sound">@lang('additional.forms.repeat_sound')</label>
                    </div>
                    {{-- repeat_sound --}}
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <label for="start_time">@lang('additional.forms.exam_start_time')</label>
                    <input id="start_time" type="datetime-local"
                        value="{{ old('start_time', isset($data) && !empty($data) && isset($data->start_time) && !empty($data->start_time) ? $data->start_time : null) }}"
                        name="start_time" placeholder="@lang('additional.forms.exam_start_time')"
                        class="form-control {{ $errors->first('start_time') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <label for="layout">@lang('additional.forms.exam_layout')</label>
                    <select name="layout_type"
                        class="form-control {{ $errors->first('layout_type') ? 'is-invalid' : '' }}">
                        @foreach (\App\Models\Exam::LAYOUTS as $key => $type)
                            <option @if (isset($data) && !empty($data) && isset($data->id) && $data->layout_type == $key) selected @endif value="{{ $key }}">
                                {{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-12 my-2">
                    <div name="description"
                        class="form-control summernote_element {{ $errors->first('description') ? 'is-invalid' : '' }}"
                        contenteditable="true" placeholder="@lang('additional.forms.exam_description')">{!! old(
                            'description',
                            isset($data) &&
                            !empty($data) &&
                            !empty($data->content) &&
                            isset($data->content[app()->getLocale() . '_description']) &&
                            !empty($data->content[app()->getLocale() . '_description'])
                                ? $data->content[app()->getLocale() . '_description']
                                : null,
                        ) !!}</div>
                </div>
            </div>
            {{-- Suallar, Section lar --}}
            <div class="row my-2 classcenter">
                <button type="submit" class="btn btn-success btn-block">
                    @if (isset($data) && !empty($data) && isset($data->id)) @lang('additional.buttons.edit')
                    @else
                        @lang('additional.buttons.add') @endif
                </button>
            </div>
        </form>
        {{-- Suallar, Section lar --}}
        @if (isset($data) && !empty($data) && isset($data->id))
            @include('frontend.exams.create_edit_exams.section_and_questions', ['data' => $data])
        @endif

    </section>

    {{-- Add Section --}}
    <div id="create_sections" class="modal custom-modal show" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>@lang('additional.pages.exams.sections') @lang('additional.buttons.add')</h3>
                    <button type="button" class="close" onclick="toggleModalnow('create_sections', 'hide')"
                        data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="selected_section_id" id="selected_section_id">
                    <div class="row my-2">
                        <div class="col-md-6 my-1">
                            <div class="form-group">
                                <label for="section_name">@lang('additional.forms.field_name')</label>
                                <input placeholder="@lang('additional.forms.field_name')" type="text" value="{{ old('section_name') }}"
                                    id="section_name" name="section_name"
                                    class="form-control {{ $errors->first('section_name') ? 'is-invalid' : '' }} w-100">
                            </div>

                        </div>

                        <div class="col-md-6 my-1">
                            <div class="form-group">
                                <label for="duration">@lang('additional.forms.exam_duration')</label>
                                <input type="number" value="{{ old('duration', 0) }}" name="duration" id="duration"
                                    class="form-control {{ $errors->first('duration') ? 'is-invalid' : '' }} w-100"
                                    placeholder="@lang('additional.forms.exam_duration')">
                            </div>

                        </div>
                        <div class="col-md-6 my-1">
                            <div class="form-group">
                                <label for="section_duration">@lang('additional.forms.field_duration')</label>
                                <input type="number" value="{{ old('section_duration', 0) }}" name="section_duration"
                                    id="section_duration"
                                    class="form-control {{ $errors->first('section_duration') ? 'is-invalid' : '' }} w-100"
                                    placeholder="@lang('additional.forms.field_duration')">
                            </div>

                        </div>


                        <div class="col-md-6 my-1">
                            <div class="form-group">
                                <label for="section_duration">@lang('additional.forms.wrong_point')</label>
                                <input type="text" value="{{ old('wrong_point', 1) }}" name="wrong_point"
                                    id="wrong_point"
                                    class="form-control {{ $errors->first('wrong_point') ? 'is-invalid' : '' }} w-100"
                                    placeholder="@lang('additional.forms.wrong_point')">
                            </div>

                        </div>

                        <div class="col-md-6 my-1">
                            <div class="form-group">
                                <label for="random">@lang('additional.forms.random')</label>
                                <input type="checkbox" {{ old('random', null) }} name="random" id="random"
                                    class="{{ $errors->first('random') ? 'is-invalid' : '' }} w-100"
                                    placeholder="@lang('additional.form.random')">
                            </div>

                        </div>


                        <div class="col-md-6 my-1">
                            <div class="form-group">
                                <label for="question_count">@lang('additional.forms.question_count')</label>
                                <input type="text" value="{{ old('question_count', null) }}" name="question_count"
                                    id="question_count"
                                    class="form-control {{ $errors->first('question_count') ? 'is-invalid' : '' }} w-100"
                                    placeholder="@lang('additional.form.question_count')">
                            </div>

                        </div>


                    </div>
                    <div class="row mt-2">
                        <button type="button" onclick="create_edit_section(event)"
                            class="btn btn-success btn-block">@lang('additional.buttons.add')</button>
                    </div>
                </div>

            </div>
        </div>
        <br>
    </div>
    {{-- Add Section --}}

@endsection

@push('css')
    @if (isset($data) && !empty($data) && isset($data->id) && !empty($data->image))
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css"
            integrity="sha512-H9jrZiiopUdsLpg94A333EfumgUBpO9MdbxStdeITo+KEIMaNfHNvwyjjDJb+ERPaRS6DpyRlKbvPUasNItRyw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endif
    <link href="https://cdn.jsdelivr.net/npm/tinymce@7.5.1/skins/ui/oxide/content.min.css" rel="stylesheet">
@endpush

@push('js')
    {{-- <script src="https://cdn.tiny.cloud/1/0j6r4v4wrpghb7ht8z0yf85cuzcv8iadyrza5gp8f4lxi1ib/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7.5.1/tinymce.min.js"></script>
    <script type="text/javascript" src="{{ asset('front/assets/js/eyvaz/customsortable.js') }}"></script>

    @if (isset($data) && !empty($data) && isset($data->id))
        {{-- Section Functions --}}
        <script>
            function create_edit_section(event, id = null) {
                event.preventDefault();
                try {
                    var selected_section_id = document.getElementById('selected_section_id');
                    if (id != null) {
                        selected_section_id.value = null;
                        get_section_information(id);
                    } else {
                        var section_name = document.getElementById('section_name').value.trim();
                        var section_duration = document.getElementById('section_duration').value.trim();
                        var wrong_point = document.getElementById('wrong_point').value.trim();
                        var duration = document.getElementById('duration').value.trim();
                        var random = document.getElementById("random");
                        var question_count = document.getElementById('question_count').value;

                        if (section_name && section_duration) {
                            sendAjaxRequestOLD("{{ route('api.setsectiondata') }}", "post", {
                                exam_id: {{ $data->id }},
                                language: document.getElementById("language").value,
                                user_id: document.getElementById("auth_id").value,
                                name: section_name,
                                time_range_sections: section_duration,
                                wrong_point,
                                duration: duration,
                                responseType: 'json',
                                selected_section_id: selected_section_id.value,
                                random: random.checked,
                                question_count: question_count ?? 0,
                            }, function(e, t) {
                                if (e) toast(e, "error");
                                else {
                                    getsectiondatas();
                                    selected_section_id.value = null;
                                    document.getElementById('section_name').value = null;
                                    document.getElementById('section_duration').value = null;
                                    document.getElementById('wrong_point').value = null;
                                    document.getElementById('duration').value = null;
                                    document.getElementById('random').checked = false;
                                    document.getElementById('question_count').value = null;
                                }
                            });
                        } else {
                            toast("@lang('additional.messages.required_fill')", 'error');
                        }
                    }
                } catch (error) {
                    toast(error, 'error');
                }
            }

            function getsectiondatas(id = null) {
                sendAjaxRequestOLD("{{ route('api.getexamsections') }}", "post", {
                    exam_id: {{ $data->id }},
                    language: document.getElementById("language").value,
                    section_id: id,
                }, function(e, t) {
                    if (e) toast(e, "error");
                    else {
                        let n = JSON.parse(t);
                        if (n.message != null && n.message != '' && n.message != ' ')
                            toast(n.message, n.status);

                        if (n.data != null && n.data.length > 0) {
                            var sectionElementsDiv = document.getElementById('section_elements');
                            section_elements.innerHTML = '';
                            toggleModalnow('create_sections', 'hide');
                            n.data.forEach(function(item) {
                                var divElement = `<div class="section_el">
                                            <div class="section_element" onclick="get_section(${item.id})"
                                                id="section_element_${item.id}">
                                                ${item.name}
                                            </div>
                                            <div class="d-flex mt-1">
                                                <button class="btn btn-warning btn-sm mt-1" type="button"
                                                    onclick="create_edit_section(event,${item.id})"><i class="fa fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm mt-1" type="button"
                                                    onclick="deletequestion(${item.id},'section')"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div>`;
                                section_elements.innerHTML += divElement;
                            });

                            get_section(n.data[n.data.length - 1].id);
                        }
                    }
                });
            }

            function get_section(id) {
                try {
                    showLoader();
                    var section_id = document.getElementById(`section_element_${id}`);
                    var section_elements = document.getElementsByClassName('section_element');
                    var section_and_questions = document.getElementById('section_and_questions');
                    var current_section_id = document.getElementById('current_section_id');
                    var questions_list = document.querySelector('#section_and_questions_left .questions_list');
                    section_and_questions.classList.add("hide");
                    for (let i = 0; i < section_elements.length; i++) {
                        const element = section_elements[i];
                        element.classList.remove("active");
                    }
                    section_id.classList.add("active");
                    section_and_questions.classList.remove("hide");
                    current_section_id.value = id;
                    questions_list.innerHTML = '';
                    sendAjaxRequestOLD(`{{ route('api.getsectiondata') }}`, "post", {
                            exam_id: {{ $data->id }},
                            section_id: id,
                        },
                        function(e,
                            t) {
                            if (e) toast(e, "error");
                            else {
                                let n = JSON.parse(t);
                                hideLoader();
                                if (n.message != null)
                                    toast(n.message, n.status);

                                if (n.data != null && n.data.length > 0) {
                                    if (n.data != null && n.data.length > 0)
                                        for (var i = 0; i < n.data.length; i++) {
                                            var maxLength = 32;
                                            var questionContent = n.data[i].question;
                                            var contentWithoutImages = questionContent.replace(/<img.*?>/g, '');
                                            contentWithoutImages = contentWithoutImages.replace(/<span.*?>/g, '');
                                            contentWithoutImages = contentWithoutImages.replace(/<p.*?>/g, '');
                                            contentWithoutImages = contentWithoutImages.replace(/<div.*?>/g, '');
                                            contentWithoutImages = contentWithoutImages.replace(/<a.*?>/g, '');
                                            if (contentWithoutImages.length > maxLength) {
                                                contentWithoutImages = truncateString(contentWithoutImages, maxLength);
                                            }

                                            var element = `<div class="question_list_element" id="question_list_element_${n.data[i].id}">
                                        <div onclick="getquestion('${n.data[i].id}')" class="question_name">${i+1}) ${contentWithoutImages}</div>
                                        <button class='btn btn-outline-danger btn-sm' type='submit' onclick='deletequestion(${n.data[i].id},"question")'><i class='fa fa-trash'></i></i></button>
                                        </div>`;
                                            questions_list.innerHTML += element;
                                        }
                                }
                            }
                        });
                } catch (error) {
                    hideLoader();
                    toast(error, 'error');
                }
            }

            function get_section_information(id) {
                try {
                    showLoader();
                    sendAjaxRequestOLD(`{{ route('api.getsectioninformation') }}`, "post", {
                            section_id: id,
                        },
                        function(e,
                            t) {
                            if (e) toast(e, "error");
                            else {
                                let n = JSON.parse(t);
                                hideLoader();
                                if (n.message != null)
                                    toast(n.message, n.status);

                                if (n.data != null) {
                                    var section_name = document.getElementById('section_name');
                                    var section_duration = document.getElementById('section_duration');
                                    var time_range_sections = document.getElementById('duration');
                                    var wrong_point = document.getElementById('wrong_point');
                                    var selected_section_id = document.getElementById('selected_section_id');
                                    var random = document.getElementById("random");
                                    var question_count = document.getElementById("question_count")
                                    section_name.value = n.data.name;
                                    section_duration.value = n.data.time_range_sections;
                                    time_range_sections.value = n.data.duration;
                                    wrong_point.value = n.data.wrong_point;
                                    selected_section_id.value = n.data.id;
                                    random.checked = n.data.random == 1 || n.data.random == '1' ? true : false;
                                    question_count.value = n.data.question_count ?? 0;
                                    toggleModalnow('create_sections', 'open');
                                }
                            }
                        });
                } catch (error) {
                    hideLoader();
                    toast(error, 'error');
                }
            }

            function create_question() {
                try {
                    var section_and_questions_right = document.getElementById('section_and_questions_right');
                    var current_section_id = document.getElementById('current_section_id').value;
                    var question_input_code = createRandomCode("string", 11);
                    var element = `<div class="form_question">
                    <form class='d-block' method="post" onsubmit="store_edit_question(event)" id="store_edit_question">
                        @csrf
                        <input type="hidden" name="section_id" id="section_id" value="${current_section_id}">
                        <input type='hidden' name='question_type' id='question_type' />
                        <input type='hidden' name='exam_id' id='{{ $data->id }}' />

                        <div class="col-sm-12 col-md-12 col-lg-12 my-1 d-none" id="question_content_textbox">
                            <div>
                                <div name="question_input_${question_input_code}"
                                    id="question_input_${question_input_code}"
                                    class="form-control question_input"
                                    contenteditable="true" placeholder="@lang('additional.forms.your_question')"></div>
                            </div>
                            <div class="my-2 d-none" id="question2_input_area">
                                <div name="question2_input_${question_input_code}"
                                    id="question2_input_${question_input_code}"
                                    class="form-control question_input2"
                                    contenteditable="true" placeholder="@lang('additional.forms.description')"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 my-3 d-none" id="question_content_audio">
                            <div class="form-group">
                                <input type="file" name="question_audio" id="question_audio" onchange="audiofileselect(event)" class="question_audio" class="file" accept="audio/*">
                                <label for="question_audio" class="custom-audio-input">
                                    <i class="fa fa-music"></i> @lang('additional.forms.upload_audio')
                                </label>
                            </div>
                            <div id="selectedAudioFile"></div>
                        </div>

                        <div id="answers_area"></div>
                        <div class="col-sm-12 col-md-12 col-lg-12 my-1 left_area">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="customDropdownButton" aria-haspopup="true" aria-expanded="false">
                                    @lang('additional.buttons.answer_type')
                                </button>
                                <div class="dropdown-menu" aria-labelledby="customDropdownButton">
                                    @foreach (App\Models\ExamQuestion::TYPES as $k => $type)
                                        <a class="dropdown-item types_element" id="types_{{ $type }}" onclick="set_type('{{ $type }}',false)" href="javascript:void(0)">{{ $k }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 col-lg-12 my-1 classjustifybetween hide question_footer_buttons" id="question_footer_buttons">
                            <div>
                                <select name='question_layout' class="form-control form-control-sm" id="question_layout">
                                    @foreach (\App\Models\ExamQuestion::LAYOUTS as $key => $type)
                                        <option {{ old('question_layout') == $type ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="width:60px">
                                <input type='number' class='form-control' name='order_number' value='1' placeholder="Sıra" />
                            </div>
                            <button class='btn btn-primary btn-sm submit_answer' type="submit">Təsdiq et</button>
                        </div>
                    </form>
                    </div>`;

                    section_and_questions_right.innerHTML = element;
                    const dropdownButton = document.getElementById('customDropdownButton');
                    const dropdownMenu = document.querySelector('.dropdown-menu');
                    dropdownButton.addEventListener('click', function() {
                        if (dropdownMenu.classList.contains('show')) {
                            dropdownMenu.classList.remove('show');
                        } else {
                            dropdownMenu.classList.add('show');
                        }
                    });

                    document.addEventListener('click', function(e) {
                        if (!dropdownButton.contains(e.target)) {
                            dropdownMenu.classList.remove('show');
                        }
                    });
                } catch (error) {
                    toast(error, 'error');
                    console.log("---------------SETTYPE------------" + error);
                }
            }

            function store_edit_question(event) {
                event.preventDefault();
                showLoader();
                try {
                    var form = $('form#store_edit_question');
                    var formData = new FormData(form[0]);
                    getandseteditortexts(formData)
                    var question_input_id = $(".question_input").attr('id');
                    var question_input = getcontenteditor(question_input_id);
                    var question_input2;
                    formData.append("question_input", question_input);
                    var answers = document.getElementsByClassName('text-input');
                    var current_section_id = document.getElementById('current_section_id').value;
                    var question_type = document.getElementById('question_type').value;
                    var matching_datas = [];
                    for (var i = 0; i < answers.length; i++) {
                        var answer_content = getcontenteditor(answers[i].id);
                        formData.append("answerres_" + i, answer_content);
                    }

                    if (question_type == 3) {
                        var textbox_0 = document.getElementsByClassName('textbox_0');
                        formData.append('textbox_0', getcontenteditor($(".textbox_0").attr('id')));
                        if (textbox_0 && textbox_0.length > 0) {
                            for (var i = 0; i < textbox_0.length; i++) {
                                var idel = textbox_0[i].getAttribute("id");
                                var content = getcontenteditor(idel);
                                console.log(content, idel, i)
                                if (content != null && content != '' && content != ' ') {
                                    formData.append('textbox_0', content);
                                } else {
                                    var content = getcontenteditor('txt_' + idel);
                                    if (content != null && content != '' && content != ' ') {
                                        formData.append('textbox_0', content);
                                    }
                                }
                            }
                        }
                        var textbox = document.getElementsByClassName("textbox");
                        if (textbox && textbox.length > 0) {
                            for (var i = 0; i < textbox.length; i++) {
                                var idel = textbox[i].getAttribute("id");
                                var content = getcontenteditor(idel);
                                console.log(content, idel, i)
                                if (content != null && content != '' && content != ' ') {
                                    formData.append('textbox_0', content);
                                } else {
                                    var content = getcontenteditor('txt_' + idel);
                                    if (content != null && content != '' && content != ' ') {
                                        formData.append('textbox_0', content);
                                    }
                                }
                            }
                        }
                        question_input2 = getcontenteditor($(".question_input2").attr('id'))
                        formData.append("question_input2", question_input2);
                    }

                    formData.append("exam_id", '{{ $data->id }}');
                    formData.append("language", '{{ app()->getLocale() }}');

                    if (question_type == 4) {
                        $(".matching_element").each(function(index, element) {
                            if (index % 2 != 0) {
                                var questionID = $(element).data('key');
                                var question_element = $(`#mathing_questions_${questionID}`);
                                var questionContent = getcontenteditor(question_element.attr("id"));
                                var answerID = $(`#mathing_answers_${questionID}`);
                                var answerContent = getcontenteditor(answerID.attr("id"));

                                if (questionContent != null && answerContent != null) {
                                    var matchingData = {
                                        question_content: questionContent,
                                        answer_content: answerContent,
                                    };
                                    matching_datas.push(matchingData);
                                }
                            }
                        });
                        formData.append("match_data", JSON.stringify(matching_datas));
                    }

                    sendAjaxRequest("{{ route('front.questions.store') }}", "post", formData, function(e, t) {
                        if (e) toast(e, "error");
                        else {
                            hideLoader();
                            let n = JSON.parse(t);
                            toast(n.message, n.status);
                            get_section(current_section_id);
                        }
                    });
                } catch (e) {
                    console.error("STORE_EDIT_QUESTION-----------------" + e);
                    toast(e, 'error');
                }
            }

            function getquestion(id) {
                try {
                    showLoader();
                    var question_list_elements = document.getElementsByClassName('question_list_element');
                    for (let index = 0; index < question_list_elements.length; index++) {
                        const element = question_list_elements[index];
                        element.classList.remove('active');
                    }
                    var question_list_element = document.getElementById(`question_list_element_${id}`);
                    question_list_element.classList.add('active');
                    sendAjaxRequestOLD(`{{ route('front.questions.get') }}`, "post", {
                        question_id: id,
                    }, function(e,
                        t) {
                        if (e) toast(e, "error");
                        else {
                            hideLoader();
                            let n = JSON.parse(t);
                            let idscontenteditable = [];
                            if (n.message != null)
                                toast(n.message, n.status);

                            if (n.data != null && n.data.id != null) {
                                var section_and_questions_right = document.getElementById(
                                    'section_and_questions_right');
                                var current_section_id = document.getElementById('current_section_id').value;
                                var answer_elements = document.querySelectorAll('.answer');
                                var answersAreaContent = n.data.answers.length > 0 ? n.data.answers.map(function(answer,
                                    index) {
                                    var elementContent = '';
                                    var codeofelement = createRandomCode('string', 11);
                                    idscontenteditable.push(codeofelement);
                                    var type;

                                    switch (n.data.type) {
                                        case 1:
                                            type = 'single';
                                            break;
                                        case 2:
                                            type = 'multi';
                                            break;
                                        case 3:
                                            type = 'textbox';
                                            break;
                                        case 5:
                                            type = 'audio';
                                            break;
                                        case 4:
                                            type = 'match';
                                            break;
                                        default:
                                            type = 'single';
                                            break;
                                    }

                                    elementContent.id = codeofelement;
                                    if (n.data.type == 1 || n.data.type == 5) {
                                        elementContent = `
                                    <div class='answer ${type}' id="${codeofelement}">
                                        <div class="answer_content">
                                            <label class="radio-input" onclick="change_radio('${type}', ${index})">
                                                <input type="radio" name="answers[${index}]" onchange="change_radio('${type}',${index})"
                                                onclick="change_radio('${type}', ${index})"
                                                    value="${index}" class="input_radios" id="input_radios_${index}"
                                                    ${answer.correct == 1 ? 'checked' : ''}
                                                    >
                                                <span class="checkmark ${answer.correct == 1 ? 'active' : ''}" id="label_checkmark_${index}"></span>
                                            </label>
                                            <span name="answer_reply[${index}]" id="answer__input_${codeofelement}" name="answer_reply[${index}]" class="text-input summernote_element" placeholder="@lang('additional.forms.answer')"
                                                contenteditable="true" placeholder="@lang('additional.forms.your_question')">${answer.answer}</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-success add_remove_buttons add_button"
                                        onclick="addoreditanswer('${type}','add','${codeofelement}')"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-danger add_remove_buttons remove_button"
                                        onclick="addoreditanswer('${type}','remove','${codeofelement}')"><i class="fa fa-minus"></i></button>
                                        </div>

                                    `;
                                    } else if (n.data.type == 2) {
                                        elementContent = `
                                        <div class='answer ${type}' id="${codeofelement}">
                                            <div class="answer_content">
                                                <label class="radio-input" onclick="change_radio('${type}', ${answer_elements.length})">
                                                    <input type="checkbox" name="answers[${answer_elements.length}]"
                                                        value="${answer_elements.length}" class="input_radios" id="input_radios_${answer_elements.length}">
                                                    <span class="checkmark ${answer.correct == 1 ? 'active' : ''}" id="label_checkmark_${index}"></span>
                                                </label>
                                                <span name="answer_reply[${index}]" id="answer__input_${codeofelement}" name="answer_reply[${index}]"
                                                class="text-input summernote_element" placeholder="@lang('additional.forms.answer')" contenteditable="true"
                                                placeholder="@lang('additional.forms.your_question')">${answer.answer}</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-success add_remove_buttons add_button"
                                            onclick="addoreditanswer('${type}','add','${codeofelement}')"><i class="fa fa-plus"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-danger add_remove_buttons remove_button"
                                            onclick="addoreditanswer('${type}','remove','${codeofelement}')"><i class="fa fa-minus"></i></button>
                                        </div>
                                    `;
                                    } else if (n.data.type == 3) {
                                        elementContent = `
                                        <div class='answer ${type}' id="${codeofelement}">
                                            <div class="answer_content">
                                                <div rows="5" name="textbox_0" class="text-input textbox_0" id="textbox_0_${codeofelement}" placeholder="@lang('additional.forms.answer')">${answer.answer}</div>
                                            </div>
                                        </div>
                                    `;
                                    } else if (n.data.type == 4) {
                                        var answer = JSON.parse(answer.answer);
                                        elementContent = `
                                        <div class="answer ${type}" id="${codeofelement}">
                                            <div class="answer_content">
                                                <div class="sides left_side">
                                                    <div class="content_element matching_element" data-key="${index}" id="mathing_questions_${index}" name="mathing_questions[${index}]" placeholder="@lang('additional.forms.answer')" contenteditable="true">${answer.question_content}</div>
                                                </div>
                                                <div class="sides right_side">
                                                    <div class="content_element matching_element" data-key="${index}" id="mathing_answers_${index}" name="mathing_answers[${index}]" placeholder="@lang('additional.forms.answer')" contenteditable="true">${answer.answer_content}</div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-success add_remove_buttons add_button"
                                                onclick="addoreditanswer('${type}','add','${codeofelement}')"><i class="fa fa-plus"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-danger add_remove_buttons remove_button"
                                                onclick="addoreditanswer('${type}','remove','${codeofelement}')"><i class="fa fa-minus"></i></button>
                                        </div>`;
                                    }
                                    return elementContent;
                                }).join('') : '';

                                var audiotag = '';


                                if (n.data.type == 5) {
                                    audiotag =
                                        `<audio controls><source src="${getFileUrl(n.data.file,'exam_questions')}" type="audio/mpeg">Your browser does not support the audio element.</audio>`;
                                }

                                var question_input_code = createRandomCode('string', 11);
                                var element = `<div class="form_question">
                                                <form class='d-block' method="post" onsubmit="store_edit_question(event)" id="store_edit_question">
                                                    @csrf
                                                    <input type="hidden" name="question_id" id="question_id" value="${n.data.id}" />
                                                    <input type="hidden" name="answers_count" id="answers_count" value="${n.data.answers.length}" />
                                                    <input type="hidden" name="section_id" id="section_id" value="${current_section_id}" />
                                                    <input type='hidden' name='question_type' id='question_type' />
                                                    <input type='hidden' name='exam_id' id='{{ $data->id }}' />
                                                    <div class="col-sm-12 col-md-12 col-lg-12 my-1 d-none" id="question_content_textbox">
                                                        <div>
                                                            <div name="question_input_${question_input_code}"
                                                                id="question_input_${question_input_code}"
                                                                class="form-control question_input"
                                                                contenteditable="true" placeholder="@lang('additional.forms.your_question')">${n.data.question}</div>
                                                        </div>

                                                        <div class='my-2 ${n.data.type!=3?`d-none`:''}' id="question2_input_area">
                                                            <div name="question2_input_${question_input_code}"
                                                                id="question2_input_${question_input_code}"
                                                                class="form-control question_input2"
                                                                contenteditable="true" placeholder="@lang('additional.forms.description')">${n.data.description}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-12 my-1 d-none" id="question_content_audio">
                                                        <div class="form-group">
                                                            <input type="file" name="question_audio" id="question_audio" onchange="audiofileselect(event)" class="question_audio" class="file" accept="audio/*">
                                                            <label for="question_audio" class="custom-audio-input">
                                                                <i class="fa fa-music"></i> ${n.data.file!=null && n.data.file!='' && n.data.file!=' ' ? "@lang('additional.forms.change_audio')" : "@lang('additional.forms.upload_audio')"}
                                                            </label>
                                                        </div>
                                                        <div id="selectedAudioFile">${audiotag}</div>
                                                    </div>
                                                    <div id="answers_area">
                                                        <div class="answers" id="${n.data.type==4?'match':''}">
                                                            ${answersAreaContent}
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-12 my-1 left_area">
                                                        <div class="dropdown">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" id="customDropdownButton" aria-haspopup="true" aria-expanded="false">
                                                                @lang('additional.buttons.answer_type')
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="customDropdownButton">
                                                                @foreach (App\Models\ExamQuestion::TYPES as $k => $type)
                                                                    <a class="dropdown-item types_element" id="types_{{ $type }}" onclick="set_type('{{ $type }}',true)" href="javascript:void(0)">{{ $k }}</a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-12 col-lg-12 my-1 classjustifybetween hide question_footer_buttons" id="question_footer_buttons">
                                                        <div>
                                                            <select name='question_layout' class="form-control form-control-sm" id="question_layout">
                                                                @foreach (\App\Models\ExamQuestion::LAYOUTS as $key => $type)
                                                                    <option  {{ old('question_layout') == $type ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div style="width:60px">
                                                            <input type='number' class='form-control' name='order_number' value='${n.data.order_number}' placeholder="Sıra" />
                                                        </div>
                                                        <button class='btn btn-primary btn-sm submit_answer' type="submit">Təsdiq et</button>
                                                    </div>
                                                </form>
                                            </div>`;

                                section_and_questions_right.innerHTML = element;
                                set_type(n.data.type, false);
                                const dropdownButton = document.getElementById('customDropdownButton');
                                const dropdownMenu = document.querySelector('.dropdown-menu');

                                var question_layout_select_option = document.querySelector(
                                    `select#question_layout option[value=${n.data.layout}]`);
                                question_layout_select_option.selected = true;
                                dropdownButton.addEventListener('click', function() {
                                    if (dropdownMenu.classList.contains('show')) {
                                        dropdownMenu.classList.remove('show');
                                    } else {
                                        dropdownMenu.classList.add('show');
                                    }
                                });

                                document.addEventListener('click', function(e) {
                                    if (!dropdownButton.contains(e.target)) {
                                        dropdownMenu.classList.remove('show');
                                    }
                                });


                                var question_input_id = $(".question_input").prop('id');
                                if (question_input_id != null)
                                    createeditor(question_input_id);
                                var question_input2_id;

                                if (n.data.type == 3) {
                                    var textbox_0 = $(".textbox_0").prop('id')
                                    if (textbox_0 != null)
                                        createeditor(textbox_0);

                                    question_input2_id = $(".question_input2").prop('id');
                                    if (question_input2_id != null)
                                        createeditor(question_input2_id);
                                }

                                for (var i = 0; i < idscontenteditable.length; i++) {
                                    var elem = $('answer__input_' + idscontenteditable[i]);
                                    if (elem != null)
                                        createeditor('answer__input_' + idscontenteditable[i]);
                                }

                                var matching_elements = document.getElementsByClassName("matching_element");
                                for (var i = 0; i < matching_elements.length; i++) {
                                    var elem2 = $(matching_elements[i].id);
                                    if (elem2 != null)
                                        createeditor(matching_elements[i].id);
                                }

                                if (n.data.type == 4) {
                                    initSortable('match');
                                }
                            }
                        }
                    });
                } catch (error) {
                    hideLoader();
                    toast(error, 'error');
                }
            }

            function deletequestion(id = null, type = null) {
                showLoader();
                var deleting_question_id = document.getElementById('deleting_question_id');
                var deleting_question_type = document.getElementById('deleting_question_type');
                var current_section = document.getElementById('current_section');
                if (type != null)
                    deleting_question_type.value = type

                if (id != null) {
                    var deleting_question_id = document.getElementById('deleting_question_id');
                    deleting_question_id.value = id;
                    toggleModalnow('deleteModal', 'open');
                    hideLoader();
                } else {
                    sendAjaxRequestOLD(`{{ route('front.questionsorsection.remove') }}`, "post", {
                        element_id: deleting_question_id.value,
                        element_type: deleting_question_type.value,
                        language: '{{ app()->getLocale() }}'
                    }, function(e,
                        t) {
                        if (e) toast(e, "error");
                        else {
                            hideLoader();
                            let n = JSON.parse(t);
                            if (n.message != null)
                                toast(n.message, n.status);

                            deleting_question_id.value = null;
                            deleting_question_type.value = null;
                            toggleModalnow('deleteModal', 'hide');

                            if (deleting_question_type == "question")
                                get_section(current_section.value);
                            else
                                getsectiondatas();
                        }
                    });
                }
            }

            function truncateString(str, maxLength) {
                let truncated = '';
                let i = 0;
                let len = 0;

                while (len < maxLength && i < str.length) {
                    let char = str.charAt(i);
                    let code = str.charCodeAt(i);
                    len += code < 128 ? 1 : 2;
                    if (len <= maxLength) {
                        truncated += char;
                    }
                    i++;
                }

                if (i < str.length) {
                    truncated += '...';
                }
                return truncated;
            }
        </script>
    @endif

    <script>
        function set_type(type, old = false) {
            try {
                var question_content_textbox = document.getElementById("question_content_textbox");
                var question_content_audio = document.getElementById("question_content_audio");
                var answers_area = document.getElementById('answers_area');
                var question_type = document.getElementById('question_type');
                var question_footer_buttons = document.getElementById('question_footer_buttons');
                var types_element = document.getElementsByClassName('types_element');
                var types_getted_element = document.getElementById(`types_${type}`);
                var question_id = document.getElementById('question_id');
                var answers_count = document.getElementById('answers_count');
                if (old == true)
                    answers_area.innerHTML = '';
                for (let index = 0; index < types_element.length; index++) {
                    const element = types_element[index];
                    element.classList.remove('active');
                }

                types_getted_element.classList.add('active');
                question_content_audio.classList.add('d-none');
                question_content_textbox.classList.remove("d-none");

                if (type == 5)
                    question_content_audio.classList.remove('d-none');


                var question_input_id = $(".question_input").prop('id');
                if (question_input_id != null)
                    createeditor(question_input_id);

                question_footer_buttons.classList.remove("hide");
                question_type.value = type;
                if (
                    (question_id == null && (answers_count == null || answers_count == 0)) ||
                    ((question_id != null && question_id.value != null) && (answers_count == null || answers_count.value ==
                        0)) || old == true
                ) {
                    var answers = ``;
                    if (type == 3) {
                        answers = `@include('frontend.exams.create_edit_exams.question_textbox')`;
                    } else if (type == 2) {
                        answers = `@include('frontend.exams.create_edit_exams.question_checkbox')`;
                    } else if (type == 4) {
                        answers = `@include('frontend.exams.create_edit_exams.question_match')`;
                    } else {
                        answers = `@include('frontend.exams.create_edit_exams.question_radio')`;
                    }

                    answers_area.innerHTML = answers;

                    if (type == 3) {
                        var textbox_0_id = document.getElementsByClassName("textbox_0");
                        if (textbox_0_id != null && textbox_0_id.length > 0) {
                            for (var i = 0; i < textbox_0_id.length; i++) {
                                var textbox_el = createeditor(textbox_0_id[i].id);
                            }
                        }

                        if ($(".question_input2").prop('id') != null)
                            var question_input2 = createeditor($(".question_input2").prop('id'));

                        var question2_input_area = document.getElementById('question2_input_area');
                        if (question2_input_area != null)
                            question2_input_area.classList.remove("d-none");

                    } else {
                        var question2_input_area = document.getElementById('question2_input_area');
                        if (question2_input_area != null)
                            question2_input_area.classList.add("d-none");
                    }

                    var textinput = $(".text-input");
                    if (textinput != null && textinput.length > 0) {
                        textinput.each(function(index, elem) {
                            var currentid = $(elem).attr('id');
                            var answer__input_ = $("#answer__input_" + currentid);
                            var button = $(elem).find('.add_button');
                            var codeofelement = createRandomCode('string', 11);
                            $(elem).attr('id', codeofelement);
                            answer__input_.attr("id", "answer__input_" + codeofelement);
                            button.attr("onclick", "addoreditanswer('single','add','" + codeofelement + "')");
                            if ($(codeofelement) != null)
                                createeditor(codeofelement);
                        });
                    }

                    var matching_element = $(".matching_element");
                    if (matching_element != null && matching_element.length > 0) {
                        for (let index = 0; index < matching_element.length; index++) {
                            const element = matching_element[index];
                            if (element != null)
                                createeditor(element.id);
                        }
                    }

                    if (type == 4) {
                        initSortable('match');
                    }

                    var question_answer_ones = document.getElementsByClassName('answer');
                    if (question_answer_ones != null && question_answer_ones.length > 0) {
                        for (var index = 0; index < question_answer_ones.length; index++) {
                            var elem = question_answer_ones[index].querySelector('.answer_content span.text-input');
                            if (elem) {
                                createeditor(elem.id);
                            }
                        }
                    }

                    var textbox_0_id = $(".textbox_0").prop('id');
                    if (textbox_0_id != null)
                        var textbox_0 = createeditor(textbox_0_id);

                }
            } catch (error) {
                toast(error, 'error');
                console.log("---------------SETTYPE------------" + error);
            }
        }

        function change_radio(type, id) {
            try {
                var clickedInput = $('#input_radios_' + id);
                if (type === 'multi' && clickedInput.attr('type') === 'checkbox') {
                    var labelspancheckmark = document.getElementById('label_checkmark_' + id);
                    if (labelspancheckmark.classList.contains('active'))
                        labelspancheckmark.classList.remove("active");
                    else
                        labelspancheckmark.classList.add('active');

                    clickedInput.prop('checked', !clickedInput.prop('checked'));
                } else if (type === 'single' && clickedInput.attr('type') === 'radio') {
                    $('.input_radios[type="radio"]').prop('checked', false);
                    clickedInput.prop('checked', true);
                } else {
                    clickedInput.prop('checked', !clickedInput.prop('checked'));
                }
            } catch (error) {
                toast(error, 'error');
                console.log("---------------ChangeRadio-----------" + error);
            }
        }

        function addoreditanswer(type, operation, itemid) {
            try {
                var element = document.getElementById(itemid);
                var answers = document.querySelector('.answers');
                var answer_elements = document.querySelectorAll('.answer');

                if (operation == "remove") {
                    element.remove();
                } else {
                    var codeofelement = createRandomCode('string', 11);
                    element = document.createElement('div');
                    element.className = `answer ${type}`;
                    element.id = codeofelement;

                    var rownumber = '';
                    if (answer_elements.length == 1)
                        rownumber = 'B)'
                    else if (answer_elements.length == 2)
                        rownumber = 'C)'
                    else if (answer_elements.length == 3)
                        rownumber = 'D)'
                    else if (answer_elements.length == 4)
                        rownumber = 'E)'
                    else if (answer_elements.length == 5)
                        rownumber = 'F)'
                    else if (answer_elements.length == 6)
                        rownumber = 'G)'

                    var innerContent = '';
                    if (type === 'single') {
                        innerContent = `
                        <label class="radio-input" onclick="change_radio('${type}', ${answer_elements.length})">
                            <input type="radio" name="answers[${answer_elements.length}]" value="${answer_elements.length}" class="input_radios" id="input_radios_${answer_elements.length}">
                            <span class="checkmark" id="label_checkmark_${answer_elements.length}"></span>
                                ${rownumber}
                            </label>
                            <span name="answer_reply[${answer_elements.length}]" id="answer__input_${codeofelement}" name="answer_reply[${answer_elements.length}]"
                                class="text-input summernote_element" placeholder="@lang('additional.forms.answer')"
                                contenteditable="true" placeholder="@lang('additional.forms.your_question')"></span>`;
                    } else if (type === 'multi') {
                        innerContent = `
                        <label class="radio-input" onclick="change_radio('${type}', ${answer_elements.length})">
                            <input type="checkbox" name="answers[${answer_elements.length}]" value="${answer_elements.length}" class="input_radios" id="input_radios_${answer_elements.length}">
                            <span class="checkmark" id="label_checkmark_${answer_elements.length}"></span>
                                ${rownumber}
                        </label>
                        <span name="answer_reply[${answer_elements.length}]" id="answer__input_${codeofelement}" name="answer_reply[${answer_elements.length}]"
                            class="text-input summernote_element" placeholder="@lang('additional.forms.answer')" contenteditable="true"
                            placeholder="@lang('additional.forms.your_question')"></span>`;
                    } else if (type === 'match') {
                        innerContent = `
                        <div class="sides left_side">
                            <div class="content_element matching_element" id="mathing_questions_${answer_elements.length}" data-key="${answer_elements.length}" name="mathing_questions[${answer_elements.length}]" placeholder="@lang('additional.forms.answer')" contenteditable="true"></div>
                        </div>
                        <div class="sides right_side">
                            <div class="content_element matching_element" id="mathing_answers_${answer_elements.length}" data-key="${answer_elements.length}" name="mathing_answers[${answer_elements.length}]" placeholder="@lang('additional.forms.answer')" contenteditable="true"></div>
                        </div>`;
                    }

                    element.innerHTML = `
                        <div class="answer_content">${innerContent}</div>
                        <button type="button" class="btn btn-sm btn-outline-success add_remove_buttons add_button"
                            onclick="addoreditanswer('${type}','add','${codeofelement}')"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger add_remove_buttons remove_button"
                            onclick="addoreditanswer('${type}','remove','${codeofelement}')"><i class="fa fa-minus"></i></button>
                    `;

                    answers.appendChild(element);
                    if (type == "match") {
                        if ($(`mathing_questions_${answer_elements.length}`) != null)
                            createeditor(`mathing_questions_${answer_elements.length}`);

                        if ($(`mathing_answers_${answer_elements.length}`) != null)
                            createeditor(`mathing_answers_${answer_elements.length}`);
                    } else {
                        if ($(`answer__input_${codeofelement}`) != null)
                            createeditor(`answer__input_${codeofelement}`);
                    }

                    var question_answer_ones = document.getElementsByClassName('answer');
                    if (question_answer_ones != null && question_answer_ones.length > 0) {
                        for (var index = 0; index < question_answer_ones.length; index++) {
                            var elem = question_answer_ones[index].querySelector('.answer_content span.text-input');
                            if (elem) {
                                createeditor(elem.id);
                            }
                        }
                    }
                }
            } catch (error) {
                toast(error, 'error');
                console.log("-----------addoreditanswer----------" + error);
            }
        }

        function audiofileselect(event) {
            try {
                var file = event.target.files[0];
                if (file != null) {
                    var selectedAudioFile = document.getElementById('selectedAudioFile');
                    var element =
                        `<audio controls><source src="${URL.createObjectURL(file)}" type="audio/mpeg">Your browser does not support the audio element.</audio>`;
                    selectedAudioFile.innerHTML = element;
                } else {
                    toast('@lang('additional.pages.exams.notfound')', 'error');
                }
            } catch (error) {
                console.log("-----------------AudioFileSelect-------------" + error);
                toast(error, 'error');
            }
        }
    </script>
    {{-- Section Functions --}}

    <script>
        function createeditor(id = null, value = null) {
            try {
                var selector = id ? `#${id}` : `.summernote_element`;
                let tinmyMceInstance = tinymce.get(id);
                if (tinmyMceInstance == null) {
                    tinymce.init({
                        selector: selector,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                        toolbar: 'fontfamily fontsize forecolor backcolor | bold italic underline strikethrough subscript superscript | link image media table | align lineheight | numlist bullist indent outdent | charmap',
                        menubar: false,
                        image_advtab: false,
                        a11y_advanced_options: true,
                        image_caption: true,
                        image_description: false,
                        image_dimensions: false,
                        image_title: true,
                        images_upload_credentials: true,
                        images_upload_url: "{{ route('api.upload_image_editor') }}",
                        automatic_uploads: true,
                        block_unsupported_drop: false,
                        file_picker_types: 'file image media',
                        images_upload_handler: function(blobInfo, progress) {
                            return new Promise((resolve, reject) => {
                                var url = "{{ route('api.upload_image_editor') }}";
                                var formData = new FormData();
                                formData.append('image', blobInfo.blob(), blobInfo.filename());

                                fetch(url, {
                                        method: 'POST',
                                        body: formData,
                                    })
                                    .then(function(response) {
                                        if (!response.ok) {
                                            throw new Error('Network response was not ok');
                                        }
                                        return response.json();
                                    })
                                    .then(function(data) {
                                        resolve(data.location);
                                    })
                                    .catch(function(error) {
                                        console.error('Error during image upload:', error);
                                        reject(error); // Hata durumunda reddet
                                    });
                            });
                        },
                        toolbar_mode: 'floating',
                        inline: false,
                        directionality: 'ltr',
                        setup: function(editor) {
                            editor.setContent(value);
                            editor.on('init', function(e, value) {
                                if (value != null) editor.setContent(value);
                            }.bind(null, null, value));
                        }
                    });
                    setTimeout(() => {
                        var tiny = tinymce.get(id);
                        tiny.setContent(value);
                        return tiny;
                    }, 1000);
                } else {
                    if (tinmyMceInstance) tinmyMceInstance.remove();
                    return createeditor(id, value)
                }
            } catch (error) {
                console.error('------------createeditorError----------------', error);
            }
        }

        function getcontenteditor(id = null) {
            try {
                var selector = id ? `#${id}` : `.summernote_element`;
                let instance = tinymce.get(id);
                if (instance != null) {
                    var contentinstance = instance.getContent();
                    return contentinstance;
                } else {
                    return '';
                }
            } catch (error) {
                createnotification('@lang('additional.toastr_types_and_notifications.error')', error, 'error');
                console.error('------------getcontentmce----------------', error);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            createeditor();
        });
    </script>
    {{--  --}}
@endpush
