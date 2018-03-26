    <ul>
            <li>
                <strong>Name: </strong> {{ $contactNumber->name }}<br/>
                <strong>Nummer: </strong> <a href="tel:{{$contactNumber->formatted_number }}">{{ $contactNumber->number}}</a><br/>
            </li>
    </ul>
