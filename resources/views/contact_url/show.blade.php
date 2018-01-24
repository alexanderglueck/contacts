@extends('layouts.app')

@section('title')
    Website: {{  $contactUrl->name }}
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Website Detailansicht</h5>
                    </div>

                    <div class="ibox-content">
                        <p><a href="{{ route('contact_urls.edit', [$contact->slug, $contactUrl->slug]) }}">Bearbeiten</a></p>
                        <p><a href="{{ route('contact_urls.delete', [$contact->slug, $contactUrl->slug]) }}">Löschen</a></p>

                        @include('partials.contact_url.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
