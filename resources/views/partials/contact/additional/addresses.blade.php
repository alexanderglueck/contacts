<a href="{{ route('contact_addresses.index', [$contact->slug]) }}">Adressen verwalten</a>

@if(count($addresses)>0)
    <ul>
        @foreach($addresses as $address)
            <li><strong>{{$address->name}}</strong><br/>
                <strong>Straße: </strong> {{ $address->street }}<br>
                <strong>PLZ, Ort: </strong> {{ $address->zip }}, {{ $address->city }}<br>
                <strong>Bundesland: </strong> {{ $address->state }}<br>
                <strong>Land: </strong> {{ $address->country->country }}
            </li>
        @endforeach

    </ul>
@else
    Keine Adressen verfügbar
@endif