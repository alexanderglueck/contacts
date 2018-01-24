@extends('layouts.app')

@section('title')
    E-Mail Adresse: {{ $contactEmail->name  }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>E-Mail Detailansicht</h5>
                    </div>

                    <p>
                        <a href="{{ route('contact_emails.index', [$contact->slug]) }}">Zurück
                            zu den E-Mail Adressen</a></p>
                    <p>
                        <a href="{{ route('contact_emails.edit', [$contact->slug, $contactEmail->slug]) }}">Bearbeiten</a>
                    </p>
                    <p>
                        <a href="{{ route('contact_emails.delete', [$contact->slug, $contactEmail->slug]) }}">Löschen</a>
                    </p>

                    @include('partials.contact_email.show')
                </div>

            </div>
        </div>
    </div>
@endsection
