@extends('layouts.app')

@section('title', 'Website hinzufügen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        Website hinzufügen
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_urls.store', [$contact->slug])  }}">
                            @include('partials.contact_url.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
