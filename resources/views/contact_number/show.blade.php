@extends('layouts.app')

@section('title')
    Nummer: {{ $contactNumber->name  }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Nummer Detailansicht
                    </div>
                    <div class="card-body">
                        <p>
                            <a href="{{ route('contact_numbers.edit', [$contact->slug, $contactNumber->slug]) }}">Bearbeiten</a>
                        </p>
                        <p>
                            <a href="{{ route('contact_numbers.delete', [$contact->slug, $contactNumber->slug]) }}">Löschen</a>
                        </p>

                        @include('partials.contact_number.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
