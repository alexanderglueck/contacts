@extends('layouts.app')

@section('title')
    {{ $contact->fullname }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Kontakt Detailansicht</h5>
                    </div>
                    <p>
                        <a href="{{ route('contacts.edit', [$contact->slug]) }}">Bearbeiten</a>
                    </p>
                    <p>
                        <a href="{{ route('contacts.delete', [$contact->slug]) }}">Löschen</a>
                    </p>
                    <p>
                        <a href="{{ route('contacts.image', [$contact->slug]) }}">Bild</a>
                    </p>

                    @include('partials.contact.show')
                </div>

            </div>
        </div>
    </div>
@endsection
