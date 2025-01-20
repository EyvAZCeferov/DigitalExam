@extends('backend.layouts.main')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>İmtahan analizi</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>İmtahan analizi</strong>
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

                        <form class="row align-items-center" id="analyze_form" onsubmit="analyzeform('analyze_form')">
                            @csrf
                            <div class="col-5">
                                <label for="user_id">İstifadəçi</label>
                                <select name="user_id" class="select2" id="user_id">
                                    <option value=""></option>
                                    @foreach ($users as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }} / {{ $value->email }} /
                                            {{ $value->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <label for="exam_id">İmtahan</label>
                                <select name="exam_id" class="select2" id="exam_id">
                                    <option value=""></option>
                                    @foreach ($exams as $value)
                                        <option value="{{ $value->id }}">{{ $value->name[app()->getLocale().'_name'] }} /
                                            {{ @$value->category->name['az_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <label for=""></label>
                                <button type="submit" class="btn btn-success btn-block btn-sm"> Axtar </button>
                            </div>
                        </form>
                    </div>
                    <div class="ibox-content row" id="content">


                    </div>
                </div>
            </div>
        </div>



    </div>

    <div id="loader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden"></span>
        </div>
    </div @endsection @push('css') <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        rel="stylesheet" />
@endpush
@push('js')
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script defer>
        setTimeout(() => {
            $('.select2').each(function() {
                $(this).select2({
                    width: '100%'
                })
            });
        }, 500);
    </script>
    <script defer>
        function analyzeform(id) {
            event.preventDefault();
            showLoader();
            var form = $(`#${id}`).serialize();
            $.post(`{{ url()->current() }}`, form, (res) => {
                if (res.status === "success") {
                    hideLoader();
                    toastr.success("Nəticələr tapıldı.");
                    $("#content").empty();
                    results = res.results;
                    createViewResults(res.results);
                } else {
                    hideLoader();
                    toastr.error(res.message);
                }
            }).fail((err) => {
                hideLoader();
                toastr.error(err);
            });
        }

        function createViewResults(results) {
            if (results != null && results.length > 0) {
                var content = $("#content");
                results.forEach(function(element) {
                    if (element.user != null && element.exam != null &&
                        element.user.name != null && element.exam.name != null
                    ) {
                        var answers = element.answers;
                        var percentagetotalquestions = getpercentanswers(answers);
                        var resultHtml = `
                        <div style="border:1px solid #ccc;width:100%;" class="my-4">
                            <div class="row px-5 my-3 d-flex align-items-center align-center align-content-center justify-content-center text-center"><h2 class="font-weight-bold text-center w-100">${element.exam.name.az_name}</h2></div>
                            <div class='px-5 my-3 align-items-center align-center align-content-center justify-content-center'>
                                ${createuserprofile(element.user,percentagetotalquestions,element)}
                            </div>
                            <canvas id="totalanswers_${element.id}"></canvas>
                            <br/>
                            <canvas id="questionanswers_${element.id}"></canvas>
                        </div>`;

                        content.append(resultHtml);

                        if (percentagetotalquestions != null) {
                            createtotalanswerschart(`totalanswers_${element.id}`, percentagetotalquestions);
                        }

                        if (answers != null) {
                            createquestionanswerschart(`questionanswers_${element.id}`, answers);
                        }

                    }
                });
            } else {
                content.empty();
            }
        }

        function createuserprofile(user,percentage,info) {
            var element = `<div class="row">`;
            if (user.picture != null) {
                element+= `
                <div class='col-3'>
                    <img  class='img img-responsive' src='/uploads/users/${user.picture}' style="width:100%;max-height:120px;object-fit:contain;" />
                </div>`;
            }
            element+= `
                <div class="col-9 row">
                    <div class="col-6">
                        <p> <h4 class="d-inline-block">Ad Soyad: </h4> <span class="d-inline-block">${user.name}</span> </p>
                    </div>
                    <div class="col-6">
                        <p> <h4 class="d-inline-block">Düz cavablar: </h4> <span class="d-inline-block">${percentage.correctQuestions}</span> </p>
                        <p> <h4 class="d-inline-block">Yanlış cavablar: </h4> <span class="d-inline-block">${percentage.incorrectQuestions}</span> </p>
                        <p> <h4 class="d-inline-block">Topladığı bal: </h4> <span class="d-inline-block">${info.point}</span> </p>
                    </div>
                </div>
                `;

            return element;
        }

        function getpercentanswers(answers) {
            var answerResults = {
                correctQuestions: 0,
                incorrectQuestions: 0,
                totalQuestions: 0,
                percentageCorrect: 0,
                percentageIncorrect: 0
            };
            if (answers && answers.length > 0) {
                answerResults.totalQuestions = answers.length;

                answers.forEach(function(element) {
                    if (element.result === 0) {
                        answerResults.incorrectQuestions += 1;
                    } else {
                        answerResults.correctQuestions += 1;
                    }
                });
            }
            if (answerResults.correctQuestions > 0 || answerResults.incorrectQuestions > 0) {
                answerResults.percentageCorrect = (answerResults.correctQuestions / answerResults.totalQuestions) * 100;
                answerResults.percentageIncorrect = (answerResults.incorrectQuestions / answerResults.totalQuestions) * 100;
            }
            return answerResults;
        }

        function createtotalanswerschart(id, percentage) {
            if (id != null && id != '' && id != ' ') {
                const ctx = document.getElementById(id);
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Doğru cavablar', 'Yanlış cavablar'],
                        datasets: [{
                            data: [percentage.correctQuestions, percentage.incorrectQuestions],
                            label: 'Doğru və yanlış cavablar',
                            borderWidth: 1,
                            borderColor: '#36A2EB',
                            backgroundColor: '#9BD0F5',
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        function createquestionanswerschart(id, answers) {
            var answerresults = [];
            var questions = [];
            var answers = [];
            var results = [];
            if (answers != null && answers.length > 0) {
                answers.forEach(function(element) {
                    if (element.question.question != null && element.exam_id != null && element.id != null) {
                        answerresults.push({
                            question: element.question,
                            result: element.result,
                            answers: element.answers,
                            answer: element.answer,
                            value: element.value,
                            id: element.id,
                        });
                    }
                });

                answerresults.forEach(function(element) {
                    if (element.question != null) {
                        questions.push({
                            id: element.question.id,
                            text: element.question.question,
                            type: element.question.type,
                            result_id: element.id,
                        });
                    } else {
                        questions.push({
                            id: null,
                            text: null,
                            type: null,
                            result_id: null,
                        });
                    }
                });

                answerresults.forEach(function(element) {
                    if (element.answer != null) {
                        answers.push({
                            id: element.answer.id,
                            text: element.answer.answer,
                            correct: element.answer.correct,
                        });
                    } else {
                        answers.push({
                            id: null,
                            text: null,
                            correct: null
                        });
                    }
                });

                answerresults.forEach(function(element) {
                    if (element.result != null) {
                        results.push({
                            id: element.answer.id,
                            text: element.answer.answer,
                            correct: element.answer.correct,
                        });
                    } else {
                        answers.push({
                            id: null,
                            text: null,
                            correct: null
                        });
                    }
                });


                if (id != null && id != '' && id != ' ') {
                    const ctx2 = document.getElementById(id);
                    // new Chart(ctx, {
                    //     type: 'bar',
                    //     data: {
                    //         labels: ['Doğru cavablar', 'Yanlış cavablar'],
                    //         datasets: [{
                    //             data: [answerresults],
                    //             label: 'Doğru və yanlış cavablar',
                    //             borderWidth: 1,
                    //             borderColor: '#36A2EB',
                    //             backgroundColor: '#9BD0F5',
                    //         }]
                    //     },
                    //     options: {
                    //         scales: {
                    //             y: {
                    //                 beginAtZero: true
                    //             }
                    //         }
                    //     }
                    // });
                }
            }
        }
    </script>
@endpush
