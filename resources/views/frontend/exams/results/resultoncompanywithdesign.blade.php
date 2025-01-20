@extends('frontend.layouts.app')
@section('title', $exam->name[app()->getLocale() . '_name'])
@push('js')
    <script defer>
        function changetab(id, result_id) {
            // Tüm nav-link ve tab-pane öğelerini seç
            var navlinks = document.querySelectorAll(`.nav-link[data-result="${result_id}"]`);
            var tabpanes = document.querySelectorAll(`.tab-pane[data-result="${result_id}"]`);

            // Aktif sınıflarını kaldır
            navlinks.forEach(element => {
                element.classList.remove("active");
            });

            tabpanes.forEach(element => {
                element.classList.remove("active", "show");
            });

            // Seçilen tab ve linki aktif yap
            var selectednavlink = document.getElementById(`nav-${id}-tab`);
            var selectedtabpane = document.getElementById(`nav-${id}`);

            selectednavlink.classList.add('active');
            selectedtabpane.classList.add('active', 'show');
        }

        function getexamresults() {
            try {
                var area_info = document.getElementById('area_info');
                showLoader();
                sendAjaxRequestOLD(`{{ route('api.examResultPageStudentsWithSubdomain') }}`, "post", {
                        exam_id: document.getElementById('exam_id').value,
                        responseType: 'json'
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
                                area_info.innerHTML = '';
                                for (var i = 0; i < n.data.length; i++) {
                                    var htmlContent = '';
                                    var exam_result = n.data[i];
                                    var resulttruefalse = n.true_false_questions[exam_result.id];
                                    var wrong_and_truecounts = n.wrong_and_truecounts[exam_result.id];
                                    if (exam_result.user) {
                                        console.log(exam_result);
                                        let exam = exam_result.exam;
                                        let questions = [];
                                        let qesutions = exam.sections.flatMap(section => section.questions);

                                        qesutions.forEach(qest => {
                                            questions.push(qest);
                                        });

                                        let tabButtons = '';
                                        if (exam.sections && exam.sections.length > 0) {
                                            exam.sections.forEach((section, index) => {
                                                tabButtons += `
                                                    <button onclick="changetab('${index}${section.id}${exam_result.id}',${exam_result.id})" class="nav-link btn-sm"
                                                        id="nav-${index}${section.id}${exam_result.id}-tab" data-bs-toggle="tab" data-result="${exam_result.id}"
                                                        data-bs-target="#nav-${index}${section.id}${exam_result.id}" type="button" role="tab"
                                                        aria-controls="nav-${index}${section.id}${exam_result.id}"
                                                        aria-selected="true">${section.name}</button>
                                                `;
                                            });
                                        }

                                        let questionsContent = '';
                                        questions.forEach((question, index) => {

                                            var result = 'null';
                                            if (resulttruefalse && resulttruefalse.hasOwnProperty(question.id
                                                    .toString())) {
                                                result = resulttruefalse[question.id.toString()];
                                            }

                                            questionsContent += `
                                                <button class="btn btn-sm btn-question f${result}"
                                                    type="button">${index + 1}</button>
                                            `;
                                        });

                                        // HTML içeriğini oluşturma
                                        htmlContent = `
                                            <section class="result_page">
                                                <div class="header">
                                                    <p>${exam_result.user.name} @lang('additional.buttons.result')</p>
                                                    <div class="header_bottom_row">
                                                        <div class="col"></div>
                                                        <div class="col true">
                                                            @lang('additional.pages.exams.true_answers') <div class="blockwithbg"></div>
                                                        </div>
                                                        <div class="col false">
                                                            @lang('additional.pages.exams.false_answers') <div class="blockwithbg"></div>
                                                        </div>
                                                        <div class="col">
                                                            @lang('additional.pages.exams.notanswered_answers') <div class="blockwithbg"></div>
                                                        </div>
                                                        <div class="col"></div>
                                                    </div>
                                                </div>

                                                <div class="content">
                                                    <nav>
                                                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                            <button onclick="changetab('home')" class="nav-link active" id="nav-home-tab"
                                                                data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab"
                                                                aria-controls="nav-home" aria-selected="true">@lang('additional.pages.exams.allquestions')</button>
                                                            ${tabButtons}
                                                        </div>
                                                    </nav>
                                                    
                                                    <div class="tab-content" id="nav-tabContent">
                                                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" data-result="${exam_result.id}"
                                                            aria-labelledby="nav-home-tab">
                                                            ${questionsContent}
                                                            <div class="footer">
                                                                <div>
                                                                    Doğru cavab: ${wrong_and_truecounts.correct} Yanlış cavab: ${wrong_and_truecounts.wrong}
                                                                </div>
                                                                <div>
                                                                    Nəticə: ${exam_result.point}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Diğer sekme içerikleri -->
                                                        ${exam.sections.map((section, index) => `
                                                                <div class="tab-pane fade" id="nav-${index}${section.id}${exam_result.id}" role="tabpanel"  data-result="${exam_result.id}"
                                                                    aria-labelledby="nav-${index}${section.id}${exam_result.id}-tab">
                                                                    ${section.questions.map((question, index) => `
                                                                    <button class="btn btn-sm btn-question ${resulttruefalse && resulttruefalse.hasOwnProperty(question.id.toString()) ==true ? resulttruefalse[question.id.toString()] : 'null'}"
                                                                        type="button">${index + 1}</button>
                                                                `).join('')}
                                                                    <div class="footer">
                                                                        <div>
                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            `).join('')}
                                                    </div>
                                                </div>

                                                <!-- Sonuç gösterme butonu -->
                                                
                                                <div class="row classcenter mt-3">
                                                    <a target='_blank' class="tonextbutton" href="/exam/results/${exam_result.id}">@lang('additional.buttons.result')</a>
                                                </div>
                                            
                                            </section>
                                        `;
                                        area_info.innerHTML += htmlContent;
                                    }
                                }
                            } else {
                                area_info.innerHTML = '<p class="text-center text-danger my-2">Məlumat tapılmadı</p>';
                            }
                        }
                    });
            } catch (error) {
                console.log(error);
                hideLoader();
                toast(error, 'error');
            }
        }

        function get_exam_result_answer_true_or_false(result_id) {
            try {
                sendAjaxRequestOLD(`{{ route('api.exam_result_answer_true_or_false') }}`, "post", {
                    result_id,
                }, function(e, t) {
                    if (e) {
                        toast(e, 'error');
                    } else {
                        // let n = JSON.parse(t);
                        return t;
                    }
                });
            } catch (error) {
                return 'false';
            }
        }


        window.addEventListener('load', function() {
            getexamresults();
        });
    </script>
@endpush

@section('content')
    <input type="hidden" name="exam_id" id="exam_id" value="{{ $exam->id }}" />
    <div class="container" id="area_info">

    </div>
@endsection
