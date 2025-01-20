@extends('backend.layouts.main')

@section('body_class', 'full-height-layout')

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
                <li class="breadcrumb-item active">
                    <strong>Suallar</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>


    <div class="fh-breadcrumb">

        <div class="fh-column">
            <div class="full-height-scroll">
                <div class="p-3">
                    <a href="{{ route('sections.create', $exam_id) }}" type="button"
                       class="btn btn-block btn-outline btn-primary">Yeni Bölmə</a>
                </div>
                <ul class="list-group elements-list">

                    @foreach($sections as $item)
                        <li class="list-group-item {{ $item->id == $section->id ? 'active' : '' }} d-flex justify-content-between align-items-center px-3">
                            <a class="nav-link d-inline-block w-100" href="{{ route('exams.questions', [$exam_id, $item->id]) }}">
                                <strong>{{ $item->name }}</strong>
                            </a>
                            <div style="min-width: max-content;">
                                <a href="{{ route('sections.edit', [$exam_id, $item->id]) }}"
                                   class="label label-danger mr-1"><i class="fa fa-edit"></i></a><a
                                    href="{{ route('sections.delete', [$exam_id, $item->id]) }}"
                                    class="label label-danger "><i class="fa fa-close"></i></a>
                            </div>
                        </li>
                    @endforeach


                </ul>

            </div>
        </div>

        <div class="full-height">
            <div class="full-height-scroll white-bg border-left">

                <div class="element-detail-box">

                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="ibox ">
                                <div class="ibox-title border-0">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5>Suallar</h5>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="{{ route('exams.index') }}" class="btn btn-w-m btn-default">Geri</a>
                                            @if($section)
                                                <a href="{{ route('exams.questions.create', [$exam_id, $section->id]) }}"
                                                   class="btn btn-w-m btn-primary">Yeni</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <table class="table table-bordered table-hover dataTables-base" data-order="2">
                                        <thead>
                                        <tr>
                                            <th>Sual</th>
                                            <th>Doğru cavab</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($questions as $data)
                                            <tr class="gradeX">
                                                <td>{!! $data->question !!}</td>
                                                <td>
                                                    @if($data->type === 1)
                                                        {!! $data?->correctAnswer()?->answer !!}
                                                    @elseif($data->type === 2)
                                                        @foreach($data?->correctAnswer() as $answer)
                                                            {!! $answer->answer !!}@if(!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {!! $data?->correctAnswer()?->answer !!}
                                                    @endif
                                                </td>

                                                <td class="text-right">
                                                    <a href="{{ route('exams.answers', [$exam_id, $section->id, $data->id]) }}"
                                                       class="btn btn-info btn-sm">Cavablar</a>
                                                    <a href="{{ route('exams.questions.edit', [$exam_id, $section->id, $data->id]) }}"
                                                       class="btn btn-warning btn-sm">Yenilə</a>
                                                    <a href="{{ route('exams.questions.delete', [$exam_id, $section->id, $data->id]) }}"
                                                       class="btn btn-danger btn-sm">Sil</a>
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

            </div>
        </div>


    </div>
@endsection
