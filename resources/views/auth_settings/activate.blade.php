@extends('layouts.app')

@section('title', '2FA Einstellungen')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>2FA Einstellungen</h5>
                    </div>
                    <div class="ibox-content">

                        <form class="form-horizontal" role="form" method="POST"
                              action="{{ route('auth_settings.enable') }}">
                            {{ csrf_field() }}

                            <p>Um 2FA zu aktivieren, klicken Sie auf den Button.</p>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit"
                                            class="btn btn-primary">
                                        2FA aktivieren
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