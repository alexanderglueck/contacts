@extends('layouts.app')

@section('title', 'Kontaktgruppe löschen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Kontaktgruppe löschen
                    </div>
                    <div class="card-body">
                        @include('partials.contact_group.delete')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
