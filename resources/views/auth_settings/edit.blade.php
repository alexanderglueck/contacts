@extends('layouts.app')

@section('title', '2FA Einstellungen')

@section('content')

    <h2>Backup Codes</h2>
    <ol>
        @foreach($backupCodes as $backupCode)
        <li>{{ $backupCode->value }}</li>
        @endforeach
    </ol>

    <form method="post" action="{{ route('auth_settings.disable') }}">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="DELETE">

        <input type="submit" value="deactivate"/>

    </form>

@endsection