@extends('layouts.app')

@section('title', 'E-Mail Adresse hinzufügen')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>E-Mail Adresse hinzufügen</h5>
                    </div>

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_emails.store', [$contact->slug]) }}">
                        @include('partials.contact_email.edit')
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
