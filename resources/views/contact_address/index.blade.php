@extends('layouts.app')

@section('title', 'Adressen verwalten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Adressen verwalten</h5>
                    </div>

                    <div class="ibox-content">

                        <p><strong>Kontakt Adressen: </strong><br>
                            <a href="{{ route('contact_addresses.create', [$contact->slug]) }}">Adresse hinzufügen</a>
                        </p>


                        @include('partials.contact_address.index')


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
