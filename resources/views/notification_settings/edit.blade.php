@extends('layouts.app')

@section('title', trans('ui.notification_settings'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ trans('ui.notification_settings') }}
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('notification_settings.update') }}">
                            {{ method_field('PUT') }}
                            @csrf

                            <div class="form-check">
                                <input class="form-check-input" {{ $settings->send_daily ? ' checked ' : '' }} type="checkbox" value="1" id="send_daily" name="send_daily">
                                <label class="form-check-label" for="send_daily">
                                    {{ trans('notification.send_daily') }}
                                </label>
                                @if ($errors->has('send_daily'))
                                    {{ $errors->first('send_daily') }}
                                @endif
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" {{ $settings->send_weekly ? ' checked ' : '' }} type="checkbox" value="1" id="send_weekly" name="send_weekly">
                                <label class="form-check-label" for="send_weekly">
                                    {{ trans('notification.send_weekly') }}
                                </label>
                                @if ($errors->has('send_weekly'))
                                    {{ $errors->first('send_weekly') }}
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ trans('ui.update') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
