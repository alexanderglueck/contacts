@extends('layouts.app')

@section('title', trans('ui.delete_role'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.delete_role') }}
                    </div>
                    <div class="card-body">
                        @include('partials.role.delete')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
