<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @if(View::hasSection('title'))
            @yield('title') - {{ optional(request()->tenant())->name ?: config('app.name', 'Contacts') }}
        @else
            {{ optional(request()->tenant())->name ?: config('app.name', 'Contacts') }}
        @endif
    </title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('css')
</head>
<body>
<div id="app">

    @include('layouts.partials.navigation')

    <main class="py-4">
        <!-- Content header -->
        @include('partials.layout.alert')
        {{--@include('partials.layout.headline')--}}


        @yield('content')
    </main>
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
@yield('js-links')

@yield('js')

</body>
</html>

