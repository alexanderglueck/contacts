@extends('layouts.app')

@section('title')
    Datum: {{ $contactDate->name  }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Datum Detailansicht
                    </div>
                    <div class="card-body">
                        <p>
                            <a href="{{ route('contact_dates.edit', [$contact->slug, $contactDate->slug]) }}">Bearbeiten</a>
                        </p>
                        <p>
                            <a href="{{ route('contact_dates.delete', [$contact->slug, $contactDate->slug]) }}">Löschen</a>
                        </p>

                        @include('partials.contact_date.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
