<nav class="navbar navbar-expand-md  navbar-light navbar-laravel">
    <a class="navbar-brand" href="{{ route('home') }}">
        {{ optional(request()->tenant())->name ?:  config('app.name', 'Contacts') }}
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
            @subscribed
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

            <li class="nav-item">
                <form action="{{ route('search.search') }}" method="post">
                    @csrf
                    <input type="search" name="search" class="form-control" placeholder="Search">
                </form>
            </li>

            @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('plans.index') }}">
                        Plans
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('news.index') }}">
                        {{ trans('ui.news') }}
                    </a>
                </li>
            @endsubscribed
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
                @impersonating
                <li class="nav-item ">
                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('impersonation-form').submit();">Stop
                        impersonating</a>
                </li>
                <form id="impersonation-form" action="{{ route('user.impersonate') }}" method="post">
                    @csrf
                    {{ method_field('delete') }}
                </form>

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
                                        @if ($team->created)
                                            <a class="dropdown-item" href="{{ route('teams.switch', $team->id) }}">
                                                {{ $team->name }}
                                            </a>
                                        @else
                                            <a class="dropdown-item disabled" href="#">
                                                {{ $team->name }}
                                            </a>
                                        @endif
                                    @endif
                                @endforeach

                            </div>
                        </li>
                    @endif
                    @endimpersonating

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

                            <a class="dropdown-item" href="{{ route('user_settings.profile.show') }}">
                                {{ trans('ui.settings') }}
                            </a>

                            <a class="dropdown-item" href="{{ route('logs.index') }}">
                                {{ trans('ui.logs') }}
                            </a>

                            @hasteams
                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{ route('teams.index') }}">
                                    {{ trans('ui.teams') }}
                                </a>

                                @if (Auth::user()->hasPermissionTo('view roles'))
                                    <a class="dropdown-item" href="{{ route('roles.index') }}">
                                        {{ trans('ui.roles') }}
                                    </a>
                                @endif
                            @endhasteams

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
