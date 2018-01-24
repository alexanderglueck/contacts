@extends('layouts.public')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('auth.login.title')</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('login.token.check') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('token') ? ' has-error' : '' }}">
                            <label for="token" class="col-md-4 control-label">Token</label>

                            <div class="col-md-6">
                                <input id="token" type="text" class="form-control" name="token" value="" required autofocus>

                                @if ($errors->has('token'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('token') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    @lang('auth.login.actions.default')
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
