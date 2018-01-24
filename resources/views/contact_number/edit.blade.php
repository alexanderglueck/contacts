@extends('layouts.app')

@section('title', 'Nummer bearbeiten')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Nummer bearbeiten</h5>
                    </div>

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_numbers.update', [$contact->slug, $contactNumber->slug]) }}">
                        {{ method_field('PUT') }}
                        @include('partials.contact_number.edit')
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
