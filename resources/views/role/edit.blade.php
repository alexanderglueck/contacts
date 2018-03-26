@extends('layouts.app')

@section('title', trans('ui.edit_role'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.edit_role') }}
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('roles.update', [$role->slug]) }}">
                            {{ method_field('PUT') }}
                            @include('partials.role.edit')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
