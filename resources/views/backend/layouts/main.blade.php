<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Globalmart Group">
    <title>Admin</title>

    <link rel="icon" type="image/svg+xml" href="">
    <link rel="alternate icon" href="">
    <link rel="mask-icon" href="">

    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">

    <link href="{{ asset('backend/assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/style.css') }}" rel="stylesheet">

    <link href="{{ asset('backend/assets/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">


    <link href="{{ asset('backend/assets/css/plugins/switchery/switchery.css') }}" rel="stylesheet">

    <link href="{{ asset('backend/assets/css/plugins/ionRangeSlider/ion.rangeSlider.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet">

    <link href="{{ asset('backend/assets/css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/plugins/dualListbox/bootstrap-duallistbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/cropper.css') }}" rel="stylesheet">
    @stack('css')

    <style>
        .dataTables_wrapper select.form-control:not([size]):not([multiple]) {
            height: 31px;
        }

        .dataTables_wrapper .dataTables_length {
            margin-right: 10px;
        }

        .dataTables_wrapper {
            padding: 0;
        }
        .ui-tooltip {
            display: none!important;
        }
        .list-group .list-group-item.active, .list-group .list-group-item:hover {
            background: #f3f3f4;
            border-color: #e7eaec;
        }

        .list-group .list-group-item a.nav-link {
            color: #676a6c;
            padding-left: 0;
        }
        .list-group .list-group-item a.label-danger {
            color: #FFFFFF;
        }
        .list-group .list-group-item a.label-danger:hover {
            background-color: #ec4758;
            border-radius: 0.25em;
        }
    </style>

</head>

<body class="fixed-sidebar no-skin-config pace-done @yield('body_class')">
<div id="wrapper">

    @include('backend.parts.aside')
    <div id="page-wrapper" class="gray-bg">

        @include('backend.parts.header')

        @yield('content')


        @include('backend.parts.footer')
    </div>
</div>

<!-- Mainly scripts -->
<script src="{{ asset('backend/assets/js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/popper.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/bootstrap.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/toastr/toastr.min.js') }}"></script>

<!-- Flot -->
<script src="{{ asset('backend/assets/js/plugins/flot/jquery.flot.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/flot/jquery.flot.spline.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/flot/jquery.flot.resize.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/flot/jquery.flot.symbol.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/flot/jquery.flot.time.js') }}"></script>

<!-- Peity -->
<script src="{{ asset('backend/assets/js/plugins/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/demo/peity-demo.js') }}"></script>

<!-- Custom and plugin javascript -->
<script src="{{ asset('backend/assets/js/inspinia.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/pace/pace.min.js') }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('backend/assets/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- Jvectormap -->
<script src="{{ asset('backend/assets/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>

<!-- EayPIE -->
<script src="{{ asset('backend/assets/js/plugins/easypiechart/jquery.easypiechart.js') }}"></script>

<!-- Sparkline -->
<script src="{{ asset('backend/assets/js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Sparkline demo data  -->
<script src="{{ asset('backend/assets/js/demo/sparkline-demo.js') }}"></script>

<script src="{{ asset('backend/assets/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('backend/assets/js/plugins/summernote/summernote-bs4.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>

<script src="{{ asset('backend/assets/js/plugins/switchery/switchery.js') }}"></script>

<script src="{{ asset('backend/assets/js/plugins/ionRangeSlider/ion.rangeSlider.min.js') }}"></script>

<script src="{{ asset('backend/assets/js/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/dualListbox/jquery.bootstrap-duallistbox.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/jasny/jasny-bootstrap.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugins/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('backend/assets/js/cropper.js') }}"></script>
<script src="{{ asset('backend/assets/js/eyvaz.js') }}"></script>

@stack('js')

<script>
    toastr.options = {
        "closeButton": true,
        "debug": {{ app()->isProduction() ?:'true' }},
        "newestOnTop": true,
        "progressBar": true,
        // "positionClass": "toastr-top-right",
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

    $(document).ready(function () {
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

        $('.summernote').summernote({
            toolbar: [
                // [groupName, [list of button]]
                ['font', ['fontname', 'fontsize', 'color', 'bold', 'italic', 'underline', 'clear', 'strikethrough', 'superscript', 'subscript']],
                ['insert', ['picture', 'link', 'video', 'table', 'hr']],
                ['para', ['ul', 'ol', 'paragraph', 'height', 'style']],
                ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
                ['view', ['fullscreen', 'codeview', 'undo', 'redo', 'help']]
            ],
            fontSizes: ['6', '7', '8', '9', '10', '11', '12', '13', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', '40', '42', '44', '46', '48', '50'],
            lineHeights: ['0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '0.9', '1.0', '1.1', '1.2', '1.4', '1.5', '2.0', '3.0', '3.5', '4.0', '4.5', '5.0', '5.5', '6.0', '6.5']
        });


        $('.input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: true,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd"
        });

        $('.dataTables-base').DataTable({
            destroy: true,
            "scrollCollapse": true,
            'responsive': true,
            aLengthMenu: [
                [10, 25, 50, 100, 200, -1],
                [10, 25, 50, 100, 200, "All"]
            ],
            order: [[$(this).attr('data-order'), 'desc']],
            iDisplayLength: 10,
            "dom": "<'html5buttons'B><'header__row' lf>Tg <'table-responsive' t> <'paginate__row' ip>",
            buttons: [
                // {extend: 'csv'},
                // {extend: 'excel', title: 'ExampleFile'},
                // {extend: 'pdf', title: 'ExampleFile'},

                // {extend: 'print',
                //     customize: function (win){
                //         $(win.document.body).addClass('white-bg');
                //         $(win.document.body).css('font-size', '10px');
                //
                //         $(win.document.body).find('table')
                //             .addClass('compact')
                //             .css('font-size', 'inherit');
                //     }
                // }
            ]

        });

        $('.dual_select').bootstrapDualListbox({
            selectorMinimalHeight: 160
        });

        flatpickr(".date-range-picker", {
            dateFormat: "Y-m-d",
            mode: "range",
            allowInput: true,
            locale: {
                firstDayOfWeek: 1
            }
        });
    });
</script>

</body>
</html>
