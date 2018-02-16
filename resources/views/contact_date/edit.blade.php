@extends('layouts.app')

@section('title', 'Datum bearbeiten')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Datum bearbeiten
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_dates.update', [$contact->slug, $contactDate->slug]) }}">
                            {{ method_field('PUT') }}
                            @include('partials.contact_date.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
