@extends('layouts.app')

@section('title', 'Kontakt bearbeiten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontakt bearbeiten</h5>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contacts.update', [$contact->slug]) }}">
                            {{ method_field('PUT') }}
                            @include('partials.contact.edit')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
