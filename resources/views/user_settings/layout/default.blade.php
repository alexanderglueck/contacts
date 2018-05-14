@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('user_settings.layout.partials.navigation')
            </div>
            <div class="col-md-9">
                @yield('user_settings.content')
            </div>
        </div>
    </div>
@endsection
