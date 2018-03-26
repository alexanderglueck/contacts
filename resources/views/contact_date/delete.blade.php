@extends('layouts.app')

@section('title', 'Datum löschen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Datum löschen
                    </div>
                    <div class="card-body">
                        @include('partials.contact_date.delete')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
