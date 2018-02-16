@extends('layouts.app')

@section('title', '2FA Einstellungen')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
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

                        <form method="post" action="{{ route('auth_settings.disable') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="DELETE">

                            <input type="submit" value="deactivate"/>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
