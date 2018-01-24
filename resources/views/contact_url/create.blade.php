@extends('layouts.app')

@section('title', 'Website hinzufügen')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Website hinzufügen</h5>
                    </div>

                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('contact_urls.store', [$contact->slug])  }}">
                            @include('partials.contact_url.edit')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
