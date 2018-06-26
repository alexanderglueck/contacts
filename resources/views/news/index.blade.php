@extends('layouts.public')

@section('title', trans('ui.news'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.news') }}
                    </div>
                    <div class="card-body">
                        @if (auth()->check() && auth()->user()->admin)
                            <p>
                                <a href="{{ route('news.create') }}">
                                    {{ trans('ui.create_news') }}
                                </a>
                            </p>
                        @endif

                        <h3>active</h3>
                        @include('partials.news.index',  ['news' => $active])

                        <h3>inactive</h3>
                        @include('partials.news.index',  ['news' => $inactive])

                        @isset($displayed)
                            <h3>displayed</h3>
                            @include('partials.news.index',  ['news' => $displayed])
                        @endisset

                        @isset($hidden)
                            <h3>hidden</h3>
                            @include('partials.news.index',  ['news' => $hidden])
                        @endisset

                        @isset($read)
                            <h3>read</h3>
                            @include('partials.news.index',  ['news' => $read])
                        @endisset

                        @isset($unread)
                            <h3>unread</h3>
                            @include('partials.news.index',  ['news' => $unread])
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
