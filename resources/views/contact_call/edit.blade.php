@extends('layouts.app')

@section('title', 'Edit call')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Edit call
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_calls.update', [$contact->slug, $contactCall->id]) }}">
                            {{ method_field('PUT') }}
                            @include('partials.contact_call.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
