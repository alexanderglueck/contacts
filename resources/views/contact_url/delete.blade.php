@extends('layouts.app')

@section('title', 'Website löschen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">

                    <div class="card-header">
                        Website löschen
                    </div>
                    <div class="card-body">
                        @include('partials.contact_url.delete')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
