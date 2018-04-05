@extends('layouts.app')

@section('title')
    {{ trans('ui.gift_idea') }}:  {{ $giftIdea->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.gift_idea') }}
                    </div>
                    <div class="card-body">
                        @if (Auth::user()->hasPermissionTo('edit giftIdeas'))
                            <p>
                                <a href="{{ route('gift_ideas.edit', [$contact->slug, $giftIdea->id]) }}">
                                    {{ trans('ui.edit_gift_idea') }}
                                </a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('delete giftIdeas'))
                            <p>
                                <a href="{{ route('gift_ideas.delete', [$contact->slug, $giftIdea->id]) }}">
                                    {{ trans('ui.delete_gift_idea') }}
                                </a>
                            </p>
                        @endif

                        @include('partials.gift_idea.show')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
