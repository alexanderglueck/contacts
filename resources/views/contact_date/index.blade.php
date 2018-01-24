@extends('layouts.app')

@section('title', 'Datumsangaben verwalten')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Datumsangaben verwalten</h5>
                    </div>
                    <p>
                        <strong>Kontakt Datumsangaben: </strong><br>
                        <a href="{{ route('contact_dates.create', [$contact->slug]) }}">Datum
                            hinzufügen</a>
                    </p>

                    @include('partials.contact_date.index')

                </div>
            </div>
        </div>
    </div>
@endsection
