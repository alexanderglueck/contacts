@extends('layouts.app')

@section('title', 'Manage calls')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Manage calls
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Calls: </strong>
                            @if (Auth::user()->hasPermissionTo('create calls'))
                                <br>
                                <a href="{{  route('contact_calls.create', [$contact->slug]) }}">
                                    Add calls
                                </a>
                            @endif
                        </p>

                        @include('partials.contact_call.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
