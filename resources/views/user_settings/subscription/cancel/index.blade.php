@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="panel">
        <div class="panel-heading">
            Cancel
        </div>
        <div class="panel panel-default">

            <form action="{{ route('user_settings.subscription.cancel.store') }}" method="post">
                @csrf

                <p>Confirm to cancel your subscription</p>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{ trans('ui.cancel') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
