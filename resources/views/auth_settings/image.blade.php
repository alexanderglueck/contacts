@extends('layouts.app')

@section('title', '2FA Einstellungen')

@section('content')

    <form method="post" action="{{ route('auth_settings.check') }}">
        {{ csrf_field() }}

        <p>
            Um 2-Factor-Authentication zu aktivieren musst du einen generierten
            Code von der Google Authenticator App eingeben, um sicherzustellen,
            dass du den korrekten QR-Code gescannt hast.
        </p>
        <p>
            Wenn du diesen Vorgang abbrichst, ist 2FA <strong>nicht</strong> für deinen Account aktiviert.
        </p>
        <img src="{{ $image }}"/>

        <br/>

        <input type="text" name="secret" value=""/>
        <input type="submit" value="Aktivieren"/>

    </form>

@endsection