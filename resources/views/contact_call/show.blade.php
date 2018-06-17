@extends('layouts.app')

@section('title')
    Call: {{  $contactCall->called_at }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Call
                    </div>
                    <div class="card-body">
                        @if (Auth::user()->hasPermissionTo('edit calls'))
                            <p>
                                <a href="{{ route('contact_calls.edit', [$contact->slug, $contactCall->id]) }}">Bearbeiten</a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('delete calls'))
                            <p>
                                <a href="{{ route('contact_calls.delete', [$contact->slug, $contactCall->id]) }}">Löschen</a>
                            </p>
                        @endif

                        @include('partials.contact_call.show')
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
