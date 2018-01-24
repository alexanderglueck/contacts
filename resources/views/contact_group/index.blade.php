@extends('layouts.app')

@section('title', 'Kontaktgruppen verwalten')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Kontaktgruppen verwalten</h5>
                    </div>
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
@endsection
