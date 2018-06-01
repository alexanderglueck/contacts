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

                <div class="form-group">
                    <div class="col-md-8 col-md-offset-4">
                        <button id="update" type="submit" class="btn btn-primary">
                            {{ trans('ui.changeCard') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://checkout.stripe.com/checkout.js"></script>
    <script>
        let handler = StripeCheckout.configure({
            key: '{{ config('services.stripe.key') }}',
            locale: 'auto',
            zipCode: true,
            token: function (token) {
                let form = $("#card-form");

                $("#update").prop('disabled', true);
                $("<input>").attr({
                    type: 'hidden',
                    name: 'token',
                    value: token.id
                }).appendTo(form);

                form.submit();
            }
        });

        $('#update').click(function (e) {
            e.preventDefault();

            handler.open({
                name: 'Contacts',
                currency: 'eur',
                key: '{{ config('services.stripe.key') }}',
                email: '{{ auth()->user()->email }}',
                panelLabel: 'Update card'
            });
        });
    </script>
@endsection
