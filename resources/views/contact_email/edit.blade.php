@extends('layouts.app')

@section('title', 'E-Mail Adresse bearbeiten')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        E-Mail Adresse bearbeiten
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_emails.update', [$contact->slug, $contactEmail->slug]) }}">
                            {{ method_field('PUT') }}
                            @include('partials.contact_email.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
