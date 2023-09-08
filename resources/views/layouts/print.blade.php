<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>@yield('title', 'Print')</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" />
    @stack('styles')
    @yield('styles')
</head>

<body class="app-print">
    <div class="print-header">
        @section('header')
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <img src="{{ asset('images/amplop.png') }}" class="print-logo" style="max-width: 100%; height: auto"/>
                </div>
            </div>
            <div class="row  mt-3">
                <div class="col-12">
                    @yield('header.center')
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-auto">
                    @yield('header.left')
                </div>
                <div class="col-auto align-self-start">
                    @yield('header.right')
                </div>
            </div>
        @show
    </div>

    <div class="print-content my-3 py-3">
        @yield('content')
    </div>

    <div class="print-footer">
        @yield('footer')
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

    @if (!request('preview'))
        <script>
        (function($) {
            $(window).on('load', function() {
                window.print();
            });
        })(jQuery);
        </script>
    @endif

    @stack('scripts')
    @yield('scripts')
</body>
</html>
