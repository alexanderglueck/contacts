@extends('layouts.app')

@section('title')
    {{ $role->name }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ $role->name }}
                    </div>
                    <div class="card-body">
                        <p>
                            <a href="{{ route('roles.edit', [$role->slug]) }}">
                                {{ trans('ui.edit_role') }}
                            </a>
                        </p>
                        <p>
                            <a href="{{ route('roles.delete', [$role->slug]) }}">
                                {{ trans('ui.delete_role') }}
                            </a>
                        </p>

                        @include('partials.role.show')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
