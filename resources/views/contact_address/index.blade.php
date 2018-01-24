@extends('layouts.app')

@section('title', 'Adressen verwalten')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Adressen verwalten</h5>
                    </div>

                    <p>
                        <strong>Kontakt Adressen: </strong><br>
                        <a href="{{ route('contact_addresses.create', [$contact->slug]) }}">Adresse
                            hinzufügen</a>
                    </p>

                    @include('partials.contact_address.index')
                </div>
            </div>
        </div>
    </div>
@endsection
