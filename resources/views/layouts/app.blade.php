<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@if(View::hasSection('title'))
            @yield('title') - {{ config('app.name', 'Laravel') }}
        @else
            {{ config('app.name', 'Laravel') }}
        @endif</title>

    <!-- Styles -->
    <link href="{{ asset('css/vendor.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">


    @yield('css')
</head>
<body class="">
    <div id="wrapper">

        <!-- Navigation -->
        @include('partials.layout.navigation')


        <!-- Page wraper -->
        <div id="page-wrapper" class="gray-bg">

            <!-- Page wrapper -->
            @include('partials.layout.topnav')

            <!-- Content header -->
            @include('partials.layout.headline')

            <!-- Main view  -->
            @yield('content')

            <!-- Footer -->
            @include('partials.layout.footer')


        </div>

    </div>
    <!-- Scripts -->

    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    @yield('js-links')

    @yield('js')
</body>
</html>
