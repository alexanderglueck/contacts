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

                    <form action="{{ route('install.store') }}" method="post">
                        @csrf
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
                                @endif
                            </p>
                            @if (trim(config('contacts.googleMapsKey')) === '')
                                <div class="form-group">
                                    <label for="maps_api_key">GOOGLE_MAPS_GEOCODING_KEY</label>
                                    <input type="text" class="form-control" name="maps_api_key" id="maps_api_key" placeholder="GOOGLE_MAPS_GEOCODING_KEY" required>
                                </div>
                            @endif

                            <p>
                                <strong>
                                    2. {{ trans('install.stripe_api') }}:
                                </strong>
                                <br>
                                @if (trim(config('services.stripe.key')) !== '' && trim(config('services.stripe.secret')) !== '')
                                    {{ trans('install.stripe_api_done') }}
                                @else

                                    <a class="btn btn-primary" href="https://stripe.com/" target="_blank">
                                        {{ trans('install.stripe_api_get') }}
                                    </a>
                                    <br>
                                @endif
                            </p>
                            @if (trim(config('services.stripe.key')) === '' || trim(config('services.stripe.secret')) === '')
                                <div class="form-group">
                                    <label for="stripe_api_key">STRIPE_API_KEY</label>
                                    <input type="text" class="form-control" name="stripe_api_key" id="stripe_api_key" placeholder="STRIPE_API_KEY" value="{{ old('stripe_api_key', config('services.stripe.key')) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="stripe_api_secret">STRIPE_API_SECRET</label>
                                    <input type="text" class="form-control" name="stripe_api_secret" id="stripe_api_secret" placeholder="STRIPE_API_SECRET" value="{{ old('stripe_api_secret', config('services.stripe.secret')) }}" required>
                                </div>
                            @endif

                            <p>
                                <strong>
                                    {{ trans('install.finish') }}:
                                </strong>
                                <br>
                            </p>

                            <button type="submit" class="btn btn-primary">
                                {{ trans('install.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
