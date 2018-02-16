@extends('layouts.app')

@section('title', 'Berichte')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">

                    <div class="card-header">
                        Ergebnis
                    </div>
                    <div class="card-body">
                        @if(count($contacts)>0)

                            {{ $contacts->links() }}

                            <ul>
                                @foreach($contacts as $contact)
                                    <li>
                                        <strong><a href="{{ route('contacts.show', [$contact->slug]) }}">{{ $contact->fullname }}</a></strong>
                                    </li>
                                @endforeach
                            </ul>

                            {{ $contacts->links() }}

                        @else
                            <p>Keine Kontakte verfügbar</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
@endsection
