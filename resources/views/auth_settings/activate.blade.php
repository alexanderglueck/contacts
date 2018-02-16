@extends('layouts.app')

@section('title', '2FA Einstellungen')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        2FA Einstellungen
                    </div>
                    <div class="card-body">

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
