@extends('layouts.app')

@section('title', 'E-Mail Adressen verwalten')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>E-Mail Adressen verwalten</h5>
                    </div>
                    <p>
                        <strong>Kontakt E-Mail Adressen: </strong><br>
                        <a href="{{ route('contact_emails.create', [$contact->slug]) }}">E-Mail
                            Adresse hinzufügen</a>
                    </p>


                    @include('partials.contact_email.index')


                </div>
            </div>
        </div>
    </div>
@endsection
