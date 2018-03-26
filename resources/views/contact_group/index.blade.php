@extends('layouts.app')

@section('title', 'Kontaktgruppen verwalten')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Kontaktgruppen verwalten
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Kontaktgruppen: </strong><br>
                            <a href="{{ route('contact_groups.create') }}">Kontaktgruppe
                                hinzufügen</a>
                        </p>

                        @include('partials.contact_group.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
