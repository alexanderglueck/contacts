@extends('layouts.app')

@section('title', 'Kontakt hinzufügen')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Kontakt hinzufügen</h5>
                    </div>

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('contacts.store') }}">
                        @include('partials.contact.edit')
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
