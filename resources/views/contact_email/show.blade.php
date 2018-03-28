@extends('layouts.app')

@section('title')
    E-Mail Adresse: {{ $contactEmail->name  }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        E-Mail Detailansicht
                    </div>
                    <div class="card-body">

                        <p>
                            <a href="{{ route('contact_emails.index', [$contact->slug]) }}">
                                Zurück zu den E-Mail Adressen</a>
                        </p>

                        @if (Auth::user()->hasPermissionTo('edit emails'))
                            <p>
                                <a href="{{ route('contact_emails.edit', [$contact->slug, $contactEmail->slug]) }}">Bearbeiten</a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('delete emails'))
                            <p>
                                <a href="{{ route('contact_emails.delete', [$contact->slug, $contactEmail->slug]) }}">Löschen</a>
                            </p>
                        @endif

                        @include('partials.contact_email.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
