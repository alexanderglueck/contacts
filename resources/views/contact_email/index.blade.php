@extends('layouts.app')

@section('title', 'E-Mail Adressen verwalten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>E-Mail Adressen verwalten</h5>
                    </div>

                    <div class="ibox-content">

                        <p><strong>Kontakt E-Mail Adressen: </strong><br>
                            <a href="{{ route('contact_emails.create', [$contact->slug]) }}">E-Mail Adresse hinzufügen</a>
                        </p>


                        @include('partials.contact_email.index')


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
