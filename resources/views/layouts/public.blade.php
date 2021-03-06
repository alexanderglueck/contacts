<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @if(View::hasSection('title'))
            @yield('title') - {{ config('app.name', 'Contacts') }}
        @else
            {{ config('app.name', 'Contacts') }}
        @endif
    </title>

    <!-- Styles -->
    <link href="{{ asset('css/vendor.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @yield('css')
</head>

<body class="gray-bg">
<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow" id="app">
    <h5 class="my-0 font-weight-normal">{{ config('app.name') }}</h5>
    <nav class="my-2 my-md-0 mr-md-3 mr-md-auto ">
        <a class="p-2 text-dark" href="{{ route('plans.index') }}">
            {{ trans('ui.plans.title') }}
        </a>
        <a class="p-2 text-dark" href="{{ route('news.index') }}">
            {{ trans('ui.news') }}
        </a>
    </nav>
    <nav class="my-2 my-md-0 mr-md-3">
        @if (Auth::guest())
            <a class="p-2 text-dark" href="{{ route('login') }}">
                Sign in
            </a>
            @if(config('contacts.signup_enabled'))
                <a class="btn btn-outline-primary" href="{{ route('register') }}">
                    Sign up
                </a>
            @endif
        @else
            <a class="p-2 text-dark" href="{{ route('home') }}">
                {{ auth()->user()->name }}
            </a>
        @endif
    </nav>

</div>

@include('partials.layout.alert')

@yield('content')

<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
@yield('js-links')

@yield('js')
</body>
</html>

