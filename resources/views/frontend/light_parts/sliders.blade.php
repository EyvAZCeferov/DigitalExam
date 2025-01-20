@if (!empty(sliders()))
    <section class="sliders slick_slider" data-slick-show="1" data-slick-scroll="1">
        @foreach (sliders() as $slider)
            <div class="slideritem" style="background-image:url({{ getImageUrl($slider->image, 'sliders') }})">
                <div class="slideritem_content">
                    @if (isset($slider->name[app()->getLocale() . '_name']) && !empty($slider->name[app()->getLocale() . '_name']))
                        <h4>
                            {{ $slider->name[app()->getLocale() . '_name'] }}</h4>
                    @endif
                    @if (isset($slider->description[app()->getLocale() . '_description']) &&
                            !empty($slider->description[app()->getLocale() . '_description']))
                        <div class="slider_description"> {!! $slider->description[app()->getLocale() . '_description'] !!}</div>
                    @endif
                    @if (isset($slider->url) && !empty($slider->url))
                        <a href="{{ $slider->url }}" class="btn btn-sm btn-secondary more_button">@lang('additional.buttons.more')
                            &nbsp; <i class="fa-solid fa-arrow-right"></i> </a>
                    {{-- @else
                        <div class="input-group mb-3 slider_search">
                            <div class="input-group-prepend">
                                <i class="fa fa-search"></i>
                            </div>
                            <input type="text" placeholder="@lang('additional.forms.searchtest')" class="form-control form-control-sm">
                            <div class="input-group-append">
                                <button type="button" onclick="searchnow(event)" class="btn btn-sm" >@lang('additional.forms.search')</button>
                            </div>
                        </div> --}}
                    @endif
                </div>
            </div>
        @endforeach
    </section>
@endif
