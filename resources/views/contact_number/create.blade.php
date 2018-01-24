@extends('layouts.app')

@section('title', 'Nummer hinzufügen')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Nummer hinzufügen</h5>
                    </div>
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_numbers.store', [$contact->slug]) }}">
                        @include('partials.contact_number.edit')
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
