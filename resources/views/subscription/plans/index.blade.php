@extends('layouts.public')

@section('title')
    @lang('ui.plans.title')
@endsection

@section('content')
    <div class="container">
        <h1 class="display-4 text-center">
            @lang('ui.plans.title')
        </h1>
        <h2 class="">
            @lang('ui.plans.personal')
        </h2>
        <div class="card-deck mb-3 text-center">
            @foreach($plans as $plan)
                <div class="card mb-4 box-shadow">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">
                            {{ $plan->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title pricing-card-title">
                            € {{ $plan->price / 100 }}
                        </h1>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>Access to all personal features</li>
                        </ul>
                        <a type="button" class="btn btn-lg btn-block btn-primary"
                           href="{{ route('subscription.index') }}?plan={{$plan->slug}}">
                            @lang('ui.plans.actions.join')
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <h2 class="">
            @lang('ui.plans.team')
        </h2>
        <div class="card-deck mb-3 text-center">
            @foreach($teamPlans as $plan)
                <div class="card mb-4 box-shadow">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">
                            {{ $plan->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title pricing-card-title">
                            € {{ $plan->price / 100 }}
                        </h1>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>Up to {{ $plan->teams_limit }} users</li>
                            <li>Access to all features</li>
                            <li>Invite team members</li>
                        </ul>
                        <a type="button" class="btn btn-lg btn-block btn-primary"
                           href="{{ route('subscription.index') }}?plan={{$plan->slug}}">
                            @lang('ui.plans.actions.join')
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
