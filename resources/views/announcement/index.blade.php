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

                        @include('partials.announcement.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
