@extends('layouts.app')

@section('title', 'E-Mail Adresse löschen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        E-Mail Adresse löschen
                    </div>
                    <div class="card-body">
                        @include('partials.contact_email.delete')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
