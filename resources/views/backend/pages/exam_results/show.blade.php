@extends('backend.layouts.main')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>İmatahan nəticələri</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('exam.results') }}">İmatahan nəticələri</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Ətraflı</strong>
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
                                <h5>Ətraflı</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('exam.results') }}" class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>

                    </div>
                    <div class="ibox-content">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">İstifadəçi</label>
                                    <input type="text" value="{{ $result?->user?->name }}" readonly name="name"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">İmtahan</label>
                                    <input type="text" value="{{ $result?->exam?->name['az_name'] }}" readonly
                                        name="name" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">İmtahan nəticəsi</label>
                                    <input type="text" value="{{ $result->exam->point ?? 0 * $result->correctAnswers() }}"
                                        readonly name="name" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">İmtahan nəticəsi</label>
                                    <input type="text" value="{{ $result->exam->point ?? 0 * $result->correctAnswers() }}"
                                        readonly name="name" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row"> 
                            @php
                                $index = 0;
                            @endphp
                            @foreach ($result->answers->groupBy('section_id') as $section_id => $answers)
                                @php
                                    $section = \App\Models\Section::find($section_id);
                                @endphp
                                <div class="col-12">
                                    <h1>Bölmə: {{ $section->name }}</h1>
                                    @foreach ($answers as $answer)
                                        @php
                                            $index++;
                                        @endphp
                                        <div class="form-group alert alert-{{ $answer->result ? 'success' : 'danger' }}">
                                            <h3>{{ $index }}. {!! $answer->question?->question !!}</h3>
                                            @if ($answer->question?->getFirstMediaUrl('exam_question'))
                                                <div class="exam__video">
                                                    <img class="image" style="width: 150px; height: auto;"
                                                        src="{{ $answer->question?->getFirstMediaUrl('exam_question') }}"
                                                        alt="">
                                                </div>
                                                <br>
                                            @endif
                                            <div class="row">
                                                <div class="col-xs-6 col-md-3">İstifadəçinin cavabı</div>
                                                <div class="col-xs-6 col-md-9">
                                                    @if ($answer->question->type === 1)
                                                        <p>{!! $answer->answer->answer !!}</p>
                                                    @elseif($answer->question->type === 2)
                                                        @php
                                                            $user_answers = \App\Models\ExamAnswer::whereIn('id', $answer->answers)->get();
                                                        @endphp
                                                        @foreach ($user_answers as $user_answer)
                                                            <p>{!! $user_answer->answer !!} </p>
                                                        @endforeach
                                                    @else
                                                        {!! $answer->value !!}
                                                    @endif
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-xs-6 col-md-3">Düzgün cavab</div>
                                                <div class="col-xs-6 col-md-9">
                                                    @if ($answer->question->type === 1)
                                                        <p>{!! $answer->question?->correctAnswer()?->answer !!}</p>
                                                    @elseif($answer->question->type === 2)
                                                        @foreach ($answer->question?->correctAnswer() as $a)
                                                            <p>{!! $a->answer !!}</p>
                                                        @endforeach
                                                    @elseif($answer->question->type === 4)
                                                        Salam
                                                    @else
                                                        {{-- <p>{!! $answer->question?->correctAnswer()?->answer !!}</p> --}}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <hr>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
