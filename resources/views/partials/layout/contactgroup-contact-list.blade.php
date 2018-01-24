@if(count($contactGroups)>0)

    <ul>
        @foreach($contactGroups as $contactGroup)
            <li> {{ $contactGroup->name }}
                <ul>
                    @foreach($contactGroup->contacts as $contact)
                        <strong><a href="{{ route('contacts.show', [$contact->slug]) }}">{{ $contact->fullname }}</a></strong><br>
                    @endforeach
                </ul>

            </li>
        @endforeach
    </ul>

@else
    <p>Keine Kontaktgruppen verfügbar</p>
@endif