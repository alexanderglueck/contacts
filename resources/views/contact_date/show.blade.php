@extends('layouts.app')

@section('title')
    Datum: {{ $contactDate->name  }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Datum Detailansicht</h5>
                    </div>
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
@endsection
