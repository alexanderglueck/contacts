@extends('layouts.app')

@section('title', 'Websiten verwalten')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Websiten verwalten</h5>
                    </div>
                    <p>
                        <strong>Kontakt Websiten: </strong><br>
                        <a href="{{  route('contact_urls.create', [$contact->slug]) }}">Website
                            hinzufügen</a>
                    </p>

                    @include('partials.contact_url.index')
                </div>
            </div>
        </div>
    </div>
@endsection
