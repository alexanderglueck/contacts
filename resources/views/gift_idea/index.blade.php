@extends('layouts.app')

@section('title', trans('ui.manage_gift_ideas'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.manage_gift_ideas') }}
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>{{ trans('ui.gift_ideas') }}: </strong>

                            @if (Auth::user()->hasPermissionTo('create giftIdeas'))
                                <br>
                                <a href="{{ route('gift_ideas.create', [$contact->slug]) }}">
                                    {{ trans('ui.create_gift_idea') }}
                                </a>
                            @endif
                        </p>

                        @include('partials.gift_idea.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
