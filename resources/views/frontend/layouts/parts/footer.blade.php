<footer id="site_footer">
    <div class="bg_footer row">
        <div class="col-md-7 col-sm-12 col-lg-6 left_column">
            @if (!empty(settings()) && isset(settings()->logo_white) && !empty(settings()->logo_white))
                <div class="logo_footer">
                    <a href="{{ route('page.welcome') }}">
                        <img src="{{ settings('logo_white') }}"
                            alt="{{ settings("name") }}" />
                    </a>
                </div>
            @endif
            <div class="row social_network">
                <div class="col-sm-6 col-md-5">
                    <h3 class="title">@lang('additional.footer.networking')</h3>
                    <div class="sub_links"><a href="{{ session()->has("subdomain") ? route('exams_front.index.subdomain',['subdomain'=>session()->get("subdomain")]) : route('exams_front.index') }}">@lang('additional.pages.exams.exams')</a>
                        @foreach(standartpages() as $key => $value)
                            <a
                            href="{{ route('pages.show', $value->slugs[app()->getLocale() . '_slug']) }}">{{ $value->name[app()->getLocale() . '_name'] }}</a>
                        @endforeach
                    </div>
                    <div class="social_media_icon_and_links">
                        <ul>
                            @if (
                                !empty(settings()) &&
                                    isset(settings()->social_media) &&
                                    !empty(settings()->social_media) &&
                                    isset(settings()->social_media['twitter']) &&
                                    !empty(trim(settings()->social_media['twitter'])))
                                <li><a href="{{ settings()->social_media['twitter'] }}" target="_blank"><i
                                            class="fa-brands fa-twitter text-white"></i></a></li>
                            @endif

                            @if (
                                !empty(settings()) &&
                                    isset(settings()->social_media) &&
                                    !empty(settings()->social_media) &&
                                    isset(settings()->social_media['facebook']) &&
                                    !empty(trim(settings()->social_media['facebook'])))
                                <li><a href="{{ settings()->social_media['facebook'] }}" target="_blank"><i
                                            class="fa-brands fa-facebook-f"></i></a></li>
                            @endif

                            @if (
                                !empty(settings()) &&
                                    isset(settings()->social_media) &&
                                    !empty(settings()->social_media) &&
                                    isset(settings()->social_media['linkedin']) &&
                                    !empty(trim(settings()->social_media['linkedin'])))
                                <li><a href="{{ settings()->social_media['linkedin'] }}" target="_blank"><i
                                            class="fa-brands fa-linkedin-in"></i></a></li>
                            @endif

                            @if (
                                !empty(settings()) &&
                                    isset(settings()->social_media) &&
                                    !empty(settings()->social_media) &&
                                    isset(settings()->social_media['instagram']) &&
                                    !empty(trim(settings()->social_media['instagram'])))
                                <li><a href="{{ settings()->social_media['instagram'] }}" target="_blank"><i
                                            class="fa-brands fa-instagram"></i></a></li>
                            @endif

                            @if (
                                !empty(settings()) &&
                                    isset(settings()->social_media) &&
                                    !empty(settings()->social_media) &&
                                    isset(settings()->social_media['tiktok']) &&
                                    !empty(trim(settings()->social_media['tiktok'])))
                                <li><a href="{{ settings()->social_media['tiktok'] }}" target="_blank"><i
                                            class="fa-brands fa-tiktok"></i></a></li>
                            @endif

                            @if (
                                !empty(settings()) &&
                                    isset(settings()->social_media) &&
                                    !empty(settings()->social_media) &&
                                    isset(settings()->social_media['telegram']) &&
                                    !empty(trim(settings()->social_media['telegram'])))
                                <li><a href="{{ settings()->social_media['telegram'] }}" target="_blank"><i
                                            class="fa-brands fa-telegram"></i></a></li>
                            @endif

                            @if (
                                !empty(settings()) &&
                                    isset(settings()->social_media) &&
                                    !empty(settings()->social_media) &&
                                    isset(settings()->social_media['whatsapp']) &&
                                    !empty(trim(settings()->social_media['whatsapp'])))
                                <li><a href="https://wa.me/{{ settings()->social_media['whatsapp'] }}"
                                        target="_blank"><i class="fa-brands fa-whatsapp"></i></a></li>
                            @endif

                        </ul>
                    </div>
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
                <div class="col-md-1 col-sm-0"></div>
                <div class="col-sm-6 col-md-6">
                    <h3 class="title">@lang('additional.footer.contactus')</h3>
                    <div class="contact_us_links">
                        <ul>
                            @if (
                                !empty(settings()) &&
                                    isset(settings()->social_media) &&
                                    !empty(settings()->social_media) &&
                                    isset(settings()->social_media['phone']) &&
                                    !empty(trim(settings()->social_media['phone'])))
                                <li><a href="tel:{{ settings()->social_media['phone'] }}" target="_blank"
                                        class="text-white"><i class="fa fa-phone"></i> &nbsp;
                                        {{ settings()->social_media['phone'] }} </a></li>
                            @endif
                            @if (
                                !empty(settings()) &&
                                    isset(settings()->social_media) &&
                                    !empty(settings()->social_media) &&
                                    isset(settings()->social_media['email']) &&
                                    !empty(trim(settings()->social_media['email'])))
                                <li><a href="tel:{{ settings()->social_media['email'] }}" target="_blank"
                                        class="text-white"><i class="fa fa-envelope"></i> &nbsp;
                                        {{ settings()->social_media['email'] }} </a></li>
                            @endif
                            @if (
                                !empty(settings()) &&
                                    isset(settings()->address) &&
                                    !empty(settings()->address) &&
                                    isset(settings()->address[app()->getLocale() . '_address']) &&
                                    !empty(trim(settings()->address[app()->getLocale() . '_address'])))
                                <li><a href="tel:{{ settings()->address[app()->getLocale() . '_address'] }}"
                                        target="_blank" class="text-white"><i class="fa fa-map-pin"></i> &nbsp;
                                        {{ settings()->address[app()->getLocale() . '_address'] }} </a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @if (
            !empty(settings()) &&
                isset(settings()->social_media['maps_google']) &&
                !empty(settings()->social_media['maps_google']))
            <div class="col-md-5 col-sm-12 col-lg-6">
                <iframe src="{{ settings()->social_media['maps_google'] }}" frameborder="0"></iframe>
            </div>
        @endif

    </div>
    <div class="copyright">
        <p>Developed By <a href="https://globalmart.az">Globalmart Group</a></p>
    </div>
</footer>
