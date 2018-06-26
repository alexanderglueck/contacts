@extends('layouts.public')

@section('title')
    {{ $news->title }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ $news->title }}
                    </div>
                    <div class="card-body">
                        @if (auth()->check() && auth()->user()->admin)
                            <p>
                                <a href="{{ route('news.edit', [$news->slug]) }}">
                                    {{ trans('ui.edit_announcement') }}
                                </a>
                            </p>
                            <p>
                                <a href="{{ route('news.delete', [$news->slug]) }}">
                                    {{ trans('ui.delete_announcement') }}
                                </a>
                            </p>
                        @endif

                        @if (auth()->check())
                            <p>
                                <a href="{{ route('news.mark_as_read', [$news->slug]) }}">
                                    {{ trans('ui.mark_as_read') }}
                                </a>
                            </p>
                        @endif

                        @include('partials.news.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
