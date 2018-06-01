@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="card">
        <div class="card-header">
            Deactivate
        </div>
        <div class="card-body">
            <form action="{{ route('user_settings.deactivate.store') }}" method="post">
                @csrf

                @subscriptionnotcancelled
                <p>This will also cancel your subscription</p>
                @endsubscriptionnotcancelled

                {!! \App\Helpers\Form::password('current_password', 'current password', true, true) !!}

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{ trans('ui.deactivate_account') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
