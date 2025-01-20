<section class="search">
    <div class="row">
        <div class="container">
            <form class="w-75" onsubmit="formsend()" id="formsend" style="margin-bottom:15px">
                <div id="messages"></div>
                @csrf
                <input type="hidden" name="language" value="{{ app()->getLocale() }}" />
                <div class="form-group">
                    <input type="text" onkeyup="searchinfields('query','datas','{{ $type }}')" name="query"
                    @if(isset($value) && !empty($value)) value="{{ $value }}" @endif
                        placeholder="@lang('additional.forms.searchkeyword')" class="form-control searchkeywords" style="border-radius: 25px;">
                    <span class="eye-icon" id="query-eye-icon" onclick="searchinfields()"><i
                            class="fa fa-search"></i></span>
                </div>
            </form>
        </div>
    </div>
</section>
