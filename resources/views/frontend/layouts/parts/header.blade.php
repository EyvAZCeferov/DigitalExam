<header id="site_footer">
    @if (!empty(settings()) && isset(settings()->logo) && !empty(settings()->logo))
        <div class="logo">
            <a href="{{ route('page.welcome') }}">
                <img src="{{ settings("logo") }}"
                    alt="{{ settings("name") }}" />
            </a>
        </div>
    @endif
    <div class="right_area">
        @if (
            (!empty(standartpages('about', 'type')) && !empty(standartpages('about', 'type')->name)) && !session()->has('subdomain'))
            <a class="header_link_item"
                href="{{ route('pages.show', standartpages('about', 'type')->slugs[app()->getLocale() . '_slug']) }}">{{ standartpages('about', 'type')->name[app()->getLocale() . '_name'] }}</a>
        @endif

        <a class="header_link_item"
        href="{{ route('exams_front.index') }}">@lang("additional.pages.exams.exams")</a>
        

        @if (auth('users')->check())
            <a href="{{ route('user.profile') }}" class="header_link_item">{{ auth('users')->user()->name }} &nbsp;
                @if (isset(auth('users')->user()->picture) && !empty(auth('users')->user()->picture))
                    @if(auth('users')->user()->user_type==1)
                        <div class="profile_picture">
                            <img src="{{ getImageUrl(auth('users')->user()->picture, 'users') }}"
                                alt="{{ auth('users')->user()->name }}" class="img-fluid img-responsive " />
                        </div>
                    @endif
                @else
                    <div class="profile_picture">
                        <img src="{{ asset('front/assets/img/bg_images/no_profile.webp') }}"
                            alt="{{ auth('users')->user()->name }}" class="img-fluid img-responsive profile_image" />
                    </div>
                @endif
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-primary login_button">@lang('additional.headers.login')</a>
        @endauth


        <div class="chosen_languages">
            <ul>
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <li class="@if ($localeCode == app()->getLocale()) active @endif"><a
                            href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            <img src="{{ asset('front/assets/img/flags/' . $localeCode . '.png') }}"
                                alt="{{ $localeCode }}">
                            <span class="text_name">{{ strtoupper($properties['name']) }}</span></a>
                    </li>
                @endforeach
            </ul>
        </div>
</div>
</header>
