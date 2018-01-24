@extends('layouts.app')

@section('title', 'Datum bearbeiten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Datum bearbeiten</h5>
                    </div>

                    <div class="ibox-content">
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
