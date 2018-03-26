@extends('layouts.app')

@section('title', 'Kontaktgruppe bearbeiten')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Kontaktgruppe bearbeiten
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_groups.update', [$contactGroup->slug]) }}">
                            {{ method_field('PUT') }}
                            @include('partials.contact_group.edit')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
