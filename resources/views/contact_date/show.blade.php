@extends('layouts.app')

@section('title')
    Datum: {{ $contactDate->name  }}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Datum Detailansicht</h5>
                    </div>

                    <div class="ibox-content">
                        <p><a href="{{ route('contact_dates.edit', [$contact->slug, $contactDate->slug]) }}">Bearbeiten</a></p>
                        <p><a href="{{ route('contact_dates.delete', [$contact->slug, $contactDate->slug]) }}">Löschen</a></p>

                        @include('partials.contact_date.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
