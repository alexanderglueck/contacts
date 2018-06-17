@extends('layouts.app')

@section('title', 'Delete note')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">

                    <div class="card-header">
                        Delete note
                    </div>
                    <div class="card-body">
                        @include('partials.contact_note.delete')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
