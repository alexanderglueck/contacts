@extends('layouts.app')

@section('title')
    Nummer: {{ $contactNumber->name  }}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Nummer Detailansicht</h5>
                    </div>

                    <div class="ibox-content">
                        <p><a href="{{ route('contact_numbers.edit', [$contact->slug, $contactNumber->slug]) }}">Bearbeiten</a></p>
                        <p><a href="{{ route('contact_numbers.delete', [$contact->slug, $contactNumber->slug]) }}">Löschen</a></p>

                        @include('partials.contact_number.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
