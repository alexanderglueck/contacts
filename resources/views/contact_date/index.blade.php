@extends('layouts.app')

@section('title', 'Datumsangaben verwalten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Datumsangaben verwalten</h5>
                    </div>

                    <div class="ibox-content">

                        <p><strong>Kontakt Datumsangaben: </strong><br>
                            <a href="{{ route('contact_dates.create', [$contact->slug]) }}">Datum hinzufügen</a>
                        </p>

                        @include('partials.contact_date.index')

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
