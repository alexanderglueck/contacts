@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Choose your team
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('tenant.store') }}" method="POST">
                            @csrf

                            <div class="form-group{{ $errors->has('team') ? ' has-danger' : '' }}">
                                <label for="team" class="col-md-4 form-control-label required">
                                    {{ trans('ui.team') }}
                                </label>

                                <div class="col-md-6">
                                    <select name="team" id="team" class="form-control">
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ return_if(old('team') == $team->id, ' selected ') }}>
                                                {{ $team->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('team'))
                                        <span class="form-text">
                                        <strong>{{ $errors->first('team') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" id="pay" class="btn btn-primary">
                                        {{ trans('ui.switch_team') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
