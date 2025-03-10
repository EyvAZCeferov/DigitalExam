<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="index, follow">
    <meta name="apple-mobile-web-app-status-bar-style" content="#6E91F3">
    <meta name="msapplication-navbutton-color" content="#6E91F3">
    <meta name="theme-color" content="#6E91F3">
    <title>@yield('title', settings('name'))</title>
    <meta name="description" content="{{ settings('description') }}">
    <meta property="og:site_name" content="{{ settings('name') }}" />
    <meta property="og:title" content="@yield('title', settings('name'))" />
    <meta property="og:description" content="@yield('description', settings('description'))" />
    <meta property="og:locale" content="{{ app()->getLocale() }}_{{ strtoupper(app()->getLocale()) }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image:url" content="@yield('image', settings('logo'))" />
    <meta property="og:image:secure_url" content="@yield('image', settings('logo'))" />
    <meta property="og:image:alt" content="@yield('title', settings('logo'))" />
    <meta property="og:type" content="website" />
    <meta name="robots" content="index,follow">
    <meta name="googlebot" content="index,follow">
    <meta name="generator" content="Globalmart Group MMC -  Development">
    <meta name="author" content="Globalmart Group MMC -  Development">
    <link rel="canonical" href="{{ route('page.welcome') }}">
    <link rel="shortlink" href="{{ route('page.welcome') }}">
    <meta name='csrf-token' content="{{ csrf_token() }}">
    <meta name='_token' content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('front/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/js/eyvaz/vendor/jquery-ui/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/js/eyvaz/vendor/jquery-ui/jquery-ui.theme.min.css') }}">
    @include('frontend.layouts.parts.favicon')
    @stack('css')

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css"
        integrity="sha384-n8MVd4RsNIU0tAv4ct0nTaAbDJwPJzDEaqSD1odI+WdtXRGWt2kTvGFasHpSy3SV" crossorigin="anonymous">
    <script language='javascript' type='text/javascript'>
        function DisableBackButton() {
            window.history.forward()
        }
        DisableBackButton();
        window.onload = DisableBackButton;
        window.onpageshow = function(evt) {
            if (evt.persisted) DisableBackButton()
        }
        window.onunload = function() {
            void(0)
        }
    </script>
   
</head>

<body class="pageonmobile_exam_page">
    <div class="container-xxl">
        @yield('content')
    </div>

    <div id="loader">
        <div class="icon">
            <div class="bar" style="background-color: #3498db; margin-left: -60px;"></div>
            <div class="bar" style="background-color: #e74c3c; margin-left: -20px;"></div>
            <div class="bar" style="background-color: #f1c40f; margin-left: 20px;"></div>
            <div class="bar" style="background-color: #2eB869; margin-left: 60px;"></div>
        </div>
    </div>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script type="text/javascript" defer src="{{ asset('front/assets/js/eyvaz/vendor/jquery-ui/jquery-ui.min.js') }}">
    </script>
    <script async defer src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="{{ asset('front/assets/js/eyvaz/base.js?v='.time()) }}"></script>
    {{-- Katex --}}
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"
        integrity="sha384-XjKyOOlGwcjNTAIQHIpgOno0Hl1YQqzUOEleOLALmuqehneUG+vnGctmUb0ZY0l8" crossorigin="anonymous">
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"
        integrity="sha384-+VBxd3r6XgURycqtZ117nYw44OOcIax56Z4dCRWbxyPt0Koah1uHoK0o4+/RRE05" crossorigin="anonymous"
        onload="renderMathInElement(document.body);"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/webfontloader@1.6.28/webfontloader.js"
        integrity="sha256-4O4pS1SH31ZqrSO2A/2QJTVjTPqVe+jnYgOWUVr7EEc=" crossorigin="anonymous"></script>
    {{-- Katex --}}

    <script async crossorigin defer>
        $(function() {
            @if (session()->has('message'))
                toast("{{ session('message') }}", 'info');
            @endif

            @if (session()->has('error'))
                toast("{{ session('error') }}", 'error');
            @endif

            @if (session()->has('info'))
                toast("{{ session('info') }}", 'info');
            @endif

            @if (session()->has('warning'))
                toast("{{ session('warning') }}", 'warning');
            @endif
            @if (session()->has('success'))
                toast("{{ session('success') }}", 'success');
            @endif
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            window.WebFontConfig = {
                custom: {
                    families: ['KaTeX_AMS', 'KaTeX_Caligraphic:n4,n7', 'KaTeX_Fraktur:n4,n7',
                        'KaTeX_Main:n4,n7,i4,i7', 'KaTeX_Math:i4,i7', 'KaTeX_Script',
                        'KaTeX_SansSerif:n4,n7,i4', 'KaTeX_Size1', 'KaTeX_Size2', 'KaTeX_Size3',
                        'KaTeX_Size4', 'KaTeX_Typewriter'
                    ],
                },
            };

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.tagName === "IMG" && !node.hasAttribute("loading")) {
                            node.setAttribute("loading", "lazy");
                        }
                    });
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>
    @stack('js')
</body>

</html>
