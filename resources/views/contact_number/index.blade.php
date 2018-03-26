@extends('layouts.app')

@section('title', 'Nummern verwalten')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Nummern verwalten
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Kontakt Nummern: </strong><br>
                            <a href="{{ route('contact_numbers.create', [$contact->slug]) }}">Nummer
                                hinzufügen</a>
                        </p>

                        @include('partials.contact_number.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
