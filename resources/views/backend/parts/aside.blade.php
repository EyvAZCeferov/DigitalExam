<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold">{{ auth('admins')->user()->name }}</span>
                        <span class="text-muted text-xs block">{{ ucfirst(auth('admins')->user()->role->name) }} <b
                                class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}">Profil</a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.logout') }}">Çıxış</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    A
                </div>
            </li>
            <li class="{{ url()->current() == route('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i> <span class="nav-label">İdarə
                        Paneli</span></a>
            </li>

            @if (auth('admins')->user()->hasPermissionFor('category-list'))
                <li class="{{ url()->current() == route('categories.index') ? 'active' : '' }}">
                    <a href="{{ route('categories.index') }}"><i class="fa fa-archive"></i> <span
                            class="nav-label">Kateqoriyalar</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('exam-list'))
                <li class="{{ url()->current() == route('exams.index') ? 'active' : '' }}">
                    <a href="{{ route('exams.index') }}"><i class="fa fa-archive"></i> <span
                            class="nav-label">İmtahanlar</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('references'))
                <li class="{{ url()->current() == route('references.index') ? 'active' : '' }}">
                    <a href="{{ route('references.index') }}"><i class="fa fa-superscript"></i> <span
                            class="nav-label">Referanslar</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('exam_start_page'))
                <li class="{{ url()->current() == route('exam_start_page.index') ? 'active' : '' }}">
                    <a href="{{ route('exam_start_page.index') }}"><i class="fa fa-gears"></i> <span
                            class="nav-label">İmtahan giriş səhifəsi</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('coupon_codes'))
                <li class="{{ url()->current() == route('coupon_codes.index') ? 'active' : '' }}">
                    <a href="{{ route('coupon_codes.index') }}"><i class="fa fa-percent"></i> <span
                            class="nav-label">Kupon Kodlar</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('exam-analyze'))
                <li class="{{ url()->current() == route('exams.analyze') ? 'active' : '' }}">
                    <a href="{{ route('exams.analyze') }}"><i class="fa fa-filter"></i> <span class="nav-label">İmtahan
                            analizi</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('exam-result'))
                <li class="{{ url()->current() == route('exam.results') ? 'active' : '' }}">
                    <a href="{{ route('exam.results') }}"><i class="fa fa-archive"></i> <span class="nav-label">İmtahan
                            nəticələri</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('role-list'))
                <li class="{{ url()->current() == route('roles.index') ? 'active' : '' }}">
                    <a href="{{ route('roles.index') }}"><i class="fa fa-users"></i> <span
                            class="nav-label">Rollar</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('admin-list'))
                <li class="{{ url()->current() == route('managers.index') ? 'active' : '' }}">
                    <a href="{{ route('managers.index') }}"><i class="fa fa-users"></i> <span
                            class="nav-label">İdarəçilər</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('user-list'))
                <li class="{{ url()->current() == route('users.index') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}"><i class="fa fa-users"></i> <span
                            class="nav-label">İstifadəçilər</span></a>
                </li>
            @endif



            @if (auth('admins')->user()->hasPermissionFor('studentratings-list'))
                <li class="{{ url()->current() == route('studentratings.index') ? 'active' : '' }}">
                    <a href="{{ route('studentratings.index') }}"><i class="fa fa-star"></i> <span
                            class="nav-label">Tələbə dəyərləndirmələri</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('counters-list'))
                <li class="{{ url()->current() == route('counters.index') ? 'active' : '' }}">
                    <a href="{{ route('counters.index') }}"><i class="fa fa-calculator"></i> <span
                            class="nav-label">Hesabat rəqəmləri</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('sliders-list'))
                <li class="{{ url()->current() == route('sliders.index') ? 'active' : '' }}">
                    <a href="{{ route('sliders.index') }}"><i class="fa fa-photo"></i> <span
                            class="nav-label">Slayderlər</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('standartpages-list'))
                <li class="{{ url()->current() == route('standartpages.index') ? 'active' : '' }}">
                    <a href="{{ route('standartpages.index') }}"><i class="fa fa-file"></i> <span
                            class="nav-label">Standart Səhifələr</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('blogs-list'))
                <li class="{{ url()->current() == route('blogs.index') ? 'active' : '' }}">
                    <a href="{{ route('blogs.index') }}"><i class="fa fa-rss"></i> <span
                            class="nav-label">Bloqlar</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('teams-list'))
                <li class="{{ url()->current() == route('teams.index') ? 'active' : '' }}">
                    <a href="{{ route('teams.index') }}"><i class="fa fa-users"></i> <span
                            class="nav-label">Komanda</span></a>
                </li>
            @endif

            @if (auth('admins')->user()->hasPermissionFor('settings'))
                <li class="{{ url()->current() == route('settings.index') ? 'active' : '' }}">
                    <a href="{{ route('settings.index') }}"><i class="fa fa-cog"></i> <span
                            class="nav-label">Parametrlər</span></a>
                </li>
            @endif

        </ul>

    </div>
</nav>
