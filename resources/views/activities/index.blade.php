@extends('layouts.app')

@section('title', trans('ui.activities'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.activities') }}
                    </div>
                    <div class="card-body">
                        @include('partials.activity.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
