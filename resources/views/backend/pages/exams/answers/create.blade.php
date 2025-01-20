@extends('backend.layouts.main')

@push('js')

    <script>
        var elem = document.querySelector('.js-switch');
        var switchery = new Switchery(elem, { color: '#1AB394' });
    </script>
@endpush

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Cavablar</h2>
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
                <li class="breadcrumb-item">
                    <a href="{{ route('exams.answers', [$exam_id, $section_id, $question_id]) }}">Cavablar</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Yeni</strong>
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
                                <h5>Yeni</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('exams.answers', [$exam_id, $section_id, $question_id]) }}" class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('exams.answers.store', [$exam_id, $section_id, $question_id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $question_id }}">
                            <input type="hidden" name="exam_id" value="{{ $exam_id }}">
                            <div class="row" >
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Cavab</label>
                                        <textarea name="answer" class="summernote form-control {{ $errors->first('answer') ? 'is-invalid' : '' }}">{{ old('answer') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="w-100">Düzgün</label>
                                        <input type="checkbox" value="1" name="correct" class="js-switch {{ $errors->first('correct') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                            </div>


                            <button class="btn btn-primary">Saxla</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
