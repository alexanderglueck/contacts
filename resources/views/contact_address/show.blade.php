@extends('layouts.app')

@section('title')
    Adresse:  {{ $contactAddress->name }}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Adresse Detailansicht</h5>
                    </div>

                    <div class="ibox-content">
                        <p><a href="{{ route('contact_addresses.edit', [$contact->slug, $contactAddress->slug]) }}">Bearbeiten</a></p>
                        <p><a href="{{ route('contact_addresses.delete', [$contact->slug, $contactAddress->slug]) }}">Löschen</a></p>

                        @include('partials.contact_address.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
