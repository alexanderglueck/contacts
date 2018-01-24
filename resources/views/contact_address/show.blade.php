@extends('layouts.app')

@section('title')
    Adresse:  {{ $contactAddress->name }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Adresse Detailansicht</h5>
                    </div>

                    <p>
                        <a href="{{ route('contact_addresses.edit', [$contact->slug, $contactAddress->slug]) }}">Bearbeiten</a>
                    </p>
                    <p>
                        <a href="{{ route('contact_addresses.delete', [$contact->slug, $contactAddress->slug]) }}">Löschen</a>
                    </p>

                    @include('partials.contact_address.show')
                </div>
            </div>
        </div>
    </div>
@endsection
