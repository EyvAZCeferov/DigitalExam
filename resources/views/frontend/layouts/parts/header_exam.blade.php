<header id="site_footer">
    @if (!empty(settings()) && isset(settings()->logo) && !empty(settings()->logo))
        <div class="logo">
            <a href="{{ route('page.welcome') }}">
                <img src="{{ getImageUrl(settings()->logo, 'settings') }}"
                    alt="{{ settings()->name[app()->getLocale() . '_name'] }}" />
            </a>
        </div>
    @endif
</header>
