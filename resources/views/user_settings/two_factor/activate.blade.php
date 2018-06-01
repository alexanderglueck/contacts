@extends('user_settings.layout.default')

@section('title', '2FA Einstellungen')

@section('user_settings.content')
    <div class="card">
        <div class="card-header">
            2FA Einstellungen
        </div>
        <div class="card-body">

            <form class="form-horizontal" role="form" method="POST"
                  action="{{ route('user_settings.two_factor.enable') }}">
                @csrf

                <p>Um 2FA zu aktivieren, klicken Sie auf den
                    Button.</p>

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
@endsection
