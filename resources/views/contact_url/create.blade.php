@extends('layouts.app')

@section('title', 'Website hinzufügen')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Website hinzufügen</h5>
                    </div>
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_urls.store', [$contact->slug])  }}">
                        @include('partials.contact_url.edit')
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
