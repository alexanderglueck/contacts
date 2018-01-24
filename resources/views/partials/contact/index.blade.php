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