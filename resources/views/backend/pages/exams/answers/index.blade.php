@extends('backend.layouts.main')

@push('js')


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
                <li class="breadcrumb-item active">
                    <strong>Cavablar</strong>
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
                                <h5>Cavablar</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('exams.questions', [$exam_id, $section_id]) }}" class="btn btn-w-m btn-default">Geri</a>
                                @if($question->type == 3)
                                    @if($answers->count() == 0)
                                        <a href="{{ route('exams.answers.create', [$exam_id, $section_id, $question_id]) }}" class="btn btn-w-m btn-primary">Yeni</a>
                                    @endif
                                @else
                                    <a href="{{ route('exams.answers.create', [$exam_id, $section_id, $question_id]) }}" class="btn btn-w-m btn-primary">Yeni</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-bordered table-hover dataTables-base" data-order="2">
                            <thead>
                            <tr>
                                <th>Cavab</th>
                                <th>Düzgün Cavab</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($answers as $data)
                                <tr class="gradeX">
                                    <td>{!! $data->answer !!}</td>
                                    <td>
                                        {{ $data->correct ? 'Bəli' : 'Xeyr'}}
                                    </td>

                                    <td class="text-right">
                                        <a href="{{ route('exams.answers.edit', [$exam_id, $section_id, $question_id, $data->id]) }}" class="btn btn-warning btn-sm">Yenilə</a>
                                        <a href="{{ route('exams.answers.delete', [$exam_id, $section_id, $question_id, $data->id]) }}" class="btn btn-danger btn-sm">Sil</a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
