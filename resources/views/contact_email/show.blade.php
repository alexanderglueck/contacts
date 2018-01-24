@extends('layouts.app')

@section('title')
    E-Mail Adresse: {{ $contactEmail->name  }}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>E-Mail Detailansicht</h5>
                    </div>

                    <div class="ibox-content">
                        <p><a href="{{ route('contact_emails.index', [$contact->slug]) }}">Zurück zu den E-Mail Adressen</a></p>
                        <p><a href="{{ route('contact_emails.edit', [$contact->slug, $contactEmail->slug]) }}">Bearbeiten</a></p>
                        <p><a href="{{ route('contact_emails.delete', [$contact->slug, $contactEmail->slug]) }}">Löschen</a></p>

                        @include('partials.contact_email.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
