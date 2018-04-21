<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@if(View::hasSection('title'))
            @yield('title') - {{ config('app.name', 'Contacts') }}
        @else
            {{ config('app.name', 'Contacts') }}
        @endif</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('css')
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md  navbar-light navbar-laravel">
        <a class="navbar-brand" href="{{ route('home') }}">
            {{ config('app.name', 'Contacts') }}
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#app-navbar-collapse"
                aria-controls="app-navbar-collapse"
                aria-expanded="false"
                aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @if (Auth::user()->hasAnyPermission([
                    'view contacts',
                    'create contacts',
                    'create import',
                    'create export'
                ]))
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle"
                       data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false"
                       id="contactsDropdown"
                    >
                        Contacts
                    </a>

                    <div class="dropdown-menu" aria-labelledby="contactsDropdown">
                        @if (Auth::user()->hasPermissionTo('view contacts'))
                        <a class="dropdown-item" href="{{ route('contacts.index') }}">
                            Contacts
                        </a>
                        @endif

                        @if (Auth::user()->hasPermissionTo('create contacts'))
                        <a class="dropdown-item" href="{{ route('contacts.create') }}">
                            Create contact
                        </a>
                        @endif

                        @if (Auth::user()->hasAnyPermission(['create import', 'create export']))
                        <div class="dropdown-divider"></div>
                        @endif

                        @if (Auth::user()->hasPermissionTo('create import'))
                        <a class="dropdown-item" href="{{ route('import.index') }}">
                            Import
                        </a>
                        @endif

                        @if (Auth::user()->hasPermissionTo('create export'))
                        <a class="dropdown-item" href="{{ route('export.index') }}">
                            Export
                        </a>
                        @endif

                        @if (Auth::user()->hasPermissionTo('view activities'))
                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="{{ route('activities.index') }}">
                                Activity log
                            </a>
                        @endif

                    </div>
                </li>
                @endif

                @if (Auth::user()->hasPermissionTo('view map'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('map.index') }}">
                        {{ trans('ui.map') }}
                    </a>
                </li>
                @endif

                @if (Auth::user()->hasPermissionTo('view calendar'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('calendar.index') }}">
                        {{ trans('ui.calendar') }}
                    </a>
                </li>
                @endif

                @if (Auth::user()->hasPermissionTo('view contactGroups'))
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle"
                       data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false"
                       id="contactGroupsDropdown"
                    >
                        Contact groups
                    </a>

                    <div class="dropdown-menu" aria-labelledby="contactGroupsDropdown">
                        <a class="dropdown-item" href="{{ route('contact_groups.index') }}">
                            Contact groups
                        </a>

                        <a class="dropdown-item" href="{{ route('contact_groups.create') }}">
                            Create contact group
                        </a>
                    </div>
                </li>
                @endif

                @if (Auth::user()->hasPermissionTo('view reports'))
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle"
                       data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false"
                       id="reportsDropdown"
                    >
                        Reports
                    </a>

                    <div class="dropdown-menu" aria-labelledby="reportsDropdown">
                        <a class="dropdown-item" href="{{ route('reports.inactive') }}">
                            Inactive
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.male') }}">
                            Male
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.female') }}">
                            Female
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.wrong_male') }}">
                            Wrong male
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.wrong_female') }}">
                            Wrong female
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.no_email') }}">
                            No email
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.no_date') }}">
                            No date
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.no_address') }}">
                            No address
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.no_number') }}">
                            No number
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.no_url') }}">
                            No website
                        </a>


                        <a class="dropdown-item" href="{{ route('reports.no_lat_lng') }}">
                            No coordinates
                        </a>
                    </div>
                </li>
                @endif
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li class="nav-item ">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @else
                    @if(count(Auth::user()->teams)> 1)
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle"
                               data-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false"
                               id="teamSwitchDropdown"
                            >
                                {{ trans('ui.switch_team') }}
                            </a>

                            <div class="dropdown-menu" aria-labelledby="teamSwitchDropdown">
                                @foreach(Auth::user()->teams as $team)
                                    @if($team->id != Auth::user()->currentTeam->id)
                                        <a class="dropdown-item" href="{{ route('teams.switch', $team->id) }}">
                                            {{ $team->name }}
                                        </a>
                                    @endif
                                @endforeach

                            </div>
                        </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false"
                           id="userDropdown"
                        >
                            @if(trim(Auth::user()->image) !== '')
                                <img class="rounded" width="20" height="20" src="{{ asset('storage/' . Auth::user()->image) }}">
                            @endif
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu" aria-labelledby="userDropdown">

                            <a class="dropdown-item" href="{{ route('user_settings.edit') }}">
                                Settings
                            </a>

                            <a class="dropdown-item" href="{{ route('logs.index') }}">
                                {{ trans('ui.logs') }}
                            </a>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="{{ route('teams.index') }}">
                                {{ trans('ui.teams') }}
                            </a>

                            @if (Auth::user()->hasPermissionTo('view roles'))
                            <a class="dropdown-item" href="{{ route('roles.index') }}">
                                {{ trans('ui.roles') }}
                            </a>
                            @endif

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>

                        </div>
                    </li>
                @endif
            </ul>
        </div>

    </nav>

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

