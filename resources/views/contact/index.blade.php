@extends('layouts.app')

@section('title', trans('ui.manage_contacts'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">

                    <div class="card-header">
                        {{ trans('ui.manage_contacts') }}
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>
                                {{ trans('ui.contacts') }}:
                            </strong>

                            @if (Auth::user()->hasPermissionTo('create contacts'))
                                <br>
                                <a href="{{ route('contacts.create') }}">
                                    {{ trans('ui.create_contact') }}
                                </a>
                            @endif
                        </p>

                        @include('partials.contact.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
