@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Dashboard</h5>
                    </div>

                    <div class="ibox-content">
                        <p><strong>Kontakte: </strong><br>
                            <a href="{{ route('contacts.index') }}">Kontakte verwalten</a>
                        </p>

                        <p><strong>Kontaktgruppen: </strong><br>
                            <a href="{{ route('contact_groups.index') }}">Kontaktgruppen verwalten</a>
                        </p>

                        <p><strong>Kalender: </strong><br>
                            <a href="{{ route('calendar.index') }}">Kalender ansehen</a>
                        </p>

                        <p><strong>Kontaktlandkarte: </strong><br>
                            <a href="{{ route('map.index') }}">Kontaktlandkarte ansehen</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
