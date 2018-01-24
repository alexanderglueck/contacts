@extends('layouts.app')

@section('title', 'Kontakt löschen')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontakt löschen</h5>
                    </div>

                    <div class="ibox-content">
                        @include('partials.contact.delete')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection