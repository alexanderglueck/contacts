<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <span>
                        @if(strlen(Auth::user()->image)>0)
                            <img alt="image" class="img-circle" width="48px"
                                 height="48px"
                                 src="{{ url(Auth::user()->image) }}"/>
                        @endif

                     </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">{{ Auth::user()->name }}</strong>
                            </span> <span class="text-muted text-xs block">Administrator <b
                                        class="caret"></b></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="{{ route('user_settings.edit') }}">Einstellungen</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Abmelden
                            </a>
                            <form id="logout-form"
                                  action="{{ route('logout') }}" method="POST"
                                  style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </div>
                <div class="logo-element">
                    GDEV+
                </div>
            </li>
            <li class="{{ App\Helpers\Navigation::isActiveRoute('home') }}">
                <a href="{{ route('home') }}"><i class="fa fa-th-large"></i>
                    <span class="nav-label">Home</span></a>
            </li>
            <li class="{{ App\Helpers\Navigation::isActiveRoute(['contacts.index', 'contacts.create']) }}">
                <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Kontakte</span><span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse {{ App\Helpers\Navigation::isActiveRoute(['contacts.index', 'contacts.create', 'import.index', 'export.index'], 'in') }}">
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('contacts.index') }}">
                        <a href="{{ route('contacts.index') }}">Übersicht</a>
                    </li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('contacts.create') }}">
                        <a href="{{ route('contacts.create') }}">Kontakt
                            hinzufügen</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('import.index') }}">
                        <a href="{{ route('import.index') }}">Kontakte
                            importieren</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('export.index') }}">
                        <a href="{{ route('export.index') }}">Kontakte
                            exportieren</a></li>
                </ul>
            </li>

            <li class="{{ App\Helpers\Navigation::isActiveRoute(['contact_groups.index', 'contact_groups.show', 'contact_groups.create']) }}">
                <a href="#"><i class="fa fa-users"></i> <span class="nav-label">Kontaktgruppen</span><span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse {{ App\Helpers\Navigation::isActiveRoute(['contact_groups.index', 'contact_groups.show', 'contact_groups.create'], 'in') }}">
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('contact_groups.index') }}">
                        <a href="{{ route('contact_groups.index') }}">Übersicht</a>
                    </li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('contact_groups.create') }}">
                        <a href="{{ route('contact_groups.create') }}">Kontaktgruppe
                            hinzufügen</a></li>

                    @foreach(\App\Models\ContactGroup::sorted()->get() as $contactGroup)
                        <li class="{{ App\Helpers\Navigation::isActiveRoute('contact_groups.show', 'active', ["contactGroup" => $contactGroup->slug]) }}">
                            <a href="{{ route('contact_groups.show', [$contactGroup->slug]) }}">{{$contactGroup->name}}</a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li class="{{ App\Helpers\Navigation::isActiveRoute('calendar.index') }}">
                <a href="{{ route('calendar.index') }}"><i
                            class="fa fa-calendar-check-o"></i> <span
                            class="nav-label">Kalender</span></a>
            </li>

            <li class="{{ App\Helpers\Navigation::isActiveRoute('map.index') }}">
                <a href="{{ route('map.index') }}"><i
                            class="fa fa-map-marker"></i> <span
                            class="nav-label">Kontaktlandkarte</span></a>
            </li>

            <li class="{{ App\Helpers\Navigation::isActiveRoute(['reports.index', 'reports.inactive', 'reports.male', 'reports.female',
            'reports.wrong_male', 'reports.wrong_female', 'reports.no_email', 'reports.no_date', 'reports.no_address', 'reports.no_number', 'reports.no_url', 'reports.no_lat_lng',
            ]) }}">
                <a href="#"><i class="fa fa-pie-chart"></i> <span
                            class="nav-label">Berichte</span><span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse {{ App\Helpers\Navigation::isActiveRoute(['reports.index', 'reports.inactive', 'reports.male', 'reports.female',
                'reports.wrong_male', 'reports.wrong_female',
                'reports.no_email', 'reports.no_date',  'reports.no_address', 'reports.no_number', 'reports.no_url', 'reports.no_lat_lng',
                ], 'in') }}">
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.index') }}">
                        <a href="{{ route('reports.index') }}">Übersicht</a>
                    </li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.inactive') }}">
                        <a href="{{ route('reports.inactive') }}">Inaktive
                            Kontakte</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.male') }}">
                        <a href="{{ route('reports.male') }}">Männliche
                            Kontakte</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.female') }}">
                        <a href="{{ route('reports.female') }}">Weibliche
                            Kontakte</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.wrong_male') }}">
                        <a href="{{ route('reports.wrong_male') }}">Falsche
                            männliche Kontakte</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.wrong_female') }}">
                        <a href="{{ route('reports.wrong_female') }}">Falsche
                            weibliche Kontakte</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.no_email') }}">
                        <a href="{{ route('reports.no_email') }}">Keine E-Mail
                            Adresse</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.no_date') }}">
                        <a href="{{ route('reports.no_date') }}">Keine
                            Datumsangabe</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.no_address') }}">
                        <a href="{{ route('reports.no_address') }}">Keine
                            Adresse</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.no_number') }}">
                        <a href="{{ route('reports.no_number') }}">Keine
                            Nummer</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.no_url') }}">
                        <a href="{{ route('reports.no_url') }}">Keine
                            Website</a></li>
                    <li class="{{ App\Helpers\Navigation::isActiveRoute('reports.no_lat_lng') }}">
                        <a href="{{ route('reports.no_lat_lng') }}">Keine
                            Koordinaten</a></li>

                </ul>
            </li>

        </ul>
    </div>
</nav>
