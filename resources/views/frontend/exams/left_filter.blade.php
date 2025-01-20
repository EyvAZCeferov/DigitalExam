<div style="z-index:100">
    <div class="filter_area @if (isset($horizontal)) horizontal @endif" id="filter_area">
        <form id="filter_inputs">
            <input type="hidden" name="categories[]"
                @if (!empty($data) && !empty($data['categories']) && count($data['categories']) > 0) value="{{ implode(',', $data['categories']) }}" @endif>
            <input type="hidden" name="sections[]"
                @if (!empty($data) && !empty($data['sections']) && count($data['sections']) > 0) value="{{ implode(',', $data['sections']) }}" @endif>
            <input type="hidden" name="companies[]"
                @if (!empty($data) && !empty($data['companies']) && count($data['companies']) > 0) value="{{ implode(',', $data['companies']) }}" @endif>
            <input type="hidden" name="price" @if (!empty($data) && !empty($data['price'])) value="{{ $data['price'] }}" @endif>
        </form>

        <button class="btn btn-danger filter_toggler" onclick="togglefilterelements('filter_elements')"> <i
                class="fa fa-filter"></i> @lang('additional.buttons.filter')</button>

        <div class="filter_elements active">
            <div class="select_box categories @if (!empty($data) && !empty($data['categories']) && count($data['categories']) > 0) active @endif ">
                <div class="heading" onclick="toggle_filter_contents(event,'categories')">
                    <span class="prefix"><i class="fa fa-layer-group"></i></span>
                    <span class="name">@lang('additional.footer.categories')</span>
                    <span class="suffix"><i class="fa fa-chevron-down"></i></span>
                </div>
                <div class="content">
                    @php
                        $categorieIds = !empty($data) && !empty($data['categories']) ? (is_array($data['categories']) ? implode(',', $data['categories']) : $data['categories']) : '';
                        $categorieIds = !empty($categorieIds) ? explode(',', $categorieIds) : [];
                    @endphp
                    @foreach (categories(null, 'exammedcats') as $category)
                        <div class="content-element {{ $category->id }}  @if (!empty($data) && !empty($categorieIds) && count($categorieIds) > 0 && in_array($category->id, $categorieIds)) active @endif "
                            onclick="setnewparametrandsearch('categories','select',{{ $category->id }})">
                            @if (isset($category->icon) && !empty($category->icon))
                                &nbsp; {!! $category->icon !!}
                            @endif
                            <span>{{ @$category->name[app()->getLocale() . '_name'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- <div class="select_box sections @if (!empty($data) && !empty($data['sections']) && count($data['sections']) > 0) active @endif ">
                <div class="heading" onclick="toggle_filter_contents(event,'sections')">
                    <span class="prefix"><i class="fa fa-plus-minus"></i></span>
                    <span class="name">@lang('additional.pages.exams.sections')</span>
                    <span class="suffix"><i class="fa fa-chevron-down"></i></span>
                </div>

                <div class="content">
                    @php
                        $brandIds = !empty($data) && !empty($data['sections']) ? (is_array($data['sections']) ? implode(',', $data['sections']) : $data['sections']) : '';
                        $brandIds = !empty($brandIds) ? explode(',', $brandIds) : [];
                    @endphp
                    @foreach (sections(null, 'exammed') as $brand)
                        <div class="content-element {{ $brand->id }}  @if (!empty($data) && !empty($brandIds) && count($brandIds) > 0 && in_array($brand->id, $brandIds)) active @endif "
                            onclick="setnewparametrandsearch('sections','select',{{ $brand->id }})">
                            <span>{{ @$brand->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div> --}}
            <div class="select_box companies @if (!empty($data) && !empty($data['companies']) && count($data['companies']) > 0) active @endif ">
                <div class="heading" onclick="toggle_filter_contents(event,'companies')">
                    <span class="prefix"><i class="fa fa-building"></i></span>
                    <span class="name">@lang('additional.pages.exams.companies')</span>
                    <span class="suffix"><i class="fa fa-chevron-down"></i></span>
                </div>

                <div class="content">
                    @php
                        $brandIds = !empty($data) && !empty($data['companies']) ? (is_array($data['companies']) ? implode(',', $data['companies']) : $data['companies']) : '';
                        $brandIds = !empty($brandIds) ? explode(',', $brandIds) : [];
                    @endphp
                    @foreach (users(null, 'company') as $brand)
                        <div class="content-element {{ $brand->id }}  @if (!empty($data) && !empty($brandIds) && count($brandIds) > 0 && in_array($brand->id, $brandIds)) active @endif "
                            onclick="setnewparametrandsearch('sections','select',{{ $brand->id }})">
                            @if (isset($brand->picture) && !empty($brand->picture))
                                <span class="brand_image"
                                    style="background-image: url({{ getImageUrl($brand->picture, 'users') }});"></span>
                            @endif
                            <span>{{ @$brand->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
