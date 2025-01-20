@extends('backend.layouts.main')

@push('js')
    <script>
        var elem = document.querySelector('.js-switch');
        var switchery = new Switchery(elem, {
            color: '#1AB394'
        });
    </script>
    <script>
        function onchange_type(event) {
            if (event.target.value == 5) {
                $("#question_content_audio").removeClass('d-none');
                $("#question_content_textbox").addClass('d-none');
            } else {
                $("#question_content_textbox").removeClass('d-none');
                $("#question_content_audio").addClass('d-none');
            }
        }
    </script>
@endpush

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Suallar</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('exams.index') }}">İmtahanlar</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('exams.questions', [$exam_id, $section_id]) }}">Suallar</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Yenilə</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="ibox-tools mb-3">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="fullscreen-link">
                                <i class="fa fa-expand"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5>Yenilə</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('exams.questions', [$exam_id, $section_id]) }}"
                                    class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>

                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('exams.questions.update', [$exam_id, $section_id, $question->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <input type="hidden" name="exam_id" value="{{ $exam_id }}">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group @if ($question->type == 5) d-none @endif"
                                        id="question_content_textbox">
                                        <label for="">Sual</label>
                                        <textarea name="question" class="form-control summernote {{ $errors->first('question') ? 'is-invalid' : '' }}">{{ old('question', $question->question) }}</textarea>
                                    </div>

                                    <div class="form-group @if ($question->type != 5) d-none @endif"
                                        id="question_content_audio">
                                        <label for="">Səs faylı</label>
                                        <div class="d-block">
                                            <audio controls>
                                                <source src="{{ getImageUrl($question->question, 'exam_questions') }}" type="audio/{{ pathinfo($question->question, PATHINFO_EXTENSION) }}">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                        <input type="file" name="question_audio" class="file" accept="audio/*">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Sual tipi</label>
                                        <select name="type"
                                            class="form-control {{ $errors->first('type') ? 'is-invalid' : '' }}">
                                            <option disabled hidden selected>Tip</option>
                                            @foreach (\App\Models\ExamQuestion::TYPES as $key => $type)
                                                <option {{ old('type', $question->type) == $type ? 'selected' : '' }}
                                                    value="{{ $type }}">{{ $key }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Sual görünüşü</label>
                                        <select name="layout" class="form-control {{ $errors->first('type') ? 'is-invalid' : '' }}">
                                            @foreach(\App\Models\ExamQuestion::LAYOUTS as $key => $type)
                                                <option {{ old('layout',$question->layout) == $type ? 'selected' : '' }} value="{{ $key }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Şəkil</label>
                                        <input type="file" name="image"
                                            class="form-control {{ $errors->first('image') ? 'is-invalid' : '' }}">
                                        <img style="max-width: 200px"
                                            src="{{ $question->getFirstMedia('exam_question')?->getUrl() }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <button class="btn btn-primary">Yenilə</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
