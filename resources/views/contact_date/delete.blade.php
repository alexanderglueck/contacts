@extends('layouts.app')

@section('title', 'Datum löschen')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Datum löschen</h5>
                    </div>
                    @include('partials.contact_date.delete')
                </div>

            </div>
        </div>
    </div>
@endsection