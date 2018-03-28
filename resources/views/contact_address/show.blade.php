@extends('layouts.app')

@section('title')
    {{trans('ui.address')}}:  {{ $contactAddress->name }}
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        {{ trans('ui.address_detail') }}
                    </div>
                    <div class="card-body">
                        @if (Auth::user()->hasPermissionTo('edit addresses'))
                            <p>
                                <a href="{{ route('contact_addresses.edit', [$contact->slug, $contactAddress->slug]) }}">Bearbeiten</a>
                            </p>
                        @endif

                        @if (Auth::user()->hasPermissionTo('delete addresses'))
                            <p>
                                <a href="{{ route('contact_addresses.delete', [$contact->slug, $contactAddress->slug]) }}">Löschen</a>
                            </p>
                        @endif

                        @include('partials.contact_address.show')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
