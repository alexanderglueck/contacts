@extends('layouts.app')

@section('title', 'Benutzer bearbeiten')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Benutzer bearbeiten</h5>
                    </div>
                    <div class="ibox-content">

                        <form class="form-horizontal" role="form" method="POST"
                              action="{{ route('user_settings.update', [$user->slug]) }}">
                            <input type="hidden" name="_method" value="PUT">
                            {{ csrf_field() }}


                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name"
                                       class="col-md-4 control-label">Name<span
                                            class="required">*</span></label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control" name="name"
                                           value="{{ old('name', $user->name) }}"
                                           required>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password"
                                       class="col-md-4 control-label">Neues
                                    Passwort</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="form-control" name="password"
                                           value="">

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password_confirmation"
                                       class="col-md-4 control-label">Neues
                                    Passwort wiederholen</label>

                                <div class="col-md-6">
                                    <input id="password_confirmation"
                                           type="password" class="form-control"
                                           name="password_confirmation"
                                           value="">

                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit"
                                            class="btn btn-primary">
                                        Benutzer aktualisieren
                                    </button>

                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>2FA bearbeiten</h5>
                    </div>
                    <div class="ibox-content">
                        <p>
                            <a href="{{ route('auth_settings.edit') }}"
                               class="btn btn-primary">
                                2FA Einstellungen
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Benutzerbild bearbeiten</h5>
                    </div>
                    <div class="ibox-content">

                        <form class="form-horizontal" role="form" method="POST"
                              enctype="multipart/form-data"
                              action="{{ route('user_settings.update_image', [$user->slug]) }}">
                            <input type="hidden" name="_method" value="PUT">
                            {{ csrf_field() }}


                            <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                <label for="image"
                                       class="col-md-4 control-label">Bild</label>

                                <div class="col-md-6">
                                    <input id="image" type="file"
                                           class="form-control" name="image"
                                           value="">

                                    @if ($errors->has('image'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('image') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit"
                                            class="btn btn-primary">
                                        Bild aktualisieren
                                    </button>

                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>API Token bearbeiten</h5>
                    </div>
                    <div class="ibox-content">

                        <form class="form-horizontal" role="form" method="POST"
                              action="{{ route('user_settings.update_api_token', [$user->slug]) }}">
                            <input type="hidden" name="_method" value="PUT">
                            {{ csrf_field() }}


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

                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{ trans('ui.notification_settings') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <p>
                            <a href="{{ route('notification_settings.edit') }}"
                               class="btn btn-primary">
                                {{ trans('ui.notification_settings') }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
