@extends('layouts.app')

@section('title', 'Datum hinzufügen')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Datum hinzufügen</h5>
                    </div>
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_dates.store', [$contact->slug]) }}">
                        @include('partials.contact_date.edit')
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
