@extends('layouts.app')

@section('title', trans('ui.create_announcement'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.create_announcement') }}
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('announcements.store') }}">
                            @include('partials.announcement.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
