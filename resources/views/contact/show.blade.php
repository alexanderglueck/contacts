@extends('layouts.app')

@section('title')
    {{ $contact->fullname }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.contact_detail') }}
                    </div>
                    <div class="card-body">
                        @if (Auth::user()->hasPermissionTo('edit contacts'))
                            <p>
                                <a href="{{ route('contacts.edit', [$contact->slug]) }}">
                                    {{ trans('ui.edit_contact') }}
                                </a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('delete contacts'))
                            <p>
                                <a href="{{ route('contacts.delete', [$contact->slug]) }}">
                                    {{ trans('ui.delete_contact') }}
                                </a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('edit contacts'))
                            <p>
                                <a href="{{ route('contacts.image', [$contact->slug]) }}">
                                    {{ trans('ui.image') }}
                                </a>
                            </p>
                        @endif

                        @include('partials.contact.show')

                        <contact-presence-channel contact-id="{{ $contact->id }}" tenant-id="{{ auth()->user()->currentTeam->id }}" ></contact-presence-channel>
                    </div>

                </div>
            </div>
        </div>
        @if (Auth::user()->hasPermissionTo('view comments'))
            <div class="row justify-content-center">
                <div class="col-md-12">

                    @include('partials.comment.index')

                </div>
            </div>
        @endif
    </div>
@endsection
