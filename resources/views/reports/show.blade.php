@extends('layouts.app')

@section('title', 'Berichte')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Ergebnis</h5>
                    </div>

                    <div class="ibox-content">
                        @if(count($contacts)>0)

                            <ul>
                                @foreach($contacts as $contact)
                                    <li>
                                        <strong><a href="{{ route('contacts.show', [$contact->slug]) }}">{{ $contact->fullname }}</a></strong>
                                    </li>
                                @endforeach
                            </ul>

                        @else
                            <p>Keine Kontakte verfügbar</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection