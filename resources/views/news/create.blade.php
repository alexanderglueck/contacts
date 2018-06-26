@extends('layouts.public')

@section('title', trans('ui.create_news'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.create_news') }}
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('news.store') }}">
                            @include('partials.news.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
