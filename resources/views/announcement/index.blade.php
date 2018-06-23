@extends('layouts.app')

@section('title', trans('ui.announcements'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.announcements') }}
                    </div>
                    <div class="card-body">
                        <p>
                            <a href="{{ route('announcements.create') }}">
                                {{ trans('ui.create_announcement') }}
                            </a>
                        </p>

                        <h2>Active</h2>
                        @include('partials.announcement.index', ['announcements' => $active])

                        <h2>Expired</h2>
                        @include('partials.announcement.index', ['announcements' => $inactive])

                        <h2>Relevant</h2>
                        @include('partials.announcement.index', ['announcements' => $displayed])

                        <h2>Irrelevant</h2>
                        @include('partials.announcement.index', ['announcements' => $hidden])

                        <h2>Read</h2>
                        @include('partials.announcement.index', ['announcements' => $read])

                        <h2>Unread</h2>
                        @include('partials.announcement.index', ['announcements' => $unread])

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
