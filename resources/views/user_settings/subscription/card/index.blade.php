@extends('user_settings.layout.default')

@section('user_settings.content')
    <div class="panel">
        <div class="panel-heading">
            Update card
        </div>
        <div class="panel panel-default">
            <form action="{{ route('user_settings.subscription.card.store') }}" method="post" id="card-form">
                @csrf

                <p>Your card will be used for future payments.</p>

                <input id="card-holder-name" type="text" value="{{ auth()->user()->name }}">

                <div id="card-element"></div>

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button id="card-button" type="submit" class="btn btn-primary"
                                data-secret="{{ $intent->client_secret }}">
                            {{ trans('ui.changeCard') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stripe = Stripe('{{ config('cashier.key') }}');

            const elements = stripe.elements();
            const cardElement = elements.create('card');

            cardElement.mount('#card-element');

            const cardHolderName = document.getElementById('card-holder-name');
            const cardForm = document.getElementById('card-form');
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
                    let form = $("#card-form");

                    $("<input>").attr({
                        type: 'hidden',
                        name: 'token',
                        value: setupIntent.payment_method
                    }).appendTo(form);

                    form.submit();
                }
            });
        })
    </script>
@endsection
