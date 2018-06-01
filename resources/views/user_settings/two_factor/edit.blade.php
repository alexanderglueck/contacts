@extends('user_settings.layout.default')

@section('title', '2FA Einstellungen')

@section('user_settings.content')

    <div class="card">
        <div class="card-header">
            Backup Codes
        </div>
        <div class="card-body">
            <ol>
                @foreach($backupCodes as $backupCode)
                    <li>{{ $backupCode->value }}</li>
                @endforeach
            </ol>

            <form method="post" action="{{ route('user_settings.two_factor.destroy') }}">
                @csrf
                <input type="hidden" name="_method" value="DELETE">

                <input type="submit" value="deactivate"/>

            </form>
        </div>
    </div>

@endsection
