@extends('layouts.app')

@section('title')
    Nummer: {{ $contactNumber->name  }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Nummer Detailansicht</h5>
                    </div>
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
@endsection
