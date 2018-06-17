@extends('layouts.app')

@section('title', 'Manage notes')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Manage notes
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Notes: </strong>
                            @if (Auth::user()->hasPermissionTo('create notes'))
                                <br>
                                <a href="{{  route('contact_notes.create', [$contact->slug]) }}">
                                    Add note
                                </a>
                            @endif
                        </p>

                        @include('partials.contact_note.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
