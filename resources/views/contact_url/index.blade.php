@extends('layouts.app')

@section('title', 'Websiten verwalten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Websiten verwalten</h5>
                    </div>

                    <div class="ibox-content">

                        <p><strong>Kontakt Websiten: </strong><br>
                            <a href="{{  route('contact_urls.create', [$contact->slug]) }}">Website hinzufügen</a>
                        </p>


                        @include('partials.contact_url.index')


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
