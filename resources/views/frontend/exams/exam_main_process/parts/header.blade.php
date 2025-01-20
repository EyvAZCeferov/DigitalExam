<div class="header_columns" id="header_columns">
    <h1 id="section_name" class="section_name">{{ $exam->sections[0]->name }}</h1>
    <div class="timer_section" id="timer_section">
        <div class="hour_area">
            <span id="minutes">
            </span>:<span id="seconds"></span>
        </div>
        <div class="icon_area">
            <i class="fas fa-stopwatch"></i>
        </div>
        <button onclick="togglehours()" type="button" class="btn btn-sm timer_button" id="timer_button">
            @lang('additional.buttons.hide')
        </button>
    </div>

    <div class="right_section">
        @if ($exam->show_calc == true)
            <a class="section calculator" href="javascript:void(0)" onclick="togglecalculator()">
                <i class="fa fa-calculator"></i>
                @lang('additional.pages.exams.calculator')
            </a>
        @endif
        @if (!empty($exam->references) && count($exam->references)>0)
            <a class="section referances" href="javascript:void(0)" onclick="togglereferances()">
                <i class="fa fa-superscript"></i>
                @lang('additional.pages.exams.referances')
            </a>
        @endif
    </div>
</div>
