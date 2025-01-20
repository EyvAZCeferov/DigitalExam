<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="icon" type="image/svg+xml" href="">
    <link rel="alternate icon" href="">
    <link rel="mask-icon" href="">

    <title>{{ env('APP_NAME') }} | Giriş</title>

    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ asset('backend/assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen animated fadeInDown d-flex align-items-center h-100">
    <div class="col">
        <h3>Xoş gəldiniz</h3>

        <form class="m-t" role="form" action="{{ route('admin.auth') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="E-posta" required="">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Şifre" required="">
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">Giriş</button>

        </form>

    </div>
</div>

<!-- Mainly scripts -->
<script src="{{ asset('backend/assets/js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/popper.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/bootstrap.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/toastr/toastr.min.js') }}"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": {{ app()->isProduction() ?:'true' }},
        "newestOnTop": true,
        "progressBar": true,
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "600",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "5000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $(document).ready(function(){
        @if(session()->has('modal'))
            $('#{{session()->get('modal')}}').modal('show')
        @endif

        @if(session()->has('error'))
            toastr.error("{{session()->get('error')}}", "Error");
        @endif

        @if(session()->has('success'))
            toastr.success("{{session()->get('success')}}", "Success");
        @endif

        @if(session()->has('warning'))
            toastr.warning("{{session()->get('warning')}}", "Warning");
        @endif

        @if(session()->has('info'))
            toastr.info("{{session()->get('info')}}", "Info");
        @endif

        @if (isset($errors) && count($errors) > 0)
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}", "Error");
            @endforeach
        @endif

    });
</script>
</body>

</html>
