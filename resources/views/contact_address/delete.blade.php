@extends('layouts.app')

@section('title', 'Adresse löschen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Adresse löschen
                    </div>
                    <div class="card-body">
                        @include('partials.contact_address.delete')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
