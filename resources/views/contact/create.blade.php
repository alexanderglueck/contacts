@extends('layouts.app')

@section('title', 'Kontakt hinzufügen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">

                    <div class="card-header">
                        Kontakt hinzufügen
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contacts.store') }}">
                            @include('partials.contact.edit')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
