@extends('layouts.app')

@section('title')
    Kontaktgruppe:  {{ $contactGroup->name  }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Kontaktgruppe Detailansicht
                    </div>
                    <div class="card-body">
                        <p>
                            <a href="{{ route('contact_groups.edit', $contactGroup->slug) }}">Bearbeiten</a>
                        </p>
                        <p>
                            <a href="{{ route('contact_groups.delete', $contactGroup->slug) }}">Löschen</a>
                        </p>

                        @include('partials.contact_group.show')
                    </div>

                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">{{ $contactGroup->name }}</div>

                    <div class="card-body">
                        @include('partials.contact.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
