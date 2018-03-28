@extends('layouts.app')

@section('title', 'E-Mail Adressen verwalten')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        E-Mail Adressen verwalten
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Kontakt E-Mail Adressen: </strong>
                            @if (Auth::user()->hasPermissionTo('create emails'))
                                <br>
                                <a href="{{ route('contact_emails.create', [$contact->slug]) }}">E-Mail
                                    Adresse hinzufügen</a>
                            @endif
                        </p>
                        @include('partials.contact_email.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
