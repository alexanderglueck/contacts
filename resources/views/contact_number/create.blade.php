@extends('layouts.app')

@section('title', 'Nummer hinzufügen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Nummer hinzufügen
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_numbers.store', [$contact->slug]) }}">
                            @include('partials.contact_number.edit')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
