@extends('layouts.app')

@section('title')
    Kontaktgruppe:  {{ $contactGroup->name  }}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kontaktgruppe Detailansicht</h5>
                    </div>

                    <div class="ibox-content">
                        <p><a href="{{ route('contact_groups.edit', $contactGroup->slug) }}">Bearbeiten</a></p>
                        <p><a href="{{ route('contact_groups.delete', $contactGroup->slug) }}">Löschen</a></p>

                        @include('partials.contact_group.show')
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{ $contactGroup->name }}</h5>
                    </div>

                    <div class="ibox-content">
                        @include('partials.contact.index')
                    </div>
                </div>
            </div>
    </div>
@endsection
