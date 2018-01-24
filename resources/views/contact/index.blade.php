@extends('layouts.app')

@section('title', 'Kontakte verwalten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontakte verwalten</h5>
                    </div>

                    <div class="ibox-content">
                        <p><strong>Kontakte: </strong><br>
                            <a href="{{ route('contacts.create') }}">Kontakt hinzufügen</a>
                        </p>


                        @include('partials.contact.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
