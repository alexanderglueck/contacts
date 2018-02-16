@extends('layouts.app')

@section('title', 'Kontakt löschen')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Kontakt löschen
                    </div>
                    <div class="card-body">
                        @include('partials.contact.delete')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
