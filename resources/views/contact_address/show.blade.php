@extends('layouts.app')

@section('title')
    Adresse:  {{ $contactAddress->name }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Adresse Detailansicht
                    </div>
                    <div class="card-body">
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
    </div>
@endsection
