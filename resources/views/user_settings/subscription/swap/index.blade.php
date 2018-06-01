@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="panel">
        <div class="panel-heading">
            Swap
        </div>
        <div class="panel panel-default">
            <form action="{{ route('user_settings.subscription.swap.store') }}" method="post">
                @csrf

                <p>Swap your plan.</p>

                <p>Current plan: € {{ auth()->user()->plan->price / 100 }}</p>

                <div class="form-group{{ $errors->has('plan') ? ' has-danger' : '' }}">
                    <label for="plan" class="col-md-4 form-control-label">
                        {{ trans('ui.plan') }}
                        <span class="required">*</span>
                    </label>

                    <div class="col-md-6">
                        <select name="plan" id="plan" class="form-control">
                            @foreach($plans as $plan)
                                <option value="{{ $plan->gateway_id }}" {{ return_if(old('plan') == $plan->gateway_id, ' selected ') }} >
                                    {{ $plan->name }}
                                    (€ {{ $plan->price / 100 }} )
                                </option>
                            @endforeach
                        </select>

                        @if ($errors->has('plan'))
                            <span class="form-text">
                                <strong>{{ $errors->first('plan') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{ trans('ui.swapPlan') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
