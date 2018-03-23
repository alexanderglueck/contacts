@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Dashboard
                    </div>

                    <div class="card-body">
                        <p>
                            <strong>Kontakte: </strong><br>
                            <a href="{{ route('contacts.index') }}">
                                Kontakte verwalten
                            </a>
                        </p>

                        <p>
                            <strong>Kontaktgruppen: </strong><br>
                            <a href="{{ route('contact_groups.index') }}">
                                Kontaktgruppen verwalten
                            </a>
                        </p>

                        <p>
                            <strong>{{ trans('ui.calendar') }}: </strong><br>
                            <a href="{{ route('calendar.index') }}">
                                {{ trans('ui.view_calendar') }}
                            </a>
                        </p>

                        <p>
                            <strong>Kontaktlandkarte: </strong><br>
                            <a href="{{ route('map.index') }}">
                                Kontaktlandkarte ansehen
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
