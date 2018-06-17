@extends('layouts.app')

@section('title')
    Note: {{  $contactNote->name }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Note
                    </div>
                    <div class="card-body">
                        @if (Auth::user()->hasPermissionTo('edit notes'))
                            <p>
                                <a href="{{ route('contact_notes.edit', [$contact->slug, $contactNote->slug]) }}">
                                    Edit
                                </a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('delete notes'))
                            <p>
                                <a href="{{ route('contact_notes.delete', [$contact->slug, $contactNote->slug]) }}">
                                    Löschen
                                </a>
                            </p>
                        @endif

                        @include('partials.contact_note.show')
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
