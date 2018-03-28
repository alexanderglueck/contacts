@extends('layouts.app')

@section('title', 'Websiten verwalten')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Websiten verwalten
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Kontakt Websiten: </strong>
                            @if (Auth::user()->hasPermissionTo('create urls'))
                                <br>
                                <a href="{{  route('contact_urls.create', [$contact->slug]) }}">Website
                                    hinzufügen</a>
                            @endif
                        </p>

                        @include('partials.contact_url.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
