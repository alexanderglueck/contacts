@extends('layouts.app')

@section('title', 'Nummer hinzufügen')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Nummer hinzufügen</h5>
                    </div>

                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_numbers.store', [$contact->slug]) }}">
                            @include('partials.contact_number.edit')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
