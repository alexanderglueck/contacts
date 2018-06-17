@extends('layouts.app')

@section('title', 'Add call')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Add call
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_calls.store', [$contact->slug])  }}">
                            @include('partials.contact_call.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
