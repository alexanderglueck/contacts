@extends('layouts.app')

@section('title', trans('ui.roles'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.roles') }}
                    </div>
                    <div class="card-body">
                        <p>
                            <a href="{{ route('roles.create') }}">
                                {{ trans('ui.create_role') }}
                            </a>
                        </p>

                        @include('partials.role.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
