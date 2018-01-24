    <ul>
            <li>
                <strong>Name: </strong> {{ $contactAddress->name }}<br/>
                <strong>Straße: </strong>{{ $contactAddress->street }}<br/>
                <strong>PLZ: </strong>   {{ $contactAddress->zip }}<br />
                <strong>Ort: </strong> {{ $contactAddress->city }}<br />
                <strong>Bundesland: </strong> {{ $contactAddress->state}}<br>
                <strong>Land: </strong> {{ $contactAddress->country->country }}<br>

            </li>
    </ul>
