@extends('layouts.app')

@section('title')
    {{ $contact->fullname }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Kontakt Detailansicht
                    </div>
                    <div class="card-body">
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

        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('partials.comment.index')

            </div>
        </div>
    </div>
@endsection
