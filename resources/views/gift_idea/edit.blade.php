@extends('layouts.app')

@section('title', trans('ui.edit_gift_idea'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.edit_gift_idea') }}
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" id="submitForm" role="form" method="POST" action="{{ route('gift_ideas.update', [$contact->slug, $giftIdea->id]) }}">
                            @method('PUT')
                            @include('partials.gift_idea.edit')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
