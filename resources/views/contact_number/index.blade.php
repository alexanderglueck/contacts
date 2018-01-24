@extends('layouts.app')

@section('title', 'Nummern verwalten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Nummern verwalten</h5>
                    </div>

                    <div class="ibox-content">
                        <p><strong>Kontakt Nummern: </strong><br>
                            <a href="{{ route('contact_numbers.create', [$contact->slug]) }}">Nummer hinzufügen</a>
                        </p>


                        @include('partials.contact_number.index')


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
