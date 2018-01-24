@extends('layouts.app')

@section('title', 'Berichte')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card card-default">


                <div class="card-body">
                    <div class="card-title">
                        <h5>Ergebnis</h5>
                    </div>

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