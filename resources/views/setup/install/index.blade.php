@extends('layouts.public')

@section('title', trans('install.install'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ trans('install.install') }}
                    </div>

                    <div class="card-body">
                        <p>
                            <strong>
                                1. {{ trans('install.maps_api') }}:
                            </strong>
                            <br>
                            @if (trim(config('contacts.googleMapsKey')) !== '')
                                {{ trans('install.maps_api_done') }}
                            @else

                                <a class="btn btn-primary" href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">
                                    {{ trans('install.maps_api_get') }}
                                </a>
                                <br>

                                {!! trans('install.maps_api_env') !!}
                            @endif
                        </p>
                        @if (trim(config('contacts.googleMapsKey')) === '')
                            <pre><code>GOOGLE_MAPS_GEOCODING_KEY=@{{GOOGLE_MAPS_API_KEY}}</code></pre>
                        @endif

                        <p>
                            <strong>
                                {{ trans('install.finish') }}:
                            </strong>
                            <br>
                        </p>

                        <pre><code>CONTACTS_INSTALLED=true</code></pre>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
