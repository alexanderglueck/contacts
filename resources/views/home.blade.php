@extends('layouts.app')

@section('title', trans('ui.dashboard'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ trans('ui.dashboard') }}
                    </div>

                    <div class="card-body">
                        @subscribed
                        @if ( ! Auth::user()->hasAnyPermission([
                            'view contacts',
                            'view contactGroups',
                            'view calendar',
                            'view map'
                        ]))
                            <p>
                                {{ trans('ui.no_permissions_ask_admin') }}
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('view contacts'))
                            <p>
                                <strong>
                                    {{ trans('ui.contacts') }}:
                                </strong><br>
                                <a href="{{ route('contacts.index') }}">
                                    {{ trans('ui.manage_contacts') }}
                                </a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('view contactGroups'))
                            <p>
                                <strong>
                                    {{ trans('ui.contact_groups') }}:
                                </strong><br>
                                <a href="{{ route('contact_groups.index') }}">
                                    {{ trans('ui.manage_contact_groups') }}
                                </a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('view calendar'))
                            <p>
                                <strong>
                                    {{ trans('ui.calendar') }}:
                                </strong><br>
                                <a href="{{ route('calendar.index') }}">
                                    {{ trans('ui.view_calendar') }}
                                </a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('view map'))
                            <p>
                                <strong>
                                    {{ trans('ui.map') }}:
                                </strong><br>
                                <a href="{{ route('map.index') }}">
                                    {{ trans('ui.view_map') }}
                                </a>
                            </p>
                        @endif
                        @else
                            <p>
                                You do not have an active subscription. <br>
                                <a class="btn btn-primary" href="{{ route('plans.index') }}">
                                    View the available plans
                                </a>
                            </p>
                        @endsubscribed
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
