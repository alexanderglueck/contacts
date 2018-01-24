@extends('layouts.app')

@section('title', 'Adresse löschen')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Adresse löschen</h5>
                    </div>
                    @include('partials.contact_address.delete')
                </div>

            </div>
        </div>
    </div>
@endsection