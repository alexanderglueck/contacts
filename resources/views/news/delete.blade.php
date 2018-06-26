@extends('layouts.public')

@section('title', trans('ui.delete_news'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.delete_news') }}
                    </div>
                    <div class="card-body">
                        @include('partials.news.delete')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
