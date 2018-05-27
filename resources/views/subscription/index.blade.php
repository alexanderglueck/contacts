@extends('layouts.public')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Subscription
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" id="payment-form" action="{{ route('subscription.store') }}" method="POST">
                        @csrf

                        <div class="form-group{{ $errors->has('plan') ? ' has-danger' : '' }}">
                            <label for="plan" class="col-md-4 form-control-label required">
                                {{ trans('ui.plan') }}
                            </label>

                            <div class="col-md-6">
                                <select name="plan" id="plan" class="form-control">
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->gateway_id }}"
                                                {{ return_if(request('plan') == $plan->slug || old('plan') == $plan->gateway_id, ' selected ') }}
                                        >
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

                        {!! \App\Helpers\Form::text('coupon', trans('ui.coupon')) !!}

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" id="pay" class="btn btn-primary">
                                    {{ trans('ui.pay') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-links')
    <script src="https://checkout.stripe.com/checkout.js"></script>
@endsection

@section('js')
    <script>
        let handler = StripeCheckout.configure({
            key: '{{ config('services.stripe.key') }}',
            locale: 'auto',
            zipCode: true,
            token: function (token) {
                let form = $("#payment-form");

                $("#pay").prop('disabled', true);
                $("<input>").attr({
                    type: 'hidden',
                    name: 'token',
                    value: token.id
                }).appendTo(form);

                form.submit();
            }
        });

        $('#pay').click(function (e) {
            e.preventDefault();

            handler.open({
                name: 'Contacts',
                description: 'Membership',
                currency: 'eur',
                key: '{{ config('services.stripe.key') }}',
                email: '{{ auth()->user()->email }}'
            });
        });
    </script>
@endsection
