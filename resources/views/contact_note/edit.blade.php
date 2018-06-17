@extends('layouts.app')

@section('title', 'Edit note')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Edit note
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_notes.update', [$contact->slug, $contactNote->slug]) }}">
                            {{ method_field('PUT') }}
                            @include('partials.contact_note.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
