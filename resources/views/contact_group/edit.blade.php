@extends('layouts.app')

@section('title', 'Kontaktgruppe bearbeiten')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Kontaktgruppe bearbeiten</h5>
                    </div>

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_groups.update', [$contactGroup->slug]) }}">
                        {{ method_field('PUT') }}
                        @include('partials.contact_group.edit')
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
