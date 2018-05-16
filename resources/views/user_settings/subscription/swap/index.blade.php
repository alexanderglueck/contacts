@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="panel">
        <div class="panel-heading">
            Swap
        </div>
        <div class="panel panel-default">
            <form action="{{ route('user_settings.profile.update') }}" method="post">
                @csrf
                {{ method_field('PUT') }}

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{ trans('ui.update') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
