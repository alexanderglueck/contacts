@extends('layouts.app')

@section('title', 'Delete call')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Delete call
                    </div>
                    <div class="card-body">
                        @include('partials.contact_call.delete')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
