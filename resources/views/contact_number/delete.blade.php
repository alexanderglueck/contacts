@extends('layouts.app')

@section('title', 'Nummer löschen')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Nummer löschen</h5>
                    </div>
                    @include('partials.contact_number.delete')
                </div>

            </div>
        </div>
    </div>
@endsection