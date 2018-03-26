@extends('layouts.app')

@section('title', trans('ui.create_role'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.create_role') }}
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('roles.store') }}">
                            @include('partials.role.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
