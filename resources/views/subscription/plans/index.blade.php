@extends('layouts.public')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <ul>
                    @foreach($plans as $plan)
                        <li class="list-group-item">
                            <a href="{{ route('subscription.index') }}?plan={{$plan->slug}}">{{ $plan->name }} {{ $plan->price / 100 }}</a>
                        </li>
                    @endforeach

                    <li class="list-group-item">
                        <a href="{{ route('plans.teams.index') }}">
                            Team plans
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
