@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="card">
        <div class="card-header">
            Change password
        </div>
        <div class="card-body">
            <form action="{{ route('user_settings.password.update') }}" method="post">
                @csrf
                {{ method_field('PUT') }}


                {!! \App\Helpers\Form::password('password_current', 'Current password', true, true) !!}
                {!! \App\Helpers\Form::password('password', 'New password', true) !!}
                {!! \App\Helpers\Form::password('password_confirmation', 'Confirm password', true) !!}

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{ trans('Change password') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
