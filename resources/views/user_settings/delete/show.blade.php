@extends('user_settings.layout.default')

@section('title', trans('ui.delete_account'))

@section('user_settings.content')

    <div class="card">
        <div class="card-header">
            {{ trans('ui.delete_account') }}
        </div>
        <div class="card-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('user_settings.delete.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-danger">
                            {{ trans('ui.delete_account') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
