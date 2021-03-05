@extends('layouts.public')

@section('content')
    <div class="container">
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

                            {!! \App\Helpers\Form::text('card-holder-name', trans('Card holder name'), auth()->user()->name, true) !!}

                            <div id="card-element"></div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" id="card-button" class="btn btn-primary"
                                            data-secret="{{ $intent->client_secret }}">
                                        {{ trans('ui.pay') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-links')
    <script src="https://js.stripe.com/v3/"></script>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stripe = Stripe('{{ config('cashier.key') }}');

            const elements = stripe.elements();
            const cardElement = elements.create('card');

            cardElement.mount('#card-element');

            const cardHolderName = document.getElementById('card-holder-name');
            const cardForm = document.getElementById('payment-form');
            const cardButton = document.getElementById('card-button');
            const clientSecret = cardButton.dataset.secret;

            cardForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                $("#card-button").prop('disabled', true);

                const {setupIntent, error} = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: cardElement,
                            billing_details: {name: cardHolderName.value}
                        }
                    }
                );

                if (error) {
                    // Display "error.message" to the user...
                    $("#card-button").prop('disabled', false);
                } else {
                    // The card has been verified successfully...
                    let form = $("#payment-form");

                    $("<input>").attr({
                        type: 'hidden',
                        name: 'token',
                        value: setupIntent.payment_method
                    }).appendTo(form);

                    form.submit();
                }
            });
        });
    </script>
@endsection
