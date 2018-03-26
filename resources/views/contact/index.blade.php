@extends('layouts.app')

@section('title', 'Kontakte verwalten')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">

                    <div class="card-header">
                        Kontakte verwalten
                    </div>
                    <div class="card-body">
                        <p><strong>Kontakte: </strong><br>
                            <a href="{{ route('contacts.create') }}">Kontakt
                                hinzufügen</a>
                        </p>

                        @include('partials.contact.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
