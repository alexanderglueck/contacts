@extends('layouts.app')

@section('title', 'Datumsangaben verwalten')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Datumsangaben verwalten
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Kontakt Datumsangaben: </strong>
                            @if (Auth::user()->hasPermissionTo('create dates'))
                                <br>
                                <a href="{{ route('contact_dates.create', [$contact->slug]) }}">Datum
                                    hinzufügen</a>
                            @endif
                        </p>

                        @include('partials.contact_date.index')

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
