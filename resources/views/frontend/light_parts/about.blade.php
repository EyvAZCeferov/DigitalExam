
@php($data=$page??standartpages("about",'type'))
@if (!empty($data) && !empty($data->name))
    @include('frontend.light_parts.section_title',['title'=>$data->name[app()->getLocale().'_name']])
    <section class="row about">
        <div class="col-sm-12 @if(count($data->images)>0) col-md-6 @else col-md-12 @endif column">
            <p class="text">{!! $data->description[app()->getLocale() . '_description'] !!}</p>
        </div>
        @if (!empty($data) && !empty($data->images) && count($data->images)>0)
            <div class="col-sm-12 col-md-6 column imagecolumn">
                @foreach ($data->images as $key => $value)
                    <img src="{{ getImageUrl($value, 'standartpages') }}" alt="{{ $value }}"
                        class="img-fluid img-responsive">
                @endforeach
            </div>
        @endif
    </section>
@endif
