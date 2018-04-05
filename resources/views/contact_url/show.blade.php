@extends('layouts.app')

@section('title')
    Website: {{  $contactUrl->name }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Website Detailansicht
                    </div>
                    <div class="card-body">
                        @if (Auth::user()->hasPermissionTo('edit urls'))
                            <p>
                                <a href="{{ route('contact_urls.edit', [$contact->slug, $contactUrl->slug]) }}">Bearbeiten</a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('delete urls'))
                            <p>
                                <a href="{{ route('contact_urls.delete', [$contact->slug, $contactUrl->slug]) }}">Löschen</a>
                            </p>
                        @endif

                        @include('partials.contact_url.show')
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
