@extends('layouts.app')

@section('title')
    Website: {{  $contactUrl->name }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Website Detailansicht</h5>
                    </div>
                    <p>
                        <a href="{{ route('contact_urls.edit', [$contact->slug, $contactUrl->slug]) }}">Bearbeiten</a>
                    </p>
                    <p>
                        <a href="{{ route('contact_urls.delete', [$contact->slug, $contactUrl->slug]) }}">Löschen</a>
                    </p>

                    @include('partials.contact_url.show')
                </div>

            </div>
        </div>
    </div>

@endsection
