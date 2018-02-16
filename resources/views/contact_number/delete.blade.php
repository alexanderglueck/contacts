@extends('layouts.app')

@section('title', 'Nummer löschen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Nummer löschen
                    </div>
                    <div class="card-body">
                        @include('partials.contact_number.delete')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
