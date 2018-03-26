@extends('layouts.app')

@section('title')
    {{ $announcement->title }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ $announcement->title }}
                    </div>
                    <div class="card-body">
                        <p>
                            <a href="{{ route('announcements.edit', [$announcement->slug]) }}">
                                {{ trans('ui.edit_announcement') }}
                            </a>
                        </p>
                        <p>
                            <a href="{{ route('announcements.delete', [$announcement->slug]) }}">
                                {{ trans('ui.delete_announcement') }}
                            </a>
                        </p>

                        @include('partials.announcement.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
