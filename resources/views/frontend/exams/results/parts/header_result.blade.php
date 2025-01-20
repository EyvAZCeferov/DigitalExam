<div class="header_columns">
    <h1 id="section_name" class='section_name'>{{ $exam->sections[0]->name }}</h1>
    <div class="timer_section" id="timer_section">
    </div>
    <div class="right_section">
        <a class="section calculator" target="_blank" href="{{ session()->has('subdomain') ? route("user.exam.resultpagestudents.subdomain",['exam_id'=>$exam->id,'subdomain'=>session()->get("subdomain")]) : route("user.exam.resultpagestudents",['exam_id'=>$exam->id]) }}">
            <i class="fa fa-user-astronaut"></i>
            @lang('additional.pages.studentratings.studentratings')
        </a>
    </div>

</div>
