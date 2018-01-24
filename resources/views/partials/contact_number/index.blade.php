@if(count($contactNumbers)>0)

    <ul>
        @foreach($contactNumbers as $contactNumber)
            <li>
                <strong><a href="{{ route('contact_numbers.show', [$contact->slug, $contactNumber->slug]) }}">{{ $contactNumber->name }}</a></strong>
            </li>
        @endforeach
    </ul>

@else
    <p>Keine Nummer verfügbar</p>
@endif