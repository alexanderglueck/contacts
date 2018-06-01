@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="card">
        <div class="card-header">
            API Token bearbeiten
        </div>
        <div class="card-body">

            <form class="form-horizontal" role="form" method="POST"
                  action="{{ route('user_settings.api_token.update', [$user->slug]) }}">
                <input type="hidden" name="_method" value="PUT">
                @csrf


                <div class="form-group{{ $errors->has('api_token') ? ' has-error' : '' }}">
                    <label for="api_token"
                           class="col-md-4 control-label">API
                        Token</label>

                    <div class="col-md-6">
                        <input type="text" readonly
                               value="{{ $user->api_token }}"
                               class="form-control" name="api_token"
                               id="api_token">

                        @if ($errors->has('api_token'))
                            <span class="help-block">
                                            <strong>{{ $errors->first('api_token') }}</strong>
                                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit"
                                class="btn btn-primary">
                            Neuen API Token generieren
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
