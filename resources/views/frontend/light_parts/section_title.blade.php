<div class="row section_title">
    @if (!empty($title))
        <h3>{{ $title }}</h3>
    @else
        <h4 class="d-inline-block p-3" style="width:max-content;"> </h4>
    @endif
    @if (isset($url) && !empty($url))
        <a href="{{ $url }}" class="btn btn-primary">@lang('additional.buttons.more') <i class="fa-solid fa-circle-right"></i>
        </a>
    @endif

    @if (isset($button) && !empty($button))
        <div class="row d-inline-block view_button_area" style="justify-content: flex-end;">
            <button onclick="togglepopup('filter_views')" class="viewbutton">@lang('additional.pages.category.view')</button>
            <div class="filter_views" id="filter_views">
                <div class="w-100 close_button_area">
                    <span onclick="togglepopup('filter_views')"><i class="fa fa-times"></i></span>
                </div>
                <div class="filter_view asc"
                    onclick="change_filter('asc','datas','{{ app()->getLocale() }}','services')">
                    <i class="fa fa-sort-alpha-down"></i>
                    @lang('additional.pages.category.atozalphabetic')
                </div>
                <div class="filter_view desc"
                    onclick="change_filter('desc','datas','{{ app()->getLocale() }}','services')">
                    <i class="fa fa-sort-alpha-up"></i>
                    @lang('additional.pages.category.ztoalphabetic')
                </div>
                <div class="filter_view random"
                    onclick="change_filter('random','datas','{{ app()->getLocale() }}','services')">
                    <i class="fa fa-random"></i>
                    @lang('additional.pages.category.random')
                </div>
                <div class="filter_view priceasc"
                    onclick="change_filter('priceasc','datas','{{ app()->getLocale() }}','services')">
                    <i class="fa fa-sort-amount-down-alt"></i>
                    @lang('additional.pages.category.forpriceasc')
                </div>
                <div class="filter_view pricedesc"
                    onclick="change_filter('pricedesc','datas','{{ app()->getLocale() }}','services')">
                    <i class="fa fa-sort-amount-up-alt"></i>
                    @lang('additional.pages.category.forpricedesc')
                </div>
            </div>
        </div>
    @endif
</div>
