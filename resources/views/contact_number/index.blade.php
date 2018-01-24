@extends('layouts.app')

@section('title', 'Nummern verwalten')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Nummern verwalten</h5>
                    </div>
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
@endsection
