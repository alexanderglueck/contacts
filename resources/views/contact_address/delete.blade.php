@extends('layouts.app')

@section('title', 'Adresse löschen')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Adresse löschen</h5>
                    </div>

                    <div class="ibox-content">

                        @include('partials.contact_address.delete')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection