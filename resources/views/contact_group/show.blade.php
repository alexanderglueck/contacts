@extends('layouts.app')

@section('title')
    Kontaktgruppe:  {{ $contactGroup->name  }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Kontaktgruppe Detailansicht</h5>
                    </div>
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

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-title">{{ $contactGroup->name }}</div>

                <div class="card-body">
                    @include('partials.contact.index')
                </div>
            </div>
        </div>
    </div>
@endsection
