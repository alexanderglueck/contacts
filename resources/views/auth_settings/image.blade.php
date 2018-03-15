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
                        <form method="post" action="{{ route('auth_settings.check') }}">
                            @csrf

                            <p>
                                Um 2-Factor-Authentication zu aktivieren musst
                                du einen generierten
                                Code von der Google Authenticator App eingeben,
                                um sicherzustellen,
                                dass du den korrekten QR-Code gescannt hast.
                            </p>
                            <p>
                                Wenn du diesen Vorgang abbrichst, ist 2FA
                                <strong>nicht</strong> für deinen Account
                                aktiviert.
                            </p>
                            <img src="{{ $image }}"/>

                            <br/>

                            <input type="text" class="" name="secret" value=""/>
                            <input type="submit" class="btn btn-primary" value="Aktivieren"/>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
