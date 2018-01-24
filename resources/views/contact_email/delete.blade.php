@extends('layouts.app')

@section('title', 'E-Mail Adresse löschen')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>E-Mail Adresse löschen</h5>
                    </div>

                    <div class="ibox-content">
                        @include('partials.contact_email.delete')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection