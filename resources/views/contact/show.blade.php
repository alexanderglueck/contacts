@extends('layouts.app')

@section('title')
    {{ $contact->fullname }}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontakt Detailansicht</h5>
                    </div>

                    <div class="ibox-content">
                        <p><a href="{{ route('contacts.edit', [$contact->slug]) }}">Bearbeiten</a></p>
                        <p><a href="{{ route('contacts.delete', [$contact->slug]) }}">Löschen</a></p>
                        <p><a href="{{ route('contacts.image', [$contact->slug]) }}">Bild</a></p>

                        @include('partials.contact.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
