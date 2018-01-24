@extends('layouts.app')

@section('title', 'Kontakte verwalten')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Kontakte verwalten</h5>
                    </div>

                    <p><strong>Kontakte: </strong><br>
                        <a href="{{ route('contacts.create') }}">Kontakt
                            hinzufügen</a>
                    </p>


                    @include('partials.contact.index')
                </div>
            </div>
        </div>
    </div>

@endsection
