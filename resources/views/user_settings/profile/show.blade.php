@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="panel panel-default">
        <form action="{{ route('user_settings.profile.update') }}" method="post">
            @csrf
            {{ method_field('PUT') }}


            {!! \App\Helpers\Form::text('name', 'name', auth()->user()->name, true, true) !!}
            {!! \App\Helpers\Form::email('email', 'email', auth()->user()->email, true) !!}

            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        {{ trans('ui.update') }}
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection
